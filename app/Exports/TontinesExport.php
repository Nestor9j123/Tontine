<?php

namespace App\Exports;

use App\Models\Tontine;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TontinesExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Tontine::with(['client', 'product', 'agent'])->orderBy('id')->get();
    }

    public function headings(): array
    {
        return [
            'Code', 'Client', 'Produit', 'Agent', 'Début', 'Fin', 'Montant total', 'Payé', 'Restant', 'Statut'
        ];
    }

    public function map($tontine): array
    {
        return [
            $tontine->code,
            optional($tontine->client)->full_name,
            optional($tontine->product)->name,
            optional($tontine->agent)->name,
            optional($tontine->start_date)?->format('Y-m-d'),
            optional($tontine->end_date)?->format('Y-m-d'),
            $tontine->total_amount,
            $tontine->paid_amount,
            $tontine->remaining_amount,
            $tontine->status,
        ];
    }
}
