<?php

namespace App\Imports;

use App\Subject;
use Maatwebsite\Excel\Concerns\ToModel;

class SubjectsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Subject([
            'level_id' => $row[0],
            'name' => $row[1],
            'code' => $row[2],
            'hours' => $row[3],
        ]);
    }
}
