<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class ConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $auth = auth()->user();
        $isUser = $auth->id === $this->user_id;

        // Count unread messages depending on viewer
        $unreadCount = $this->messages()
            ->where('is_read', false)
            ->where('sender_type', $isUser ? 'business' : 'user')
            ->count();

        if ($isUser) {
            $profile = [
                'business_id' => Hashids::encode($this->business->id),
                'name' => $this->business->business_name,
                "slug" => $this->business->slug,
                'profile' => image_url($this->business->business_profile),
                'full_address' => $this->business?->address.', '
                    .$this->business?->location?->upazila_name.', '
                    .$this->business?->location?->district_name.', '
                    .$this->business?->post_code,
                "rate" => $this->business?->averageRating,
            ];
        } else {
            $profile = [
                'id' => Hashids::encode($this->user->id),
                'name' => $this->user->name,
                'profile' => image_url($this->user->profile),
                'full_address' => $this->user?->address.', '
                    .$this->user?->location?->upazila_name.', '
                    .$this->user?->location?->district_name.', '
                    .$this->user?->post_code,
                "rate" => $this->user?->averageRating,
            ];
        }

        return [
            'id' => Hashids::encode($this->id),
            'profile' => $profile,
            'last_message' => new MessageResource($this->messages()->latest()->first()),
            'unread_count' => $unreadCount,
            'my_role' => $isUser ? 'user' : 'business',
            'is_online' => $isUser ? $this?->business->user->isOnline() : $this?->user->isOnline(),
            'created_at'  => $this->created_at->toDateTimeString(),
            "account_verification" => $isUser? $this->business->user->account_verification == 'confirmed' : $this->user?->account_verification?->status == 'confirmed',
        ];
    }
}
