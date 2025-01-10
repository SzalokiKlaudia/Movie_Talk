<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    /** @use HasFactory<\Database\Factories\MovieFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'release_date',
        'duration_minutes',
        'image_url',
        'trailer_url',
        'cast_url',
    ];

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'movie_genres'); // Egy film több műfajhoz is tartozhat
    }

    public function keywords()
    {
        return $this->belongsToMany(Keyword::class, 'movie_keywords'); // Egy filmhez több kulcsszó is tartozhat
    }

    public function userMovies()
    {
        return $this->hasMany(UserMovie::class, 'movie_id', 'id');
    }
}
