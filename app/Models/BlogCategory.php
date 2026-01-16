<?php

namespace App\Models;

use Spatie\Translatable\HasTranslations;

class BlogCategory extends BaseModel
{

    use HasTranslations;
    protected $fillable = ['name' , 'is_active'];
    public $translatable = ['name'];

    /**
     * Get the blogs for the category
     */
    public function blogs()
    {
        return $this->hasMany(Blog::class, 'category_id');
    }

    public function getBlogsCountAttribute()
    {
        return $this->blogs()->count();
    }
}
