<?php

namespace App\Http\Requests;

use App\Models\Spot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateSpotImageOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        $spot = $this->route('spot');
        $user = $this->user();

        if (! $spot instanceof Spot) {
            return true;
        }

        return $user && ($user->role === 'admin' || (int) $spot->created_by === (int) $user->id);
    }

    public function rules(): array
    {
        return [
            'image_order' => ['required', 'array', 'min:1', 'max:10'],
            'image_order.*' => ['required', 'integer', 'distinct', 'exists:images,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->any()) {
                return;
            }

            $spot = $this->route('spot');

            if (! $spot instanceof Spot) {
                return;
            }

            $currentImageIds = $spot->images()->pluck('id')->map(fn($id) => (int) $id)->all();
            $requestedImageIds = array_map('intval', $this->input('image_order', []));

            $sortedCurrentImageIds = $currentImageIds;
            $sortedRequestedImageIds = $requestedImageIds;

            sort($sortedCurrentImageIds);
            sort($sortedRequestedImageIds);

            if ($sortedCurrentImageIds !== $sortedRequestedImageIds) {
                $validator->errors()->add('image_order', 'A képsorrend csak a spot saját képeit tartalmazhatja.');
            }
        });
    }
}
