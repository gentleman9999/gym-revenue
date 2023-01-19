<?php

namespace App\Models\Traits;

use App\Support\Uuid;

trait Duplicateable
{
    public function scopeDuplicate($model)
    {
        $model = $model->first()->writeable();
        $class = (new ($model::class))->getAggregate();
        $id = Uuid::new();

        $class::retrieve($id)
            ->create($model->replicate()->toArray())
            ->persist();

        return $model->findorfail($id);
    }
}
