<?php
namespace App\Utils;

use Illuminate\Support\Facades\DB;

class Util{

    public function activityLog($user_id, $action, $system, $model_type=null, $model_id=null, $date, $time, $properties=null, $notes=null){
        DB::table('action_histories')->insert([
            'user_id'   =>  $user_id,
            'action'   =>  $action,
            'system'   =>  $system,
            'model_type'   =>  $model_type,
            'model_id'   =>  $model_id,
            'date'   =>  $date,
            'time'   =>  $time,
            'properties'   =>  $properties,
            'notes'   =>  $notes,
        ]);
    }
}
