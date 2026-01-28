<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Category extends Model
{
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'parent_id',
        'status',
        'title',
        'description',
        'ancestor_slugs'
    ];

    protected $casts = [
        'ancestor_slugs' => 'array',
    ];

    /**
     * Determine if the model should be searchable.
     * Only "Leaf" categories (those with no children) will be indexed.
     */
    public function shouldBeSearchable(): bool
    {
        return !$this->children()->exists();
    }

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return [
            'id'             => (int) $this->id,
            'name'           => $this->name,
            'slug'           => $this->slug,
            'ancestor_slugs' => $this->ancestor_slugs,
            'status'         => $this->status,
        ];
    }

    /**
     * Standard Booted method to handle search index syncing
     */
    protected static function booted()
    {
        static::saved(fn($category) => static::syncSearch($category));
        static::deleted(fn($category) => static::syncSearch($category));
    }

    protected static function syncSearch(Category $category): void
    {
        foreach ([$category, $category->parent] as $node) {
            if (!$node) continue;

            $node->refresh();

            $node->shouldBeSearchable()
                ? $node->searchable()
                : $node->unsearchable();
        }
    }


    /**
     * Relationships
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Recursive Relationships
     */
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    public function allParent()
    {
        return $this->parent()->with('allParent');
    }

    /**
     * Fetch all products in this category and all its child categories
     */
    public function products()
    {
        // This assumes you have a 'categories' relationship on the Product model
        $categoryIds = $this->allChildren()->pluck('id')->push($this->id);

        return Product::whereHas('categories', function ($query) use ($categoryIds) {
            $query->whereIn('categories.id', $categoryIds);
        })->get();
    }

    /**
     * Update ancestor slugs (e.g., ["food", "meat", "beef"])
     */
    public function updateAncestorSlugs(): void
    {
        $slugs = collect([$this->slug]);

        $parent = $this->parent;
        while ($parent) {
            $slugs->push($parent->slug);
            $parent = $parent->parent;
        }

        $this->updateQuietly([
            'ancestor_slugs' => $slugs->reverse()->values()->toArray(),
        ]);
    }
}
