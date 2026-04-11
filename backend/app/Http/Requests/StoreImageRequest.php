<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'spot_id' => ['required', 'integer', 'exists:spots,id'],
            'image' => ['required', 'file', 'image', 'max:3072', 'mimes:jpg,jpeg,png'],
        ];
    }
}
