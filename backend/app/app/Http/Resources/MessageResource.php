<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Vinkla\Hashids\Facades\Hashids;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => Hashids::encode($this->id),
            'message'     => $this->message,
            'attachments' => $this->attachments->pluck('url')->map(function ($url) {
                return image_url($url);
            }),
            'sender_type' => $this->sender_type,
            'is_read'     => (bool) $this->is_read,
            // 'created_at'  => $this->created_at->diffForHumans(),
            'created_at' => $this->created_at,
        ];
    }
}
