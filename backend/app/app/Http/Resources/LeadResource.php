<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class LeadResource extends JsonResource
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
            "quantity" => $this->quantity,
            "unit_name" => $this->unit_name,
            "status" => $this->status,
            "description" => $this->description,
            "category" => new CategoryResource($this->category),
            "location" => new LocationResource($this->location),
            "user" => [
                "number" => !empty($this->user->number),
                "email" => !empty($this->user->number),
                "address" => !empty($this->user->address),
                "account_verification" => $this->user?->account_verification?->status == 'confirmed',
            ],
            "created_at" => $this->created_at,
            "proposal" => $this->proposal()->count(),
        ];
    }
}
