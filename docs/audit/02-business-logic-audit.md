# Phase 1 — Functional & Business Logic Audit Report
**Project:** SIAKAD Enterprise & SPP (Universitas SIAKAD Enterprise)  
**Date:** 2026-10-13  
**Auditor:** Multidisciplinary University Information System Engineering Team  
**Deliverable:** `docs/audit/02-business-logic-audit.md`  
**Status:** Completed (Evidence-Based Audit)

---

## 1. Executive Summary of Functional Audit

This Phase 1 audit conducted an in-depth, code-level inspection across all ten academic domains (Domains A through J). The application contains powerful, advanced enterprise capabilities—including pessimistic locking for high-concurrency KRS registration, Haversine geo-fencing for QR attendance, and cryptographic HMAC protection for Bank Host-to-Host (H2H) webhooks.

However, the audit revealed **critical business logic defects, state synchronization hazards between legacy and university domains, unhandled race conditions, and disconnected workflows** that compromise academic integrity, financial accuracy, and data consistency.

---

## 2. Domain-by-Domain Functional Audit

---

### Domain A: Student Admission (PMB) & Student Records

#### 1. Features Audited
- **PMB Self-Registration:** [`App\Http\Controllers\Web\PmbController`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/PmbController.php) (`GET /pmb/register`, `POST /pmb/register`)
- **Student Master Management:** [`App\Http\Controllers\Web\MahasiswaController`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/MahasiswaController.php), [`App\Http\Controllers\Web\SiswaController`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/SiswaController.php)
- **Extended Biodata & PDP:** [`App\Http\Controllers\Web\StudentBiodataController`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/StudentBiodataController.php) (`GET /siakad/biodata`, `PUT /siakad/biodata/update`)

#### 2. Business Rules & Logic Discovered
- Applicants can self-register by providing name, email, password, study program, address, and phone.
- The system automatically issues a NIM using prefix `'10924094'` concatenated with 4 digits (`sprintf('%04d', $nextId)`).
- When a student registers or is created by an admin, the system creates records in both `siswas` and `mahasiswas`.

