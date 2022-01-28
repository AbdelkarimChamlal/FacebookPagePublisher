<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\LoginController;
use Session;

class Util
{
    public static function prepareBasicData($req){
        $data = [];
        $connected = LoginController::loggedIn($req);
        $warnings = [];
        $success = [];
        $messages = [];

        if(Session::get('message')) {
            array_push($messages, Session::get('message'));
        }
        if(Session::get('warning')) {
            array_push($messages, Session::get('warning'));
        }
        if(Session::get('success')) {
            array_push($messages, Session::get('success'));
        }
        $data = [
            "warnings" => $warnings,
            "success" => $success,
            "messages" => $messages,
            "connected" => $connected,
        ];
        return $data;
    }
}
