<?php

declare(strict_types=1);

namespace App\Models\Endusers;

use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MembershipType extends Model
{
    use HasFactory;
    use SoftDeletes;

    /** @var array<string> */
    protected $fillable = ['id', 'client_id', 'name'];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }
}
