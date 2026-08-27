<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::livewire('/books', 'books.index')->name('books.index');
    Route::livewire('/authors', 'authors.index')->name('authors.index');
    Route::livewire('/genres', 'genres.index')->name('genres.index');
    
});




require __DIR__.'/settings.php';
