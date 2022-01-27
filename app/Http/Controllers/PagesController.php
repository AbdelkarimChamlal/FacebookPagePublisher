<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FacebookAPI;
use App\Post;
use App\Error;
use Config;
class PagesController extends Controller
{
    public function welcome(Request $req){
        $logedin = LoginController::loggedIn($req);
        return view('welcome')->with('logedIn',$logedin);
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
        return view('pages.pages')->with('pages',$pages);
    }

    public function page(Request $req, $id){
        # check if user is loged in
        if(!LoginController::loggedIn($req)) return redirect('/login')->with('message',' please connect your facebook first');
        
        # check if the user is allowed to view this page
        $pagesData = FacebookAPI::getPages(session('access_token'));
        $pagesResults = json_decode($pagesData['body'])->data;
        $pagesContainId = false;
        foreach($pagesResults as $page){
            if ($page->id == $id){
                $pagesContainId = true;
                break;
            }
        }
        if(!$pagesContainId) return redirect('/pages')->with('message','access denied');

        # collect user posts 
        $posts = Post::where('user_id',session('id'))->where('page_id',$id)->get();

        # return view
        $data = [
            'posts' => $posts,
            'id' => $id
        ];

        return view('pages.page')->with($data);
    }

    public function createPost(Request $req, $id){
        # check if user is loged in
        if(!LoginController::loggedIn($req)) return redirect('/login')->with('message',' please connect your facebook first');

        $data = [
            'id' => $id,
        ];

        return view('pages.create')->with($data);
    }


