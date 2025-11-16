<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Payment;

class PaymentReminderEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;
    public $isAdvance;

    public function __construct(Payment $payment, $isAdvance = false)
    {
        $this->payment = $payment;
        $this->isAdvance = $isAdvance;
    }

    public function build()
    {
        $subject = $this->isAdvance 
            ? 'Rappel: Paiement dû bientôt pour votre tontine'
            : 'Rappel: Paiement en retard pour votre tontine';

        return $this->subject($subject)
            ->markdown('emails.payments.reminder')
            ->with([
                'payment' => $this->payment,
                'isAdvance' => $this->isAdvance,
                'client' => $this->payment->client,
                'tontine' => $this->payment->tontine,
            ]);
    }
}
