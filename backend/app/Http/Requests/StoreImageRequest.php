<?php

namespace App\Http\Requests;

use App\Models\Spot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $spotId = $this->input('spot_id');

        if (! is_numeric($spotId)) {
            return true;
        }

        $spot = Spot::find($spotId);

        if (! $spot) {
            return true;
        }

        $user = $this->user();

        return $user && ($user->role === 'admin' || (int) $spot->created_by === (int) $user->id);
    }

    public function rules(): array
    {
        return [
            'spot_id' => ['required', 'integer', 'exists:spots,id'],
            'image' => ['required', 'file', 'image', 'max:3072', 'mimes:jpg,jpeg,png'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->any()) {
                return;
            }

            $spot = Spot::withCount('images')->find($this->input('spot_id'));

            if ($spot && $spot->images_count >= 10) {
                $validator->errors()->add('image', 'Egy spothoz maximum 10 képet tölthetsz fel.');
            }
        });
    }
}
