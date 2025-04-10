<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserMovie extends Model
{

    use HasFactory, SoftDeletes;

    protected $table = 'user_movies';

    protected $fillable = [
        'user_id',
        'movie_id',
        'rating',
        'watching_date',
      
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
