<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductUnitResource extends JsonResource
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
            "short_form" => $this->short_form,
            "plural_form" => $this->plural_form,
            "full_form" => $this->full_form,
            "status" => $this->status
        ];
    }
}
