# Project Overview — CI Library (Books CRUD)

This project is a CodeIgniter 4 (PHP 8.1+) application implementing a simple Library (Books) CRUD with a clean Bootstrap UI, database migrations/seeders, and clear project structure. It is designed for quick local development (XAMPP/MariaDB) and easy extension.

- Framework: CodeIgniter 4
- Language: PHP 8.1+
- DB: MySQL/MariaDB (with XAMPP socket), optional SQLite for quick-start
- UI: Bootstrap 5 (CDN)
- Testing: PHPUnit 10


## Quick Start
- Install dependencies: `composer install`
- Configure DB in `.env` (see “Database & Environment”)
- Create/refresh schema: `php spark migrate` (or `php spark migrate:refresh -f`)
- Seed sample data: `php spark db:seed BooksSeeder` (or `php spark db:seed DatabaseSeeder`)
- One-shot reset + seed (Laravel-like): `composer run fresh-seed`
- Run dev server: `php spark serve --port 8080`
- Open: `http://127.0.0.1:8080/books`


## Directory & File Map (What each part does)
- `public/`
  - `index.php` — Front controller (web root). Your web server must point here.
- `app/`
  - `Controllers/`
    - `Home.php` — Redirects `/` to `/books` (landing to Library).
    - `Books.php` — Books CRUD controller (index/show/new/create/edit/update/delete).
  - `Models/`
    - `BookModel.php` — ORM-like model for `books` table; sets `allowedFields`, timestamps.
  - `Views/`
    - `templates/header.php` — Global header, Bootstrap include, navbar (“CI Library”).
    - `templates/footer.php` — Global footer, Bootstrap JS bundle.
    - `books/index.php` — Table list of books with actions.
    - `books/create.php` — Form to add a book.
    - `books/edit.php` — Form to update a book.
    - `books/show.php` — Book detail page.
  - `Config/`
    - `Routes.php` — Explicit route definitions for Books and static pages.
    - `Database.php` — Connection groups (default MySQL, optional `sqlite`).
    - Other `Config/*` — Session, Cache, Logger, etc.
  - `Database/`
    - `Migrations/2025-09-17-091930_CreateBooksTable.php` — Defines `books` schema.
    - `Seeds/BooksSeeder.php` — Seeds sample book rows.
    - `Seeds/DatabaseSeeder.php` — Aggregator; calls `BooksSeeder` (extend here as needed).
- `writable/`
  - `logs/` — Application logs.
  - `cache/`, `session/`, `database/` — Runtime data; keep writable for the web user.
- `vendor/` — Composer dependencies (do not edit).
- `main/dist/index.html` — Standalone, static Bootstrap UI template (design reference only).
- `.env` — Environment overrides: baseURL, DB credentials, session, etc.
- `doc.md` — Step-by-step CRUD guide (how to run/build/migrate/seed).
- `project.md` — This detailed explanation of the project’s structure and design.
- `composer.json` — Dependencies and scripts (includes `fresh-seed`).


## Application Flow (Request → Response)
1. A request (e.g., `GET /books`) hits `public/index.php` (front controller).
2. `app/Config/Routes.php` maps it to `App\Controllers\Books::index`.
3. Controller loads `BookModel` to query the DB.
4. Controller returns a View (e.g., `books/index`) wrapped with templates (`header`, `footer`).
5. Forms post to controller actions (create/update/delete) with `csrf_field()` for CSRF protection.
6. Flash messages and validation errors render back in views.


## Routes (explicit, safe ordering)
- `GET /` → redirect to `/books`
- `GET /books` → `Books::index` (list)
- `GET /books/new` → `Books::new` (create form)
- `POST /books` → `Books::create` (store)
- `GET /books/(:num)/edit` → `Books::edit/$1` (edit form)
- `POST /books/(:num)/update` → `Books::update/$1` (update)
- `POST /books/(:num)/delete` → `Books::delete/$1` (delete)
- `GET /home` and `GET /(:segment)` → Static page handler (from `Pages` controller)

Notes:
- Routes are defined before the catch‑all static pages so they aren’t intercepted.
- We use `POST` for update/delete endpoints. If you prefer HTTP verb spoofing, add hidden `_method` and enable it.


## Controllers
- `Home`
  - `index()` → redirects to `/books` to make Library the landing page.
