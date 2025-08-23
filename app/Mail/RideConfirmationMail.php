<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RideConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The customer's name.
     * @var string
     */
    public $customerName;

    /**
     * The type of SKUT vehicle.
     * @var string
     */
    public $skutType;

    /**
     * The duration of the ride.
     * @var string
     */
    public $duration;

    /**
     * The booking date.
     * @var string
     */
    public $bookingDate;

    /**
     * The booking time.
     * @var string
     */
    public $bookingTime;

    /**
     * The amount paid for the ride.
     * @var string
     */
    public $amount;

    /**
     * Create a new message instance.
     *
     * @param string $customerName The name of the customer.
     * @param string $skutType The type of vehicle (e.g., Scooter).
     * @param string $duration The duration of the ride (e.g., '1 Hour(s)').
     * @param string $bookingDate The date of the booking (e.g., '01/20/2024').
     * @param string $bookingTime The time of the booking (e.g., '10:30 AM').
     * @param string $amount The price paid in CAD (e.g., '15.00').
     */
    public function __construct(
        $customerName,
        $skutType,
        $duration,
        $bookingDate,
        $bookingTime,
        $amount
    ) {
        $this->customerName = $customerName;
        $this->skutType = $skutType;
        $this->duration = $duration;
        $this->bookingDate = $bookingDate;
        $this->bookingTime = $bookingTime;
        $this->amount = $amount;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your SKUT Ride is Confirmed 🚀',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.ride-confirmation-mail',
            with: [
                'customerName' => $this->customerName,
                'skutType' => $this->skutType,
                'duration' => $this->duration,
                'bookingDate' => $this->bookingDate,
                'bookingTime' => $this->bookingTime,
                'amount' => $this->amount,
            ],
        );
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
