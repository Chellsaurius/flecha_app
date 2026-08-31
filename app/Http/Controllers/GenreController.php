<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGenreRequest;
use App\Http\Requests\UpdateGenreRequest;
use App\Models\Genre;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Collection;

class GenreController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $genres = Genre::query()
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

        return response()->json($genres);
    }

    public function store(StoreGenreRequest $request): JsonResponse
    {
        $genre = Genre::create($request->validated());

        return response()->json($genre, 201);
    }

    public function show(Genre $genre): JsonResponse
    {
        return response()->json($genre);
    }

    public function update(
        UpdateGenreRequest $request,
        Genre $genre
    ): JsonResponse {
        $genre->update($request->validated());

        return response()->json($genre->fresh());
    }

    public function destroy(Genre $genre): Response|JsonResponse
    {
        if ($genre->books()->exists()) {
            return response()->json([
                'message' => 'The genre cannot be deleted because it has associated books.',
            ], 409);
        }

        $genre->delete();

        return response()->noContent();
    }
}