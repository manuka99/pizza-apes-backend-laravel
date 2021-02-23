<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image', 'description', 'slug', 'root_id'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_categories');
    }

    public function setSlugAttribute($slug)
    {
        $this->attributes['slug'] = str_replace(' ', '_', strtolower($slug));
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'root_id');
    }

    public static function allCategoryChildren($id)
    {
        $categories = Category::find($id)->children;
        foreach ($categories as $category) {
            $category->children = self::allCategoryChildren($category->id);
        }
        return $categories;
    }
}
