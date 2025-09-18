<?php
declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * BookModel
 * Model untuk tabel `books` yang menangani operasi database dasar.
 */
class BookModel extends Model
{
    // Nama tabel di database
    protected $table            = 'books';
    // Primary key dari tabel
    protected $primaryKey       = 'id';
    // Menggunakan auto increment pada kolom primary key
    protected $useAutoIncrement = true;
    // Tipe data hasil query: array asosiatif
    protected $returnType       = 'array';
    // Proteksi field: hanya field yang ada di $allowedFields yang boleh di-isi massal
    protected $protectFields    = true;
    // Daftar field yang boleh diisi (mass assignment)
    protected $allowedFields    = ['title', 'author', 'description'];

    // Aktifkan timestamp otomatis (created_at, updated_at)
    protected $useTimestamps = true;
    // Nama kolom untuk created_at
    protected $createdField  = 'created_at';
    // Nama kolom untuk updated_at
    protected $updatedField  = 'updated_at';
}
