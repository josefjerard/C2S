# C2S

Mentee Management System built with PHP + MySQL (XAMPP).

## Requirements

- XAMPP (PHP 8.x, MySQL/MariaDB, Apache)

## Setup

1. Place the project in `C:\xampp\htdocs\C2S`.
2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Import the database (either):
   - phpMyAdmin → Import → select `database.sql`, or
   - CLI: `Get-Content database.sql | & "C:\xampp\mysql\bin\mysql.exe" -u root`
4. Open http://localhost/C2S/

DB credentials are set in `config.php` (defaults: host `127.0.0.1`, user `root`, no password). Two databases are used:

- `c2s_accounts` — user accounts (login/registration)
- `c2s_mentees` — all mentee records

## Accounts

Default seeded account: username `admin`, password `admin12345`. Register a new account from the login page — each account gets its own completely blank workspace, and mentees are private per account. Use **Log Out** in the top bar to end the session.

## Features

- Add, edit, view, delete mentees
- Fields: mentee name, status, contact number, birthday, address, CLDP 1–3, potential mentor (Yes/No), C2S 101, other trainings, remarks
- Module/Lesson picker: grouped list box with 4 modules × 6 lessons
- Status options: Active / Inactive / Transferred to Other Ministry
- Training progress options (CLDP 1–3): Unenrolled / Ongoing / Incomplete / Completed
- C2S 101 options: Lesson 1–5 / Completed
- Age is computed automatically from birthday (no manual input)
- Contact number accepts digits only
- Dashboard stats, search, and status filter
- CSRF protection and prepared statements

## Project Structure

```
C2S/
├── index.php               Dashboard (list, search, filter)
├── create.php              Add mentee
├── edit.php                Edit mentee
├── view.php                Mentee details
├── delete.php              Delete handler
├── config.php              DB connection + session
├── database.sql            Schema + sample data
├── includes/
│   ├── functions.php       Helpers (validation, CSRF, flash)
│   ├── header.php
│   ├── footer.php
│   └── _form_fields.php    Shared add/edit form
└── assets/
    └── style.css           Styling
```
