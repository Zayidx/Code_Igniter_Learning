<?php
declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration membuat tabel `books` untuk menyimpan data buku.
 */
class CreateBooksTable extends Migration
{
    /**
     * Naikkan (apply) migrasi: buat tabel dan kolomnya
     */
    public function up(): void
    {
        // Definisi kolom-kolom tabel
        $this->forge->addField([
            // Kolom id (primary key, auto increment)
            'id'         => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            // Judul buku
            'title'      => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            // Nama penulis
            'author'     => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            // Deskripsi buku (opsional)
            'description'=> [
                'type' => 'TEXT',
                'null' => true,
            ],
            // Timestamp dibuat
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            // Timestamp diperbarui
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        // Set primary key pada kolom id
        $this->forge->addKey('id', true);
        // Buat tabel books jika belum ada
        $this->forge->createTable('books', true);
    }

    /**
     * Turunkan (rollback) migrasi: hapus tabel
     */
    public function down(): void
    {
        // Hapus tabel books jika ada
        $this->forge->dropTable('books', true);
    }
}
