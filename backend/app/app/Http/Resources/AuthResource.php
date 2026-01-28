<?php

namespace App\Http\Resources;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         // 🔹 Unread conversations as normal user
        $unreadAsUser = Conversation::where('user_id', $this->id)
            ->whereHas('messages', function ($q) {
                $q->where('is_read', false)->where('sender_type', 'business');
            })
            ->count();

        // 🔹 Unread conversations as business owner
        $unreadAsBusiness = 0;
        if ($this->business) {
            $unreadAsBusiness = Conversation::where('business_id', $this->business->id)
                ->whereHas('messages', function ($q) {
                    $q->where('is_read', false)->where('sender_type', 'user');
                })
                ->count();
        }

        return [
            "id" => Hashids::encode($this->id),
            "name" => $this->name,
            "profile" => $this->profile && (Str::startsWith($this->profile, 'http://') || Str::startsWith($this->profile, 'https://')) ?
                $this->profile : ($this->profile? image_url($this->profile):''),
            "number" => $this->number,
            "email" => $this->email,
            "address" => $this->address,
            "location" => new LocationResource($this->location),
            "post_code" => $this->post_code,
            "account_verification" => $this->account_verification?->status,
            "business" => $this->business?[
                'id' => Hashids::encode($this->business->id),
                'business_name' => $this->business->business_name,
                'business_profile' => $this->business->business_profile?image_url($this->business->business_profile) : ''
            ]:'',
            'total_unread_conversations'    => $unreadAsUser + $unreadAsBusiness,
        ];
    }
}
