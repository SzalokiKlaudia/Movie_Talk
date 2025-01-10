<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumTopic extends Model
{
    use HasFactory;

    protected $table = 'forum_topics';

    protected $fillable = [
        'user_id',
        'title',
        'created_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function forumComments()
    {
        return $this->hasMany(ForumComment::class, 'forum_topic_id', 'id');
    }
}