- `Books`
  - `index()` → fetches all books ordered by `created_at` desc; renders table.
  - `show($id)` → fetches a single book; 404 if not found.
  - `new()` → shows create form.
  - `create()` → validates input, inserts, redirects with flash.
  - `edit($id)` → shows edit form for existing book; 404 if not found.
  - `update($id)` → validates, updates record, redirects with flash.
  - `delete($id)` → deletes record, redirects with flash.

Validation
- `title`: required 2–255 chars
- `author`: required 2–255 chars
- `description`: optional


## Model
- `BookModel`
  - `$table = 'books'`, `$primaryKey = 'id'`, auto-increment.
  - `$allowedFields = ['title','author','description']` (mass-assignment safe list).
  - `$useTimestamps = true` → writes `created_at` and `updated_at` automatically.
  - `$returnType = 'array'` for convenience in views.


## Views & Templates
- `templates/header.php` — Sets `<title>` from `$title`, includes Bootstrap, draws navbar with link to `/books`.
- `templates/footer.php` — Footer and Bootstrap JS bundle.
- `books/index.php` —
  - Flash message area.
  - “Add Book” button to `/books/new`.
  - Table of books (Title, Author, Created) with actions (View/Edit/Delete).
  - First column shows sequential row numbers (1, 2, 3, …) independent of DB `id`.
- `books/create.php` & `books/edit.php` —
  - Forms include `csrf_field()` and show validation errors.
  - Fields: Title (required), Author (required), Description (optional).
- `books/show.php` —
  - Displays book details and quick actions (edit/delete).

Security/UX
- Escapes all output with `esc()`.
- Uses CSRF token in forms.
- Uses flash messages for user feedback.


## Database & Environment
- Primary DB: MySQL/MariaDB (XAMPP)
  - For XAMPP default, `.env` typically uses the Unix socket:
    - `database.default.hostname = /opt/lampp/var/mysql/mysql.sock`
    - `database.default.username = root`
    - `database.default.password =` (blank by default)
    - `database.default.database = ci4`
    - `database.default.DBDriver  = MySQLi`
    - MariaDB collation: `database.default.DBCollat = utf8mb4_general_ci`
  - If using TCP:
    - `database.default.hostname = 127.0.0.1`
    - `database.default.port = 3306`
    - Ensure user `'user'@'127.0.0.1'` exists and has privileges.
- Optional quick-start DB: SQLite
  - `app/Config/Database.php` defines a `sqlite` group.
  - To switch: set `$defaultGroup = 'sqlite'` and in `.env` set
    - `database.default.DBDriver = SQLite3`
    - `database.default.database = writable/database/ci4.sqlite`

Migrations
- `CreateBooksTable` creates `books` with columns:
  - `id` INT AI PK
  - `title` VARCHAR(255)
  - `author` VARCHAR(255)
  - `description` TEXT NULL
  - `created_at`, `updated_at` DATETIME NULL (managed by model timestamps)

Seeders
- `BooksSeeder` inserts a couple of sample books.
- `DatabaseSeeder` calls `BooksSeeder`; extend it to add more seeders.


## Developer Commands
- Run server: `php spark serve --port 8080`
- List routes: `php spark routes`
- Migrate up: `php spark migrate`
- Rollback last batch: `php spark migrate:rollback`
- Refresh (rollback then latest): `php spark migrate:refresh -f`
- Seed specific class: `php spark db:seed BooksSeeder`
- Seed all (aggregator): `php spark db:seed DatabaseSeeder`
- One-shot reset + seed: `composer run fresh-seed`
- Optimize autoload: `composer dump-autoload -o`
- Tests: `composer test` or `vendor/bin/phpunit -c phpunit.xml.dist`


