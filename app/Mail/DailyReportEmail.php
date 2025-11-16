<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyReportEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $stats;

    public function __construct($stats)
    {
        $this->stats = $stats;
    }

    public function build()
    {
        return $this->subject('Rapport quotidien - Tontine App')
            ->markdown('emails.reports.daily')
            ->with(['stats' => $this->stats]);
    }
}
