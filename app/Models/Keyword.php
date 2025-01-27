<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
      
    ];

    public function movies()
    {
        return $this->hasMany(MovieKeyword::class, 'keyword_id'); // 1:N kapcsolat
    }
}
