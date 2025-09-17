# Books CRUD (CodeIgniter 4) — Step by Step

This guide walks you through creating a simple Books CRUD using CodeIgniter 4. It covers database setup, model, controller, views, routes, and a static UI template. The code is already added to this repo — use these steps to understand, run, or recreate it.

## Prerequisites
- PHP 8.1+
- Composer
- A database (MySQL recommended; defaults set in `.env`)
- CodeIgniter 4 dev tools (`php spark`) available

## 1) Configure the App
This repo uses MySQL by default. You can optionally switch to SQLite for a quick-start (see “Switching to SQLite” below). Example MySQL settings in `.env`:

```
CI_ENVIRONMENT = development
app.baseURL = 'http://127.0.0.1:8081/'

database.default.hostname = 127.0.0.1
database.default.database = ci4
database.default.username = root
database.default.password = root
database.default.DBDriver  = MySQLi
database.default.port      = 3306

MariaDB note: if you see collation errors, set
`database.default.DBCollat = utf8mb4_general_ci`.
For MySQL 8, `utf8mb4_0900_ai_ci` is fine.
```

Tip: Create the `ci4` database first if it doesn’t exist.

## 2) Install Dependencies

```
composer install
```

## 3) Create the Database Table (Migration)
This repo includes a migration:

- `app/Database/Migrations/2025-09-17-091930_CreateBooksTable.php`

Run migrations:

```
php spark migrate
```

## 4) Seed Sample Data (Optional)
Seeder is provided:

- `app/Database/Seeds/BooksSeeder.php`

Run seeder:

```
php spark db:seed BooksSeeder
```

## 5) Model
`app/Models/BookModel.php` defines the `books` table fields (`title`, `author`, `description`) and enables timestamps.

Key points:
- Uses `protected $allowedFields = ['title', 'author', 'description'];`
- `useTimestamps = true` to auto-manage `created_at` and `updated_at`.

## 6) Controller
`app/Controllers/Books.php` provides the CRUD actions:

- `index()` — list books
- `show($id)` — view one item
- `new()` — show create form
- `create()` — handle creation
- `edit($id)` — show edit form
- `update($id)` — handle update
- `delete($id)` — handle deletion

Notes:
- Simple validation rules on create/update.
- Redirects with flash messages.

## 7) Views (Bootstrap UI)
Views live in `app/Views/books/`:

- `index.php` — table list with actions
- `create.php` — form to add
- `edit.php` — form to update
- `show.php` — detail view

They render inside a simple site shell:

- `app/Views/templates/header.php` (adds Bootstrap CSS and a navbar)
- `app/Views/templates/footer.php` (adds Bootstrap JS bundle)

## 8) Routes
Routes are defined in `app/Config/Routes.php` (before the catch‑all):

```
$routes->get('books', 'Books::index');
$routes->get('books/new', 'Books::new');
$routes->post('books', 'Books::create');
$routes->get('books/(:num)/edit', 'Books::edit/$1');
$routes->post('books/(:num)/update', 'Books::update/$1');
$routes->post('books/(:num)/delete', 'Books::delete/$1');
$routes->get('books/(:num)', 'Books::show/$1');
```

Order matters so that `(:segment)` catch‑all for pages doesn’t intercept `books` routes.

## 9) Run the Dev Server

```
php spark serve --port 8081
```

Go to:

- `http://127.0.0.1:8081/books` — CRUD UI
- `http://127.0.0.1:8081/` — home

## Switching to SQLite (optional)
If you want to avoid MySQL credentials locally, you can switch to SQLite:

1. In `app/Config/Database.php`, set `$defaultGroup = 'sqlite'` (the `sqlite` group is already defined).
2. In `.env`, uncomment the two SQLite lines at the bottom:
   - `database.default.DBDriver = SQLite3`
   - `database.default.database = writable/database/ci4.sqlite`
3. Run `php spark migrate` and (optionally) `php spark db:seed BooksSeeder`.

## 10) Static UI Template (Optional)
A standalone UI/UX template is included at:

- `main/dist/index.html`

Open it directly in a browser to preview a Bootstrap-styled CRUD layout and modals. This file is static and not wired to the backend — it’s a design reference you can adapt.

## 11) Summary of Files Added

- Migration: `app/Database/Migrations/2025-09-17-091930_CreateBooksTable.php`
- Seeder: `app/Database/Seeds/BooksSeeder.php`
- Model: `app/Models/BookModel.php`
- Controller: `app/Controllers/Books.php`
- Views: `app/Views/books/{index,create,edit,show}.php`
- Templates updated: `app/Views/templates/{header,footer}.php`
- Routes updated: `app/Config/Routes.php`
- Static template: `main/dist/index.html`

## Troubleshooting
- 404 on `/books`: ensure routes are in place before the pages catch‑all and server restarted.
- DB errors: verify `.env` DB settings and that the `books` table exists (rerun `php spark migrate`).
- CSRF: if enabled and you see token errors, ensure forms include `<?= csrf_field() ?>` (already present).

## Next Steps
- Add pagination to `index()`.
- Add search/filter.
- Use RESTful routes with method spoofing (`_method`) if you prefer PUT/DELETE.
- Add tests under `tests/` using `CIUnitTestCase`.
