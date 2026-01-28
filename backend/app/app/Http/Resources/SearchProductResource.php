<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class SearchProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => Hashids::encode($this->id),
            "name" => $this->name,
            "slug" => $this->slug,
            "business" => new ShortBusinessResource($this->business),
            "details" => $this->details,
            "price" => $this->price,
            "moq" => $this->moq,
            "video_url" => $this->video_url,
            "specification" => $this->specification,
            "stock" => $this->stock,
            "unit_quantity" => $this->unit_quantity,
            "product_unit" => new ProductUnitResource($this->product_unit),
            "feature_image" => $this->featureImage?image_url($this->featureImage->url) : '',
        ];
    }
}
