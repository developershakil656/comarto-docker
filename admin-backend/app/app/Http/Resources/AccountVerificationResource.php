<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountVerificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'nid_number' => $this->nid_number,
            'nid_front'  => image_url($this->nid_front),
            'nid_back'   =>  image_url($this->nid_back),
            'status'     => $this->status,
            'user'       => [
                'id'   => $this->user?->id,
                'name' => $this->user?->name,
                'email'=> $this->user?->email,
            ],
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
