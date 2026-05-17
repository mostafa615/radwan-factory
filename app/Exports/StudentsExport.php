<?php

namespace App\Exports;

use App\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class StudentsExport implements FromCollection, WithHeadings, WithEvents
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
        return Student::all();
    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'username',
            'Email',
            'Phone',
            'level_id',
            'department_id',
            'Code',
            'Active',
            'account_confirm',
            'set number',
            'national id',
            'graduated',
            'can see result',
            'Created_at',
            'Updated_at'
        ];
    }
}
