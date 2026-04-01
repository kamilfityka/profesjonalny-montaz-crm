<?php

namespace App\Models\Exports;

use App\Models\Client;
use App\Models\Reclamation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class Reclamations implements FromCollection, WithMapping, WithHeadings
{
    public function __construct(protected $data)
    {
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Miejscowość',
            'Imię i nazwisko',
            'Nr telefonu',
            'Co do zrobienia',
            'Data',
        ];
    }

    public function map($row): array
    {
        /** @var Reclamation $row */
        return [
            $row->address,
            $row->name,
            $row->phone,
            strip_tags($row->text),
            $row->created_at->format("d-m-Y H:i:s"),
        ];
    }
}
