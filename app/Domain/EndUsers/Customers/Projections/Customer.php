<?php

declare(strict_types=1);

namespace App\Domain\EndUsers\Customers\Projections;

use App\Domain\EndUsers\Projections\EndUser;
use App\Models\Traits\Sortable;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Customer extends EndUser
{
    use Notifiable;
    use SoftDeletes;
    use HasFactory;
    use Sortable;

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }
}
