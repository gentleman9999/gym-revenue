<?php

namespace App\Mail\EndUser;

use App\Models\Endusers\Lead;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailFromRep extends Mailable
{
    use Queueable, SerializesModels;

    protected $data, $lead, $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $data, string $lead_id, string $user_id)
    {
        $this->data = $data;
        $this->lead = Lead::find($lead_id);
        $this->user = User::find($user_id);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->user->email)
            ->subject($this->data['subject'])
            ->markdown('emails.endusers.email-from-rep', ['data' => $this->data, 'user'=> $this->user->toArray()]);
    }
}