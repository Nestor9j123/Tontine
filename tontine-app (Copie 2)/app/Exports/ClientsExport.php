<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ClientsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Client::with('agent')->orderBy('id')->get();
    }

    public function headings(): array
    {
        return [
            'Code', 'Prénom', 'Nom', 'Téléphone', 'Ville', 'Agent', 'Actif', 'Créé le'
        ];
    }

    public function map($client): array
    {
        return [
            $client->code,
            $client->first_name,
            $client->last_name,
            $client->phone,
            $client->city,
            optional($client->agent)->name,
            $client->is_active ? 'Oui' : 'Non',
            optional($client->created_at)?->format('Y-m-d H:i'),
        ];
    }
}
