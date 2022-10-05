<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RequestNotify extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $booking;

    public function __construct($booking){

        $this->booking = $booking;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //dd($booking);
        return $this->subject('Booking Request Recieved')->view('emails.newrequest');
    }
}
