<?php
declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BooksSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'title'   => 'Clean Code',
                'author'  => 'Robert C. Martin',
                'description' => 'A Handbook of Agile Software Craftsmanship.',
            ],
            [
                'title'   => 'The Pragmatic Programmer',
                'author'  => 'Andrew Hunt, David Thomas',
                'description' => 'Your journey to mastery.',
            ],
        ];

        foreach ($data as $row) {
            $this->db->table('books')->insert($row);
        }
    }
}

