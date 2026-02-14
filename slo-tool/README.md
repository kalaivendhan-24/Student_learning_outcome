# Student Learning Outcome Mapping Tool & Academic Engagement System

## Tech Stack
- PHP 8+
- MySQL 8+
- HTML5, CSS3, JavaScript

## Project Structure
```text
/slo-tool
 ├── /config
 ├── /auth
 ├── /admin
 ├── /faculty
 ├── /mentor
 ├── /parent
 ├── /assets
 │    ├── css
 │    ├── js
 │    └── images
 ├── /database
 └── index.php
```

## Setup Steps
1. Create database and seed data by running `database/schema.sql` in MySQL.
2. Update DB credentials in `config/db.php`.
3. Place `slo-tool` in web root (`htdocs` in XAMPP/WAMP).
4. Start Apache and MySQL.
5. Run setup checker: `http://localhost/slo-tool/install.php`.
6. Open `http://localhost/slo-tool/`.

## Sample Login Credentials
- Admin: `admin@slo.edu` / `password`
- Faculty: `faculty@slo.edu` / `password`
- Mentor: `mentor@slo.edu` / `password`
- Parent: `parent@slo.edu` / `password`

## Implemented Modules
- Authentication with role-based redirection and hashed password verification.
- Admin management of users, courses, students, and mentor assignment.
- Faculty CO/PO/PSO mapping, marks entry, attainment logic, analytics, and alerts.
- Mentor progress monitoring, feedback/action plans, and parent communication.
- Parent visibility of performance, alerts, and suggestions.

## Notes
- PDF export is optional and not included in this baseline.
- For production, add CSRF tokens, rate limiting, and stronger validation.
