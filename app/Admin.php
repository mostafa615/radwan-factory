<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laratrust\Traits\LaratrustUserTrait;
use Illuminate\Notifications\Notifiable;

class Admin extends Model
{
    use LaratrustUserTrait;
    use Notifiable;
    protected $table = "lms_admins";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'phone',
        'email',
        'branch_id',
        'password',
        'active',
        'account_confirm',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
     public function branch() {
        return $this->belongsTo("App\Branch", "branch_id");
    }

    public function getFirstNameAttribute($value){

        return ucfirst($value);
    }
    public function getLastNameAttribute($value){

        return ucfirst($value);
    }
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
