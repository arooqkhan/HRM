<?php

namespace App\Mail;

use App\Models\Leave;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class LeaveNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $leave;
    public $employeeName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Leave $leave, $employeeName)
    {
        $this->leave = $leave;
        $this->employeeName = $employeeName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.leaveNotification')
                    ->with([
                        'leave' => $this->leave,
                        'employeeName' => $this->employeeName,
                    ]);
    }
}
