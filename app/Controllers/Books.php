<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\BookModel;                 // Import model BookModel untuk akses DB
use CodeIgniter\HTTP\RedirectResponse;    // Type-hint untuk response redirect

/**
 * Controller CRUD Buku
 * Berisi aksi: index (list), show (detail), new (form create),
 * create (store), edit (form edit), update, delete
 */
class Books extends BaseController
{
    /**
     * Tampilkan daftar buku (GET /books)
     */
    public function index(): string
    {
        $model = new BookModel();                                   // Inisialisasi model
        $data = [
            'title' => 'Books',                                      // Judul halaman
            'books' => $model->orderBy('created_at', 'DESC')->findAll(), // Ambil semua buku, urut terbaru
        ];

        // Render view header, konten index, dan footer
        return view('templates/header', $data)
            . view('books/index', $data)
            . view('templates/footer');
    }

    /**
     * Tampilkan detail satu buku (GET /books/{id})
     */
    public function show(int $id): string
    {
        $model = new BookModel();                // Model
        $book  = $model->find($id);              // Cari buku berdasarkan id

        if (!$book) {                            // Jika tidak ditemukan -> 404
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Book not found');
        }

        $data = [
            'title' => $book['title'],           // Gunakan judul buku sebagai title halaman
            'book'  => $book,                    // Data buku untuk view
        ];

        // Tampilkan halaman detail
        return view('templates/header', $data)
            . view('books/show', $data)
            . view('templates/footer');
    }

    /**
     * Form tambah buku (GET /books/new)
     */
    public function new(): string
    {
        $data = [
            'title' => 'Add Book',               // Judul halaman
        ];

        // Tampilkan form create
        return view('templates/header', $data)
            . view('books/create')
            . view('templates/footer');
    }

    /**
     * Simpan buku baru (POST /books)
     * Analog dengan "store()" pada resource controller Laravel
     */
    public function create(): RedirectResponse|string
    {
        // Aturan validasi input
        $validationRules = [
            'title'  => 'required|min_length[2]|max_length[255]',   // Wajib, min 2, max 255
            'author' => 'required|min_length[2]|max_length[255]',   // Wajib, min 2, max 255
            'description' => 'permit_empty|string',                 // Boleh kosong, string
        ];

        // Jika validasi gagal, tampilkan form lagi dengan error
        if (!$this->validate($validationRules)) {
            $data = [
                'title' => 'Add Book',              // Judul halaman
                'validation' => $this->validator,   // Kirim validator ke view untuk menampilkan error
            ];
            return view('templates/header', $data)
                . view('books/create', $data)
                . view('templates/footer');
        }

        // Jika validasi lolos, simpan data ke database
        $model = new BookModel();
        $model->insert([
            'title'       => (string) $this->request->getPost('title'),       // Ambil input judul
            'author'      => (string) $this->request->getPost('author'),      // Ambil input penulis
            'description' => (string) $this->request->getPost('description') ?: null, // Ambil deskripsi atau null
        ]);

        // Redirect kembali ke list dengan flash message
        return redirect()->to('/books')->with('message', 'Book added');
    }

    /**
     * Form edit buku (GET /books/{id}/edit)
     */
    public function edit(int $id): string
    {
        $model = new BookModel();         // Model
        $book  = $model->find($id);       // Cari buku
        if (!$book) {                     // Jika tidak ada -> 404
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Book not found');
        }

        $data = [
            'title' => 'Edit Book',       // Judul halaman
            'book'  => $book,             // Kirim data buku ke view
        ];
        // Tampilkan form edit
        return view('templates/header', $data)
            . view('books/edit', $data)
            . view('templates/footer');
    }

    /**
     * Update data buku (POST /books/{id}/update)
     */
    public function update(int $id): RedirectResponse|string
    {
        // Aturan validasi sama seperti saat create
        $validationRules = [
            'title'  => 'required|min_length[2]|max_length[255]',
            'author' => 'required|min_length[2]|max_length[255]',
            'description' => 'permit_empty|string',
        ];

        $model = new BookModel();        // Model
        $book  = $model->find($id);      // Data lama buku
        if (!$book) {                    // Jika tidak ada -> 404
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Book not found');
        }

        // Jika validasi gagal, tampilkan form edit kembali dengan error
        if (!$this->validate($validationRules)) {
            $data = [
                'title' => 'Edit Book',
                'book'  => $book,
                'validation' => $this->validator,
            ];
            return view('templates/header', $data)
                . view('books/edit', $data)
                . view('templates/footer');
        }

        // Lolos validasi -> update ke database
        $model->update($id, [
            'title'       => (string) $this->request->getPost('title'),
            'author'      => (string) $this->request->getPost('author'),
            'description' => (string) $this->request->getPost('description') ?: null,
        ]);

        // Redirect dengan pesan sukses
        return redirect()->to('/books')->with('message', 'Book updated');
    }

    /**
     * Hapus buku (POST /books/{id}/delete)
     */
    public function delete(int $id): RedirectResponse
    {
        $model = new BookModel();    // Model
        $model->delete($id);         // Hapus berdasarkan id
        return redirect()->to('/books')->with('message', 'Book deleted'); // Redirect
    }
}
