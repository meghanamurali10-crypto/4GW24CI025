# College Stock & Inventory Management System

Production-style PHP/MySQL inventory portal for an engineering college, designed for WAMP Server.

## Stack

- Core PHP with PDO prepared statements
- MySQL database: `college_stock_db`
- Bootstrap 5, Bootstrap Icons, JavaScript and AJAX-style UI interactions
- PHP sessions, role guards, CSRF tokens and password hashing
- Built-in fallback PDF writer; optional TCPDF/FPDF support can be added under `vendor/`
- CSV-compatible import/export with a `vendor/` path reserved for PhpSpreadsheet without Composer

## WAMP Setup

1. Copy `college_stock_portal` to `C:\wamp64\www\college_stock_portal`.
2. Start WAMP and ensure Apache + MySQL are green.
3. Open phpMyAdmin.
4. Import `database/college_stock_db.sql`.
5. Check database credentials in `config/config.php`.
   Default WAMP settings are:
   - host: `localhost`
   - user: `root`
   - password: empty
6. Open `http://localhost/college_stock_portal/login.php`.

## Demo Accounts

All seeded accounts use password: `password`

- Admin: `admin@college.test`
- Principal: `principal@college.test`
- CSE Department: `cse@college.test`
- ECE Department: `ece@college.test`

## Main Workflows

1. Admin maintains categories, items, users and stock transactions.
2. Department users create item requests using checkboxes and required quantities.
3. Principal approves or rejects requests with remarks.
4. Admin issues approved stock. The system creates OUTWARD transactions and prevents negative stock.
5. Admin exports stock book PDFs and CSV reports.

## Excel Notes

The import screen accepts `.csv` and `.xlsx`. Without Composer, true `.xlsx` parsing requires manually placing PhpSpreadsheet and its autoloader at `vendor/autoload.php`. The included immediate import path is CSV-compatible and uses this column order:

`code, name, category, quantity, unit, price, min, location, description`

## Security Features

- Passwords are hashed with `password_hash()`.
- Login uses `password_verify()` and session regeneration.
- Role-based access protects admin, principal and department modules.
- SQL uses PDO prepared statements.
- CSRF token validation protects POST forms.
- Upload validation blocks PHP, JS, EXE, shell and batch files.
- Uploaded files receive randomized names.
- All login, approval, PDF export, import/export and stock actions are logged.
- Item direct editing is locked after 10 minutes; later changes must use stock transactions.

## Important Files

- `database/college_stock_db.sql`: schema and dummy data
- `config/config.php`: WAMP database and app settings
- `includes/functions.php`: reusable security, upload and stock functions
- `admin/requests.php`: admin stock issue desk
- `principal/requests.php`: approval desk
- `department/request.php`: checkbox item request UI

