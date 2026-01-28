<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
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
            "upazila_name" => $this->upazila_name,
            "district_name" => $this->district_name,
            "upazila_name_bn" => $this->upazila_name_bn,
            "district_name_bn" => $this->district_name_bn,
            "status" => $this->status
        ];
    }
}
