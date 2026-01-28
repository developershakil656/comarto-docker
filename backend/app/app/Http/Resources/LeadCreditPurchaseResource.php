<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadCreditPurchaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "package_name" => $this->package->name,
            "amount" => $this->amount,
            "payment_method" => $this->payment_method,
            "transaction_id" => $this->transaction_id,
            "status" => $this->status,
            "credits" => $this->credits,
            "duration_months" => $this->package->duration_months,
            "created_at" => $this->created_at
        ];
    }
}
