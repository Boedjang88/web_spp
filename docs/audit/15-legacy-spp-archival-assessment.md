# Audit Report 15: Legacy SPP Transaction Archival Assessment

> [!IMPORTANT]
> **READ-ONLY ASSESSMENT NOTICE:**
> In accordance with Section 2 of `AGENTS.md` ("Zero Destructive Operations: Never drop tables, truncate financial ledgers, or wipe student transcripts") and Section 5 of the Batch G directive, this assessment is strictly **READ-ONLY**.
> **NO data has been moved, modified, truncated, deleted, or migrated.**

---

## 1. System Overview & Domain Bridging Context
The application maintains two concurrent financial domains:
1. **Legacy School SPP Domain:** Managed via `spps` and `pembayarans` (cashier-based, monthly payments e.g., 'Januari 2026', paper receipts).
2. **Modern University SIAKAD Domain:** Managed via `ukts`, `pembayaran_ukts`, `tagihan_vas`, `tagihan_va_items`, `transaksi_h2hs`, and `financial_clearances` (semester-based UKT invoices, Virtual Accounts, H2H banking webhooks).

Because historical students still possess balances, receipts, and clearances recorded in `pembayarans`, any uncoordinated truncation or migration would create catastrophic cascading failures across academic and administrative subsystems.

---

## 2. Dependency Map: The `pembayarans` Financial Ledger

```
                                  ┌──────────────────────┐
                                  │      pembayarans     │
                                  │   (Financial Ledger) │
                                  └──────────┬───────────┘
               ┌─────────────────────────────┼────────────────────────────┐
               │                             │                            │
               ▼                             ▼                            ▼
      ┌─────────────────┐           ┌─────────────────┐          ┌─────────────────┐
      │   Foreign Keys  │           │ Business Logic  │          │    Reporting    │
      ├─────────────────┤           ├─────────────────┤          ├─────────────────┤
      │ id_siswa ➔ siswas│          │ Siswa::         │          │ LaporanController│
      │ id_petugas➔users │          │  info_tunggakan │          │ ExportPembayaran│
      │ id_spp ➔ spps   │          │ Siswa::         │          │  ReportJob      │
      └─────────────────┘           │  status_spp     │          │ Executive       │
                                    │ Financial       │          │  Dashboard      │
                                    │  Clearance Sync │          │ Filament Charts │
                                    └─────────────────┘          └─────────────────┘
```

### 2.1 Complete Inventory of Subsystem Dependencies
1. **Academic Registration & KRS Unlocking:**
   - Method: `Siswa::getInfoTunggakanAttribute()`
   - Behavior: Dynamically aggregates `pembayarans` to determine unpaid months. If historical payments are moved without an abstraction layer, active students will falsely appear in arrears and their KRS will be automatically locked by `SmartKrsService`.
2. **Receipt Verification & Legal Documents:**
   - Routes: `web/pembayaran/{id}/cetak`, `pembayaran.cetak.signed`, and `CetakKwitansiController`
   - Behavior: Parents, alumni, and auditors query historical receipts. Removing rows breaks printable URLs and invalidates signed PDF downloads.
3. **Institutional Financial Reporting & Tax Audits:**
   - Files: `LaporanController`, `ExportPembayaranReportJob`, `GenerateKwitansiBatchJob`
   - Behavior: Periodic CSV/PDF financial summaries aggregate total cash receipts by date range (`tgl_bayar`).
4. **Filament Administration Panels:**
   - Resource: `App\Filament\Resources\PembayaranResource`
   - Widgets: `StatsOverview`, `PemasukanBulananChart`
   - Behavior: Renders cashier audit histories, totals, and monthly revenue graphs.
5. **Database Foreign Key Integrity:**
   - In SQLite/MySQL, `pembayarans.id_siswa` cascades from `siswas(id)`, and `pembayarans.id_petugas` references `users(id)`.
   - Sprint A implemented model deletion guards preventing cashier deletion when transactions exist.

---

## 3. Statutory & Regulatory Retention Requirements

