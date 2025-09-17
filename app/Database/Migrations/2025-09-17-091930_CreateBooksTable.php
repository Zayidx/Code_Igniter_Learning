<?php
declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBooksTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'title'      => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'author'     => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'description'=> [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('books', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('books', true);
    }
}

