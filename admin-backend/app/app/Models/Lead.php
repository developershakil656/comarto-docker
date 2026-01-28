<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Lead extends Model
{
    use Searchable;
    protected $fillable = ['user_id','category_id','location_id','quantity', 'unit_name','description','status'];

    #search related all functions
    public function shouldBeSearchable(): bool
    {
        return true;
    }

    public function toSearchableArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'description' => $this->description,
            'updated_at' => $this->updated_at,
            'upazila_name' => $this->location->upazila_name ?? null,
            'district_name' => $this->location->district_name ?? null,
            'category' => $this->category->only('id', 'name','slug'),
            'status' => $this->status
        ];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function location() { return $this->belongsTo(Location::class); }
    public function proposal() { return $this->hasMany(BusinessLead::class); }
}
