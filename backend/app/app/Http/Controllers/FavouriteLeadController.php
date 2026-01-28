<?php

namespace App\Http\Controllers;

use App\Http\Resources\BusinessLeadResource;
use App\Http\Resources\LeadResource;
use App\Models\BusinessLeadFavourite;
use App\Models\Lead;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class FavouriteLeadController extends Controller
{
    // Toggle favourite (add/remove)
    public function toggle($leadId)
    {
        $leadId = $leadId ? Hashids::decode($leadId)[0] : [];
        $business = auth()->user()->business;
        $lead = Lead::findOrFail($leadId);

        // Prevent favouriting own inquiry
        if ($lead->user_id === auth()->id()) {
            return error_response("You cannot favourite your own inquiry!");
        }

        $favourite = BusinessLeadFavourite::where('business_id', $business->id)
            ->where('lead_id', $leadId)
            ->first();

        if ($favourite) {
            $favourite->delete();
            return success_response("Lead removed from favourites");
        } else {
            BusinessLeadFavourite::create([
                'business_id' => $business->id,
                'lead_id'     => $leadId,
            ]);
            return success_response("Lead added to favourites");
        }
    }

    // List all favourites
    public function index()
    {
        $business = auth()->user()->business;

        $favourites = BusinessLeadFavourite::where('business_id', $business->id)->get();

        return success_response("Favourite leads fetched successfully", BusinessLeadResource::collection($favourites));
    }
}
