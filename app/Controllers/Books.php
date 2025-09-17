<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\BookModel;
use CodeIgniter\HTTP\RedirectResponse;

class Books extends BaseController
{
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

    public function show(int $id): string
    {
        $model = new BookModel();
        $book  = $model->find($id);

        if (!$book) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Book not found');
        }

        $data = [
            'title' => $book['title'],
            'book'  => $book,
        ];

        return view('templates/header', $data)
            . view('books/show', $data)
            . view('templates/footer');
    }

    public function new(): string
    {
        $data = [
            'title' => 'Add Book',
        ];

        return view('templates/header', $data)
            . view('books/create')
            . view('templates/footer');
    }

    public function create(): RedirectResponse|string
    {
        $validationRules = [
            'title'  => 'required|min_length[2]|max_length[255]',
            'author' => 'required|min_length[2]|max_length[255]',
            'description' => 'permit_empty|string',
        ];

        if (!$this->validate($validationRules)) {
            $data = [
                'title' => 'Add Book',
                'validation' => $this->validator,
            ];
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

    public function edit(int $id): string
    {
        $model = new BookModel();
        $book  = $model->find($id);
        if (!$book) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Book not found');
        }

        $data = [
            'title' => 'Edit Book',
            'book'  => $book,
        ];
        return view('templates/header', $data)
            . view('books/edit', $data)
            . view('templates/footer');
    }

    public function update(int $id): RedirectResponse|string
    {
        $validationRules = [
            'title'  => 'required|min_length[2]|max_length[255]',
            'author' => 'required|min_length[2]|max_length[255]',
            'description' => 'permit_empty|string',
        ];

        $model = new BookModel();
        $book  = $model->find($id);
        if (!$book) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Book not found');
        }

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

        $model->update($id, [
            'title'       => (string) $this->request->getPost('title'),
            'author'      => (string) $this->request->getPost('author'),
            'description' => (string) $this->request->getPost('description') ?: null,
        ]);

        return redirect()->to('/books')->with('message', 'Book updated');
    }

    public function delete(int $id): RedirectResponse
    {
        $model = new BookModel();
        $model->delete($id);
        return redirect()->to('/books')->with('message', 'Book deleted');
    }
}

