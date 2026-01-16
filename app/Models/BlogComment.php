<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogComment extends Model
{
    protected $fillable = [
        'user_id',
        'blog_id',
        'comment',
        'is_approved',
        'rate'
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'rate' => 'integer'
    ];

    /**
     * Get the user that owns the comment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the blog that owns the comment
     */
    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }
}
