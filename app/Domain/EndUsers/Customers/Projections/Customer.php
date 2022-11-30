<?php

namespace App\Domain\EndUsers\Customers\Projections;

use App\Models\GymRevProjection;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Customer extends GymRevProjection
{
    use Notifiable;
    use SoftDeletes;
    use HasFactory;
    use Sortable;
}
