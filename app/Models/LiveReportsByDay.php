<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveReportsByDay extends Model
{
    use HasFactory;

    /** @var string */
    protected $primaryKey = 'id';

    /** @var string */
    protected $keyType = 'string';

    /** @var array<string> */
    protected $fillable = ['id', 'client_id', 'gr_location_id', 'date', 'action', 'entity', 'value'];

    /** @var array<string, string> */
    protected $casts = [
        'value' => 'float',
        'date' => 'date',
    ];
}
