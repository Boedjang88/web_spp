# AUDIT 42: OPERATIONS RUNBOOK & ADMINISTRATIVE HANDOVER
**SIAKAD Enterprise & SPP — Day-2 Operations, Scheduled Tasks, Semester Rollovers & Troubleshooting**  
**Execution Date:** 2026-10-13  
**Status:** ESTABLISHED & OPERATIONAL  
**Lead Engineer / Architect:** Antigravity Autonomous Lead Architect

---

## 1. Executive Summary

This operations runbook equips university IT administrators, system operators, and DevOps engineers with standardized procedures for running, maintaining, and troubleshooting **SIAKAD Enterprise & SPP** throughout the academic lifecycle.

---

## 2. Standard Background Services & Daemons

### 2.1 Crontab Scheduled Tasks
The application relies on Laravel's task scheduler. Ensure the following line is configured in the production server's `crontab -e`:
```bash
* * * * * cd /var/www && php artisan schedule:run >> /dev/null 2>&1
```

### 2.2 Supervisor Queue Daemon Configuration
Create `/etc/supervisor/conf.d/siakad-worker.conf`:
```ini
[program:siakad-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/artisan queue:work --sleep=3 --tries=3 --max-time=3600 --timeout=90
autostart=true
autorestart=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/log/siakad-worker.log
```

---

## 3. Routine Administrative Artisan Commands

| Command | Recommended Schedule | Description & Purpose |
| :--- | :---: | :--- |
| `php artisan ews:scan` | Daily at 02:00 AM | Analyzes student academic risk, GPA drops, and attendance red flags; dispatches risk alerts. |
| `php artisan attendance:archive-telemetry --days=180` | 1st of every month | Minimizes student GPS coordinates older than 180 days (UU PDP No. 27/2022 compliance). |
| `php artisan cache:clear` | As needed | Clears application cache (dashboard statistics, temp caches). |
| `php artisan queue:restart` | After every deployment| Gracefully restarts queue workers to load updated codebase. |

---

## 4. Semester Rollover Standard Operating Procedure (SOP)

At the transition between academic semesters (Ganjil -> Genap -> Pendek):

### Phase 1: Close Previous Semester
1. **Enforce Grade Locks:** Ensure all lecturers have finalized grades. Once the grading deadline passes, grades are read-only.
2. **Compute Semester Statistics:** Trigger IPS/IPK recalculation to establish dynamic SKS ceilings for the upcoming semester.

### Phase 2: Initialize New Semester
1. **Activate Academic Year:**
   - In Admin Panel (`/admin` -> Tahun Akademik) or via database: Set `is_active = true` on the new semester record.
   - Configure start dates (`tgl_mulai`, `tgl_selesai`), KRS window (`tgl_krs_mulai`, `tgl_krs_selesai`), and grading dates.
2. **Generate UKT Invoices:**
   - Run batch invoice generation for active enrolled students according to their registered UKT tier.
   - Sync invoice records to bank H2H partner portals.
3. **Open Smart KRS Portal:**
   - Verify class offerings, quotas, and lecturer schedules in `/web/matakuliah` and `/web/jadwal`.
   - Students with financial clearance can now enroll up to their approved SKS ceiling.

---

## 5. Operations Troubleshooting Guide

### 5.1 Issue: Queue Workers Stuck or Not Processing
- **Symptoms:** Asynchronous export downloads or email notifications not delivering.
- **Diagnostic:** Run `php artisan queue:failed`.
- **Resolution:**
  ```bash
  php artisan queue:retry all
  sudo supervisorctl restart siakad-worker:*
  ```

### 5.2 Issue: Stale Dashboard Statistics
- **Symptoms:** Financial totals or student counts don't reflect recent registrations.
- **Diagnostic:** Check cached key `dashboard_admin_aggregates_{date}`.
- **Resolution:**
  ```bash
  php artisan cache:forget "dashboard_admin_aggregates_$(date +%Y-%m-%d)"
  ```

### 5.3 Issue: Concurrent Lock Wait Timeout on KRS Enrollment
- **Symptoms:** Database lock timeout during rush enrollment.
- **Resolution:** Ensure database connection pool has sufficient headroom (`max_connections >= 200` in MySQL `my.cnf`), and verify transactions maintain `lockForUpdate()` timeouts <= 5 seconds.

---

## 6. Conclusion
Standardized daemon monitoring, automated maintenance commands, and step-by-step rollover checklists ensure high system availability and operational calm.
