<?php

declare(strict_types=1);

namespace App\Domain\Templates\CallScriptTemplates\Projections;

use App\Models\GymRevDetailProjection;
use Illuminate\Database\Eloquent\SoftDeletes;

class CallScriptTemplateDetails extends GymRevDetailProjection
{
    use SoftDeletes;

    /** @var array<string>  */
    protected $fillable = [
        'detail', 'value', 'misc', 'active',
    ];

    /** @var array<string, string>  */
    protected $casts = [
        'misc' => 'array',
    ];

    public static function getRelatedModel(): CallScriptTemplate
    {
        return new CallScriptTemplate();
    }

    public static function fk(): string
    {
        return "call_template_id";
    }
}
