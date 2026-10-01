<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParseListingTextRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['text' => ['required', 'string', 'min:20', 'max:5000']];
    }
}
