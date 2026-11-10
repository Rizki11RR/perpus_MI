<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookCopy;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $password = env('SIPUS_DEV_ADMIN_PASSWORD');

        if (blank($password)) {
            return;
        }

        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator SIPUS',
                'email' => 'admin@local.invalid',
                'password' => $password,
                'role' => 'admin',
                'is_active' => true,
            ],
        );

        if (app()->environment('testing')) {
            return;
        }

        $category = BookCategory::firstOrCreate(['name' => 'Contoh Lokal'], ['description' => 'Data contoh untuk pengembangan lokal.']);
        $book = Book::firstOrCreate(
            ['book_code' => 'LOCAL-001'],
            ['category_id' => $category->id, 'title' => 'Contoh Buku Lokal'],
        );
        BookCopy::firstOrCreate(['barcode' => 'LOCAL-COPY-001'], ['book_id' => $book->id]);
    }
}
