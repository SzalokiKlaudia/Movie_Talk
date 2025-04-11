<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'user_name' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:50',
            'birth_year' => 'nullable|digits:4|integer|min:1950|max:' . date('Y'),
        ];
    }

    public function messages(): array
    {
        return [
            'birth_year.digits' => 'Birth year must be 4 character and numbers only!',
            'birth_year.integer' => 'Birth year must contain numbers.',
            'email.email' => 'The email must be an email.',
            'user_name.string' => 'The username must be characters.',
            'user_name.max' => 'The length of username must be less than 20 characters'
        ];
    }
}
