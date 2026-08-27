<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Author;
use App\Models\Genre;
use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $author = Author::create([
            'name' => 'Gabriel García Márquez',
        ]);

        $genre = Genre::create([
            'name' => 'Fantasía',
        ]);

        Book::create([
            'book_code' => 'A12',
            'title' => 'Cien años de soledad',
            'author_id' => $author->id,
            'genre_id' => $genre->id,
            'publication_year' => 1967,
            'description' => 'Novela de Gabriel García Márquez.',
        ]);
    }
}
