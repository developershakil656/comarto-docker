<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class UserResource extends JsonResource
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
            "name" => $this->name,
            "profile" => $this->profile && (Str::startsWith($this->profile, 'http://') || Str::startsWith($this->profile, 'https://')) ?
                $this->profile : ($this->profile? image_url($this->profile):''),
            "number" => $this->number,
            "email" => $this->email,
            "address" => $this->address,
            "location" => new LocationResource($this->location),
            "post_code" => $this->post_code,
            "status" => $this->status,
            "account_verification" => $this->account_verification?->status,
            "business" => $this->business?[
                'id' => $this->business->id,
                'business_name' => $this->business->business_name,
                'business_profile' => $this->business->business_profile?image_url($this->business->business_profile) : ''
            ]:'',
        ];
    }
}
