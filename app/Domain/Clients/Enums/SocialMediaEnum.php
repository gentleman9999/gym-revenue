<?php

declare(strict_types=1);

namespace App\Domain\Clients\Enums;

enum SocialMediaEnum: string
{
    case FACEBOOK  = "Facebook";
    case INSTAGRAM = "Instagram";
    case LINKEDIN  = "LinkedIn";
    case TWITTER   = "Twitter";
}
