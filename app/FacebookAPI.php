<?php

namespace App;
use HTTP_Request2;

class FacebookAPI
{
    # provides a 0auth2 redirect link to get access token
    public static function getAuthLink($app_id, $api_version, $callback_url, $permissions){
        return "https://www.facebook.com/$api_version/dialog/oauth?client_id=$app_id&response_type=code&redirect_uri=$callback_url&scope=$permissions";
    }

    # exchanges code provided by step 1 in 0auth2 for a long lived access_token
    public static function ecxhangeShortToken($app_id, $app_secret, $api_version, $callback_url, $code){
        $url = "https://graph.facebook.com/$api_version/oauth/access_token?client_id=$app_id&redirect_uri=$callback_url&client_secret=$app_secret&code=$code";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $data = ['body'=>$head, 'httpCode'=>$httpCode];
        return $data;
    }

    # checks if an access token is valid
    public static function validateToken($access_token){
        $ch = curl_init();
        $url = "https://graph.facebook.com/me?fields=name,id,email&access_token=$access_token";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $httpCode==200;
    }

    # gets all the pages that provided user provided access to.
    public static function getPages($access_token){
        $url = "https://graph.facebook.com/me/accounts?access_token=$access_token";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $data = ['body'=>$head, 'httpCode'=>$httpCode];
        return $data;
    }

    # get the current user details 
    public static function getUserDetails($access_token){
        $url = "https://graph.facebook.com/me?fields=name,id,email&access_token=$access_token";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $data = ['body'=>$head, 'httpCode'=>$httpCode];
        return $data;
    }

    # post a message to a facebook page
    public static function postMessage($access_token, $message, $page_id){
        $url ="https://graph.facebook.com/$page_id/feed?message=$message&access_token=$access_token";
        $request = new HTTP_Request2();
        $request->setUrl($url);
        $request->setMethod(HTTP_Request2::METHOD_POST);
        $request->setConfig(array(
            'follow_redirects' => TRUE
        ));
        $response = $request->send();
        $data = ['body'=>$response->getBody(), 'httpCode'=>$response->getStatus()];
        return $data;
    }

    # schedule a post to a facebook page
    public static function scheduleMessage($access_token, $message, $schedule_time, $page_id){
        $url ="https://graph.facebook.com/$page_id/feed?published=false&message=$message&scheduled_publish_time=$schedule_time&access_token=$access_token";
        $request = new HTTP_Request2();
        $request->setUrl($url);
        $request->setMethod(HTTP_Request2::METHOD_POST);
        $request->setConfig(array(
            'follow_redirects' => TRUE
        ));
        $response = $request->send();
        $data = ['body'=>$response->getBody(), 'httpCode'=>$response->getStatus()];
        return $data;
    }

    # post a picture to a facebook page
    public static function postPicture($access_token, $image_path, $message, $page_id){
        $request = new HTTP_Request2();
        $url = "https://graph.facebook.com/v12.0/$page_id/photos?message=$message&access_token=$access_token";
        $request->setUrl($url);
        $request->setMethod(HTTP_Request2::METHOD_POST);
        $request->setConfig(array(
          'follow_redirects' => TRUE
        ));
        $request->addUpload('source', $image_path, $image_path, '<Content-Type Header>');
        $response = $request->send();
        $data = ['body'=>$response->getBody(), 'httpCode'=>$response->getStatus()];
        return $data;
    }

    # schedule a picture to a facebook page
    public static function schedulePicture($access_token, $image_path, $message, $schedule_time, $page_id){
        $request = new HTTP_Request2();
        $url = "https://graph.facebook.com/v12.0/$page_id/photos?published=false&message=$message&scheduled_publish_time=$schedule_time&access_token=$access_token";
        $request->setUrl($url);
        $request->setMethod(HTTP_Request2::METHOD_POST);
        $request->setConfig(array(
            'follow_redirects' => TRUE
        ));
        $request->addUpload('source', $image_path, $image_path, '<Content-Type Header>');
        $response = $request->send();
        $data = ['body'=>$response->getBody(), 'httpCode'=>$response->getStatus()];
        return $data;
    }

