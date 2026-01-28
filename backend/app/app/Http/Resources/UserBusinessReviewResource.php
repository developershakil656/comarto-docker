<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Vinkla\Hashids\Facades\Hashids;

class UserBusinessReviewResource extends JsonResource
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
            "rate" => $this->rating,
            "message" => $this->message,
            "status" => $this->status,
            "business" => [
                'id' => Hashids::encode($this->business->id),
                'business_name' => $this->business->business_name,
                'slug' => $this->business->slug,
                'business_profile' => $this->business->business_profile?image_url($this->business->business_profile):''
            ],
            "user" => [
                'id' => Hashids::encode($this->user->id),
                'name' => $this->user->name,
                'profile' => $this->user->profile?image_url($this->user->profile):''
            ],
            "date" => Carbon::parse($this->created_at)->format('d F, Y'),
            "images" => ReviewGalleryResource::collection($this->gallery),
        ];
    }
    
}
