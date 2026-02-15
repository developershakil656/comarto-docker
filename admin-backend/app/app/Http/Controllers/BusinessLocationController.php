<?php

namespace App\Http\Controllers;

use App\Http\Resources\LocationResource;
use App\Models\Location;
use Illuminate\Http\Request;

class BusinessLocationController extends Controller
{
    /**
     * Search active locations for business
     */
    public function search(Request $request)
    {
        $locations = Location::search($request->keyword)
            ->options([
                'limit' => 15,
                'offset' => 0,
                'filter' => ['(upazila_name != "empty")', '(status = "active")']
            ])->get();
            
        return success_response('locations fetched', LocationResource::collection($locations));
    }
}