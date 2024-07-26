<?php

namespace App\Mail\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class PasswordReset
 *
 * @since 1.0.0
 * @version 1.0.0
 * @author John Doe <john@doe.test>
 */
class PasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
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
        return $this->subject('Your password reset request')
        ->view('emails.user.reset_password')->with([
            'name' => $this->user['name'],
            'token' => $this->user['token'],
        ]);
    }
}
// end of class
// end of file
