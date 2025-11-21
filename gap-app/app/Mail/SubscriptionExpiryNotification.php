<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionExpiryNotification extends Mailable
{
    use Queueable, SerializesModels;


    public $user;
    public $subscription;
    public $daysLeft;
    
    /**
     * Create a new message instance.
     */
    public function __construct($user, $subscription, $daysLeft)
    {
        $this->user = $user;
        $this->subscription = $subscription;
        $this->daysLeft = $daysLeft;
    }   
   

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Subscription Expiry Notification',
        );
    }

   public function build ()
    {
        return $this->subject('Your Subscription is About to Expire')
                    ->markdown('emails.subscription_expiry_notification')
                    ->with([
                        'user' => $this->user,
                        'subscription' => $this->subscription,
                        'daysLeft' => $this->daysLeft,
                    ]);
    }
}
