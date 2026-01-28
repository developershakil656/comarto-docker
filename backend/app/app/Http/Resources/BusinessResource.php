<?php

namespace App\Http\Resources;

use App\Services\LeadCreditService;
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
        $creditService = new LeadCreditService;
        return [
            "id" => Hashids::encode($this->id),
            "name" => $this->name,
            "slug" => $this->slug,
            "business_name" => $this->business_name,
            "business_profile" => $this->business_profile?image_url($this->business_profile) : '',
            "rating" => $this->averageRating,
            "number" => $this->number,
            "alternate_number" => $this->alternate_number,
            "email" => $this->email,
            "address" => $this->address,
            "location" => new LocationResource($this->location),
            "post_code" => $this->post_code,
            "business_type" => $this->business_type,
            "status" => $this->status,
            "gallery" => BusinessGalleryResource::collection($this->gallery),
            "lead_credits" => $creditService->balances($this->id),
            "categories" => $this->categories,
            "account_verification" => $this->user?->account_verification?->status == 'confirmed',
        ];
    }
}
