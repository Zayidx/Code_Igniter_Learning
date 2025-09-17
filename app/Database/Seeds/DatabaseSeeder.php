<?php
declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Add all seeders you want to run on a fresh database
        $this->call(BooksSeeder::class);
    }
}

