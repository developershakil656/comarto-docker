<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationRequest;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use Illuminate\Database\QueryException as Expectation;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Location::get();
        return success_response('all locations fetched.',LocationResource::collection($data));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LocationRequest $request)
    {
        try {
            if($data = Location::create($request->all()))
                return success_response('locaion successfully created',new LocationResource($data));

        } catch (Expectation $e) {
            return error_response('something went wrong!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Location::find($id);
        if($data)
            return success_response('', new LocationResource($data));
        else
            return error_response('No Data Found!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LocationRequest $request, string $id)
    {
        try {
            $request = $request->only('upazila_name','upazila_name_bn','district_name','district_name_bn','status');
            $data = Location::find($id);
            
            if($data && $data->update($request))
                return success_response('location successfully updated',new LocationResource($data));
            else
                return error_response('No Data Found!');

        } catch (Expectation $e) {
            return error_response('something went wrong!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Location::find($id);
        if($data && $data->delete())
            return success_response('locaion successfuly deleted');
        else
            return error_response('No Data Found!');
    }
}
