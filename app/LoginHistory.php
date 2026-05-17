<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    /**
     * table name of model
     *
     * @var type
     */
    protected $table = "login_histories";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ip', 'phone_details', 'user_id'
    ];

    public static function getBrowser($info) {
        $arr = explode(" ", $info);
        return isset($arr[10])? $arr[10] : '';
    }

    public static function getPlatform($info) {
        $arr = explode(" ", $info);
        return isset($arr[10])? $arr[10] : '';
    }

    public static function getInfo(\Illuminate\Http\Request $request) {
        $info = $request->header('user-agent');//get_browser(null, true);
        return '
            <ul>
                <li>اسم المتصفح : '.self::getBrowser($info) != null? self::getBrowser($info) : ''.'</li>
                <li>نوع نظام التشغيل  : '.$info.'</li>
            </ul>
        ';
    }
}
