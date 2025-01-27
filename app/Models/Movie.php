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

    /*public function genres()
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
    }*/

    public function genres()
    {
        return $this->hasMany(MovieGenre::class, 'movie_id');//1:N
    }

    public function keywords()
    {
        return $this->hasMany(MovieKeyword::class, 'movie_id'); // 1:N kapcsolat
    }


    // 1 film -> több felhasználó által tárolt film
    public function userMovies()
    {
        return $this->hasMany(UserMovie::class, 'movie_id'); // 1:N kapcsolat
    }
}
