<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\HomeTopCategoryResource;
use App\Models\HomeTopCategory;
use Illuminate\Http\Request;

class HomeTopCategoryController extends Controller
{
    public function index()
    {
        $items = HomeTopCategory::with('category')
            ->orderBy('serial')
            ->get();

        return success_response(
            'Home top categories fetched successfully',
            HomeTopCategoryResource::collection($items)
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'recommended' => 'nullable|array',
            'serial' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        $item = HomeTopCategory::create($data);

        return success_response(
            'Home top category created successfully',
            new HomeTopCategoryResource($item->load('category'))
        );
    }

    public function show(HomeTopCategory $homeTopCategory)
    {
        return success_response(
            'Home top category details',
            new HomeTopCategoryResource($homeTopCategory->load('category'))
        );
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'recommended' => 'sometimes|array',
            'serial' => 'sometimes|integer|min:1',
            'status' => 'sometimes|in:active,inactive',
        ]);

        $homeTopCategory = HomeTopCategory::find($id);
        $homeTopCategory->update($data);

        return success_response(
            'Home top category updated successfully',
            new HomeTopCategoryResource($homeTopCategory->load('category'))
        );
    }

    public function destroy($id)
    {
        $homeTopCategory = HomeTopCategory::find($id);
        $homeTopCategory->delete();

        return success_response('Home top category deleted successfully');
    }
}
