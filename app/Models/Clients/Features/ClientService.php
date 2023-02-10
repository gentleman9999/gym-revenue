<?php

declare(strict_types=1);

namespace App\Models\Clients\Features;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientService extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Uuid;

    /** @var bool  */
    public $incrementing = false;

    /** @var array<string>  */
    protected $fillable = ['feature_name', 'slug', 'active'];

    /** @var string  */
    protected $primaryKey = 'id';

    /** @var string  */
    protected $keyType = 'string';
}
