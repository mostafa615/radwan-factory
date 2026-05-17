<?php

namespace App\Exports;

use App\StudentSubject;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class StdSbjExport implements FromCollection, WithHeadings, WithEvents
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
        return StudentSubject::all();
    }

    public function headings(): array
    {
        return [
            '#',
            'Student_ID',
            'Subject_ID',
            'Created_at',
            'Updated_at'
        ];
    }

}
