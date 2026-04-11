<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSpotRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:60'],
            'description' => ['required', 'string'],
            'latitude' => ['required', 'string', 'max:25'],
            'longitude' => ['required', 'string', 'max:25'],
            'created_by' => ['required', 'exists:users,id'],
            'sports_and_tags' => ['array'],
            'sports_and_tags.*' => ['exists:sports_and_tags,id'],
        ];
    }
}
