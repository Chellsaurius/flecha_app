<?php

namespace App\Http\Requests;

use App\Support\GenreValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class StoreGenreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return GenreValidationRules::rules();
    }
}