<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\LocationResource;
use App\Http\Resources\ProductUnitResource;
use App\Models\Category;
use App\Models\Location;
use App\Models\ProductUnit;
use Illuminate\Http\Request;

class BusinessNecessaryController extends Controller
{
    #search active locations for business
    public function locations(Request $request){
        $locations = Location::search($request->keyword)
            ->options([
                'limit' => 15,
                'offset' => 0,
                'filter' => ['(upazila_name != "empty")', '(status = "active")']
            ])->get();
        return success_response('locations fetched',LocationResource::collection($locations));
    }

    #search active categories for business
    public function categories(Request $request){
        $data = Category::search($request->keyword)
        ->options([
            'limit' => 15,
            'offset' => 0,
            'filter' => ['(status = "active")']
        ])->get();
        return success_response('categories fetched',CategoryResource::collection($data));
    }

    #search active categories for business
    public function product_units(Request $request){
        $data = ProductUnit::search($request->keyword)
        ->options([
            'limit' => 15,
            'offset' => 0,
            'filter' => ['(status = "active")']
        ])->get();

        return success_response('product units fetched',ProductUnitResource::collection($data));
    }
}
