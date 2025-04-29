<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ya estamos en auth middleware
    }

    public function rules(): array
    {
        return [
            // si tu tabla usa 'nombre' y 'apellido' en lugar de 'name':
            'nombre'   => ['required', 'string', 'max:50'],
            'apellido' => ['required', 'string', 'max:50'],
            'email'    => [
                'required',
                'email',
                'max:100',
                Rule::unique('usuarios', 'email')->ignore($this->user()->id),
            ],
        ];
    }
}
