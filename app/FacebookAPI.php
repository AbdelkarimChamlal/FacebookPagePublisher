<?php

namespace App;
class FacebookAPI
{
    public function getAuthLink($app_id, $api_version, $callback_url, $permissions){
        return "https://www.facebook.com/$api_version/dialog/oauth?client_id=$app_id&response_type=code&redirect_uri=$callback_url&scope=$permissions";
    }
}
