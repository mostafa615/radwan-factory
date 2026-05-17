<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
// use Laratrust\Traits\LaratrustUserTrait;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    // use LaratrustUserTrait;
    use EntrustUserTrait;
    use Notifiable;

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
        'password',
        'type_id',
        'type',
        'active',
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

    public function notifications() {
        return $this->hasMany("App\Notification", "user_id");
    }

    public function toDoctor() {
        return Doctor::find($this->fid);
    }

    public function loginHistories() {
        return $this->hasMany("App\LoginHistory");
    }

    public function toStudent() {
        return Student::find($this->fid);
    }

    public function toParent() {
        return ParentStd::find($this->fid);
    }
}