| Regulatory Framework | Mandatory Retention Period | Compliance Requirement |
| :--- | :--- | :--- |
| **UU Ketentuan Umum Perpajakan (UU KUP) No. 28/2007, Art. 28** | **10 Years** | Books, records, and financial documents that form the basis for calculating tax must be retained for at least 10 years in Indonesia. |
| **UU Dokumen Perusahaan No. 8/1997, Art. 11** | **10 Years** | Financial ledgers, annual balance sheets, and transaction journals must be maintained for 10 years from the end of the corporate fiscal year. |
| **UU Pelindungan Data Pribadi (UU PDP) No. 27/2022, Art. 16** | Duration of processing basis | Personal data must be protected against unauthorized access during retention; historical records must implement role-based access control (RBAC). |
| **Standar Nasional Dikti (SN-Dikti / PDDikti)** | **Permanent (Transcripts & Clearances)** | Financial clearance records supporting degree conferral must be auditable during accreditation (BAN-PT / LAM-PT). |

---

## 4. Archival Architecture Options Assessment

### Option A: In-Place Indexing & Partitioning (Recommended for Current Scale)
- **Mechanism:** Retain records in `pembayarans`, ensure compound indexes on `(tgl_bayar, id_siswa)`, and partition by calendar year when transitioning to MySQL enterprise (`PARTITION BY RANGE (YEAR(tgl_bayar))`).
- **Complexity:** Low.
- **Data Integrity:** Maximum (Zero breaking changes, foreign keys preserved).
- **Report Compatibility:** 100% compatible.

### Option B: Read-Only Archive Table (`pembayarans_archive`)
- **Mechanism:** Create `pembayarans_archive` table with identical schema. Periodically move transactions older than 5 years via scheduled batch job.
- **Complexity:** Medium-High.
- **Impact:** Requires rewriting `Siswa::info_tunggakan`, `LaporanController`, and `PembayaranResource` with `UNION ALL` views.
- **Risk:** Risk of race conditions during live migration; requires continuous foreign key maintenance.

### Option C: Parquet / JSONL Cold Storage Object Store (S3 / GCS)
- **Mechanism:** Export settled transactions older than 7 years into immutable GZIP-compressed JSONL/Parquet files stored in an S3-compatible bucket with SHA-256 manifests.
- **Complexity:** High.
- **Impact:** Historical receipts become asynchronous download requests rather than immediate relational lookups.
- **Justification Threshold:** Only justified when relational row count exceeds 10,000,000 records.

---

## 5. Comparative Evaluation Matrix

| Evaluation Criteria | Option A: In-Place Partitioning | Option B: Archive Table | Option C: Object Store Cold Storage |
| :--- | :--- | :--- | :--- |
| **Operational Complexity** | **Very Low** | Medium | High |
| **Query Performance** | **High** (Indexed) | High for active; Slow for unified | Requires external query engine (Athena/DuckDB) |
| **Report Compatibility** | **100% (No changes)** | Requires View refactoring | Requires ETL pipeline |
| **Storage Cost Impact** | Negligible ($< 50$ MB SQLite) | Negligible | Low storage, high integration cost |
| **Data Integrity & FKs** | **Fully Guaranteed** | Cross-table FK complexity | Relational FKs broken |
| **Access Control (RBAC)**| Standard Laravel Policy | Dual-table policy | IAM bucket policies |
| **Auditability (BPK/KAP)**| Immediate | Immediate | Requires retrieval workflow |
| **Rollback Risk** | **Zero** | Medium | High |

---

## 6. Stakeholder Policy Decisions Required

Before any archival or data partitioning pipeline can be approved for execution, the university leadership must formally decide:

1. **Policy Threshold (Years of Active Retention):**
   - *Recommendation:* Retain all transactions within the standard student study tenure (7 years / 14 semesters) in active tables.
2. **Access Rights to Historical Ledgers:**
   - *Recommendation:* Restrict modification to Superadmin/Auditor; grant Read-Only access to students and cashiers.
3. **Legal Hold Procedures:**
   - *Recommendation:* Maintain a boolean flag `is_legal_hold` preventing automated archival for contested tuition disputes.
4. **Alumni Transcript & Receipt Availability:**
   - *Recommendation:* Historical kwitansi print endpoints must resolve archived records seamlessly via unified service layer.
