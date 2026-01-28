<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class ProductUnit extends Model
{
    use Searchable;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'short_form',
        'plural_form',
        'full_form',
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
            'short_form' => $this->short_form,
            'plural_form' => $this->plural_form,
            'full_form' => $this->full_form,
            'status' => $this->status,
        ];
    }
}
