<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\HomeTopCategory;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
    public function top_categories()
    {
        // Get all active top category rows
        $tops = HomeTopCategory::where('status', 'active')->orderBy('serial', 'asc')->get();

        return $tops->map(function ($top) {
            $parentId    = $top->category_id;
            $recommended = $top->recommended;

            // fetch recommended (preserve order)
            $recommendedCats = collect();
            if ($recommended) {
                $order = array_flip($recommended);
                $recommendedCats = Category::whereIn('id', $recommended)
                    ->where('status', 'active')
                    ->when($parentId, fn($q) => $q->where('parent_id', $parentId))
                    ->get()
                    ->sortBy(fn($c) => $order[$c->id] ?? PHP_INT_MAX)
                    ->values();
            }

            // limit check
            if ($recommendedCats->count() >= 6) {
                return [
                    'category' => [
                        'name' => $top->category->name,
                        'slug' => $top->category->slug
                    ],
                    'categories'   => CategoryResource::collection($recommendedCats->take(6)->values()),
                ];
            }

            // fetch remaining
            $need = 6 - $recommendedCats->count();

            $extras = Category::where('status', 'active')
                ->when($parentId, fn($q) => $q->where('parent_id', $parentId))
                ->when($recommended, fn($q) => $q->whereNotIn('id', $recommended))
                ->latest()
                ->take($need)
                ->get();

            return [
                'category' => [
                    'name' => $top->category->name,
                    'slug' => $top->category->slug
                ],
                'categories'   => CategoryResource::collection($recommendedCats->concat($extras)->values()),
            ];
        });
    }

    public function suggested_categories(Request $request)
    {
        $keywords = $request->keywords ?? []; // expecting an array of keywords
        $allCategories = collect(); // a collection to hold all results

        foreach ($keywords as $keyword) {
            // Perform a search for each individual keyword
            $categories = Category::search($keyword)
                ->options([
                    'limit' => 15,
                    'offset' => 0,
                    'filter' => ['(status = "active")']
                ])
                ->get();

            $allCategories = $allCategories->merge($categories);
        }
        // Remove duplicates based on the category's ID
        $categories = $allCategories->unique('parent_id');

        $labels = [
            'Similar to your search',
            'Recommended for you',
            'You may also like',
            'Popular with others',
            'Suggested categories',
        ];
        $response = [];
        // $addedParents = [];

        foreach ($categories as $index => $category) {
            // Step 2: Get parent category
            $parent = $category->parent;

            if (!$parent) {
                continue; // skip if no parent OR already added
            }

            // Step 3: Get 6 child categories of parent
            if(!empty($category->children)){
                $childCategories = $parent->children()->limit(6)->get();
            }else{
                $childCategories = $parent->children()->limit(6)->get();
            }
            

            // Step 4: Format response
            $response[] = [
                'category' => [
                    'name' => $labels[$index] ?? 'Suggested',
                    'slug' => $parent->slug,
                ],
                'categories' => CategoryResource::collection($childCategories),
            ];

            // Mark parent as added
            // $addedParents[] = $parent->id;

            // Stop when we have 5 unique parents
            if (count($response) >= 5) {
                break;
            }
        }

        return success_response('Suggested categories fetched successfully', $response);
    }
}
