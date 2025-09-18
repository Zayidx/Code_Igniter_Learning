<?php
declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder untuk mengisi contoh data pada tabel `books`.
 */
class BooksSeeder extends Seeder
{
    public function run(): void
    {
        // Bersihkan data lama agar seeding idempotent saat migrate:refresh --seed
        $db = $this->db;                                 // Koneksi DB
        $db->disableForeignKeyChecks();                  // Matikan FK sementara (aman untuk dev)
        $db->table('books')->truncate();                 // Kosongkan tabel books
        $db->enableForeignKeyChecks();                   // Nyalakan lagi FK

        // Data contoh yang akan dimasukkan ke tabel
        $data = [
            [
                'title'   => 'Clean Code',                                     // Judul buku
                'author'  => 'Robert C. Martin',                                // Penulis
                'description' => 'A Handbook of Agile Software Craftsmanship.', // Deskripsi
            ],
            [
                'title'   => 'The Pragmatic Programmer',
                'author'  => 'Andrew Hunt, David Thomas',
                'description' => 'Your journey to mastery.',
            ],
        ];

        // Loop data dan insert ke tabel books
        foreach ($data as $row) {
            $this->db->table('books')->insert($row); // Insert satu baris data
        }
    }
}
