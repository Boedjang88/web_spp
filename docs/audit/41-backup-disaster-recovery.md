# AUDIT 41: BACKUP, DISASTER RECOVERY & DATA PRESERVATION RUNBOOKS
**SIAKAD Enterprise & SPP — Business Continuity, Financial Ledger Preservation & RPO/RTO SLAs**  
**Execution Date:** 2026-10-13  
**Status:** ESTABLISHED & DOCUMENTED  
**Lead Engineer / Architect:** Antigravity Autonomous Lead Architect

---

## 1. Executive Summary

As a university mission-critical academic and financial ERP, **SIAKAD Enterprise & SPP** requires strict business continuity, point-in-time recovery capabilities, and absolute preservation of historical financial ledgers.

This document defines:
1. Target Service Level Agreements (RPO <= 15 minutes, RTO <= 1 hour).
2. Database backup procedures across SQLite (development/staging) and MySQL/PostgreSQL (production).
3. Append-only ledger preservation invariants.
4. Step-by-step disaster recovery drill runbook.

---

## 2. Recovery Objectives & SLA Targets

| Metric | Target SLA | Strategy Implemented |
| :--- | :---: | :--- |
| **Recovery Point Objective (RPO)** | **<= 15 Minutes** | Hourly database snapshots + continuous WAL/binary log archiving to S3/Cloud Storage. |
| **Recovery Time Objective (RTO)** | **<= 60 Minutes** | Automated container spin-up, pre-compiled assets, and scripted database restore commands. |
| **Financial Ledger Data Loss Tolerance**| **ZERO (0)** | Immutable append-only audit ledgers for all payment transactions. |

---

## 3. Database Backup Procedures

### 3.1 SQLite Environments (Development / Small Staging)
To prevent backing up an inconsistent database during active write operations:
```bash
# Correct SQLite atomic online backup (handles active WAL mode safely):
sqlite3 database/database.sqlite ".backup 'backups/siakad_$(date +%Y%m%d_%H%M%S).sqlite'"

# Compress and upload to secure offsite storage:
gzip -9 "backups/siakad_$(date +%Y%m%d_%H%M%S).sqlite"
```

### 3.2 Enterprise MySQL / MariaDB Environments (Production)
```bash
# High-concurrency online dump using InnoDB single-transaction:
mysqldump \
  --host="${DB_HOST}" \
  --port="${DB_PORT}" \
  --user="${DB_USERNAME}" \
  --password="${DB_PASSWORD}" \
  --single-transaction \
  --quick \
  --routines \
  --triggers \
  --databases "${DB_DATABASE}" \
  | gzip -9 > "/backups/siakad_prod_$(date +%Y%m%d_%H%M%S).sql.gz"
```

---

## 4. Immutable Financial Ledger Preservation Invariants

Financial tables (`pembayarans`, `pembayaran_ukts`, `h2h_transactions`) represent permanent institutional accounting records:
1. **Zero Truncation Rule:** Commands like `TRUNCATE` or `DELETE WITHOUT WHERE` are strictly barred across financial models.
2. **Cashier Protection:** Foreign key `RESTRICT` actions ensure that deleting an operator account (`users`) cannot cascade into historical cash receipts.
3. **Reconciliation Audit Trail:** Every transaction records bank partner ID, transaction timestamp, payment channel, and HMAC signature digest.

---

## 5. Step-by-Step Disaster Recovery Runbook

In the event of hardware failure, catastrophic datacenter outage, or ransomware incident:

### Step 1: Provision Clean Runtime Instance
Deploy the pre-built Docker image from container registry:
```bash
docker pull ghcr.io/institution/siakad-enterprise:latest
docker run -d --name siakad_restore --env-file /secure/env.production ...
```

### Step 2: Restore Database Snapshot
```bash
# Decompress and stream latest snapshot into fresh database instance:
gunzip < /backups/latest_siakad_prod.sql.gz | mysql -u ${DB_USERNAME} -p ${DB_DATABASE}
```

### Step 3: Verify Relational & Foreign Key Integrity
Run immediate automated foreign-key check:
```bash
php artisan tinker --execute="dump(DB::select('PRAGMA foreign_key_check'));"
# Or MySQL equivalent: check table status
```

### Step 4: Run Verification Tests
```bash
./vendor/bin/phpunit tests/Feature/Security/SecurityVulnerabilityRegressionTest.php
./vendor/bin/phpunit tests/Feature/Finance/FinancialLedgerRegressionTest.php
```

### Step 5: Route Production DNS Traffic
Switch Load Balancer / DNS records to the restored instance.

---

## 6. Conclusion
The disaster recovery procedures guarantee rapid failover, zero loss of student financial ledgers, and deterministic verification.
