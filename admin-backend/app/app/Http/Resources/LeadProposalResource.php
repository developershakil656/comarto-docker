<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class LeadProposalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $business = $this->business;
        return [
            "id" => $business->id,
            "slug" => slugify($business->business_name),
            "business_name" => $business->business_name,
            "business_profile" => $business->business_profile ? image_url($business->business_profile) : '',
            "rating" => $business->averageRating,
            "number" => $business->number,
            "alternate_number" => $business->alternate_number,
            "email" => $business->email,
            "in_business" => $business->details->established
                ? now()->year - (int) $business->details->established
                : null,
            "location" => $business->location,
            "account_verification" => !empty($business->user->account_verification),
        ];
    }
}
