# DreamHome Property Management System

DreamHome is a Laravel web application for managing rental property operations. It includes staff login, role-based dashboards, property, owner, client, viewing, inspection, lease, and branch management, plus a public property viewing page where clients can submit approval requests.

## System Requirements

- PHP: 8.3 or later
- Laravel: 13.x
- Composer
- Node.js and npm
- Database: PostgreSQL is used by the current local configuration
- Required PHP extensions: PDO, PDO PostgreSQL, OpenSSL, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo
- Recommended local stack: Laragon, XAMPP, Herd, or another PHP/PostgreSQL-capable environment

## Installation Steps

1. Clone or copy the project into your web server directory.

2. Install PHP dependencies:

```bash
composer install
```

3. Install frontend dependencies:

```bash
npm install
```

4. Create the environment file:

```bash
copy .env.example .env
```

On macOS/Linux:

```bash
cp .env.example .env
```

5. Generate the Laravel application key:

```bash
php artisan key:generate
```

6. Update `.env` with the database settings. The current development setup uses:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=dreamhome
DB_USERNAME=postgres
```

Set `DB_PASSWORD` according to your local PostgreSQL password.

## Database Setup

Use one of the following options.

### Option 1: Import SQL File

If a `dreamhome.sql` database export is provided, create a PostgreSQL database named `dreamhome`, then import the SQL file using pgAdmin or:

```bash
psql -h 127.0.0.1 -U postgres -d dreamhome -f dreamhome.sql
```

After import, confirm that the `.env` database credentials match your PostgreSQL setup.

### Option 2: Run Migrations

Run:

```bash
php artisan migrate
```

If seeders are prepared for your submitted database, run:

```bash
php artisan db:seed
```

Note: the current `DatabaseSeeder` only creates Laravel's default factory user unless it is updated with the project evaluator accounts.

## Run Instructions

Start the Laravel development server:

```bash
php artisan serve
```

Open the application in a browser:

```text
http://127.0.0.1:8000
```

Useful pages:

- Staff login: `http://127.0.0.1:8000/login`
- Public property viewing page: `http://127.0.0.1:8000/properties-for-rent`
- Client request approval queue: `http://127.0.0.1:8000/client-requests`

Build frontend assets if required:

```bash
npm run build
```

During development, Vite can be started with:

```bash
npm run dev
```

## Test Accounts

The current development database contains these role-based accounts:

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@dreamhome.com` | Use the password from the supplied database/export |
| Manager | `manager@dreamhome.com` | Use the password from the supplied database/export |
| Supervisor | `supervisor@dreamhome.com` | Use the password from the supplied database/export |
| Staff | `staff@dreamhome.com` | Use the password from the supplied database/export |

If the database is rebuilt from migrations only, create users with one of these roles: `admin`, `manager`, `supervisor`, or `staff`. Admin and manager users are required to approve public client requests.

## Known Limitations

- Public client requests are stored for admin/manager approval, but email notifications are not implemented.
- Property cards use local images from `public/images`; there is no per-property image upload module yet.
- The public request approval process creates a client record and optional viewing, but does not automatically notify the client.
- The current seeder does not fully populate all evaluator test accounts.
- If using a fresh database without an SQL dump, verify that all required base tables, including users/session-related tables, are present before testing authentication.
