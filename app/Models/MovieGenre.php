<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovieGenre extends Model
{
    use HasFactory;

    protected $table = 'movie_genres';

    protected $fillable = [
        'movie_id',
        'genre_id',
      
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class, 'movie_id');//N:1-hez
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class, 'genre_id'); // N:1 kapcsolat
    }
}
