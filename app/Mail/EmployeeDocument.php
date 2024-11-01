<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmployeeDocument extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

     public  $accouncementDocument;

    public function __construct($accouncementDocument)
    {
        $this->accouncementDocument = $accouncementDocument;  
    }

    /**
     * Get the message envelope.
     */
    public function build()
    {
        return $this->view('emails.employee_document');
    }

    /**
     * Get the message content definition.
     */
    

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
   
}