    public function createPostHandler(Request $req, $id){
        # check if user is loged in
        if(!LoginController::loggedIn($req)) return redirect('/login')->with('message',' please connect your facebook first');

        # check if the user is allowed to view this page
        $pagesData = FacebookAPI::getPages(session('access_token'));
        $pagesResults = json_decode($pagesData['body'])->data;
        $pagesContainId = false;
        $access_token = "";
        foreach($pagesResults as $page){
            if ($page->id == $id){
                $pagesContainId = true;
                $access_token = $page->access_token;
                break;
            }
        }
        if(!$pagesContainId) return redirect('/pages')->with('message','access denied');

        $validatePostType = $req->validate([
                                'postOptions' => 'required',
                                'postTimeOptions' =>'required',
                            ]);
        # in case of a sheduled post check if the scheduled date is valid
        if($req->get('postTimeOptions') == "schedule"){
            $validatePostType = $req->validate([
                'scheduleTime' => 'required',
            ]);
            $timeDifference = strtotime($req->get('scheduleTime')) - $_SERVER['REQUEST_TIME'];
            if($timeDifference < 600)
            return redirect('/pages/'.$id.'/create')->withErrors('schedules posts should have at least 10 minutes delay');
        }
        if($req->get('postOptions') == "text"){
            $validatePostType = $req->validate([
                'textMessage' => 'required'
            ]);
            if($req->get('postTimeOptions') == "now"){
                $response = FacebookAPI::postMessage($access_token, $req->get('textMessage'), $id);
                if($response['httpCode'] == 200){
                    $post = new Post();
                    $post->post_id = json_decode($response['body'])->id;
                    $post->user_id = session('id');
                    $post->page_id = $id;
                    $post->post_media = "";
                    $post->post_share_time = $_SERVER['REQUEST_TIME'];
                    $post->post_message = $req->get('textMessage');
                    $post->post_type = "TEXT";
                    $post->post_share_type = "POSTED";
                    $post->save();
                }else{
                    $error = new Error();
                    $error->user_id = session('id');
                    $error->page_id = $id;
                    $error->http_code = $response['httpCode'];
                    $error->body = $response['body'];
                    $error->save();
                }            
            }else if($req->get('postTimeOptions') == "schedule"){
                $response = FacebookAPI::scheduleMessage($access_token, $req->get('textMessage'),strtotime($req->get('scheduleTime')), $id);
                if($response['httpCode'] == 200){
                    $post = new Post();
                    $post->post_id = json_decode($response['body'])->id;
                    $post->user_id = session('id');
                    $post->page_id = $id;
                    $post->post_media = "";

                    $post->post_message = $req->get('textMessage');
                    $post->post_type = "TEXT";
                    $post->post_share_type = "SCHEDULED";
                    $post->post_share_time = strtotime($req->get('scheduleTime'));

                    $post->save();
                }else{
                    $error = new Error();
                    $error->user_id = session('id');
                    $error->page_id = $id;
                    $error->http_code = $response['httpCode'];
                    $error->body = $response['body'];
                    $error->save();
                }            
            }

        }else 
        if($req->get('postOptions') == "picture")
        {
            $validatePostType = $req->validate([
                'pictureMessage' => 'required',
                'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048'
            ]);

            $name = $req->file('image')->getClientOriginalName();
 
            $path = $req->file('image')->store('public/videos');

            $imagePath = Config::get('services.storage.abs_path').$path;

            if($req->get('postTimeOptions') == "now"){
                $response = FacebookAPI::postPicture($access_token, $imagePath, $req->get('pictureMessage'), $id);
                if($response['httpCode'] == 200){
                    $post = new Post();
                    $post->post_id = json_decode($response['body'])->id;
                    $post->user_id = session('id');
                    $post->page_id = $id;
                    $post->post_media = $imagePath;
                    $post->post_message = $req->get('pictureMessage');
                    $post->post_type = "PICTURE";
                    $post->post_share_type = "POSTED";
                    $post->post_share_time = $_SERVER['REQUEST_TIME'];

                    $post->save();
                }else{
                    $error = new Error();
                    $error->user_id = session('id');
                    $error->page_id = $id;
                    $error->http_code = $response['httpCode'];
                    $error->body = $response['body'];
                    $error->save();
                }
            }else if($req->get('postTimeOptions') == "schedule"){
                $response = FacebookAPI::schedulePicture($access_token, $imagePath, $req->get('pictureMessage'), strtotime($req->get('scheduleTime')), $id);
                if($response['httpCode'] == 200){
                    $post = new Post();
                    $post->post_id = json_decode($response['body'])->id;
                    $post->user_id = session('id');
                    $post->page_id = $id;
                    $post->post_media = $imagePath;
                    $post->post_message = $req->get('pictureMessage');
                    $post->post_type = "PICTURE";
                    $post->post_share_type = "SCHEDULED";
                    $post->post_share_time = strtotime($req->get('scheduleTime'));

                    $post->save();
                }else{
                    $error = new Error();
                    $error->user_id = session('id');
                    $error->page_id = $id;
                    $error->http_code = $response['httpCode'];
                    $error->body = $response['body'];
                    $error->save();
                }
            }

        }else 
        if($req->get('postOptions') == "video")
        {
            $validatePostType = $req->validate([
                'videoTitle' => 'required',
                'videoDescription' => 'required',
                'video' => 'required|mimes:mp4,wmv,mpeg4,flv,avi'
            ]);
            $name = $req->file('video')->getClientOriginalName();
 
            $path = $req->file('video')->store('public/videos');

            $videoPath = Config::get('services.storage.abs_path').$path;

            if($req->get('postTimeOptions') == "now"){
                $response = FacebookAPI::postVideo($access_token, $videoPath, $req->get('videoTitle'), $req->get('videoDescription'), $id);
                if($response['httpCode'] == 200){
                    $post = new Post();
                    $post->post_id = json_decode($response['body'])->id;
                    $post->user_id = session('id');
                    $post->page_id = $id;
                    $post->post_media = $videoPath;
                    $post->post_message = $req->get('videoDescription');
                    $post->post_type = "VIDEO";
                    $post->post_share_type = "POSTED";
                    $post->post_share_time = $_SERVER['REQUEST_TIME'];

                    $post->save();
                }else{
                    $error = new Error();
                    $error->user_id = session('id');
                    $error->page_id = $id;
                    $error->http_code = $response['httpCode'];
                    $error->body = $response['body'];
                    $error->save();
                }     
            }else if($req->get('postTimeOptions') == "schedule"){
                $response = FacebookAPI::scheduleVideo($access_token, $videoPath, $req->get('videoTitle'), $req->get('videoDescription'), strtotime($req->get('scheduleTime')), $id);
                if($response['httpCode'] == 200){
                    $post = new Post();
                    $post->post_id = json_decode($response['body'])->id;
                    $post->user_id = session('id');
                    $post->page_id = $id;
                    $post->post_media = $videoPath;
                    $post->post_message = $req->get('videoDescription');
                    $post->post_type = "VIDEO";
                    $post->post_share_type = "SCHEDULED";
                    $post->post_share_time = strtotime($req->get('scheduleTime'));

                    $post->save();
                }else{
                    $error = new Error();
                    $error->user_id = session('id');
                    $error->page_id = $id;
                    $error->http_code = $response['httpCode'];
                    $error->body = $response['body'];
                    $error->save();
                }
            }
        }
        return redirect('/pages/'.$id);
    }
}
