# Parking Slot Booking

A small PHP app for booking and releasing parking slots. Originally a bare
PHP/MySQL prototype; this version adds proper security (prepared statements,
CSRF protection, output escaping), self-service slot release, booking
timestamps with auto-expiry, and a `.env`-based configuration so it can run
against either SQLite (zero setup, great for local dev) or MySQL
(production).

## Requirements

- PHP 8.1+ with the `pdo_sqlite` and/or `pdo_mysql` extensions
- A web server (Apache/Nginx) for production, or PHP's built-in server for
  local development
- MySQL 5.7+ / MariaDB, only if you choose the MySQL driver

No Composer packages are required — everything is dependency-free PHP.

## Quick start (local, SQLite)

```bash
cp .env.example .env
# Leave DB_CONNECTION=sqlite (the default) — the database file and schema
# are created automatically on first run.

php -S 127.0.0.1:8000 -t public
```

Visit `http://127.0.0.1:8000/index.php` to book a slot and
`http://127.0.0.1:8000/admin.php` to manage bookings.

## Production setup (MySQL)

1. Create the database and tables:
   ```bash
   mysql -u root -p < database/schema.mysql.sql
   ```
2. Copy `.env.example` to `.env` and set:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_NAME=parking
   DB_USER=your_db_user
   DB_PASS=your_db_password
   APP_ENV=production
   ```
3. Point your web server's document root at the `public/` directory (not
   the project root — `.env`, `src/`, and `database/` should stay outside
   the web-accessible path).
4. Make sure `.env` is never committed — it's already in `.gitignore`.

## How it works

- **Booking** (`public/index.php`): pick an open slot and enter a name.
  You'll get a 6-character release code in the confirmation message — save
  it if you might need to free the slot yourself before it auto-expires.
- **Self-release**: use the "Release your slot" form on the booking page
  with the slot number and your release code.
- **Auto-expiry**: bookings older than `BOOKING_EXPIRY_HOURS` (in `.env`,
  default 24) are automatically freed the next time the page loads. Set to
  `0` to disable.
- **Admin panel** (`public/admin.php`): shows all slots and lets you force-
  release any of them, no code required.

## ⚠️ Admin panel has no login

By design (per current project requirements), `admin.php` is **not**
password-protected — anyone with the URL can release any slot. If you want
to lock it down later, the natural place to add a session-based auth check
is at the top of `public/admin.php`; there's a comment marking the spot.

## Project structure

```
public/            Web-accessible front controllers + assets
  index.php         Booking page (GET) + book/release actions (POST)
  admin.php         Admin page (GET) + unbook action (POST)
  assets/style.css
src/                Application code (not web-accessible)
  bootstrap.php      Loads env, starts session, sets error display
  Env.php            Minimal .env file parser
  Database.php       PDO connection factory (mysql or sqlite)
  SlotRepository.php All slot queries (book, release, expire, etc.)
  Csrf.php           CSRF token generation/validation
  Flash.php          One-time success/error messages across redirects
  helpers.php        e() escaping helper, redirect(), render()
views/              PHP templates, separated from logic
database/
  schema.mysql.sql   Run manually for production
  schema.sqlite.sql  Applied automatically for local SQLite use
.env.example        Copy to .env and fill in
```

## What changed from the original version

- Switched from raw `mysqli` string interpolation to PDO with prepared
  statements everywhere (fixes SQL injection risk)
- All dynamic output now passes through `htmlspecialchars()` (fixes stored
  XSS — e.g. a booking name like `<script>...</script>` used to render live)
- Added CSRF tokens to every form
- Moved DB credentials out of source code and into a git-ignored `.env`
- Added booking timestamps, configurable auto-expiry, and a self-service
  release-code system
- Added flash messages so booking/release failures (e.g. "slot just taken")
  are shown to the user instead of failing silently
- Reorganized into `public/` + `src/` + `views/` so the app code and config
  aren't sitting in the web root
