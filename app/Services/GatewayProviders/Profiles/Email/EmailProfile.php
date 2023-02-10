<?php

declare(strict_types=1);

namespace App\Services\GatewayProviders\Profiles\Email;

interface EmailProfile
{
    /**
     * @param mixed $email_address
     * @param string $subject
     * @param string $msg
     *
     * @return mixed
     */
    public function fireMsg($email_address, string $subject, string $msg);

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
