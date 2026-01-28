<?php

namespace App\Http\Controllers;

use App\Http\Requests\FavouriteRequest;
use App\Http\Resources\SearchProductResource;
use App\Models\Favourite;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;

class FavouriteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->getActiveFavouriteProducts();

        return success_response('favourite products fetched.', SearchProductResource::collection($data));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FavouriteRequest $request)
    {
        // Example logic for storing a favourite
        Favourite::firstOrCreate([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id
        ]);

        $data = $this->getActiveFavouriteProducts();
        return success_response('Favourite added successfully.', SearchProductResource::collection($data));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $product_id)
    {
        $product_id = Hashids::decode($product_id)[0] ?? null;
        Favourite::where('user_id', Auth::id())
            ->where('product_id', $product_id)
            ->delete();

        // $data = $this->getActiveFavouriteProducts();
        return success_response('product removed from favourite');
    }


    // ✅ Private method to reuse
    private function getActiveFavouriteProducts()
    {
        return Favourite::where('user_id', Auth::id()) // optional: filter by user
            ->whereHas('product', function ($query) {
                $query->where('status', 'active');
            })
            ->with(['product' => function ($query) {
                $query->where('status', 'active');
            }])
            ->get()
            ->pluck('product')
            ->filter()
            ->values(); // optional: reset keys
    }
}
