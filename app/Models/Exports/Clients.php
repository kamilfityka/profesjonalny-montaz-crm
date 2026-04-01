<?php

namespace App\Models\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class Clients implements FromCollection, WithMapping, WithHeadings
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
            'Firma/Osoba',
            'Adres',
            'Email',
            'Telefon',
        ];
    }

    public function map($row): array
    {
        /** @var Client $row */
        return [
            ($row->company_name?:$row->name),
            $row->city.' '.$row->street,
            $row->email,
            $row->phone,
        ];
    }
}
