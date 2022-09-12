<?php

declare(strict_types=1);

namespace App\Services\GatewayProviders\Profiles\Voice;

use App\Domain\Users\Models\User;
use Twilio\Rest\Api\V2010\Account\CallInstance;

interface VoiceProfile
{
    public function initializeCall(User $caller, string $to, bool $record = false): CallInstance;

    public function getCallStatus(string $sid): CallInstance;
}
