<?php

namespace App\Exports;

use App\Doctor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class DoctorsExport implements FromCollection, WithHeadings, WithEvents
{
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:W1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            },
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        ob_end_clean();
        return Doctor::all();
    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'UserName',
            'Email',
            'Phone',
            'Active',
            'account_confirm',
            'Created_at',
            'Updated_at'
        ];
    }
}
