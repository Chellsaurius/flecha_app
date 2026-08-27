<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Models\Book;
use App\Services\BookCodeGenerator;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateBookRequest;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class BookController extends Controller
{
    public function index(Request $request)
    {
        return Book::with(['author', 'genre'])
            ->when(
                $request->filled('search'),
                fn ($query) => $query->search($request->input('search'))
            )
        // $query = Book::with(['author', 'genre']);
        // Para implementar borrado lógico:
        // $query->where('is_active', true);
        
        ->orderBy('title')
        ->paginate(10);
    }

    public function store(
        StoreBookRequest $request,
        BookCodeGenerator $bookCodeGenerator
    ): JsonResponse {
        $book = Book::create([
            'book_code' => $bookCodeGenerator->generate(),
            'title' => $request->validated('title'),
            'author_id' => $request->validated('author_id'),
            'genre_id' => $request->validated('genre_id'),
            'publication_year' => $request->validated('publication_year'),
            'description' => $request->validated('description'),
        ]);

        return response()->json(
            $book->load(['author', 'genre']),
            201
        );
    }

    public function show(Book $book): Book
    {
        return $book->load(['author', 'genre']);
    }
    
    public function update(
        UpdateBookRequest $request,
        Book $book
    ): Book {
        $book->update([
            'title' => $request->validated('title'),
            'author_id' => $request->validated('author_id'),
            'genre_id' => $request->validated('genre_id'),
            'publication_year' => $request->validated('publication_year'),
            'description' => $request->validated('description'),
        ]);

        return $book->fresh(['author', 'genre']);
    }

    public function destroy(Book $book): Response
    {
        $book->delete();

        // Para implementar borrado lógico:
        // $book->update(['is_active' => false]);

        return response()->noContent();
    }

}