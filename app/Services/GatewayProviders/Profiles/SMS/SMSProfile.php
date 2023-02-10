<?php

declare(strict_types=1);

namespace App\Services\GatewayProviders\Profiles\SMS;

interface SMSProfile
{
    /**
     * @param mixed $phone_number
     * @param string $msg
     *
     * @return mixed
     */
    public function fireMsg($phone_number, string $msg);

    /**
     * @return mixed
     */
    public function fireBulkMsg();

    /**
     * @param string $msg
     *
     * @return mixed
     */
    public function translateMessage(string $msg);
}
