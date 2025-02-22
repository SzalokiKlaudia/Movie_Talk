<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserMovieRequest extends FormRequest
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

        $currentDate = now()->toDateString(); // most dtum

        return [
                'rating' => 'required|integer|between:1,5', 
                'watching_date' => 'required|date|after_or_equal:1950-01-01|before_or_equal:' . $currentDate, //mostaninál ksiebb vagy egyenlő
                'id' => 'required|exists:user_movies,id', // létezik-e az ab-ban

        ];
    }

    public function messages()
    {
        return [
            'rating.required' => 'The rate to the movie is required!!',
            'rating.integer' => 'Rate must be an integer!',
            'rating.between' => 'The movie rate must be between 1-5',
            'watching_date.required' => 'Do not forget to add the date to the movie!',
            'watching_date.date' => 'The watching date cannot be later than today or too early!',
            'id' => 'The movie record does not exist for this user.',

        ];
    }
}
