<?php

namespace App\Support;

use Illuminate\Validation\Rule;

class GenreValidationRules
{
    public static function rules(?int $genreId = null): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('genres', 'name')->ignore($genreId),
            ],
        ];
    }
}