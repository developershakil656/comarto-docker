<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "slug" => $this->slug,
            "business" => new BusinessResource($this->business),
            "price" => $this->price,
            "moq" => $this->moq,
            "stock" => $this->stock,
            "status" => $this->status,
            "unit_quantity" => $this->unit_quantity,
            "product_unit" => new ProductUnitResource($this->product_unit),
            "feature_image" => $this->featureImage?image_url($this->featureImage->url) : '',
        ];
    }
}
