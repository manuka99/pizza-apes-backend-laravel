<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'url_name',
        'product_name',
        'status',
        'visibility',
        'published_on',
        'is_featured',
        'short_description',
        'description',
        'type',
        'is_trashed',
        'default_variation',
        'image',
        'label',
        'symbol'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    public function galleryImages()
    {
        return $this->hasMany(Gallery::class);
    }

    public function options()
    {
        return $this->hasMany(Options::class);
    }

    public function productVarients()
    {
        return $this->hasMany(ProductVarient::class);
    }

    public function suggestedProducts()
    {
        return $this->HasMany(SuggestedProducts::class, 'pid_parent');
    }
    public function suggestedByProducts()
    {
        return $this->BelongsToMany(SuggestedProducts::class, 'suggested_products', 'pid_parent', 'pid');
    }

    // algolia

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->only(
            'url_name',
            'product_name',
            'short_description',
            'description',
            'label',
            'symbol'
        );

        return $array;
    }

    /**
     * Get the name of the index associated with the model.
     *
     * @return string
     */
    public function searchableAs()
    {
        return 'pizza_apes';
    }
}
