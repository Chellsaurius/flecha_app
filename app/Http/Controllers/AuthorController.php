<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Models\Author;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Collection;

class AuthorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $authors = Author::query()
            ->when(
                $request->filled('search'),
                fn ($query) => $query->where(
                    'name',
                    'ILIKE',
                    '%' . $request->input('search') . '%'
                )
            )
            ->orderBy('name')
            ->get();

        return response()->json($authors);
    }

    public function store(StoreAuthorRequest $request): JsonResponse
    {
        $author = Author::create($request->validated());

        return response()->json($author, 201);
    }

    public function show(Author $author): JsonResponse
    {
        return response()->json($author);
    }

    public function update(
        UpdateAuthorRequest $request,
        Author $author
    ): JsonResponse {
        $author->update($request->validated());

        return response()->json($author->fresh());
    }

    public function destroy(Author $author): Response|JsonResponse
    {
        if ($author->books()->exists()) {
            return response()->json([
                'message' => 'The author cannot be deleted because it has associated books.',
            ], 409);
        }

        $author->delete();

        return response()->noContent();
    }
}