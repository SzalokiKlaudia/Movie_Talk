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
            'title' => 'nullable|string|max:20',
            'keyword' => 'nullable|string|max:20',
            'genre' => 'nullable|string|max:20',
            'releaseFrom' => 'nullable|date',
            'releaseTo' => 'nullable|date|after_or_equal:releaseFrom',
        ];
    }

    public function messages(): array
    {
        return [
            'title.string' => 'The title must be a string.',
            'title.max' => 'The title may not be greater than 20 characters.',
            'keyword.max' => 'The keyword may not be greater than 20 characters.',
            'releaseFrom.date' => 'The release from date must be a valid date.',
            'releaseTo.date' => 'The release to date must be a valid date.',
            'releaseTo.after_or_equal' => 'The release to date must be after or equal to the release from date.',
        ];
    }
}
