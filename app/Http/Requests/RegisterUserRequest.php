<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
            'user_name' => 'required|string|max:30|unique:users,user_name',
            'email' => 'required|email|max:50|unique:users,email',
            'name' => 'required|string|max:30',
            'gender' => 'required|string|max:10',
            'birth_year' => 'required|integer|max:' .date('Y'),
            'password' => 'required|string|confirmed|min:8',
            
        ];
    }

    public function messages(): array
    {
        return [
            'user_name.string' => 'The characters can not be greater than 30 character',
            'email' => 'Email must be a valid type',
            'name' => 'Name can not be greater than 30 character',
            'gender' => 'Did you forget to choose a gender?',
            'birth_year' => 'The year can not be greater than 4 number, and smaller than'.date('Y'),
            'password' => 'Password is required, can not be greater than 8 character! ',
            
        ];
    }
}
