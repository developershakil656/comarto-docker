<?php

namespace App\Http\Controllers;

use App\Models\BusinessType;
use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessTypeRequest;
use App\Http\Resources\BusinessTypeResource;
use Illuminate\Database\QueryException as Expectation;

class BusinessTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $types = BusinessType::get();
        return success_response('all business types fetched.',BusinessTypeResource::collection($types));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BusinessTypeRequest $request)
    {
        try {
            $request = $request->all();
            
            if($type = BusinessType::create($request))
                return success_response('business type successfully created',new BusinessTypeResource($type));

        } catch (Expectation $e) {
            return error_response('something went wrong!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $type = BusinessType::find($id);
        if($type)
            return success_response('', new BusinessTypeResource($type));
        else
            return error_response('No Data Found!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BusinessTypeRequest $request, string $id)
    {
        try {
            $request = $request->only('type','status');
            $type = BusinessType::find($id);
            
            if($type && $type->update($request))
                return success_response('business type successfully updated',new BusinessTypeResource($type));
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
        $type = BusinessType::find($id);
        if($type && $type->delete())
            return success_response('business type successfuly deleted');
        else
            return error_response('No Data Found!');
    }
}
