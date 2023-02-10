<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Support\Uuid;
use Illuminate\Database\Eloquent\Model;

trait Duplicateable
{
    public function scopeDuplicate(?Model $model): ?Model
    {
        $model = $model->first()->writeable();
        $class = (new ($model::class))->getAggregate();
        $id    = Uuid::get();

        $class::retrieve($id)
            ->create($model->replicate()->toArray())
            ->persist();

        return $model->findorfail($id);
    }
}
