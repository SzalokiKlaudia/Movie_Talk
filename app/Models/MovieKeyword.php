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

}
