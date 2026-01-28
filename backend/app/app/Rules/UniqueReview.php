<?php

namespace App\Rules;

use App\Models\BusinessReview;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class UniqueReview implements ValidationRule
{
    protected $userId;
    protected $ignoreReviewId;
    protected $currentBusinessId;

    public function __construct($userId, $ignoreReviewId = null)
    {
        $this->userId = $userId;
        $this->ignoreReviewId = $ignoreReviewId;
        $this->currentBusinessId = Auth::user()->business
            ? Auth::user()->business->id
            : null;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = BusinessReview::where('user_id', $this->userId)
            ->where('business_id', $value)
            ->whereNull('parent_id');

        // Exclude current review ID when updating
        if ($this->ignoreReviewId) {
            $query->where('id', '!=', $this->ignoreReviewId);
        }

        if ($query->exists()) {
            $fail('You have already submitted a review for this business.');
        }elseif($this->currentBusinessId == $value){
            $fail("You can't review your own business.");
        }
    }
}
