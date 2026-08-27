<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Database\Eloquent\Collection;

class AuthorController extends Controller
{
    public function index(): Collection
    {
        return Author::orderBy('name')->get();
    }
}