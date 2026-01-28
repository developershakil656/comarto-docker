<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Location extends Model
{
    use Searchable;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'upazila_name',
        'upazila_name_bn',
        'district_name',
        'district_name_bn',
        'status'
    ];

    #search related all functions
    public function shouldBeSearchable(): bool
    {
        return true;
    }

    public function toSearchableArray(): array
    {
        return [
            'upazila_name' => $this->upazila_name?$this->upazila_name:'empty',
            'district_name' => $this->district_name,
            'status' => $this->status
        ];
    }
}
