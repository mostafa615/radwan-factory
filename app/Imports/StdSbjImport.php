<?php

namespace App\Imports;

use App\Student;
use App\StudentSubject;
use Maatwebsite\Excel\Concerns\ToModel;
use DB;
class StdSbjImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $row[0] = str_replace(" ", "", $row[0]);

        $std = Student::where("code", $row[0])->first();

        $countOfAssign = StudentSubject::where("course_id", $row[1])->where("student_id", $std['id'])->count();

        /*$assign = StudentSubject::create([
            'student_id' => $std['id'],
            'course_id' => $row[1]
        ]);
        return $assign;*/

        if ($std == null) {
            if(DB::table('unassigned_students')->where('student_code', $row[0])->exists()){
                return null;
            }else{
                DB::insert('insert into unassigned_students (student_code, name,level,department) values (?,?,?,?)', [$row[0], $row[2],$row[3] ,$row[4] ]);
                return null;
            }
        }elseif($countOfAssign > 0){
            DB::table('student_courses')
                  ->where("course_id", $row[1])
                  ->where("student_id", $std['id'])
                  ->update(['updated_at' => '2021-03-24 10:50:20']);
            return null;
        }else {
            $assign = StudentSubject::create([
                "student_id" => $std['id'],
                "course_id" => $row[1],
            ]);
            return $assign;
        }

        /*return new StudentSubject([
            'student_id' => $row[0],
            'subject_id' => $row[1]
        ]);*/
    }
}
