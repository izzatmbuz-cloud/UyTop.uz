<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SaveListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'deal_type' => ['required', Rule::in(['rent', 'sale'])],
            'rental_unit' => ['nullable', 'required_if:deal_type,rent', Rule::in(['whole', 'room', 'bed'])],
            'property_type' => ['required', Rule::in(['apartment', 'house', 'dormitory'])],
            'students_allowed' => ['nullable', Rule::in(['yes', 'no', 'unknown'])],
            'district_id' => ['required', 'integer', Rule::exists('districts', 'id')->where('active', true)],
            'title' => ['required', 'string', 'min:8', 'max:255'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'currency' => ['required', Rule::in(['UZS', 'USD'])],
            'price' => ['nullable', 'numeric', 'min:0'],
            'price_basis' => ['required', Rule::in(['monthly_unit', 'total', 'from_total', 'per_m2', 'on_request'])],
            'utilities_mode' => ['nullable', Rule::in(['included', 'fixed', 'unknown'])],
            'utilities_amount' => ['nullable', 'required_if:utilities_mode,fixed', 'numeric', 'min:0'],
            'utilities_payment_timing' => ['nullable', Rule::in(['move_in', 'later', 'unknown'])],
            'deposit_mode' => ['nullable', Rule::in(['none', 'fixed', 'unknown'])],
            'deposit_amount' => ['nullable', 'required_if:deposit_mode,fixed', 'numeric', 'min:0'],
            'commission_mode' => ['nullable', Rule::in(['none', 'fixed', 'unknown'])],
            'commission_amount' => ['nullable', 'required_if:commission_mode,fixed', 'numeric', 'min:0'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:100'],
            'free_places' => ['nullable', 'integer', 'min:0', 'lte:capacity'],
            'available_from' => ['nullable', 'date'],
            'min_months' => ['nullable', 'integer', 'min:1', 'max:120'],
            'area_m2' => ['nullable', 'numeric', 'min:1', 'max:100000'],
            'rooms' => ['nullable', 'integer', 'min:1', 'max:100'],
            'floor' => ['nullable', 'integer', 'min:0', 'max:200'],
            'location_text' => ['nullable', 'string', 'max:1000'],
            'amenity_ids' => ['array'],
            'amenity_ids.*' => ['integer', 'distinct', 'exists:amenities,id'],
            'submit_for_moderation' => ['boolean'],
            'images' => ['array', 'max:8'],
            'images.*' => ['file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            $listing = $this->route('listing');
            $existingCount = $listing?->media()->count() ?? 0;
            $newCount = count($this->file('images', []));

            if ($existingCount + $newCount > 8) {
                $validator->errors()->add('images', 'Ko‘pi bilan 8 ta rasm yuklash mumkin.');
            }

            if ($this->boolean('submit_for_moderation') && $existingCount + $newCount < 1) {
                $validator->errors()->add('images', 'Moderatsiyaga yuborish uchun kamida bitta rasm kerak.');
            }
        }];
    }
}
