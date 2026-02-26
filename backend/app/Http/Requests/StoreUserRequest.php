<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'string',
                'min:3',
                'max:24',
                'unique:users,username',
                'regex:/^[a-z][a-z0-9_]{2,23}$/',
                /* Username szabályok:
                    - 3 és 24 karakter közötti hosszúság
                    - Az első karakter mindig egy kisbetű (a-z)
                    - Többi részen megengedett karakterek: kisbetű (a-z), szám (0-9) és aláhúzás (_)
                    - Nem lehet csak szám vagy csak aláhúzás (az első betű miatt ez eleve kizárt)
                    - Csak betű lehet (pl. "adam" oké) 
                */
            ],
            'email' => ['required', 'email:rfc,dns', 'unique:users,email', 'max:255'],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:255',
                'confirmed',

                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[!@#$%&_]/',

                'regex:/^[A-Za-z0-9!@#$%&_]+$/',
            ],
        ];
    }
}
