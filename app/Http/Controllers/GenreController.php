<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Collection;

class GenreController extends Controller
{
    public function index(): Collection
    {
        return Genre::orderBy('name')->get();
    }
}