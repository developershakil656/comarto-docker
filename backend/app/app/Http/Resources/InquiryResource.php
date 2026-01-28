<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class InquiryResource extends JsonResource
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
            "description" => $this->description,
            "category" => new CategoryResource($this->category),
            "location" => new LocationResource($this->location),
            "created_at" => $this->created_at,
            "status" => $this->status,
            "proposal" => InquiryBusinessResource::collection($this->proposal),
        ];
    }
}
