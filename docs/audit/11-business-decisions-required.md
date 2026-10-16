# PHASE 9 AUDIT REPORT: BUSINESS POLICY DECISIONS REQUIRED FROM SYSTEM OWNER
**Sistem Informasi Akademik & Keuangan Terpadu (SIAKAD Enterprise & SPP)**
**Document Reference:** `docs/audit/11-business-decisions-required.md`  
**Execution Timestamp:** 2026-10-13T07:15:00+07:00  
**Audit Team:** University Academic Business Analyst, Senior Software Architect, Security Engineer  
**Stakeholder Decision Horizon:** University Academic Senate, Vice Rector 1 (Academic Affairs), Vice Rector 2 (Finance & Operations), Head of ICT (Biro TI)

---

## 1. Purpose & Guiding Principles

In accordance with Operating Principle 2.1 (*"Evidence before assumptions"*) and 2.4 (*"Batch unresolved business-policy questions into a concise decision list"*), technical engineers must **never invent university policies, academic rules, or financial thresholds**.

During the deep-dive audit of SIAKAD Enterprise, 7 critical business policies could not be authoritatively confirmed from source code or database migrations alone. These policies directly govern academic progression, financial accounting, and regulatory compliance.

Each decision item below outlines:
1. **The Current Code Implementation** (what the codebase currently does).
2. **The Policy Ambiguity or Defect** (why this is problematic).
3. **Recommended Institutional Option** (aligned with SN-Dikti and best practices).
4. **Alternative Options** (trade-offs).
5. **Impact of Decision** (database schema, business logic, user experience).

---

## 2. Decision Matrix for University Stakeholders

### Decision 1: Dual-Domain Entity Convergence (`siswas` vs `mahasiswas`)

- **Current Implementation:** The application maintains two student tables: `siswas` (inherited from the school SPP legacy system) and `mahasiswas` (created for university higher-ed features). 10 relational tables point to `id_siswa`, while 3 point to `id_mahasiswa`. 20 user accounts have NULL in both foreign keys.
- **Ambiguity:** Is SIAKAD intended to operate exclusively as a University Higher Education System (Institut/Universitas/Politeknik), or must it continue supporting a dual K-12 School + University hybrid within the same tenant?
- **Options for System Owner:**
  - **Option 1A (Recommended - Full Higher-Ed Convergence):** Formalize `mahasiswas` as the single canonical student model. Migrate existing `siswas` records into `mahasiswas` with unique `nim`, `prodi_id`, and `tahun_masuk`. Create a backward-compatible Eloquent view or proxy model `Siswa extends Mahasiswa` so SPP billing routes continue working without code regression.
  - **Option 1B (Dual-Tenant Separation):** Maintain both tables permanently, using a strict tenant discriminator (`tenant_type = 'school' | 'university'`) on the `users` table and preventing cross-entity relationship queries.
- **Decision Required By:** Head of Biro TI & Academic Registrar.

---

### Decision 2: SKS Maximum Limit Calculation Formula (IPS Matrix)

- **Current Implementation:** `PortalController.php#L72` hardcodes a flat limit of **24 SKS** for all students regardless of previous academic performance.
- **Ambiguity:** What is the university's approved regulation governing maximum SKS registration based on the previous semester's Indeks Prestasi Semester (IPS)?
- **Options for System Owner:**
  - **Option 2A (Recommended - Standard SN-Dikti / Permendikbud Matrix):**
    - **IPS >= 3.00:** Maximum **24 SKS**
    - **2.50 <= IPS < 3.00:** Maximum **21 SKS** (or 22 SKS)
    - **2.00 <= IPS < 2.50:** Maximum **18 SKS** (or 20 SKS)
    - **IPS < 2.00:** Maximum **15 SKS** (or 16 SKS)
    - **Semester 1 (Freshmen):** Fixed package (usually 18–20 SKS) regardless of GPA.
  - **Option 2B (Custom Institutional Regulation):** Provide the university's official SKS bracket table from the Academic Handbook (*Buku Pedoman Akademik*).
- **Decision Required By:** Vice Rector 1 (Academic Affairs).

---

### Decision 3: Minimum Student Attendance Threshold for Final Exam Eligibility (UAS)

- **Current Implementation:** `KartuUjianController.php` requires **75% attendance**, while `PresensiMahasiswaService.php` calculates percentages but does not automatically block exam card issuance if attendance is below the threshold.
- **Ambiguity:** What exact attendance percentage is required to take the Final Exam (UAS), and can Dosen Pengampu or Dosen PA grant waivers?
- **Options for System Owner:**
  - **Option 3A (Recommended - Strict 75% Rule with Medical Waiver Workflow):**
    - Exactly **75% attendance** (12 out of 16 meetings) required.
    - System automatically marks student as **Ineligible for UAS** if attendance < 75%.
    - If student was sick or on official university assignment, a formal waiver must be submitted via e-Surat and approved by Kaprodi before the exam card can be downloaded.
  - **Option 3B (Warning Only):** System calculates attendance and displays a warning banner, but leaves final exam admission entirely to the physical discretion of exam proctors.
