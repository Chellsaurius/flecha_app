<?php

namespace App\Http\Requests;

use App\Support\AuthorValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAuthorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return AuthorValidationRules::rules($this->author->id);
    }
}