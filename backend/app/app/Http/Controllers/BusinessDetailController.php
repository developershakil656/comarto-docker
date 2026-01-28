<?php

namespace App\Http\Controllers;

use App\Http\Requests\BusinessDetailRequest;
use App\Http\Resources\BusinessDetailResource;
use App\Models\BusinessDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class BusinessDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = BusinessDetail::where('business_id', Auth::user()->business->id)->first();
        if ($data)
            return success_response('business details fetched.', new BusinessDetailResource($data));
        else
            return error_response('no business details created!', [], 404);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(BusinessDetailRequest $request)
    {
        $data = BusinessDetail::where('business_id', Auth::user()->business->id)->first();

        if ($data && $data->update($request->except('business_id')))
            return success_response('details successfully updated', new BusinessDetailResource($data));
        else
            return error_response('No Data Found!');
    }
}