#### 3. Verified Defects & Architectural Risks
> [!CAUTION]
> **Defect A.1: Race Condition in NIM Generation & Missing Transaction Boundary**  
> - **Location:** [`PmbController.php#L41-L96`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/PmbController.php#L41-L96)  
> - **Evidence:**  
>   ```php
>   $lastMhs = Mahasiswa::latest('id')->first();
>   $nextId = ($lastMhs ? $lastMhs->id : 0) + 1;
>   $nim = '10924094' . sprintf('%04d', $nextId);
>   ```
> - **Impact:** Under concurrent registrations (e.g., peak PMB registration days), two requests can read the same `$lastMhs->id`, generating identical NIMs. Furthermore, steps 54 through 96 (`User::create`, `Siswa::create`, `Mahasiswa::create`) are executed outside a database transaction (`DB::transaction`). If `Mahasiswa::create` fails due to duplicate NIM, an orphaned `User` and `Siswa` remain in the database while the applicant receives an unhandled 500 error.
> - **Recommended Fix:** Enclose registration in `DB::transaction()` and generate NIM inside an atomic sequence lock or database sequence table.

> [!WARNING]
> **Defect A.2: Blind Foreign Key Mapping Between `id_spp` and `id_ukt`**  
> - **Location:** [`MahasiswaController.php#L89-L90`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/MahasiswaController.php#L89-L90), [`MahasiswaController.php#L158`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/MahasiswaController.php#L158)  
> - **Evidence:**  
>   ```php
>   Mahasiswa::updateOrCreate(
>       ['nisn' => $siswa->nisn],
>       [
>           ...
>           'id_prodi' => ProgramStudi::first()?->id ?? 1,
>           'id_ukt' => $siswa->id_spp,
>       ]
>   );
>   ```
> - **Impact:** `spps.id` (legacy school tuition tariff) is directly assigned to `mahasiswas.id_ukt` (university tuition tariff band). Because `spps` and `ukts` are separate tables with independent primary key counters, assigning `id_spp` to `id_ukt` associates the student with an incorrect UKT band.
> - **Recommended Fix:** Resolve the appropriate `Ukt` record by matching the student's cohort year and program study, instead of copying `id_spp`.

---

### Domain B: Academic Calendar & Semester Periods

#### 1. Features Audited
- **Semester Configuration:** [`App\Models\TahunAkademik`](file:///home/masgansss/Projects/Dev/web_spp/app/Models/TahunAkademik.php)
- **Period Activation:** Fields `is_active`, `tgl_mulai`, `tgl_selesai`, `tgl_krs_mulai`, `tgl_krs_selesai`.

#### 2. Business Rules & Logic Discovered
- `TahunAkademik` defines the active semester, date range of lectures, and date window for KRS registration.
- Scope `TahunAkademik::active()` retrieves the active semester.

#### 3. Verified Defects & Architectural Risks
> [!IMPORTANT]
> **Defect B.1: Missing KRS Calendar Date Boundary Enforcement on the Server**  
> - **Location:** [`SmartKrsService.php#L146-L165`](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Academic/SmartKrsService.php#L146-L165), [`SmartKrsController.php#L59-L79`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/SmartKrsController.php#L59-L79)  
> - **Evidence:** Neither `SmartKrsController::store` nor `SmartKrsService::enrollClassWithPessimisticLock` verifies whether `now()` is between `$activeYear->tgl_krs_mulai` and `$activeYear->tgl_krs_selesai`. Furthermore, the controller accepts `$request->id_tahun_akademik` without asserting that the requested academic year is currently active (`is_active == true`).
> - **Impact:** A student can submit KRS course enrollments outside the official academic calendar period or enroll in inactive past/future academic years.
> - **Recommended Fix:** Enforce server-side check in `SmartKrsService`:
>   ```php
>   $ta = TahunAkademik::findOrFail($idTahunAkademik);
>   if (!$ta->is_active) {
>       throw new Exception('Tahun akademik tidak aktif.');
>   }
>   if ($ta->tgl_krs_mulai && $ta->tgl_krs_selesai && !now()->between($ta->tgl_krs_mulai, $ta->tgl_krs_selesai)) {
>       throw new Exception('Periode pengisian KRS telah ditutup atau belum dibuka.');
>   }
>   ```

---

### Domain C: Curriculum & Course Management

#### 1. Features Audited
- **Curriculum & Courses:** [`App\Models\Kurikulum`](file:///home/masgansss/Projects/Dev/web_spp/app/Models/Kurikulum.php), [`App\Models\MataKuliah`](file:///home/masgansss/Projects/Dev/web_spp/app/Models/MataKuliah.php)
- **Prerequisites:** [`App\Models\MataKuliahPrasyarat`](file:///home/masgansss/Projects/Dev/web_spp/app/Models/MataKuliahPrasyarat.php)

#### 2. Business Rules & Logic Discovered
- Courses belong to a curriculum (`id_kurikulum`) and have credit units (`sks_teori`, `sks_praktik`, `sks_total`).
- Courses can define prerequisites via `mata_kuliah_prasyarats`, which includes a `nilai_minimum` column (e.g., minimum grade 'C').

#### 3. Verified Defects & Architectural Risks
> [!WARNING]
> **Defect C.1: Prerequisite Verification Ignores Minimum Grade Contract**  
> - **Location:** [`SmartKrsService.php#L110-L139`](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Academic/SmartKrsService.php#L110-L139)  
> - **Evidence:**  
>   ```php
>   $hasPassed = KrsDetail::whereHas('krs', function ($q) use ($idSiswa) {
>       $q->where('id_siswa', $idSiswa);
>   })
>   ->whereHas('kelasKuliah', function ($q) use ($prasyarat) {
>       $q->where('id_mk', $prasyarat->id_mk_prasyarat);
>   })
>   ->where('is_lulus', true)
>   ->exists();
>   ```
> - **Impact:** The code checks only `is_lulus == true`, completely ignoring the `nilai_minimum` column specified in the `mata_kuliah_prasyarats` pivot table. If a prerequisite requires at least grade 'B' (`nilai_minimum = 'B'`), a student with grade 'C' (who is marked `is_lulus = true`) is incorrectly permitted to enroll.
> - **Recommended Fix:** Inspect the prerequisite's `nilai_minimum` and compare against `bobot_mutu` or letter grade.

---

### Domain D: Course Registration (Smart KRS)

#### 1. Features Audited
- **Registration Controller:** [`App\Http\Controllers\Web\SmartKrsController`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/SmartKrsController.php), [`App\Http\Controllers\Api\SmartKrsController`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Api/SmartKrsController.php)
- **Registration Engine:** [`App\Services\Academic\SmartKrsService`](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Academic/SmartKrsService.php)
- **PA Approval:** [`App\Http\Controllers\Web\DosenKrsApprovalController`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/DosenKrsApprovalController.php)

#### 2. Business Rules & Logic Discovered
- Registration requires financial clearance (`isFinanciallyCleared`).
- Uses `lockForUpdate()` on `KelasKuliah` to guarantee seat quota atomicity.
- Checks timetable conflicts against previously enrolled courses in the active KRS.
- Enforces maximum SKS based on prior semester GPA (IPS).

#### 3. Verified Defects & Architectural Risks
> [!CAUTION]
> **Defect D.1: Class Seat Quota Leaks on KRS Rejection**  
> - **Location:** [`DosenKrsApprovalController.php#L47-L60`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/DosenKrsApprovalController.php#L47-L60)  
> - **Evidence:** When an Academic Advisor rejects a student's KRS (`reject()`), `$krs->update(['status_krs' => 'Ditolak'])` is executed. However, the enrolled course details in `krs_details` are neither removed nor has `KelasKuliah::total_terisi` been decremented.
> - **Impact:** Courses on a rejected KRS continue to occupy class capacity. Other eligible students cannot enroll because the seats appear full.
> - **Recommended Fix:** When a KRS is rejected, either transition details to a `Dibatalkan` state and decrement `total_terisi`, or require the student to revise their selections through an authorized amendment workflow.

> [!WARNING]
> **Defect D.2: Missing Advisor Ownership Check in KRS Approvals**  
> - **Location:** [`DosenKrsApprovalController.php#L33-L45`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/DosenKrsApprovalController.php#L33-L45)  
> - **Evidence:** The `approve` method accepts an arbitrary `$id` parameter and directly updates `$krs->update(['status_krs' => 'Disetujui'])` without checking whether the authenticated lecturer is actually the student's assigned academic advisor (`$krs->id_dosen_wali`).
> - **Impact:** Any authenticated user with lecturer privileges can approve or reject any student's KRS across the university.

---

### Domain E: Scheduling & Teaching Assignments

#### 1. Features Audited
- **Scheduling Service:** [`App\Services\Academic\SchedulingService`](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Academic/SchedulingService.php)
- **Schedule Web Controller:** [`App\Http\Controllers\Web\JadwalController`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/JadwalController.php)

#### 2. Business Rules & Logic Discovered
- `SchedulingService` contains collision detection for Room Capacity, Room Double-Booking, and Lecturer Overlap.

#### 3. Verified Defects & Architectural Risks
> [!CRITICAL]
> **Defect E.1: Web Schedule Management Completely Bypasses Conflict Validation**  
> - **Location:** [`JadwalController.php#L118-L140`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/JadwalController.php#L118-L140), [`JadwalController.php#L152-L170`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/JadwalController.php#L152-L170)  
> - **Evidence:** In `JadwalController::store` and `update`, `JadwalPelajaran::create($validated)` and `$jadwal->update($validated)` are invoked directly without ever calling `SchedulingService::validateScheduleConflict()`.
> - **Impact:** Administrators can create timetable conflicts where the same lecturer is scheduled to teach two classes at the exact same hour, or multiple classes are scheduled in the same room.

> [!WARNING]
> **Defect E.2: Disconnection Between `JadwalPelajaran` and `JadwalKuliah`**  
> - **Location:** [`JadwalController.php#L20-L33`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/JadwalController.php#L20-L33)  
> - **Evidence:** The web management interface exclusively manages `JadwalPelajaran` (fixed cohort timetable), whereas the university Smart KRS system registers students into `KelasKuliah` and checks conflicts against `JadwalKuliah`.
> - **Impact:** Changes made by administrators in `/web/jadwal` do not update the actual schedules checked during student KRS registration in `SmartKrsService`.

---

### Domain F: Attendance Management

#### 1. Features Audited
- **Web Student Attendance:** [`App\Http\Controllers\Web\StudentAttendanceController`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/StudentAttendanceController.php)
- **API Geo-Attendance:** [`App\Http\Controllers\Api\GeoAttendanceController`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Api/GeoAttendanceController.php)
- **Digital BAP:** [`App\Http\Controllers\Web\DosenBapController`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/DosenBapController.php)

#### 2. Business Rules & Logic Discovered
- QR tokens expire in 10 seconds (stored in cache: `qr_attendance_bap_{id}`).
- Geofencing enforces a maximum 20-meter Haversine distance from the lecture room coordinates.

#### 3. Verified Defects & Architectural Risks
> [!CAUTION]
> **Defect F.1: Overwriting Attendance Across Meetings (Meeting Clashing Bug)**  
> - **Location:** [`StudentAttendanceController.php#L152-L166`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/StudentAttendanceController.php#L152-L166), [`GeoAttendanceController.php#L152-L166`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Api/GeoAttendanceController.php#L152-L166)  
> - **Evidence:**  
>   ```php
>   PresensiMahasiswa::updateOrCreate(
>       [
>           'id_mahasiswa' => $mahasiswa->id,
>           'id_kelas_kuliah' => $idKelas,
>       ],
>       [
>           'id_bap' => $idBap,
>           'waktu_hadir' => now(),
>           ...
>       ]
>   );
>   ```
> - **Impact:** Because `id_bap` is placed in the attributes payload rather than the lookup attributes, when a student attends Meeting 2 of a course, `updateOrCreate` locates the record from Meeting 1 and **overwrites** it. As a result, a student will only ever have 1 attendance record per course for the entire semester. Historical attendance across 16 meetings is permanently erased on each check-in.
> - **Recommended Fix:** Include `id_bap` in the search criteria:
>   ```php
>   PresensiMahasiswa::updateOrCreate(
>       [
>           'id_mahasiswa' => $mahasiswa->id,
>           'id_kelas_kuliah' => $idKelas,
>           'id_bap' => $idBap,
>       ],
>       [ ... ]
>   );
>   ```

---

### Domain G: Assessment & Grading

#### 1. Features Audited
- **Grading Service:** [`App\Services\Academic\GradingService`](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Academic/GradingService.php)
- **Nilai Web Controller:** [`App\Http\Controllers\Web\NilaiController`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/NilaiController.php)

#### 2. Business Rules & Logic Discovered
- `GradingService` calculates weighted scores based on `BobotPenilaian` (Kehadiran, Tugas, Quiz, UTS, UAS, Praktikum) and maps scores to standard university letters (A = 4.0, AB = 3.5, B = 3.0, BC = 2.5, C = 2.0, D = 1.0, E = 0.0).

#### 3. Verified Defects & Architectural Risks
> [!CAUTION]
> **Defect G.1: Grading Synchronization Corrupts Semester & Course Mappings**  
> - **Location:** [`GradingService.php#L90-L108`](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Academic/GradingService.php#L90-L108)  
> - **Evidence:**  
>   ```php
>   \App\Models\Nilai::updateOrCreate(
>       [
>           'id_siswa' => $krs->id_siswa,
>           'id_mapel' => $detail->kelasKuliah->id_mk,
>       ],
>       [
>           'semester' => 'Ganjil',
>           'tahun_ajaran' => (string) date('Y'),
>           ...
>       ]
>   );
>   ```
> - **Impact:**  
>   1. `'semester'` is hardcoded to `'Ganjil'`, even during Genap semesters.  
>   2. `'id_mapel'` is blindly populated with `$detail->kelasKuliah->id_mk` (`mata_kuliahs.id`). Because `mapels` and `mata_kuliahs` have distinct auto-increment IDs, this updates grades for the completely wrong subject in the student's KHS.
>   3. In `NilaiController#L171`, passing grade is hardcoded as `nilai_akhir >= 75`. A university student earning 70 (Grade B, passing in `GradingService`) is incorrectly marked failed (`is_lulus = false`) when edited through `/web/nilai`.

> [!WARNING]
> **Defect G.2: Data Leak Fallback in Student Grade View**  
> - **Location:** [`NilaiController.php#L23-L26`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/NilaiController.php#L23-L26)  
> - **Evidence:**  
>   ```php
>   if ($user && $user->isMahasiswa()) {
>       $siswa = $user->siswa ?? $user->mahasiswa ?? Siswa::first();
>       $query->where('id_siswa', $siswa?->id ?? 1);
>   }
>   ```
> - **Impact:** If an authenticated student user account is not cleanly linked to a student profile, the query falls back to `Siswa::first()` or ID 1. The student is shown another student's confidential grades.

---

### Domain H: Academic Results (KHS, Transcripts, Graduation Eligibility)

#### 1. Features Audited
- **Clearance Service:** [`App\Services\Academic\GraduationClearanceService`](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Academic/GraduationClearanceService.php)
- **Graduation Controller:** [`App\Http\Controllers\Web\WisudaController`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/WisudaController.php)
- **Exam Pass Eligibility:** [`App\Domain\Academic\Actions\VerifyExamCardEligibilityAction`](file:///home/masgansss/Projects/Dev/web_spp/app/Domain/Academic/Actions/VerifyExamCardEligibilityAction.php)

#### 2. Business Rules & Logic Discovered
- `GraduationClearanceService` audits accumulated passed SKS against a 144 SKS threshold, verifies that all mandatory courses have grade >= C, and checks a 100 SKPI point minimum.

#### 3. Verified Defects & Architectural Risks
> [!CRITICAL]
> **Defect H.1: Broken Method Call Crashes Clearance View & Self-Graduation Hole**  
> - **Location:** [`WisudaController.php#L23`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/WisudaController.php#L23), [`WisudaController.php#L40-L46`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/WisudaController.php#L40-L46)  
> - **Evidence:**  
>   1. `WisudaController::index` calls `$clearanceService->verifyStudentClearance($siswa)`. However, `GraduationClearanceService` does not have any method named `verifyStudentClearance` (the actual method is `auditGraduation`). This throws a `BadMethodCallException` which is caught silently.  
>   2. In `WisudaController::register`, a student can click register and the controller directly updates:
>      ```php
>      $siswa->update([
>          'status_kelulusan' => 'Lulus Wisuda',
>          'tgl_kelulusan' => now(),
>          'nomor_ijazah' => 'PIN/' . date('Y') . '/' . sprintf('%06d', $siswa->id),
>      ]);
>      ```
> - **Impact:** Any active student can immediately issue themselves official graduation status and a National Diploma Number (PIN) without ever meeting the 144 SKS requirement, passing mandatory courses, or defending a thesis.

> [!WARNING]
> **Defect H.2: Zero Anti-IDOR Authorization on KHS Printing**  
> - **Location:** [`NilaiController.php#L196-L219`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/NilaiController.php#L196-L219)  
> - **Evidence:** `cetakKhs($siswaId)` accepts any `$siswaId` without verifying whether the authenticated user owns the student record or has administrative permissions.
> - **Impact:** A student can print any other student's KHS by changing the numeric ID in the URL.

---

### Domain I: Finance, Tuition & Payments

#### 1. Features Audited
- **H2H Billing Service:** [`App\Services\Finance\H2hBillingService`](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Finance/H2hBillingService.php)
- **UKT Billing Service:** [`App\Services\Finance\UktBillingService`](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Finance/UktBillingService.php)
- **Student UKT Portal:** [`App\Http\Controllers\Web\UktPortalController`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/UktPortalController.php)
- **Webhooks:** [`App\Http\Controllers\Api\H2hWebhookController`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Api/H2hWebhookController.php)

#### 2. Business Rules & Logic Discovered
- Payments via Bank H2H are verified via HMAC-SHA256 signatures and protected by an atomic idempotency layer.
- Upon full payment, `FinancialClearance` is released, instantly unlocking Smart KRS access.

#### 3. Verified Defects & Architectural Risks
> [!CRITICAL]
> **Defect I.1: Incomplete Ledger Synchronization Between Bank Webhook and UKT Portal**  
> - **Location:** [`H2hBillingService.php#L374-L428`](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Finance/H2hBillingService.php#L374-L428), [`UktPortalController.php#L34-L37`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/UktPortalController.php#L34-L37)  
> - **Evidence:** When a bank payment webhook arrives at `/api/h2h/webhook`, `H2hBillingService::reconcilePaymentRecord` writes to `transaksi_h2hs` and updates `tagihan_vas`. It **does not create a record in `pembayaran_ukts`**. Meanwhile, the student UKT portal (`/siakad/ukt`) queries:
>   ```php
>   $riwayatPembayaran = PembayaranUkt::where('id_mahasiswa', $mahasiswa->id)...
>   ```
> - **Impact:** When a student pays via a legitimate bank webhook, the money is recorded in `transaksi_h2hs`, but their student UKT portal shows **zero payment history** and cannot generate an official receipt (`PembayaranUkt`).

---

### Domain J: Other Modules (DBPTA/Skripsi, KKN, EDOM, E-Surat)

#### 1. DBPTA / Skripsi Workflow ([`DbptaWorkflowService.php`](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Thesis/DbptaWorkflowService.php))
- **Defect J.1: Self-Approved Bimbingan and Instant Yudisium on Upload:**
  - In `addBimbinganLog#L58`, student logbook entries are automatically created with `'status_acc' => 'Disetujui'` without lecturer review.
  - In `submitPemberkasanFinal#L98`, uploading a draft directly transitions status to `'Lulus Yudisium'`.
  - In `addBimbinganLog#L39-L49`, dummy `Guru` records are created on the fly to bypass foreign key constraints.

#### 2. Kuliah Kerja Nyata (KKN) ([`KknRegistrationService.php`](file:///home/masgansss/Projects/Dev/web_spp/app/Domain/Academic/Services/KknRegistrationService.php))
- **Defect J.2: Missing Minimum SKS Eligibility Precondition:**
  - `registerStudent` accepts KKN registrations without checking if the student has earned the mandatory minimum credits (typically 100 SKS). A semester 1 student can register for KKN.

#### 3. EDOM Evaluation Gate ([`EdomBlockingGateMiddleware.php`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Middleware/EdomBlockingGateMiddleware.php))
- **Defect J.3: Dead Middleware:**
  - `EdomBlockingGateMiddleware` is implemented to block KHS viewing if student evaluations are pending, but it is **not registered in `bootstrap/app.php` and not attached to any route**. Students can bypass evaluations completely.

#### 4. E-Surat Approval ([`StudentEsuratController.php`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/StudentEsuratController.php), [`EsuratApprovalController.php`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/EsuratApprovalController.php))
- **Defect J.4: State String Mismatch:**
  - `StudentEsuratController::store` creates letter requests with `'status' => 'DRAFT'`.
  - `EsuratApprovalController::index` filters incoming requests with `'status' => 'MENUNGGU_PERSETUJUAN'`.
  - Student requests never appear on the administrator's review desk.

---

## 3. Unconfirmed University Policies Requiring Stakeholder Decision

The following business rules cannot be established from codebase evidence and require authoritative confirmation from university leadership:

| No | Business Domain | Policy Question | Current Code Implementation | Options for Confirmation |
|:---|:---|:---|:---|:---|
| 1 | **KRS SKS Limit** | What are the exact GPA-to-SKS thresholds according to the Academic Senate? | IPS ≥ 3.0: 24 SKS<br/>IPS 2.5–2.99: 21 SKS<br/>IPS 2.0–2.49: 18 SKS<br/>IPS < 2.0: 15 SKS | A: Retain current tiers<br/>B: Align with standard SN-Dikti 2024 regulation |
| 2 | **Freshmen SKS Policy** | Are 1st and 2nd semester students placed on a fixed study package (e.g., 20 SKS) or allowed free selection up to 24 SKS? | Hardcoded to 24 SKS max for all new students | A: Fixed cohort package (20 SKS)<br/>B: Free registration up to 24 SKS |
| 3 | **Repeated Course Retakes** | When a student repeats a course, does the highest grade or the latest grade take precedence on the academic transcript? | `GraduationClearanceService` takes the highest grade (`$bobot > $currentBobot`) | A: Highest grade (*highest*)<br/>B: Latest grade (*latest*) |
| 4 | **Exam Card Attendance Threshold** | What is the minimum attendance percentage required to sit for midterm (UTS) and final (UAS) exams? | Only checks financial clearance; zero attendance check | A: 75% minimum attendance<br/>B: 80% minimum attendance<br/>C: Financial clearance only |
| 5 | **KKN SKS Requirement** | How many accumulated SKS are required for a student to register for KKN? | Zero check (unrestricted) | A: Minimum 100 SKS<br/>B: Minimum 110 SKS |
| 6 | **Skripsi Eligibility** | What prerequisites are required before proposing a bachelor thesis (DBPTA)? | Zero check | A: Minimum 120 SKS + Metodologi Penelitian passed<br/>B: Minimum 130 SKS |
| 7 | **Passing Grade Standard** | What is the official passing letter grade for university courses? | `GradingService`: Grade C (≥ 55.0)<br/>`NilaiController`: ≥ 75 | A: Grade C (≥ 55.0) is passing<br/>B: Grade D (≥ 45.0) is passing for non-core |
| 8 | **Entity Master Convergence** | Should `Mahasiswa` be the permanent single source of truth, with `Siswa` phased into a legacy compatibility view? | Dual tables synced manually with empty catch blocks | A: Converge to `Mahasiswa` as master<br/>B: Maintain dual synchronization |

---

## 4. Prioritized Functional Defect Matrix

| ID | Title | Domain | Severity | Impact | Affected Files |
|:---|:---|:---|:---|:---|:---|
| **BUG-01** | Attendance Overwritten Across Meetings | Attendance | **P0 (Critical)** | Historical attendance erased on each check-in | `StudentAttendanceController.php`, `GeoAttendanceController.php` |
| **BUG-02** | Student Self-Graduation without Defense or SKS Check | Graduation | **P0 (Critical)** | Unverified students can issue official diplomas | `WisudaController.php`, `GraduationClearanceService.php` |
| **BUG-03** | Webhook Payment Divergence from UKT Portal | Finance | **P0 (Critical)** | Paid H2H bank transactions missing in student UKT portal | `H2hBillingService.php`, `UktBillingService.php` |
| **BUG-04** | PMB Concurrent Race Condition on NIM Generation | Admission | **P1 (High)** | Duplicate NIM collisions, unhandled 500 errors | `PmbController.php` |
| **BUG-05** | Schedule Conflict Engine Bypassed in Web Management | Scheduling | **P1 (High)** | Timetable collisions for lecturers and lecture halls | `JadwalController.php`, `SchedulingService.php` |
| **BUG-06** | KRS Rejection Does Not Release Class Seats | KRS | **P1 (High)** | Blocked seats in classes due to rejected records | `DosenKrsApprovalController.php` |
| **BUG-07** | EWS Crash on Missing Database Column | EWS | **P1 (High)** | Column `status_hadir` does not exist; crashes scan | `EarlyWarningService.php` |
| **BUG-08** | E-Surat Status Mismatch Between Student and Admin | Services | **P1 (High)** | Letters stuck in DRAFT never visible to BAAK | `StudentEsuratController.php`, `EsuratApprovalController.php` |
| **BUG-09** | KHS Printing IDOR Vulnerability | Security | **P1 (High)** | Any student can inspect any peer's transcript | `NilaiController.php` |
| **BUG-10** | Inactive EDOM Blocking Middleware | Evaluation | **P2 (Medium)** | Students view KHS without submitting evaluations | `EdomBlockingGateMiddleware.php`, `bootstrap/app.php` |
