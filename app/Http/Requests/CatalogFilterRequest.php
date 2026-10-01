<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CatalogFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'deal_type' => ['nullable', Rule::in(['rent', 'sale'])],
            'rental_unit' => ['nullable', Rule::in(['whole', 'room', 'bed'])],
            'students_allowed' => ['nullable', Rule::in(['yes'])],
            'district_id' => ['nullable', 'integer', 'exists:districts,id'],
            'currency' => ['nullable', Rule::in(['UZS', 'USD'])],
            'price_min' => ['nullable', 'numeric', 'min:0'],
            'price_max' => ['nullable', 'numeric', 'min:0', 'gte:price_min'],
            'free_places_min' => ['nullable', 'integer', 'min:1', 'max:30'],
            'available_from' => ['nullable', 'date'],
            'property_type' => ['nullable', Rule::in(['apartment', 'house', 'dormitory'])],
            'rooms_min' => ['nullable', 'integer', 'min:1', 'max:30'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['string', Rule::in(['wifi', 'furniture'])],
            'author_type' => ['nullable', Rule::in(['owner', 'agent', 'developer'])],
            'search' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', Rule::in(['confirmed_at', 'price_asc', 'price_desc', 'date'])],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['amenities' => array_values(array_filter((array) $this->input('amenities', [])))]);
    }
}
