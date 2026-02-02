<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'details',
        'price',
        'moq',
        'video_url',
        'specification',
        'status',
        'unit_quantity',
        'product_unit_id',
        'business_id',
        'stock',
        'category_slugs',
        'random_sort_key'
    ];

    protected $casts = [
        'specification' => 'array',
    ];

    #search related all functions
    public function shouldBeSearchable(): bool
    {
        return true;
    }

    public function toSearchableArray(): array
    {
        // Eager load relationships if not present to prevent indexing empty data
        $this->loadMissing(['business.location', 'business.details', 'categories',]);

       return [
            'name' => $this->name,
            'slug' => $this->slug,
            // 'details' => $this->details,
            'business_type' => $this->business?->business_type,
            'upazila_name' => $this->business?->location?->upazila_name,
            'district_name' => $this->business?->location?->district_name,
            'status' => $this->status,
            'stock' => $this->stock,
            'business' => $this->business ? [
                "business_name" => $this->business->business_name,
                "number" => $this->business->number,
                "alternate_number" => $this->business->alternate_number,
                "payment_type" => $this->business->details?->payment_type,
                // "website" => $this->business->details?->website,
            ] : null,
            'related_categories' => $this->relatedCategories(),
            'random_sort_key' => (int) $this->random_sort_key,
        ];
    }

    protected function specification(): Attribute
    {
        return Attribute::make(
            get: fn($value) => json_decode($value, true),
            set: fn($value) => json_encode($value),
        );
    }
    protected function categoryIds(): Attribute
    {
        return Attribute::make(
            get: fn($value) => json_decode($value, true),
            set: fn($value) => json_encode($value),
        );
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id', 'id');
    }

    public function product_unit()
    {
        return $this->belongsTo(ProductUnit::class, 'product_unit_id', 'id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    public function gallery()
    {
        return $this->hasMany(ProductGallery::class, 'product_id')->orderBy('serial', 'asc');
    }

    public function featureImage()
    {
        return $this->hasOne(ProductGallery::class, 'product_id')
            ->orderBy('serial');
    }

    // Get all parent category IDs including itself
    // public function relatedCategories()
    // {
    //     $all_categories = [];
    //     foreach ($this->categories as $category) {
    //         $all_categories = array_merge($all_categories, $category->getAllParentCategory());
    //     }
    //     return array_values(collect($all_categories)->unique('slug')->values()->toArray());
    // }

    public function relatedCategories()
    {
        if (!$this->relationLoaded('categories')) {
            return [];
        }

        return $this->categories
            ->flatMap(fn($category) => $category->ancestor_slugs ?? [])
            ->unique()
            ->values()
            ->toArray();
    }
}
