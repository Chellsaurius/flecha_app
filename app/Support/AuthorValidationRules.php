<?php

namespace App\Support;

use Illuminate\Validation\Rule;

class AuthorValidationRules
{
    public static function rules(?int $authorId = null): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('authors', 'name')->ignore($authorId),
            ],
        ];
    }
}