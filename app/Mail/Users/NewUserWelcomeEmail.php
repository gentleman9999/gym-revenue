<?php

namespace App\Mail\Users;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUserWelcomeEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // @todo - get the user's role or security role
        $role = 'some Role';

        return $this->from(
            env('MAIL_FROM_ADDRESS', 'developers@capeandbay.com'),
            env('MAIL_FROM_NAME', 'Cape & Bay Dev Team')
        )
            ->subject('You have been invited to join '.env('APP_NAME')." as {$role}!")
            ->view('emails.users.new-welcome-email', ['new_user' => $this->user]);
    }
}
