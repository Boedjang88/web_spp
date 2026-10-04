# PRODUCT.md - Product Truth & Purpose

## Core Identity
- **Product Name**: Universitas SIAKAD Enterprise
- **Category**: Enterprise Higher Education Management & Financial Portal (SIAKAD Perguruan Tinggi)
- **Primary Audience**: Mahasiswa (Students), Dosen Pengajar (Lecturers), BAAK / Finance Officers, Academic Deans.

## Target Audience & Operational Context
- **Mahasiswa**: Mobile-first PWA access for Geo-Fenced GPS attendance check-in, Smart KRS registration, E-KHS grade viewing, and UKT Virtual Account payments. Needs high reliability under spotty mobile coverage.
- **Dosen & Staff**: Desktop-first Filament admin panel for grade input, BAP verification, parallel task distribution, EWS risk auditing, and PDDikti Neofeeder sync.

## Key Performance & Security Constraints
- **Concurrency**: Must sustain 10,000+ simultaneous student check-ins during peak 08:00 AM lecture slots (Redis atomic locks, MySQL `SELECT ... FOR UPDATE`).
- **Data Privacy**: UU PDP compliance with column-level PII encryption (NIK, Bank Accounts) and administrative unmasking audit logs.
- **Regulatory**: Strict compliance with Kemendikbudristek, PDDikti Neofeeder WS API, and BAN-PT/LAM accreditation rules.

## Brand Voice & Tone
- **Professional, Authoritative, Crisp, and Uncluttered**.
- Zero gimmicky "AI slop" visuals, zero floating neon blobs, zero redundant emojis.
