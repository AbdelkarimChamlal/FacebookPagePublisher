<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FacebookAPI;
use App\Post;
use App\Error;
use App\Util;
use Session;
use Config;
class PagesController extends Controller
{
    public function welcome(Request $req){
        $data = Util::prepareBasicData($req);
        return view('welcome')->with($data);
    }

    public function login(Request $req){
        #if user is already signed in
        if(LoginController::loggedIn($req)) return redirect('/')->with('message',' already loged in');
        
        
        $app_id = Config::get('services.facebookApp.id');
        $verion = Config::get('services.facebookApp.api_version');
        $callBack = Config::get('services.facebookApp.callback');
        $permissions = Config::get('services.facebookApp.permissions');
        $authLink = FacebookAPI::getAuthLink($app_id,$verion,$callBack,$permissions);
        $connected = LoginController::loggedIn($req);

        $data = Util::prepareBasicData($req);
        $data['authLink'] = $authLink;

        return view('pages.login')->with($data);
    }

    public function pages(Request $req){
        # check if user is loged in
        if(!LoginController::loggedIn($req)) return redirect('/login')->with('message',' please connect your facebook first');

        $pagesData = FacebookAPI::getPages(session('access_token'));
        $pagesResults = json_decode($pagesData['body'])->data;
        $pages = [];
        foreach($pagesResults as $page){
            $item = [];
            $item['id'] = $page->id;
            $item['name'] = $page->name;
            array_push($pages, $item);
        }
        $data = Util::prepareBasicData($req);
        $data['pages'] = $pages;
        return view('pages.pages')->with($data);
    }


}
