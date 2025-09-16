<?php

namespace App\Mail;
use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;
    public $email;
    public $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Employee $employee, $email, $password)
    {
        $this->employee = $employee;  
        $this->email = $email;        
        $this->password = $password;  
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.employee_registered');
    }
}

