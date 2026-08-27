<?php

namespace App\Services;

use App\Models\Book;

class BookCodeGenerator
{
    public function generate(): string
    {
        do {
            $letter = chr(random_int(65, 90));
            $numbers = str_pad(
                (string) random_int(0, 99),
                2,
                '0',
                STR_PAD_LEFT
            );

            $code = $letter . $numbers;
        } while (Book::where('book_code', $code)->exists());

        return $code;
    }
}