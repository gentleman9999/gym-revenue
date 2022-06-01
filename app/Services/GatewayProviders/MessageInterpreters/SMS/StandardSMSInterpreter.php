<?php

namespace App\Services\GatewayProviders\MessageInterpreters\SMS;

use App\Services\GatewayProviders\MessageInterpreters\MessageInterpreterService;
use Str;

class StandardSMSInterpreter extends MessageInterpreterService
{
    private array $token_library = [
        '%first_name%' => 'first_name',
        '%last_name%' => 'last_name',
        '%name%' => 'name',
        '%email%' => 'email',
        '%alternate_email%' => 'alternate_email',
        '%address1%' => 'address1',
        '%address2%' => 'address2',
        '%city%' => 'city',
        '%state%' => 'state',
        '%zip%' => 'zip',
        '%phone%' => 'phone',
        '%job_title%' => 'job_title',
    ];

    public function __construct($user_id)
    {
        parent::__construct($user_id);
    }

    public function translate(string $msg)
    {
        $results = $msg;
        $wordsplosion = explode(' ', $msg);
        $new_msg = '';
        foreach ($wordsplosion as $idx => $word) {
            if (str_contains($word, '%')) {
                $token = Str::between($word, '%', '%');
                $corrected_token = "%{$token}%";
                if (array_key_exists($corrected_token, $this->token_library)) {
                    $new_word = $this->getTranslatedValue($this->token_library[$corrected_token]);
                    $new_word = str_replace($corrected_token, $new_word, $word);
                    $new_msg .= ($idx > 0) ? " {$new_word}" : "{$new_word}";
                } else {
                    $new_msg .= ($idx > 0) ? " {$word}" : "{$word}";
                }
            } else {
                $new_msg .= ($idx > 0) ? " {$word}" : "{$word}";
            }
        }

        if (! empty($new_msg)) {
            $results = $new_msg;
        }

        return $results;
    }
}
