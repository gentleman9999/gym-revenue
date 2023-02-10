<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailCampaignMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    protected $data;
    protected $markup;
    protected $tokens = ['name'];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $subject, string $markup, array $data)
    {
        $this->subject = $subject;
        $this->markup  = $markup;
        $this->data    = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
//            ->from($this->user->email)
            ->subject($this->transform($this->subject))
            ->html($this->transform($this->markup));
    }

    protected function transform($string)
    {
        foreach ($this->tokens as $token) {
            $string = str_replace("%{$token}%", $this->data[$token] ?? 'UNKNOWN_TOKEN', $string);
        }

        return $string;
    }
}
