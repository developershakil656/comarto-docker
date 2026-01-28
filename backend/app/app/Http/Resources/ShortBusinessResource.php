<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class ShortBusinessResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => Hashids::encode($this->id),
            "slug" => $this->slug,
            "business_name" => $this->business_name,
            "business_profile" => $this->business_profile ? image_url($this->business_profile) : '',
            "rating" => $this->averageRating,
            "business_type" => $this->business_type,
            "number" => $this->number,
            "alternate_number" => $this->alternate_number,
            "in_business" => $this->details->established
                ? now()->year - (int) $this->details->established
                : null,
            "location" => trim(($this->location->upazila_name ? $this->location->upazila_name . ', ' : '') . $this->location->district_name),
            "account_verification" => $this->user?->account_verification?->status == 'confirmed',
        ];
    }
}
