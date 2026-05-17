<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActionHistory extends Model
{
    protected $table = "action_histories";

    protected $fillable = [
        'user_id','action', 'system' , 'model_type', 'model_id' , 'date' , 'time', 'properties', 'notes'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
