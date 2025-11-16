<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PaymentsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Payment::with(['client', 'tontine', 'collector', 'validator'])
            ->orderBy('id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Référence', 'Client', 'Tontine', 'Montant', 'Date', 'Collecté par', 'Statut', 'Validé par', 'Validé le'
        ];
    }

    public function map($payment): array
    {
        return [
            $payment->reference,
            optional($payment->client)->full_name,
            optional($payment->tontine)->code,
            $payment->amount,
            optional($payment->payment_date)?->format('Y-m-d'),
            optional($payment->collector)->name,
            $payment->status,
            optional($payment->validator)->name,
            optional($payment->validated_at)?->format('Y-m-d H:i'),
        ];
    }
}
