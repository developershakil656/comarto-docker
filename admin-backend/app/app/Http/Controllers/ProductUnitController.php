<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductUnitRequest;
use App\Http\Resources\ProductUnitResource;
use App\Models\ProductUnit;
use Illuminate\Database\QueryException as Expectation;

class ProductUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = ProductUnit::get();
        return success_response('all product units fetched.',ProductUnitResource::collection($units));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductUnitRequest $request)
    {
        try {
            $request = $request->all();
            
            if($unit = ProductUnit::create($request))
                return success_response('product unit successfully created',new ProductUnitResource($unit));

        } catch (Expectation $e) {
            return error_response('something went wrong!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $unit = ProductUnit::find($id);
        if($unit)
            return success_response('', new ProductUnitResource($unit));
        else
            return error_response('No Data Found!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductUnitRequest $request, string $id)
    {
        try {
            $request = $request->only('short_form','plural_form','full_form','status');
            $unit = ProductUnit::find($id);
            
            if($unit && $unit->update($request))
                return success_response('product unit successfully updated',new ProductUnitResource($unit));
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
        $unit = ProductUnit::find($id);
        if($unit && $unit->delete())
            return success_response('product unit successfuly deleted');
        else
            return error_response('No Data Found!');
    }
}
