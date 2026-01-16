<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogReaction extends Model
{
    protected $fillable = [
        'user_id',
        'blog_id',
        'reaction'
    ];

    /**
     * Get the user that owns the reaction
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the blog that owns the reaction
     */
    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }
}
