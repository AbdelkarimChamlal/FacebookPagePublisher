<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function loggedIn(Request $req){
        $access_token = session('access_token');
        if(isset($access_token)){
            echo " there is  a token";
        }else{
            echo " there is no token";
        }
    }

    public function logIn(Request $req){

    }

    public function callback(Request $req){
        $this->loggedIn($req);
        echo "hi";
    }

    public function logOut(Request $req){

    }
}
