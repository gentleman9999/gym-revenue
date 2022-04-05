<?php

namespace App\Models\Traits;

trait Sortable
{
    public function scopeSort($query)
    {
        $sort = request()->sort;
        $dir = request()->dir;
        if(!$sort){
            return;
        }
        $query->orderBy($sort, $dir);
    }
}
