<?php

namespace App\Imports;

use App\Student;
use App\User;
use Maatwebsite\Excel\Concerns\ToModel;
use DB;
class StudentsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $row[1] = str_replace(" ", "", $row[1]);
        $row[4] = str_replace(" ", "", $row[4]);
        $row[5] = str_replace(" ", "", $row[5]);
        $row[6] = str_replace(" ", "", $row[6]);
        $row[9] = str_replace(" ", "", $row[9]);

        $stdOld = Student::where("code", $row[4])->first();

        if(!$stdOld){
            $stds = Student::create([
                'name' => $row[0],
                'username' => $row[1],
                'level_id' => $row[2],
                'department_id' => $row[3],
                'code' => $row[4],
                'email' => $row[5],
                'password' => bcrypt($row[6]),
                'phone' => $row[7],
                'set_number' => $row[8],
                'national_id' => $row[9],
                'active' => $row[10],
                'account_confirm' => $row[11],
                'graduated' => $row[12],
                'can_see_result' => $row[13],
            ]);

            $stds->attachRole('student');
            $stdref = $stds->refresh();

            $stduser = User::create([
                'name' => $row[0],
                'username' => $row[1],
                'email' => $row[5],
                'password' => bcrypt($row[6]),
                'phone' => $row[7],
                'active' => $row[10],
                'account_confirm' => $row[11],
                'type' =>'student',
                'fid' => $stdref->id
            ]);

            $stduser->attachRole('student');
            return $stds;

        }else{
            $stdOld->update([
                'level_id' => $row[2],
                'department_id' => $row[3],
            ]);
            return $stdOld;
        }


    }



}
