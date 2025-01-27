<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMovie extends Model
{

    use HasFactory;

    protected $table = 'user_movies';

    protected $fillable = [
        'user_id',
        'movie_id',
        'rating',
        'insert_date'
      
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }



       // 1 film -> több saját film (felhasználói profil)
       public function movie()
       {
           return $this->belongsTo(Movie::class, 'movie_id'); // N:1 kapcsolat
       }
}
