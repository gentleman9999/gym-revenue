<?php

namespace App\Services\GatewayProviders\Profiles\SMS;

interface SMSProfile
{
    public function fireMsg($phone_number, string $msg);
    public function fireBulkMsg();
    public function translateMessage(string $msg);


}
