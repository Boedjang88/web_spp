# AUDIT 25: ACADEMIC BUSINESS RULES & GRADING INVARIANTS
**SIAKAD Enterprise & SPP — Batch I Business Logic & Cross-Module Consistency**  
**Date:** 2026-10-13  
**Status:** VERIFIED & HARDENED  
**Scope:** SN-Dikti Alignment, SKS Calculation, Prerequisites, Repeat Grading, and Threshold Enforcement

---

## 1. Executive Summary
In compliance with Batch I.3 of the Antigravity Master Execution Plan, this document formalizes the authoritative academic business rules and invariants enforced across the application. All thresholds have been verified against source code implementations and centralized in [`config/academic.php`](file:///home/masgansss/Projects/Dev/web_spp/config/academic.php).

---

## 2. Invariant Specifications & Mathematical Definitions

### 2.1 Grade Rank Hierarchy & Mutu Weighting
Course grading follows the standard 4.00 Indonesian higher education scale:
$$\text{Grade Ranks: } A = 4.00, \; AB = 3.50, \; B = 3.00, \; BC = 2.50, \; C = 2.00, \; D = 1.00, \; E = 0.00$$

- **Passing Grade Minimum for Prerequisite:** To unlock an advanced dependent course (`MataKuliahPrasyarat`), the student must attain at least grade **C** ($\ge 2.00$) unless an explicit override is recorded.
- **Passing Status on Mandatory Courses:** Grade **D** is strictly disqualified for graduation clearance on mandatory courses (`Wajib Program Studi`, `Wajib Nasional`).
- **Disqualifying Grades:** Grades `['D', 'E', 'BL', 'T']` disqualify mandatory course clearance.

### 2.2 SKS Calculation & Repeated Course Grade Replacement
A major integrity risk in academic systems is duplicate credit accumulation when students re-take courses to improve grades.
- **Implementation in [`GraduationClearanceService`](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Academic/GraduationClearanceService.php):**
  - Course attempts are partitioned by unique `id_mk` (Mata Kuliah).
  - When multiple enrollment records exist for the same course across semesters, the system selects the attempt with the highest quality points (`bobot_mutu`).
  - Total passed SKS aggregates each unique passed course exactly once:
    $$\text{Passed SKS} = \sum_{mk \in \text{Unique Passed Courses}} \text{SKS}(mk)$$
  - Confirmed by automated test `test_passed_sks_calculation_replaces_repeated_course_grade` in [`StudentLifecycleInvariantsTest.php`](file:///home/masgansss/Projects/Dev/web_spp/tests/Feature/Academic/StudentLifecycleInvariantsTest.php).

### 2.3 Dynamic SKS Ceilings (SN-Dikti Standards)
The maximum credits a student may register in a regular semester depends strictly on their prior semester Grade Point Average (IPS):

| Semester GPA (IPS) | Allowed Maximum SKS | Configuration Key |
| :--- | :--- | :--- |
| $\text{IPS} \ge 3.00$ | **24 SKS** | `academic.max_sks_matrix.bracket_4` |
| $2.50 \le \text{IPS} < 3.00$ | **22 SKS** | `academic.max_sks_matrix.bracket_3` |
| $2.00 \le \text{IPS} < 2.50$ | **20 SKS** | `academic.max_sks_matrix.bracket_2` |
| $\text{IPS} < 2.00$ | **18 SKS** | `academic.max_sks_matrix.bracket_1` |

First-semester freshmen default to an institutional cohort package of 20–24 SKS.

### 2.4 KKN & DBPTA / Skripsi Institutional Thresholds
- **Kuliah Kerja Nyata (KKN):** Minimum **100 passed SKS** (`config('academic.kkn_min_sks', 100)`). Students below 100 SKS are prevented from submitting KKN registration.
- **DBPTA / Skripsi Proposal:** Minimum **110 passed SKS** (`config('academic.skripsi_min_sks', 110)`). Students below 110 SKS are prevented from submitting proposal registration.
- **Graduation (Yudisium) Requirement:** Minimum **144 passed SKS** and minimum **100 SKPI non-academic activity points**.

### 2.5 Exam Clearance Attendance Invariant (SN-Dikti 75% Rule)
Under Standar Nasional Pendidikan Tinggi (SN-Dikti), a student must attend at least **75%** of scheduled semester meetings (minimum 12 out of 16 meetings) to sit for final semester examinations (UAS).
- Enforced in `VerifyExamCardEligibilityAction` and `MahasiswaController`.
- Below 75% attendance automatically locks final exam card printing with an advisory banner.

### 2.6 Incomplete Grade (BL / T) Expiration Policy
Grades marked as Incomplete (`BL` / `T`) have a strict 14-day resolution window (`config('academic.incomplete_grade_grace_days', 14)`).
- The scheduled command `php artisan grades:expire-incomplete` identifies unfinalized grades past 14 days and converts them automatically to failing grade `E` (0.00 quality points) with audit logging.
