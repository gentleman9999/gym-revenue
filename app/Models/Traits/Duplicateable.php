<?php

namespace App\Models\Traits;

use App\Domain\Templates\SmsTemplates\Projections\SmsTemplate;
use App\Support\Uuid;

trait Duplicateable
{
    public function scopeDuplicate($model)
    {
//        $model = (new SmsTemplate())->first()->writeable();
        $model = $model->first()->writeable();
        $class = (new ($model::class))->getAggregate();
        $id = Uuid::new();

        return $class::retrieve($id)
            ->create($model->replicate()->toArray())
            ->persist();
    }
}
