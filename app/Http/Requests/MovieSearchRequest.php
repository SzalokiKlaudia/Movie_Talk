<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MovieSearchRequest extends FormRequest
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
            'title' => 'nullable|string|max:255',
            'keyword' => 'nullable|string|max:255',
            'genre' => 'nullable|string|max:255',
            'releaseFrom' => 'nullable|date',
            'releaseTo' => 'nullable|date|after_or_equal:releaseFrom',
        ];
    }

    public function messages(): array
    {
        return [
            'title.string' => 'The title must be a string.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'keyword.string' => 'The keyword must be a string.',
            'keyword.max' => 'The keyword may not be greater than 255 characters.',
            'genre.string' => 'The genre must be a string.',
            'genre.max' => 'The genre may not be greater than 255 characters.',
            'releaseFrom.date' => 'The release from date must be a valid date.',
            'releaseTo.date' => 'The release to date must be a valid date.',
            'releaseTo.after_or_equal' => 'The release to date must be after or equal to the release from date.',
        ];
    }
}
