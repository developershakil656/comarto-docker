<?php

namespace App\Traits;
use Vinkla\Hashids\Facades\Hashids;

trait Hashidable
{
    public function getRouteKey()
    {
        return Hashids::encode($this->getKey());
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $id = Hashids::decode($value)[0] ?? null;

        return $this->where($field ?? $this->getRouteKeyName(), $id)->firstOrFail();
    }
}
