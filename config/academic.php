<?php

return [
    /*
    |--------------------------------------------------------------------------
    | University Academic & Financial Business Policies (SN-Dikti & SIAKAD)
    |--------------------------------------------------------------------------
    |
    | Authoritative institutional defaults aligned with Permendikbudristek,
    | Standar Nasional Pendidikan Tinggi (SN-Dikti), and UU PDP No. 27/2022.
    |
    */

    // UKT Billing Grace Period (Days after due date before KRS auto-lock is enforced)
    'ukt_grace_period_days' => (int) env('UKT_GRACE_PERIOD_DAYS', 7),

    // High-Sensitivity GPS Telemetry Retention Window (Days before coordinates are scrubbed)
    'gps_attendance_retention_days' => (int) env('GPS_ATTENDANCE_RETENTION_DAYS', 180),

    // Minimum Attendance Percentage for Final Exam (UAS) Eligibility (75% = 12/16 meetings)
    'min_attendance_exam_percent' => (int) env('MIN_ATTENDANCE_EXAM_PERCENT', 75),

    // Minimum Required Lecture Meetings before Course BAP Finalization
    'bap_min_meetings_final' => (int) env('BAP_MIN_MEETINGS_FINAL', 16),

    // Minimum Cumulative Credits (SKS) for KKN Registration
    'kkn_min_sks' => (int) env('KKN_MIN_SKS', 100),

    // Minimum Cumulative Credits (SKS) for DBPTA / Skripsi Proposal Submission
    'skripsi_min_sks' => (int) env('SKRIPSI_MIN_SKS', 110),

    // Incomplete Grade (Nilai T / Tunda) Expiration Window in Days
    'incomplete_grade_grace_days' => (int) env('INCOMPLETE_GRADE_GRACE_DAYS', 14),

    // SKS Maximum Ceiling Matrix based on Previous Semester GPA (IPS)
    'max_sks_matrix' => [
        'bracket_4' => ['min_ips' => 3.00, 'max_sks' => 24],
        'bracket_3' => ['min_ips' => 2.50, 'max_sks' => 22],
        'bracket_2' => ['min_ips' => 2.00, 'max_sks' => 20],
        'bracket_1' => ['min_ips' => 0.00, 'max_sks' => 18],
    ],
];
