<?php

namespace App\Services\GatewayProviders\Profiles\Email;

interface EmailProfile
{
    public function fireMsg($email_address, string $subject, string $msg);
    public function fireBulkMsg();
    public function translateMessage(string $msg);


}