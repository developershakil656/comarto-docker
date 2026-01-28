<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class BusinessDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "established" => $this->established,
            "in_business" => $this->established
                ? now()->year - (int) $this->established
                : null,
            "number_of_employee" => $this->number_of_employee,
            "about" => $this->about,
            "direction" => $this->direction,
            "payment_type" => $this->payment_type,
            "tin" => $this->tin,
            "website" => $this->website,
            "facebook" => $this->facebook,
            "video_url" => $this->video_url,
            "timing" => $this->timing,
            // "business_id " => $this->business_id ,
        ];
    }
}
