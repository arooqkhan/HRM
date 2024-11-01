<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Shift extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public  $shift;
    public function __construct($shift)
    {
        $this->shift = $shift;
    }

    /**
     * Get the message envelope.
     */
    public function build()
    {
        return $this->view('emails.shift') // Adjust the view name as needed
                    ->with([
                        'employeeName' => $this->shift->employee->first_name . ' ' . $this->shift->employee->last_name,
                        'shiftType' => $this->shift->shift_type,
                        'addDuty' => $this->shift->add_duty,
                        'date' => $this->shift->date,
                        'startTime' => $this->shift->start_time,
                        'endTime' => $this->shift->end_time,
                    ]);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
