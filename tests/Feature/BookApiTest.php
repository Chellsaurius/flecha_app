<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('unauthenticated user cannot access books', function () {
    $response = $this->getJson('/api/books');

    $response->assertUnauthorized();
});

test('authenticated user can list books', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/books');

    $response->assertOk();
});

test('authenticated user can create a book', function () {
    $user = User::factory()->create();
    $author = Author::create([
        'name' => 'Gabriel García Márquez',
    ]);
    $genre = Genre::create([
        'name' => 'Novela',
    ]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/books', [
        'title' => 'La Hojarasca',
        'author_id' => $author->id,
        'genre_id' => $genre->id,
        'publication_year' => 1955,
        'description' => 'Primera novela del autor.',
    ]);

    $response
        ->assertCreated()
        ->assertJsonFragment([
            'title' => 'La Hojarasca',
            'publication_year' => 1955,
        ]);

    expect($response->json('book_code'))
        ->toMatch('/^[A-Z][0-9]{2}$/');

    $this->assertDatabaseHas('books', [
        'title' => 'La Hojarasca',
        'author_id' => $author->id,
        'genre_id' => $genre->id,
    ]);
});

test('book creation validates required fields', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/books', []);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'title',
            'author_id',
            'genre_id',
            'publication_year',
        ]);
});

test('authenticated user can search books', function () {
    $user = User::factory()->create();

    $author = Author::create([
        'name' => 'Julio Cortázar',
    ]);

    $genre = Genre::create([
        'name' => 'Novela',
    ]);

    Book::create([
        'book_code' => 'A12',
        'title' => 'Rayuela',
        'author_id' => $author->id,
        'genre_id' => $genre->id,
        'publication_year' => 1963,
        'description' => null,
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/books?search=Rayuela');

    $response
        ->assertOk()
        ->assertJsonFragment([
            'title' => 'Rayuela',
        ]);
});

test('authenticated user can show a book', function () {
    $user = User::factory()->create();

    $author = Author::create([
        'name' => 'Juan Rulfo',
    ]);

    $genre = Genre::create([
        'name' => 'Novela',
    ]);

    $book = Book::create([
        'book_code' => 'B34',
        'title' => 'Pedro Páramo',
        'author_id' => $author->id,
        'genre_id' => $genre->id,
        'publication_year' => 1955,
        'description' => null,
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson("/api/books/{$book->id}");

    $response
        ->assertOk()
        ->assertJsonFragment([
            'book_code' => 'B34',
            'title' => 'Pedro Páramo',
        ]);
});

test('authenticated user can update a book without changing its code', function () {
    $user = User::factory()->create();

    $author = Author::create([
        'name' => 'Carlos Fuentes',
    ]);

    $genre = Genre::create([
        'name' => 'Novela',
    ]);

    $book = Book::create([
        'book_code' => 'C56',
        'title' => 'Título Original',
        'author_id' => $author->id,
        'genre_id' => $genre->id,
        'publication_year' => 1960,
        'description' => null,
    ]);

    Sanctum::actingAs($user);

    $response = $this->putJson("/api/books/{$book->id}", [
        'title' => 'La Región Más Transparente',
        'author_id' => $author->id,
        'genre_id' => $genre->id,
        'publication_year' => 1958,
        'description' => 'Descripción actualizada.',
    ]);

    $response
        ->assertOk()
        ->assertJsonFragment([
            'book_code' => 'C56',
            'title' => 'La Región Más Transparente',
        ]);

    $this->assertDatabaseHas('books', [
        'id' => $book->id,
        'book_code' => 'C56',
        'title' => 'La Región Más Transparente',
    ]);
});

test('authenticated user can delete a book', function () {
    $user = User::factory()->create();

    $author = Author::create([
        'name' => 'Elena Garro',
    ]);

    $genre = Genre::create([
        'name' => 'Novela',
    ]);

    $book = Book::create([
        'book_code' => 'D78',
        'title' => 'Los Recuerdos del Porvenir',
        'author_id' => $author->id,
        'genre_id' => $genre->id,
        'publication_year' => 1963,
        'description' => null,
    ]);

    Sanctum::actingAs($user);

    $response = $this->deleteJson("/api/books/{$book->id}");

    $response->assertNoContent();

    $this->assertDatabaseMissing('books', [
        'id' => $book->id,
    ]);
});