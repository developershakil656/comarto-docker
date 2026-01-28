<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;

class BusinessReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // $user = auth('api')->user();
        // // Only true if user exists AND has a business AND IDs match
        // $owner = false;
        // if ($user && $this->user) {
        //     $owner = $user->id === $this->user->id;
        // }
        return [
            "id" => Hashids::encode($this->id),
            "rate" => $this->rating,
            "message" => $this->message,
            "status" => $this->status,
            "user" => [
                'id' => Hashids::encode($this->user->id),
                'name' => $this->user->name,
                'profile' => $this->user->profile ? image_url($this->user->profile):''
            ],
            // "owner"=>$owner,
            "date" => Carbon::parse($this->created_at)->format('d F, Y'),
            "images" => ReviewGalleryResource::collection($this->gallery),
            "replies" => BusinessReviewReplyResource::collection($this->replies),
        ];
    }
}
