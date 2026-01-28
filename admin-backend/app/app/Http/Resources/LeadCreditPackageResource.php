<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadCreditPackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'credits'         => $this->credits,
            'price'           => $this->price,
            'currency'        => $this->currency,
            'duration_months' => $this->duration_months,
            'is_active'       => $this->is_active,
            'created_at'      => $this->created_at?->toDateTimeString(),
        ];
    }
}
