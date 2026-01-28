<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;

class BusinessReviewReplyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Only true if user exists AND has a business AND IDs match
        $owner = $this->user?->id === $this->business?->user_id;
        
        return [
            "id" => Hashids::encode($this->id),
            "message" => $this->message,
            "status" => $this->status,
            "user" => [
                'id' => Hashids::encode($this->user->id),
                'name' => $owner ? $this->business->business_name : $this->user->name,
                'profile' => $owner ?
                    ($this->business->business_profile ? image_url($this->business->business_profile) : '') : ($this->user->profile ? image_url($this->user->profile) : '')
            ],
            "owner"=>$owner,
            "date" => Carbon::parse($this->created_at)->format('d F, Y'),
            "images" => ReviewGalleryResource::collection($this->gallery),
        ];
    }
}