- **Decision Required By:** Academic Senate & Head of BAAK.

---

### Decision 4: Incomplete Grade (*Nilai T / Tunda*) Expiration Window & Conversion

- **Current Implementation:** `AcademicComplianceAndLmsTest.php` and `ExpiredIncompleteGradesCommand` automatically convert incomplete grade `T` into failing grade `E` after **30 days**.
- **Ambiguity:** What is the statutory grace period for students to complete missing assignments or remedial assessments before an incomplete grade becomes permanent?
- **Options for System Owner:**
  - **Option 4A (Recommended - 14-Day Calendar Grace Period):** Grade `T` automatically reverts to grade `E` 14 days after the official grade submission deadline announced in the Academic Calendar.
  - **Option 4B (30-Day Calendar Grace Period):** Grade `T` reverts to `E` 30 days after initial submission by the lecturer.
  - **Option 4C (No Auto-Conversion):** Grade `T` requires explicit manual revision by the lecturer; system generates an alert to Kaprodi without auto-converting.
- **Decision Required By:** Vice Rector 1 (Academic Affairs).

---

### Decision 5: Cashier Offboarding & Transaction Ledger Retention Policy

- **Current Implementation:** Foreign key `pembayarans.id_petugas` references `users.id` with `ON DELETE CASCADE`. Deleting a cashier drops all transactions they ever processed.
- **Ambiguity:** How should historical payment records behave when a cashier or staff member leaves the university?
- **Options for System Owner:**
  - **Option 5A (Recommended - ON DELETE RESTRICT):** The database strictly prohibits deleting any staff user who has processed financial transactions. Staff offboarding must be handled via `is_active = false` or role revocation, preserving full audit attribution.
  - **Option 5B (ON DELETE SET NULL with Historical Snapshot):** Foreign key is set to NULL upon user deletion, but payment rows permanently retain text fields `nama_petugas_snapshot` and `nip_petugas_snapshot`.
- **Decision Required By:** Head of Finance (BAUK) & Internal Audit.

---

### Decision 6: Public Bill Inquiry (`/cek-tagihan`) & Indonesian Privacy Law (UU PDP)

- **Current Implementation:** Unauthenticated public route `/cek-tagihan` permits searching students by partial name and exposes full NIK, phone number, address, unpaid tuition amounts, and grades.
- **Ambiguity:** Should the university maintain a public bill inquiry portal for parents and banks, and if so, under what privacy constraints?
- **Options for System Owner:**
  - **Option 6A (Recommended - Strict Auth or Multi-Factor Verification):**
    - Disable partial-name search completely.
    - Require **Exact NIM/NISN + Date of Birth (DD-MM-YYYY)** or a 6-digit PIN sent to the parent's phone.
    - Mask all personal identifiers (e.g. `NIK: 327301******0001`, `Nama: A**** P******`).
    - Expose *only* active billing nominal and Virtual Account number; never display grades, transcripts, or home addresses.
  - **Option 6B (Decommission Public Route):** Completely shut down public `/cek-tagihan`. Require parents to log into the dedicated Parent Portal (`/wali`).
- **Decision Required By:** Legal Counsel & Head of Biro TI.

---

### Decision 7: Early Warning System (EWS) Automated Intervention Workflow

- **Current Implementation:** `RunEarlyWarningScan` command computes dropout risk scores (Low, Medium, High, Critical) based on attendance (< 75%), GPA (< 2.00), and unpaid tuition.
- **Ambiguity:** When a student is flagged as High or Critical risk, who should receive automated notifications, and should enrollment be restricted?
- **Options for System Owner:**
  - **Option 7A (Recommended - Staged Escalation):**
    - **Medium Risk:** In-app notification to Student and Dosen Pembimbing Akademik (DPA).
    - **High Risk:** Official notification dispatched to DPA and Kaprodi; student flagged for mandatory academic counseling before KRS approval.
    - **Critical Risk:** Formal notification dispatched to Parents/Wali via Telegram/WhatsApp and physical summons letter generated via e-Surat.
  - **Option 7B (Advisor-Only Visibility):** EWS dashboard is accessible only to DPA and Kaprodi; no automated messages are sent directly to students or parents.
- **Decision Required By:** Dean of Students (Wakil Rektor 3 - Kemahasiswaan).

---

## 3. Summary & Recommended Action Plan

The audit team recommends proceeding with **Option 1A, Option 2A, Option 3A, Option 4A, Option 5A, Option 6A, and Option 7A** as institutional defaults during Phase 10 implementation.

If university leadership requires adjustments to any of these defaults, they can be configured via `config/academic.php` and `config/finance.php` without requiring fundamental architectural rewrites.
