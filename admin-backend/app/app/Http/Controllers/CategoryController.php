<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Database\QueryException as Expectation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::query();

        // Keyword search
        if ($request->search) {
            $query->where('name', 'LIKE', '%'.$request->search.'%');
        }

        // Limit if provided
        if ($request->limit) {
            $query->limit((int) $request->limit);
        }

        return success_response(
            'all categories fetched.',
            CategoryResource::collection($query->get())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        // try {
        // Prepare data except icon
        $data = $request->only('name', 'parent_id', 'status', 'title', 'description');
        $data['slug'] = slugify($data['name']);

        // Handle icon upload if exists
        if ($request->hasFile('icon')) {
            $data['icon'] = image_link_generator(
                $request->file('icon'),
                '/all-categories/',
                $data['slug']
            );
        }

        // Create category
        $category = Category::create($data);
        $category->updateAncestorSlugs();

        return success_response(
            'Category successfully created',
            new CategoryResource($category)
        );
        // } catch (\Exception $e) {
        //     return error_response('Something went wrong!');
        // }
    }

    /**
     * Display the specified resource.
     */
    // public function show(string $id)
    // {
    //     $category = Category::find($id);
    //     if ($category)
    //         return success_response('', new CategoryResource($category));
    //     else
    //         return error_response('No Data Found!');
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, string $id)
    {
        try {
            $data = $request->only('name', 'parent_id', 'status', 'title', 'description');
            $data['slug'] = slugify($data['name']);

            $category = Category::find($id);
            if (! $category) {
                return error_response('No Data Found!');
            }

            // Check if new icon is uploaded
            if ($request->hasFile('icon')) {

                // Delete old icon if not from external URL
                if (
                    $category->icon &&
                    ! Str::startsWith($category->icon, ['http://', 'https://'])
                ) {
                    delete_image($category->icon);
                }

                // Store new icon
                $data['icon'] = image_link_generator(
                    $request->file('icon'),
                    '/all-categories/',
                    $category->slug
                );
            }

            $category->update($data);
            $category->updateAncestorSlugs();

            return success_response('Category successfully updated', new CategoryResource($category));
        } catch (\Exception $e) {
            return error_response('Something went wrong!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $category = Category::find($id);
            if ($category && ($category->delete() || false)) {
                return success_response('category successfuly deleted');
            } else {
                return error_response('No Data Found!');
            }
        } catch (Expectation $e) {
            return error_response('first delete all child categories!');
        }
    }
}
