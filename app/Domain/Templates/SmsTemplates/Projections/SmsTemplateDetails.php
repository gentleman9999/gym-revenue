<?php

namespace App\Domain\Templates\SmsTemplates\Projections;

use App\Models\GymRevDetailProjection;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsTemplateDetails extends GymRevDetailProjection
{
    use SoftDeletes;

    protected $fillable = [
        'field', 'value', 'misc',
    ];

    protected $casts = [
        'misc' => 'array',
    ];

    public static function getRelatedModel()
    {
        return new SmsTemplate();
    }

    public static function fk(): string
    {
        return "sms_template_id";
    }
}
