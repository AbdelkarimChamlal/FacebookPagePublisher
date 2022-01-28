<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FacebookAPI;
use App\Post;
use App\Error;
use App\Util;
use Config;
class PostsController extends Controller
{

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

        # form basic data
        $data = Util::prepareBasicData($req);

        # add costume data
        $data['posts'] = $posts;
        $data['id'] = $id;

        return view('pages.page')->with($data);
    }

    
    public function createPost(Request $req, $id){
        # check if user is loged in
        if(!LoginController::loggedIn($req)) return redirect('/login')->with('message',' please connect your facebook first');

        # form basic data
        $data = Util::prepareBasicData($req);

        # add costume data
        $data['id'] = $id;

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

        # validate post inputs
        $validatePostType = $req->validate([
                                'postOptions' => 'required',
                                'postTimeOptions' =>'required',
                            ]);


        # validate post share type
        if($req->get('postTimeOptions') == "schedule"){
            $validatePostType = $req->validate([
                'scheduleTime' => 'required',
            ]);
            $timeDifference = strtotime($req->get('scheduleTime')) - $_SERVER['REQUEST_TIME'];
            if($timeDifference < 600)
            return redirect('/pages/'.$id.'/create')->withErrors('schedules posts should have at least 10 minutes delay');
        }


        # text post
        if($req->get('postOptions') == "text"){
            $validatePostType = $req->validate([
                'textMessage' => 'required'
            ]);
            if($req->get('postTimeOptions') == "now"){
                $response = FacebookAPI::postMessage($access_token, $req->get('textMessage'), $id);
                if($response['httpCode'] == 200){
                    Post::addNewPost(json_decode($response['body'])->id,session('id'),$id,"",$req->get('textMessage'),"TEXT","POSTED",$_SERVER['REQUEST_TIME']);
                    return redirect('/pages/'.$id)->with('success', 'Posted successfuly');
                }else{
                    Error::addNewError(session('id'), $id, $response['httpCode'],$response['body']);
                    return redirect('/pages/'.$id)->with('warning', 'Failed to Post');
                }            
            }else if($req->get('postTimeOptions') == "schedule"){
                $response = FacebookAPI::scheduleMessage($access_token, $req->get('textMessage'),strtotime($req->get('scheduleTime')), $id);
                if($response['httpCode'] == 200){
                    Post::addNewPost(json_decode($response['body'])->id,session('id'),$id,"",$req->get('textMessage'),"TEXT","SCHEDULED",strtotime($req->get('scheduleTime')));
                    return redirect('/pages/'.$id)->with('success', 'Posted successfuly');

                }else{
                    Error::addNewError(session('id'), $id, $response['httpCode'],$response['body']);
                    return redirect('/pages/'.$id)->with('warning', 'Failed to Post');
                }            
            }

        }else 



        # picture post
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
                    Post::addNewPost(json_decode($response['body'])->id,session('id'),$id,$imagePath,$req->get('pictureMessage'),"PICTURE","POSTED",$_SERVER['REQUEST_TIME']);
                    return redirect('/pages/'.$id)->with('success', 'Posted successfuly');
                }else{
                    Error::addNewError(session('id'), $id, $response['httpCode'],$response['body']);
                    return redirect('/pages/'.$id)->with('warning', 'Failed to Post');
                }
            }else if($req->get('postTimeOptions') == "schedule"){
                $response = FacebookAPI::schedulePicture($access_token, $imagePath, $req->get('pictureMessage'), strtotime($req->get('scheduleTime')), $id);
                if($response['httpCode'] == 200){
                    Post::addNewPost(json_decode($response['body'])->id,session('id'),$id,$imagePath,$req->get('pictureMessage'),"PICTURE","SCHEDULED",strtotime($req->get('scheduleTime')));
                    return redirect('/pages/'.$id)->with('success', 'Posted successfuly');

                }else{
                    Error::addNewError(session('id'), $id, $response['httpCode'],$response['body']);
                    return redirect('/pages/'.$id)->with('warning', 'Failed to Post');
                }
            }

        }else 


        #video post
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
                    Post::addNewPost(json_decode($response['body'])->id,session('id'),$id,$videoPath,$req->get('videoDescription'),"VIDEO","POSTED",$_SERVER['REQUEST_TIME']);
                    return redirect('/pages/'.$id)->with('success', 'Posted successfuly');

                }else{
                    Error::addNewError(session('id'), $id, $response['httpCode'],$response['body']);
                    return redirect('/pages/'.$id)->with('warning', 'Failed to Post');
                }     
            }else if($req->get('postTimeOptions') == "schedule"){
                $response = FacebookAPI::scheduleVideo($access_token, $videoPath, $req->get('videoTitle'), $req->get('videoDescription'), strtotime($req->get('scheduleTime')), $id);
                if($response['httpCode'] == 200){
                    Post::addNewPost(json_decode($response['body'])->id,session('id'),$id,$videoPath,$req->get('videoDescription'),"VIDEO","SCHEDULED",strtotime($req->get('scheduleTime')));
                    return redirect('/pages/'.$id)->with('success', 'Posted successfuly');
                }else{
                    Error::addNewError(session('id'), $id, $response['httpCode'],$response['body']);
                    return redirect('/pages/'.$id)->with('warning', 'Failed to Post');
                }
            }
        }
        return redirect('/pages/'.$id)->with('warning','failed');
    }


    public function publishPost(Request $req, $id ,$post_id){
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

        # check if the user is the one posted this post
        $post = Post::where('page_id',$id)->where('post_id',$post_id)->where('user_id',session('id'))->get()->first();
        if($post){
            $postId = $post_id;
            if(!str_contains($postId,"_")) $postId = $id."_".$post_id;

            # check if post is published
            $response = FacebookAPI::isPublished($access_token,$postId);
            if($response['httpCode'] == 200){
                $res = json_decode($response['body']);
                if($res->is_published){
                    $post->post_share_time = $_SERVER['REQUEST_TIME'];
                    $post->save();
                    return redirect('/pages/'.$id)->with('message','post is already published');
                }else{
                    $response = FacebookAPI::publish($access_token, $postId);
                    if($response['httpCode'] == 200){
                        $post->post_share_time = $_SERVER['REQUEST_TIME'];
                        $post->save();
                        return redirect('/pages/'.$id)->with('success','published successfully');
                    }else{
                        return redirect('/pages/'.$id)->with('message','Failed to publish the post');
                    }
                }
            }else{
                return redirect('/pages/'.$id)->with('message','Failed to check if the post is published');
            }
        }else{
            return redirect('/pages')->with('message','access dinied');
        }
    }

    public function removePostConfirmation(Request $req, $page_id, $post_id){
        # check if user is loged in
        if(!LoginController::loggedIn($req)) return redirect('/login')->with('message',' please connect your facebook first');

        # check if the user is allowed to view this page
        $pagesData = FacebookAPI::getPages(session('access_token'));
        $pagesResults = json_decode($pagesData['body'])->data;
        $pagesContainId = false;
        $access_token = "";
        foreach($pagesResults as $page){
            if ($page->id == $page_id){
                $pagesContainId = true;
                $access_token = $page->access_token;
                break;
            }
        }
        if(!$pagesContainId) return redirect('/pages')->with('message','access denied');

        # form basic data
        $data = Util::prepareBasicData($req);

        # form post id
        $postId = $post_id;
        if(!str_contains($post_id, $page_id)) $postId = $page_id.'_'.$post_id;

        # get post
        $postIntern = Post::where('user_id',session('id'))->where('post_id',$post_id)->where('page_id',$page_id)->get()->first();
        $postOnFacebook = FacebookAPI::getPost($access_token, $postId);
        if($postIntern){
            if($postOnFacebook['httpCode']==200){
                $data['post'] = json_decode($postOnFacebook['body'])->message;
                $data['post_id'] = $post_id;
                $data['page_id'] = $page_id;
            }else{
                $postIntern->delete();
                return redirect('/pages/'.$page_id)->with('warning','couldn\'t fetch post from facebook');
            }
        }else{
            return redirect('/pages/'.$page_id)->with('warning','post not found');
        }

        return view('pages.remove')->with($data);
    }

    public static function  removePostHandler(Request $req,$page_id,$post_id){
        # check if user is loged in
        if(!LoginController::loggedIn($req)) return redirect('/login')->with('message',' please connect your facebook first');

        # check if the user is allowed to view this page
        $pagesData = FacebookAPI::getPages(session('access_token'));
        $pagesResults = json_decode($pagesData['body'])->data;
        $pagesContainId = false;
        $access_token = "";
        foreach($pagesResults as $page){
            if ($page->id == $page_id){
                $pagesContainId = true;
                $access_token = $page->access_token;
                break;
            }
        }
        if(!$pagesContainId) return redirect('/pages')->with('message','access denied');

        # form post id
        $postId = $post_id;
        if(!str_contains($post_id, $page_id)) $postId = $page_id.'_'.$post_id;

        # get post
        $postIntern = Post::where('user_id',session('id'))->where('post_id',$post_id)->where('page_id',$page_id)->get()->first();
        $postOnFacebook = FacebookAPI::getPost($access_token, $postId);
        if($postIntern){
            if($postOnFacebook['httpCode']==200){
                $postIntern->delete();
                $response = FacebookAPI::removePost($access_token, $postId);
                if($response['httpCode'] == 200){
                    return redirect('/pages/'.$page_id)->with('success','Post Deleted');
                }else{
                    return redirect('/pages/'.$page_id)->with('warning','post deleted from our database, but failed to delete from facebook');
                }
            }else{
                $postIntern->delete();
                return redirect('/pages/'.$page_id)->with('warning','couldn\'t fetch post from facebook');
            }
        }else{
            return redirect('/pages/'.$page_id)->with('warning','post not found');
        }
    }

    public static function editPost(Request $req, $page_id, $post_id){
        # check if user is loged in
        if(!LoginController::loggedIn($req)) return redirect('/login')->with('message',' please connect your facebook first');

        # check if the user is allowed to view this page
        $pagesData = FacebookAPI::getPages(session('access_token'));
        $pagesResults = json_decode($pagesData['body'])->data;
        $pagesContainId = false;
        $access_token = "";
        foreach($pagesResults as $page){
            if ($page->id == $page_id){
                $pagesContainId = true;
                $access_token = $page->access_token;
                break;
            }
        }
        if(!$pagesContainId) return redirect('/pages')->with('message','access denied');

        # form basic data
        $data = Util::prepareBasicData($req);

        $post = Post::where('post_id',$post_id)->first();
        if($post){
            $data['post'] = $post;
            $data['page_id'] = $page_id;
            $data['post_id'] = $post_id;
            if($post->post_type == "TEXT" || $post->post_type == "PICTURE"){
                # form post id
                $postId = $post_id;
                if(!str_contains($post_id, $page_id)) $postId = $page_id.'_'.$post_id;
                $data['facebookPost'] = json_decode(FacebookAPI::getPost($access_token, $postId)['body']);
            }else if($post->post_type == "VIDEO"){
                $data['facebookPost'] = json_decode(FacebookAPI::getVideo($access_token, $post_id)['body']);
            }

            return view('pages.edit')->with($data);
        }else{
            return redirect('/pages/'.$page_id);
        }


    }


    public static function editPostHandler(Request $req, $page_id, $post_id){
        # check if user is loged in
        if(!LoginController::loggedIn($req)) return redirect('/login')->with('message',' please connect your facebook first');

        # check if the user is allowed to view this page
        $pagesData = FacebookAPI::getPages(session('access_token'));
        $pagesResults = json_decode($pagesData['body'])->data;
        $pagesContainId = false;
        $access_token = "";
        foreach($pagesResults as $page){
            if ($page->id == $page_id){
                $pagesContainId = true;
                $access_token = $page->access_token;
                break;
            }
        }
        if(!$pagesContainId) return redirect('/pages')->with('message','access denied');

        # form basic data
        $data = Util::prepareBasicData($req);

        $post = Post::where('post_id',$post_id)->first();
        if($post){
            if($post->post_type == "TEXT" || $post->post_type == "PICTURE"){
                # form post id
                $postId = $post_id;
                if(!str_contains($post_id, $page_id)) $postId = $page_id.'_'.$post_id;
                $response = FacebookAPI::updatePost($access_token, $postId, $req->get('message'));
                if($response['httpCode'] == 200){
                    $post->post_message = $req->get('message');
                    $post->save();
                    return redirect('/pages/'.$page_id)->with('success','Post Updated');
                }else{
                    return redirect('/pages/'.$page_id)->with('warning','Failed To Updated');
                }

            }else if($post->post_type == "VIDEO"){
                $response = FacebookAPI::updateVideo($access_token, $post_id, $req->get('videoTitle'), $req->get('videoDescription'));
                if($response['httpCode'] == 200){
                    $post->post_message = $req->get('videoDescription');
                    $post->save();
                    return redirect('/pages/'.$page_id)->with('success','Post Updated');
                }else{
                    return redirect('/pages/'.$page_id)->with('warning','Failed To Updated');
                }
            }
        }else{
            return redirect('/pages/'.$page_id)->with('warning','Failed to fetch post');
        }
    }
}
