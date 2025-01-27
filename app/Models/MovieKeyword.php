<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovieKeyword extends Model
{
    
    use HasFactory;

    protected $table = 'movie_keywords';

    protected $fillable = [
        'movie_id',
        'keyword_id',
      
    ];

       // 1 film -> több kulcsszó
       public function movie()
       {
           return $this->belongsTo(Movie::class, 'movie_id'); // N:1 kapcsolat
       }

       public function keyword()
       {
           return $this->belongsTo(Keyword::class, 'keyword_id'); // N:1 kapcsolat
       }

}
