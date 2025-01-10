<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumComment extends Model
{
    use HasFactory;

    protected $table = 'forum_comments';

    protected $fillable = [
        'forum_topic_id',
        'user_id',
        'comment',
        'created_at'  
    ];

    public function forumTopic()
    {
        return $this->belongsTo(ForumTopic::class, 'forum_topic_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
