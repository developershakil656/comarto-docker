<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class BusinessLeadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lead = $this->lead;
        return [
            "id" => Hashids::encode($lead->id),
            "quantity" => $lead->quantity,
            "unit_name" => $lead->unit_name,
            "status" => $lead->status,
            "description" => $lead->description,
            "category" => new CategoryResource($lead->category),
            "location" => new LocationResource($lead->location),
            // "user" => new LeadUserResource($lead->user),
            "user" => [
                "number" => !empty($lead->user->number),
                "email" => !empty($lead->user->number),
                "address" => !empty($lead->user->address),
                "account_verification" => $lead->user?->account_verification == 'confirmed',
            ],
            "created_at" => $lead->created_at,
        ];
    }
}
