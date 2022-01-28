<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Error extends Model
{
    public static function addNewError($user_id, $page_id, $http_code, $body){
        $error = new Error();
        $error->user_id = $user_id;
        $error->page_id = $page_id;
        $error->http_code = $http_code;
        $error->body = $body;
        $error->save();
    }
}
