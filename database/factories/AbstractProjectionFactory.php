<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractProjectionFactory extends Factory
{
    public function newModel(array $attributes = []): Model
    {
        return parent::newModel($attributes)->writeable();
    }
}
