<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function welcome(Request $req){
        $logedin = LoginController::loggedIn($req);
        return view('welcome')->with('logedIn',$logedin);
    }
}
