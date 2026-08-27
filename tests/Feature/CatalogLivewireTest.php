<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('user can create an author', function () {
    Livewire::test('authors.index')
        ->set('name', 'octavio paz')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('authors', [
        'name' => 'Octavio Paz',
    ]);
});

test('user can edit an author', function () {
    $author = Author::create([
        'name' => 'Autor Original',
    ]);

    Livewire::test('authors.index')
        ->call('edit', $author->id)
        ->assertSet('editingAuthorId', $author->id)
        ->set('name', 'jorge luis borges')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('authors', [
        'id' => $author->id,
        'name' => 'Jorge Luis Borges',
    ]);
});

test('author with associated books cannot be deleted', function () {
    $author = Author::create([
        'name' => 'Juan Rulfo',
    ]);

    $genre = Genre::create([
        'name' => 'Novela',
    ]);

    Book::create([
        'book_code' => 'A12',
        'title' => 'Pedro Páramo',
        'author_id' => $author->id,
        'genre_id' => $genre->id,
        'publication_year' => 1955,
        'description' => null,
    ]);

    Livewire::test('authors.index')
        ->call('delete', $author->id);

    $this->assertDatabaseHas('authors', [
        'id' => $author->id,
    ]);

    $this->assertDatabaseHas('books', [
        'author_id' => $author->id,
    ]);
});

test('user can create a genre', function () {
    Livewire::test('genres.index')
        ->set('name', 'ciencia ficción')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('genres', [
        'name' => 'Ciencia Ficción',
    ]);
});

test('user can edit a genre', function () {
    $genre = Genre::create([
        'name' => 'Genero Original',
    ]);

    Livewire::test('genres.index')
        ->call('edit', $genre->id)
        ->assertSet('editingGenreId', $genre->id)
        ->set('name', 'realismo mágico')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('genres', [
        'id' => $genre->id,
        'name' => 'Realismo Mágico',
    ]);
});

test('genre with associated books cannot be deleted', function () {
    $author = Author::create([
        'name' => 'Gabriel García Márquez',
    ]);

    $genre = Genre::create([
        'name' => 'Realismo Mágico',
    ]);

    Book::create([
        'book_code' => 'B34',
        'title' => 'Cien Años de Soledad',
        'author_id' => $author->id,
        'genre_id' => $genre->id,
        'publication_year' => 1967,
        'description' => null,
    ]);

    Livewire::test('genres.index')
        ->call('delete', $genre->id);

    $this->assertDatabaseHas('genres', [
        'id' => $genre->id,
    ]);

    $this->assertDatabaseHas('books', [
        'genre_id' => $genre->id,
    ]);
});