<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSpotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:60'],
            'description' => ['required', 'string'],
            'latitude' => ['required', 'regex:/^[0-9.]+$/', 'max:25'],
            'longitude' => ['required', 'regex:/^[0-9.]+$/', 'max:25'],
            'sports_and_tags' => ['array'],
            'sports_and_tags.*' => ['exists:sports_and_tags,id'],
        ];
    }
}
