<?php

namespace App\Domain\Templates\CallScriptTemplates\Projections;

use App\Models\GymRevDetailProjection;
use Illuminate\Database\Eloquent\SoftDeletes;

class CallScriptTemplateDetails extends GymRevDetailProjection
{
    use SoftDeletes;

    protected $fillable = [
        'detail', 'value', 'misc', 'active',
    ];

    protected $casts = [
        'misc' => 'array',
    ];

    public static function getRelatedModel()
    {
        return new CallScriptTemplate();
    }

    public static function fk(): string
    {
        return "call_template_id";
    }
}
