<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends Model
{
    protected $fillable = [
        'book_code',
        'title',
        'author_id',
        'genre_id',
        'publication_year',
        'description',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        $search = trim($search);

        return $query->where(function ($q) use ($search) {
            $q->where('title', 'ILIKE', "%{$search}%")
                ->orWhereHas('author', function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%");
                })
                ->orWhereHas('genre', function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%");
                });
        });
    }

    
}