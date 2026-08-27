<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class Author extends Model
{
    protected $fillable = [
        'name',
    ];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => Str::title(
                trim(preg_replace('/\s+/', ' ', $value))
            )
        );
    }
}