<?php

namespace App\Http\Controllers;

use App\Http\Requests\FollowingRequest;
use App\Http\Resources\ShortBusinessResource;
use App\Models\Following;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;

class FollowingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->getActiveFollowingBusinesses();
        return success_response('follwing businesses fetched.', ShortBusinessResource::collection($data));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FollowingRequest $request)
    {
        // Example logic for storing a follwing
        Following::firstOrCreate([
            'user_id' => Auth::id(),
            'business_id' => $request->business_id
        ]);

        $data = $this->getActiveFollowingBusinesses();
        return success_response('business following.', ShortBusinessResource::collection($data));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $business_id)
    {
        $business_id = Hashids::decode($business_id)[0] ?? null;
        Following::where('user_id', Auth::id())
            ->where('business_id', $business_id)
            ->delete();

        // $data = $this->getActiveFollowingBusinesses();
        return success_response('business unfollwed');
    }


    // ✅ Private method to reuse
    private function getActiveFollowingBusinesses()
    {
        return Following::where('user_id', Auth::id()) // optional: filter by user
            ->whereHas('business', function ($query) {
                $query->where('status', 'active');
            })
            ->with(['business' => function ($query) {
                $query->where('status', 'active');
            }])
            ->get()
            ->pluck('business')
            ->filter()
            ->values(); // optional: reset keys
    }
}
