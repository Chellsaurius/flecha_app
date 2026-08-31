<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('unauthenticated users cannot access genres api', function () {
    $this->getJson('/api/genres')
        ->assertUnauthorized();
});

test('authenticated users can list genres', function () {
    Sanctum::actingAs(User::factory()->create());

    Genre::create(['name' => 'Novela']);
    Genre::create(['name' => 'Ciencia Ficción']);

    $this->getJson('/api/genres')
        ->assertOk()
        ->assertJsonCount(2);
});

test('authenticated users can create a genre', function () {
    Sanctum::actingAs(User::factory()->create());

    $response = $this->postJson('/api/genres', [
        'name' => 'Fantasía',
    ]);

    $response
        ->assertCreated()
        ->assertJsonFragment([
            'name' => 'Fantasía',
        ]);

    $this->assertDatabaseHas('genres', [
        'name' => 'Fantasía',
    ]);
});

test('genre name is required', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->postJson('/api/genres', [
        'name' => '',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('name');
});

test('genre name must be unique', function () {
    Sanctum::actingAs(User::factory()->create());

    Genre::create([
        'name' => 'Novela',
    ]);

    $this->postJson('/api/genres', [
        'name' => 'Novela',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('name');
});

test('authenticated users can search genres', function () {
    Sanctum::actingAs(User::factory()->create());

    Genre::create(['name' => 'Fantasía']);
    Genre::create(['name' => 'Terror']);

    $this->getJson('/api/genres?search=Fantasía')
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment([
            'name' => 'Fantasía',
        ]);
});

test('authenticated users can show a genre', function () {
    Sanctum::actingAs(User::factory()->create());

    $genre = Genre::create([
        'name' => 'Novela Histórica',
    ]);

    $this->getJson("/api/genres/{$genre->id}")
        ->assertOk()
        ->assertJsonFragment([
            'id' => $genre->id,
            'name' => 'Novela Histórica',
        ]);
});

test('authenticated users can update a genre', function () {
    Sanctum::actingAs(User::factory()->create());

    $genre = Genre::create([
        'name' => 'Nombre Original',
    ]);

    $this->putJson("/api/genres/{$genre->id}", [
        'name' => 'Nombre Actualizado',
    ])
        ->assertOk()
        ->assertJsonFragment([
            'name' => 'Nombre Actualizado',
        ]);

    $this->assertDatabaseHas('genres', [
        'id' => $genre->id,
        'name' => 'Nombre Actualizado',
    ]);
});

test('genre with associated books cannot be deleted', function () {
    Sanctum::actingAs(User::factory()->create());

    $author = Author::create([
        'name' => 'George Orwell',
    ]);

    $genre = Genre::create([
        'name' => 'Distopía',
    ]);

    Book::create([
        'book_code' => 'B02',
        'title' => '1984',
        'author_id' => $author->id,
        'genre_id' => $genre->id,
        'publication_year' => 1949,
        'description' => null,
    ]);

    $this->deleteJson("/api/genres/{$genre->id}")
        ->assertStatus(409);

    $this->assertDatabaseHas('genres', [
        'id' => $genre->id,
    ]);
});

test('genre without associated books can be deleted', function () {
    Sanctum::actingAs(User::factory()->create());

    $genre = Genre::create([
        'name' => 'Género Eliminable',
    ]);

    $this->deleteJson("/api/genres/{$genre->id}")
        ->assertNoContent();

    $this->assertDatabaseMissing('genres', [
        'id' => $genre->id,
    ]);
});