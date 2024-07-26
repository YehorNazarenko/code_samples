<?php

namespace App\Mail\Notifications\HumanMetricReminder;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class DailyReminder
 *
 * @version 0.0.3
 * @since 0.0.3
 * @author John Doe <john@doe.test>
 */
class DailyReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $result;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($result)
    {
        $this->result = $result;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = '';
        switch( $this->result ) {
            case 'weekly':
                $subject = 'Weekly Reminder: Please do not forget to do what you should do.';
                break;
            case 'bi-weekly':
                $subject = 'Bi-weekly Reminder: Please do not forget to do what you should do.';
                break;
            case 'monthly':
                $subject = 'Monthly Reminder: Please do not forget to do what you should do.';
                break;
            default:
                $subject = 'Daily Reminder: Please do not forget to do what you should do.';
                break;
        }
        return $this->view('emails.notifications.table_name_score')->subject($subject);
    }
}
// end of class DailyReminder
// end of file DailyReminder.php
