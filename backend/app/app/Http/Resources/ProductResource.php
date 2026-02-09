<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class ProductResource extends JsonResource
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
            "details" => $this->details,
            "price" => $this->price,
            "moq" => $this->moq,
            "video_url" => $this->video_url,
            "specification" => $this->specification,
            "categories" => CategoryResource::collection($this->categories),
            "ancestor_slugs" => $this?->categories?->first()?->ancestor_slugs,
            "stock" => $this->stock,
            "unit_quantity" => $this->unit_quantity,
            "product_unit" => new ProductUnitResource($this->product_unit),
            "status" => $this->status,
            "gallery" => ProductGalleryResource::collection($this->gallery),
        ];
    }
}
