<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public static function addNewPost($post_id, $user_id, $page_id, $media, $post_message, $post_type, $post_share_type, $post_share_time){
        $post = new Post();

        $post->post_id = $post_id;
        $post->user_id = $user_id;
        $post->page_id = $page_id;
        $post->post_media = $media;
        $post->post_message = $post_message;
        $post->post_type = $post_type;
        $post->post_share_type = $post_share_type;
        $post->post_share_time = $post_share_time;

        $post->save();
    }
}
