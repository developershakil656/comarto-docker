<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Str;

class LeadUserResource extends JsonResource
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
            "name" => $this->name,
            "profile" => $this->profile && (Str::startsWith($this->profile, 'http://') || Str::startsWith($this->profile, 'https://')) ?
                $this->profile : ($this->profile? image_url($this->profile):''),
            "number" => $this->number,
            "email" => $this->email,
            "address" => $this->address,
            "location" => new LocationResource($this->location),
            "post_code" => $this->post_code,
            "account_verification" => $this->account_verification?->status == 'confirmed',
        ];
    }
}
