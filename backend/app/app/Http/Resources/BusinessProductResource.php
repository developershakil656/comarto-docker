<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class BusinessProductResource extends JsonResource
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
            "slug" => $this->slug,
            "price" => $this->price,
            "unit_quantity" => $this->unit_quantity,
            "product_unit" => new ProductUnitResource($this->product_unit),
            "feature_image" => $this->featureImage?image_url($this->featureImage->url) : '',
            "categories" => CategoryResource::collection($this->categories)
        ];
    }
}
