<?php

declare(strict_types=1);

namespace App\Enums;

enum CallOutcomesEnum: string
{
    case SpokeWithALead = 'CONTACTED';
    case LeftAVoicemail = 'VOICEMAIL';
    case LeadHungUp = 'HUNG_UP';
    case WrongNumber = 'WRONG_NUMBER';
    case AnAppointmentWasScheduled = 'APPOINTMENT';
    case MadeTheSaleOvcrThePhone = 'SALE';
}
