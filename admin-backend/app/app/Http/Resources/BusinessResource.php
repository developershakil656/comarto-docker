<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class BusinessResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "slug" => $this->slug,
            "name" => $this->name,
            "business_name" => $this->business_name,
            "business_profile" => $this->business_profile ? image_url($this->business_profile) : '',
            "rating" => $this->averageRating,
            "business_type" => $this->business_type,
            "number" => $this->number,
            "alternate_number" => $this->alternate_number,
            "address" => $this->address,
            "post_code" => $this->post_code,
            "location" => trim(($this->location->upazila_name ? $this->location->upazila_name . ', ' : '') . $this->location->district_name),
            "location_id" => $this->location->id,
            "account_verification" => $this->user?->account_verification?->status == 'confirmed',
            "status" => $this->status,
            "created_at" => $this->created_at?->toDateTimeString(),
        ];
    }
}
