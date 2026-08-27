<?php

namespace App\Support;

class BookValidationRules
{
    public static function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'author_id' => [
                'required',
                'integer',
                'exists:authors,id',
            ],
            'genre_id' => [
                'required',
                'integer',
                'exists:genres,id',
            ],
            'publication_year' => [
                'required',
                'integer',
                'min:1',
                'max:' . now()->year,
            ],
            'description' => [
                'nullable',
                'string',
            ],
        ];
    }
}