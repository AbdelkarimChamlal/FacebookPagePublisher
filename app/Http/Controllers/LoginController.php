<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FacebookAPI;
use Config;
use CURLFILE;
use HTTP_Request2;
class LoginController extends Controller
{
    public static function loggedIn(Request $req){
        
        $access_token = session('access_token');
        if(isset($access_token)){
            # if the token is not valid or expired redirect to the login page with an error msg
            if(!FacebookAPI::validateToken($access_token))
            return false;
        }else{
            # redirect to the login page
            return false;
        }
        return true;
    }



    public function callback(Request $req){
        #if user is already signed in
        if(LoginController::loggedIn($req)) $req->session()->flush();

        # check if there is a code
        if($req->get('code') !== null){
            $app_id = Config::get('services.facebookApp.id');
            $app_secret = Config::get('services.facebookApp.secret');
            $verion = Config::get('services.facebookApp.api_version');
            $callBack = Config::get('services.facebookApp.callback');
            $exchangeResults = FacebookAPI::ecxhangeShortToken($app_id,$app_secret,$verion,$callBack,$req->get('code'));
            
            # in case the code is valid the results should contain the access_token
            if($exchangeResults['httpCode'] == 200){
                session(['access_token' => json_decode($exchangeResults['body'])->access_token]);
                $userDetails = FacebookAPI::getUserDetails(session('access_token'));
                $profile = json_decode($userDetails['body']);
                session(['name'=> $profile->name, 'id' => $profile->id]);
                return redirect('/')->with('message', 'loged in');
            }
            # in case the code had expired or non valid
            else{
                return redirect('/login')->with('message', 'failed to log in');
            }
        }
        # login operation failed
        else{
            return redirect('/login')->with('message','failed to log in please try again');
        }
    }

    public function logOut(Request $req){
        $req->session()->flush();
        return redirect('/')->with('message', 'loged out');
    }

}
