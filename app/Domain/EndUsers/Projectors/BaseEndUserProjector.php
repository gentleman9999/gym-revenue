<?php

namespace App\Domain\EndUsers\Projectors;

use App\Domain\EndUsers\Projections\EndUser;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

abstract class BaseEndUserProjector extends Projector
{
    abstract protected function getModel(): EndUser;
}
