<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('unauthenticated users cannot access authors api', function () {
    $this->getJson('/api/authors')
        ->assertUnauthorized();
});

test('authenticated users can list authors', function () {
    Sanctum::actingAs(User::factory()->create());

    Author::create(['name' => 'Isaac Asimov']);
    Author::create(['name' => 'George Orwell']);

    $this->getJson('/api/authors')
        ->assertOk()
        ->assertJsonCount(2);
});

test('authenticated users can create an author', function () {
    Sanctum::actingAs(User::factory()->create());

    $response = $this->postJson('/api/authors', [
        'name' => 'Gabriel García Márquez',
    ]);

    $response
        ->assertCreated()
        ->assertJsonFragment([
            'name' => 'Gabriel García Márquez',
        ]);

    $this->assertDatabaseHas('authors', [
        'name' => 'Gabriel García Márquez',
    ]);
});

test('author name is required', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->postJson('/api/authors', [
        'name' => '',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('name');
});

test('author name must be unique', function () {
    Sanctum::actingAs(User::factory()->create());

    Author::create([
        'name' => 'Isaac Asimov',
    ]);

    $this->postJson('/api/authors', [
        'name' => 'Isaac Asimov',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('name');
});

test('authenticated users can search authors', function () {
    Sanctum::actingAs(User::factory()->create());

    Author::create(['name' => 'Isaac Asimov']);
    Author::create(['name' => 'George Orwell']);

    $this->getJson('/api/authors?search=Asimov')
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment([
            'name' => 'Isaac Asimov',
        ]);
});

test('authenticated users can show an author', function () {
    Sanctum::actingAs(User::factory()->create());

    $author = Author::create([
        'name' => 'Julio Verne',
    ]);

    $this->getJson("/api/authors/{$author->id}")
        ->assertOk()
        ->assertJsonFragment([
            'id' => $author->id,
            'name' => 'Julio Verne',
        ]);
});

test('authenticated users can update an author', function () {
    Sanctum::actingAs(User::factory()->create());

    $author = Author::create([
        'name' => 'Nombre Original',
    ]);

    $this->putJson("/api/authors/{$author->id}", [
        'name' => 'Nombre Actualizado',
    ])
        ->assertOk()
        ->assertJsonFragment([
            'name' => 'Nombre Actualizado',
        ]);

    $this->assertDatabaseHas('authors', [
        'id' => $author->id,
        'name' => 'Nombre Actualizado',
    ]);
});

test('author with associated books cannot be deleted', function () {
    Sanctum::actingAs(User::factory()->create());

    $author = Author::create([
        'name' => 'Isaac Asimov',
    ]);

    $genre = Genre::create([
        'name' => 'Ciencia Ficción',
    ]);

    Book::create([
        'book_code' => 'A01',
        'title' => 'Fundación',
        'author_id' => $author->id,
        'genre_id' => $genre->id,
        'publication_year' => 1951,
        'description' => null,
    ]);

    $this->deleteJson("/api/authors/{$author->id}")
        ->assertStatus(409);

    $this->assertDatabaseHas('authors', [
        'id' => $author->id,
    ]);
});

test('author without associated books can be deleted', function () {
    Sanctum::actingAs(User::factory()->create());

    $author = Author::create([
        'name' => 'Autor Eliminable',
    ]);

    $this->deleteJson("/api/authors/{$author->id}")
        ->assertNoContent();

    $this->assertDatabaseMissing('authors', [
        'id' => $author->id,
    ]);
});