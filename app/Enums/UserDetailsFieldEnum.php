<?php

declare(strict_types=1);

namespace App\Enums;

enum UserDetailsFieldEnum: string
{
    case SMS_TRANSMISSION = 'sms-transmission';
    case EMAIL_TRANSMISSION = 'email-transmission';
    case COLUMN_CONFIG = 'column-config';
    case OWNER_USER_ID = 'owner_user_id';
    case EMAILED_BY_REP = 'emailed_by_rep';
    case SMS_BY_REP = 'sms_by_rep';
    case CALLED_BY_REP = 'called_by_rep';
    case NOTE_CREATED = 'note_created';
}
