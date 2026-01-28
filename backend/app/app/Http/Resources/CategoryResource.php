<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class CategoryResource extends JsonResource
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
            "icon" => $this->icon?(config('app.admin_backend_url').'/'.$this->icon):'',
            "children" => $this->children?->isNotEmpty(),
            "title" => $this->title,
            "description" => $this->description
        ];
    }
}
