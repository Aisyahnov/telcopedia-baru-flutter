<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $appends = ['image_url'];

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=f8f9fa&color=9F1521&size=200';
        }

        if (\Illuminate\Support\Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }

        if (\Illuminate\Support\Str::startsWith($this->image, 'products/')) {
            return url('api/storage/' . $this->image);
        }

        return url('api/storage/' . $this->image);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function returns()
    {
        return $this->hasMany(ProductReturn::class);
    }
}