    # post a video to a facebook page
    public static function postVideo($access_token, $video_path, $video_title, $video_description, $page_id){
        $request = new HTTP_Request2();
        $url = "https://graph-video.facebook.com/v12.0/$page_id/videos?title=$video_title&description=$video_description&access_token=$access_token";
        $request->setUrl($url);
        $request->setMethod(HTTP_Request2::METHOD_POST);
        $request->setConfig(array(
          'follow_redirects' => TRUE
        ));
        $request->addUpload('source', $video_path, $video_path, '<Content-Type Header>');
        $response = $request->send();
        $data = ['body'=>$response->getBody(), 'httpCode'=>$response->getStatus()];
        return $data;
    }

    # schedule a video to a facebook page
    public static function scheduleVideo($access_token, $video_path, $video_title, $video_description, $schedule_time, $page_id){
        $request = new HTTP_Request2();
        $url = "https://graph-video.facebook.com/v12.0/$page_id/videos?published=false&title=$video_title&description=$video_description&scheduled_publish_time=$schedule_time&access_token=$access_token";
        $request->setUrl($url);
        $request->setMethod(HTTP_Request2::METHOD_POST);
        $request->setConfig(array(
            'follow_redirects' => TRUE
        ));
        $request->addUpload('source', $video_path, $video_path, '<Content-Type Header>');
        $response = $request->send();
        $data = ['body'=>$response->getBody(), 'httpCode'=>$response->getStatus()];
        return $data;
    }

    # gets publish state.
    public static function isPublished($access_token, $post_id){
        $url = "https://graph.facebook.com/$post_id?fields=is_published&access_token=$access_token";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $data = ['body'=>$head, 'httpCode'=>$httpCode];
        return $data;
    }

    # publish a post.
    public static function publish($access_token, $post_id){
        $url = "https://graph.facebook.com/$post_id?is_published=true&access_token=$access_token";
        $request = new HTTP_Request2();
        $request->setUrl($url);
        $request->setMethod(HTTP_Request2::METHOD_POST);
        $request->setConfig(array(
          'follow_redirects' => TRUE
        ));

        $response = $request->send();
        $data = ['body'=>$response->getBody(), 'httpCode'=>$response->getStatus()];
        return $data;
    }

    # get post 
    public static function getPost($access_token, $post_id){
        $url = "https://graph.facebook.com/$post_id?access_token=$access_token";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $head = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $data = ['body'=>$head, 'httpCode'=>$httpCode];
        return $data;
    }

    # update a video post 
    public static function getVideo($access_token, $post_id){
        $url = "https://graph.facebook.com/$post_id?fields=title,description&access_token=$access_token";
        $request = new HTTP_Request2();
        $request->setUrl($url);
        $request->setMethod(HTTP_Request2::METHOD_GET);
        $request->setConfig(array(
        'follow_redirects' => TRUE
        ));
        $response = $request->send();
        $data = ['body'=>$response->getBody(), 'httpCode'=>$response->getStatus()];
        return $data;
    }

    # remove a post from a facebook page
    public static function removePost($access_token, $post_id){
        $url = "https://graph.facebook.com/$post_id?access_token=$access_token";
        $request = new HTTP_Request2();
        $request->setUrl($url);
        $request->setMethod(HTTP_Request2::METHOD_DELETE);
        $request->setConfig(array(
        'follow_redirects' => TRUE
        ));
        $response = $request->send();
        $data = ['body'=>$response->getBody(), 'httpCode'=>$response->getStatus()];
        return $data;
    }

    # update a post message (used for text and pictures)
    public static function updatePost($access_token, $post_id, $new_message){
        $url = "https://graph.facebook.com/$post_id?message=$new_message&access_token=$access_token";
        $request = new HTTP_Request2();
        $request->setUrl($url);
        $request->setMethod(HTTP_Request2::METHOD_POST);
        $request->setConfig(array(
        'follow_redirects' => TRUE
        ));
        $response = $request->send();
        $data = ['body'=>$response->getBody(), 'httpCode'=>$response->getStatus()];
        return $data;
    }

    # update a video post 
    public static function updateVideo($access_token, $post_id, $video_title, $video_description){
        $url = "https://graph.facebook.com/$post_id?title=$video_title&description=$video_description&access_token=$access_token";
        $request = new HTTP_Request2();
        $request->setUrl($url);
        $request->setMethod(HTTP_Request2::METHOD_POST);
        $request->setConfig(array(
        'follow_redirects' => TRUE
        ));
        $response = $request->send();
        $data = ['body'=>$response->getBody(), 'httpCode'=>$response->getStatus()];
        return $data;
    }
}
