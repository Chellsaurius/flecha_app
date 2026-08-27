<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('books component renders successfully', function () {
    Livewire::test('books.index')
        ->assertStatus(200);
});

test('user can create a book from livewire', function () {
    $author = Author::create([
        'name' => 'Gabriel García Márquez',
    ]);

    $genre = Genre::create([
        'name' => 'Novela',
    ]);

    Livewire::test('books.index')
        ->set('title', 'El Coronel No Tiene Quien Le Escriba')
        ->set('author_id', $author->id)
        ->set('genre_id', $genre->id)
        ->set('publication_year', 1961)
        ->set('description', 'Novela corta.')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('books', [
        'title' => 'El Coronel No Tiene Quien Le Escriba',
        'author_id' => $author->id,
        'genre_id' => $genre->id,
        'publication_year' => 1961,
    ]);

    $book = Book::first();

    expect($book->book_code)
        ->toMatch('/^[A-Z][0-9]{2}$/');
});

test('book creation validates required fields', function () {
    Livewire::test('books.index')
        ->call('save')
        ->assertHasErrors([
            'title',
            'author_id',
            'genre_id',
            'publication_year',
        ]);
});

test('user can edit a book', function () {
    $author = Author::create([
        'name' => 'Juan Rulfo',
    ]);

    $genre = Genre::create([
        'name' => 'Novela',
    ]);

    $book = Book::create([
        'book_code' => 'A12',
        'title' => 'Título Original',
        'author_id' => $author->id,
        'genre_id' => $genre->id,
        'publication_year' => 1950,
        'description' => null,
    ]);

    Livewire::test('books.index')
        ->call('edit', $book->id)
        ->assertSet('editingBookId', $book->id)
        ->assertSet('title', 'Título Original')
        ->set('title', 'Pedro Páramo')
        ->set('publication_year', 1955)
        ->call('save')
        ->assertHasNoErrors();

    $book->refresh();

    expect($book->title)->toBe('Pedro Páramo');
    expect($book->publication_year)->toBe(1955);
    expect($book->book_code)->toBe('A12');
});

test('user can delete a book', function () {
    $author = Author::create([
        'name' => 'Elena Garro',
    ]);

    $genre = Genre::create([
        'name' => 'Novela',
    ]);

    $book = Book::create([
        'book_code' => 'B34',
        'title' => 'Los Recuerdos del Porvenir',
        'author_id' => $author->id,
        'genre_id' => $genre->id,
        'publication_year' => 1963,
        'description' => null,
    ]);

    Livewire::test('books.index')
        ->call('delete', $book->id);

    $this->assertDatabaseMissing('books', [
        'id' => $book->id,
    ]);
});

test('books can be searched by title', function () {
    $author = Author::create([
        'name' => 'Julio Cortázar',
    ]);

    $genre = Genre::create([
        'name' => 'Novela',
    ]);

    Book::create([
        'book_code' => 'C56',
        'title' => 'Rayuela',
        'author_id' => $author->id,
        'genre_id' => $genre->id,
        'publication_year' => 1963,
        'description' => null,
    ]);

    Livewire::test('books.index')
        ->set('search', 'Rayuela')
        ->assertSee('Rayuela');
});