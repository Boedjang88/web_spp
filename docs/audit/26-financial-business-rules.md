# AUDIT 26: FINANCIAL BUSINESS RULES & TRANSACTION LEDGER INTEGRITY
**SIAKAD Enterprise & SPP — Batch I Business Logic & Cross-Module Consistency**  
**Date:** 2026-10-13  
**Status:** VERIFIED & HARDENED  
**Scope:** Dual-Domain Ledger, UKT Invoicing, Bank H2H Webhooks, Clearance Locks, and Grace Periods

---

## 1. Executive Summary
In compliance with Batch I.4 of the Antigravity Master Execution Plan, this document outlines the authoritative financial rules governing student billing, payment recording, banking webhook integrations, and financial clearance gates. The system enforces strict atomic ledger consistency and zero-destructive financial handling.

---

## 2. Invariant Specifications & Financial Integrity Rules

### 2.1 Dual-Domain Ledger Consistency
The application accommodates both historical school SPP monthly records and modern university semester UKT invoices:
- **Legacy SPP Table (`pembayarans`):** Keyed by `id_siswa`, `id_spp`, `bulan_dibayar`, `tahun_dibayar`. Protected by composite unique indexes preventing duplicate monthly payments.
- **University UKT Tables (`tagihan_ukts`, `pembayaran_ukts`):** Keyed by `id_mahasiswa`, `id_tahun_akademik`, `id_ukt`. Tracks multi-installment payments with atomic balance calculation:
  $$\text{Sisa Tagihan} = \max\left(0, \text{Total Tagihan} - \sum \text{Pembayaran Valid}\right)$$
- **Non-Negative Balance Invariant:** Balances are clamped at zero (`max(0, ...)`), preventing negative balance overflow or receipt rendering crashes.

### 2.2 Cashier & Staff Deletion Protection
To preserve financial audit trails and prevent orphaned transaction ledgers:
- The `User` and `Guru` models implement deleting guards (`Model::deleting`) that prohibit deleting any user who has recorded payment receipts (`pembayarans.id_petugas`).
- Any attempt by an administrator to delete an active cashier with transaction history throws a protected validation exception.

### 2.3 UKT Grace Period & Smart KRS Integration
The university provides an institutional grace window for students awaiting funds or scholarship disbursements:
- **Policy:** 7-day grace window after invoice due date (`config('academic.ukt_grace_period_days', 7)`).
- **Rule in [`SmartKrsService.php`](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Academic/SmartKrsService.php):**
  - If a student has an outstanding invoice but the current timestamp is within `tgl_jatuh_tempo + 7 days`, `isFinanciallyCleared()` returns `true`, permitting course enrollment.
  - Once the grace period expires, `isFinanciallyCleared()` returns `false`, locking course enrollment until full payment or administrative scholarship waiver.

### 2.4 Bank Host-to-Host (H2H) Webhook Security & Idempotency
All external payment notifications from partner banks (Bank Mandiri, BRI, BNI, BSI) or gateways (Midtrans, Xendit) must satisfy:
1. **HMAC SHA-256 Signature Verification:** Incoming requests must provide valid `X-Signature` calculated as:
   $$\text{Signature} = \text{HMAC-SHA256}(\text{Raw Payload}, \text{Secret Key})$$
2. **Timestamp Replay Protection:** Request headers must include `X-Timestamp`. Requests older than 300 seconds (5 minutes) are rejected with HTTP 401.
3. **Pessimistic Locking & Idempotency:** Webhook processing wraps mutations in `DB::transaction()` and uses `lockForUpdate()` on `tagihan_ukts`. Duplicate transaction notifications return HTTP 200 with idempotent acknowledgement without crediting balances twice.

### 2.5 Financial Audit Trail & Receipt Verification
- Every manual cashier payment and automated H2H webhook generates an immutable receipt with a unique serial number (`nomor_pembayaran` / `nomor_transaksi`).
- Kwitansi downloads and views require exact user ownership verification or elevated finance officer roles, preventing cross-tenant financial document scraping (Anti-IDOR).