## Code Style & Conventions
- PHP 8.1+, PSR-12, 4 spaces, one class per file.
- PSR-4 namespaces under `App\`.
- Controllers: PascalCase extending `BaseController`.
- Models: singular PascalCase; `$allowedFields` must include form fields.
- Views: snake_case `.php` under `app/Views/`.
- Use `declare(strict_types=1);`, scalar and return types.
- Escape output (`esc()`), use `csrf_field()` in forms.


## Static UI Template (`main/dist/index.html`)
- Standalone HTML template showcasing a Books table with modals (Add/Edit/View).
- Not wired to the backend; useful for UX reference or future integration.


## Deployment Notes
- Set `CI_ENVIRONMENT = production` in `.env` for live.
- Set `app.baseURL` to your production URL.
- Ensure web root points to `public/` only.
- Ensure `writable/` is owned by the web user and writable.
- Generate encryption key: `php spark key:generate` (writes to `.env`).
- Consider enabling HTTPS: `app.forceGlobalSecureRequests = true`.
- Optional: `composer dump-autoload -o`, `php spark optimize`.


## Extending the Project
- Add fields to Books
  1) Create a new migration to add columns.
  2) Add fields to `BookModel::$allowedFields`.
  3) Update forms/views.
  4) Migrate: `php spark migrate`.
- Add another resource (e.g., Authors)
  1) `make:model`, `make:controller`, `make:migration` (or manual files following Books pattern).
  2) Define routes before catch‑all.
  3) Add views and templates.
  4) Add seeder, include in `DatabaseSeeder`.


## Troubleshooting
- “Access denied” (MySQL): verify `.env` credentials and DB/user privileges.
- “Unknown collation utf8mb4_0900_ai_ci” on MariaDB: use `utf8mb4_general_ci`.
- 404 on `/books`: verify `app/Config/Routes.php` and restart server.
- CSRF errors: ensure `<?= csrf_field() ?>` is present and session is working.
- Permissions: ensure `writable/` is writable by the web user.


## Autoload & Migrations (PSR-4 note)
CodeIgniter migration files are timestamp-prefixed, e.g. `2025-09-17-091930_CreateBooksTable.php` with class `CreateBooksTable` inside. Composer’s PSR-4 autoloader warns because the filename doesn’t match the class. This is expected: CodeIgniter locates migrations via its own locator, not Composer.

- We exclude migrations/seeds from Composer classmap in `composer.json`:
  - `autoload.exclude-from-classmap`: `app/Database/Migrations`, `app/Database/Migrations/**`, `app/Database/Seeds`, `app/Database/Seeds/**`, and `**/Database/Migrations/**`.
- If you add new migrations/seeders and see similar warnings:
  - Run: `composer dump-autoload -o`
  - Ensure the exclude paths above are present.
  - Verify timestamp format in `app/Config/Migrations.php` matches your filenames (`$timestampFormat`).


## Step-by-Step CRUD Tutorial (with code)
This section shows how to build the Books CRUD from scratch. The repo already contains these files; use this as a learning reference.

1) Make a Migration
- Command: `php spark make:migration CreateBooksTable`
- File example (`app/Database/Migrations/xxxxxx_CreateBooksTable.php`):

```php
<?php
declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBooksTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'title'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'author'     => ['type' => 'VARCHAR', 'constraint' => 255],
            'description'=> ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('books');
    }

    public function down(): void
    {
        $this->forge->dropTable('books');
    }
}
```

2) Create the Model
- Command: `php spark make:model BookModel`
- File: `app/Models/BookModel.php`

```php
<?php
declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class BookModel extends Model
{
    protected $table         = 'books';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['title', 'author', 'description'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
```

3) Register Routes
- File: `app/Config/Routes.php`

```php
$routes->get('books', 'Books::index');
$routes->get('books/new', 'Books::new');
$routes->post('books', 'Books::create');          // store
$routes->get('books/(:num)/edit', 'Books::edit/$1');
$routes->post('books/(:num)/update', 'Books::update/$1');
$routes->post('books/(:num)/delete', 'Books::delete/$1');
$routes->get('books/(:num)', 'Books::show/$1');   // detail
```

4) Implement the Controller
- Command: `php spark make:controller Books`
- File: `app/Controllers/Books.php` (key methods are shown below)

List (GET /books)
```php
public function index(): string
{
    $model = new BookModel();
    $data = [
        'title' => 'Books',
        'books' => $model->orderBy('created_at', 'DESC')->findAll(),
    ];
    return view('templates/header', $data)
        . view('books/index', $data)
        . view('templates/footer');
}
```

Show create form (GET /books/new)
```php
public function new(): string
{
    $data = ['title' => 'Add Book'];
    return view('templates/header', $data)
        . view('books/create')
        . view('templates/footer');
}
```

Store/create (POST /books) — this is the “store” function
```php
public function create(): \CodeIgniter\HTTP\RedirectResponse|string
{
    $rules = [
        'title'  => 'required|min_length[2]|max_length[255]',
        'author' => 'required|min_length[2]|max_length[255]',
        'description' => 'permit_empty|string',
    ];

    if (! $this->validate($rules)) {
        $data = ['title' => 'Add Book', 'validation' => $this->validator];
        return view('templates/header', $data)
            . view('books/create', $data)
            . view('templates/footer');
    }

    $model = new BookModel();
    $model->insert([
        'title'       => (string) $this->request->getPost('title'),
        'author'      => (string) $this->request->getPost('author'),
        'description' => (string) $this->request->getPost('description') ?: null,
    ]);

    return redirect()->to('/books')->with('message', 'Book added');
}
```

Edit form (GET /books/{id}/edit)
```php
public function edit(int $id): string
{
    $model = new BookModel();
    $book  = $model->find($id);
    if (! $book) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Book not found');
    }
    $data = ['title' => 'Edit Book', 'book' => $book];
    return view('templates/header', $data)
        . view('books/edit', $data)
        . view('templates/footer');
}
```

Update (POST /books/{id}/update)
```php
public function update(int $id): \CodeIgniter\HTTP\RedirectResponse|string
{
    $rules = [
        'title'  => 'required|min_length[2]|max_length[255]',
        'author' => 'required|min_length[2]|max_length[255]',
        'description' => 'permit_empty|string',
    ];

    $model = new BookModel();
    $book  = $model->find($id);
    if (! $book) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Book not found');
    }

    if (! $this->validate($rules)) {
        $data = ['title' => 'Edit Book', 'book' => $book, 'validation' => $this->validator];
        return view('templates/header', $data)
            . view('books/edit', $data)
            . view('templates/footer');
    }

    $model->update($id, [
        'title'       => (string) $this->request->getPost('title'),
        'author'      => (string) $this->request->getPost('author'),
        'description' => (string) $this->request->getPost('description') ?: null,
    ]);

    return redirect()->to('/books')->with('message', 'Book updated');
}
```

Delete (POST /books/{id}/delete)
```php
public function delete(int $id): \CodeIgniter\HTTP\RedirectResponse
{
    $model = new BookModel();
    $model->delete($id);
    return redirect()->to('/books')->with('message', 'Book deleted');
}
```

Detail (GET /books/{id})
```php
public function show(int $id): string
{
    $model = new BookModel();
    $book  = $model->find($id);
    if (! $book) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Book not found');
    }
    $data = ['title' => $book['title'], 'book' => $book];
    return view('templates/header', $data)
        . view('books/show', $data)
        . view('templates/footer');
}
```

5) Create Views
- Index table (`app/Views/books/index.php`):

```php
<?php $no = 1; foreach ($books as $item): ?>
  <tr>
    <td><?= $no++ ?></td>
    <td><?= esc($item['title']) ?></td>
    <td><?= esc($item['author']) ?></td>
    <td><?= esc($item['created_at'] ?? '-') ?></td>
    <td>
      <a class="btn btn-sm btn-outline-secondary" href="<?= site_url('books/' . $item['id']) ?>">View</a>
      <a class="btn btn-sm btn-outline-primary" href="<?= site_url('books/' . $item['id'] . '/edit') ?>">Edit</a>
      <form action="<?= site_url('books/' . $item['id'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Delete this book?');">
        <?= csrf_field() ?>
        <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
      </form>
    </td>
  </tr>
<?php endforeach; ?>
```

- Create form (`app/Views/books/create.php`):

```php
<form action="<?= site_url('books') ?>" method="post">
  <?= csrf_field() ?>
  <input name="title" value="<?= esc(old('title')) ?>" required>
  <input name="author" value="<?= esc(old('author')) ?>" required>
  <textarea name="description"><?= esc(old('description')) ?></textarea>
  <button type="submit">Create</button>
  <!-- show $validation errors if present -->
  <?php if (isset($validation)) foreach ($validation->getErrors() as $e) echo '<div>'.$e.'</div>'; ?>
  <!-- end -->
  </form>
```

- Edit form (`app/Views/books/edit.php`): similar to create, but action posts to `books/{id}/update` and uses existing `$book` values.

- Show (`app/Views/books/show.php`): display `title`, `author`, `description`, and action buttons.

6) Optional: Seeder
- File: `app/Database/Seeds/BooksSeeder.php`

```php
$this->db->table('books')->insert([
  'title' => 'Clean Code',
  'author' => 'Robert C. Martin',
  'description' => 'A Handbook of Agile Software Craftsmanship.',
]);
```

7) Run It
- Migrate: `php spark migrate`
- Seed: `php spark db:seed BooksSeeder`
- Laravel-like refresh + seed: `composer run fresh-seed`
