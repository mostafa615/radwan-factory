<?php

namespace App\Imports;

use App\Doctor;
use App\User;
use Maatwebsite\Excel\Concerns\ToModel;

class DoctorsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $docs = Doctor::create([
            'name' => $row[0],
            'username' => $row[1],
            'email' => $row[2],
            'password' => bcrypt($row[3]),
            'phone' => $row[4],
            'active' => $row[5],
            'account_confirm' => $row[6],
        ]);
        $docs->attachRole('doctor');
        $docref = $docs->refresh();

        $docuser = User::create([
            'name' => $row[0],
            'username' => $row[1],
            'email' => $row[2],
            'password' => bcrypt($row[3]),
            'phone' => $row[4],
            'active' => $row[5],
            'account_confirm' => $row[6],
            'type' =>'doctor',
            'fid' => $docref->id
        ]);
        $docuser->attachRole('doctor');

        return $docs;
    }
}
