<?php

namespace App\Domain\Templates\EmailTemplates\Projections;

use App\Models\GymRevDetailProjection;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTemplateDetails extends GymRevDetailProjection
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
        return new EmailTemplate();
    }

    public static function fk(): string
    {
        return "email_template_id";
    }
}
