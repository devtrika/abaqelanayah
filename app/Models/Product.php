<?php
namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Support\Facades\Auth;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends BaseModel implements HasMedia
{
    use HasFactory, SoftDeletes, HasTranslations, InteractsWithMedia , HasSlug, \App\Traits\CompressesImages;

    protected $fillable = [
        'name',
        'description',
        'category_id',
        'parent_category_id',
        'base_price',
        'is_refunded',
        'discount_percentage',
        'is_active',
        'brand_id',
        'quantity',
    ];

    public $translatable = ['name', 'description'];

    protected $casts = [
        'base_price'          => 'decimal:2',
        'discount_percentage' => 'int',
        'is_active'           => 'boolean',
        'quantity'            => 'int',
    ];


     public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }
    public function getAvailableQuantityAttribute()
    {
        return $this->quantity;
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function getInCartAttribute()
    {
        if (! Auth::check()) {
            return false;
        }
        $user = Auth::user();
        $cart = $user->cart;
        if (! $cart) {
            return false;
        }
        return $cart->items()
            ->where('product_id', $this->id)
            ->exists();
    }

    public function getIsAvailableAttribute()
    {
        return $this->available_quantity > 0;
    }
    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'parent_category_id');
    }
    public function getIsFavouriteAttribute()
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }
        return $user->favourites()->where('product_id', $this->id)->exists();
    }


    public function options()
{
    return $this->hasMany(ProductOption::class);
}

    public function getFinalPriceAttribute()
    {
        if ($this->discount_percentage) {
            return $this->base_price - ($this->base_price * $this->discount_percentage / 100);
        }

        return $this->base_price;
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getImageUrlAttribute()
    {
        return $this->getFirstMediaUrl('product-images') ?: asset('storage/images/default.png');
    }

    public function registerMediaConversions(\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        // Small thumb - generated immediately for instant display
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(200)
            ->format('webp')
            ->nonQueued()
            ->performOnCollections('product-images');
    }

}
