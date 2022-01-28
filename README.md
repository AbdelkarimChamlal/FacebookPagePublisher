
# Welcome to FacebookPagePublisher!

Hi! There, Welcome to FacebookPagePublisher project.
FacebookPagePublisher its a web tool developed using Laravel and its objective is to make management of Facebook pages easier.

check this [video](https://youtu.be/6NbnvlIAiHA) to get a good understanding on the features offered by the tool 


# How to use it?

 1. you will need to create a  [Facebook App](https://developers.facebook.com/), and acquire a App id and an App Secret.
 2. Next thing is to activate [Login with Facebook](https://developers.facebook.com/products/facebook-login/) feature for your Facebook App.
 3. Acquire a SSL certificate to be able to use login with Facebook feature.
 4. Configure .env.example and rename it to .env
```
    FACEBOOK_APP_ID=app_id
    FACEBOOK_APP_SECRET=app_secret
    FACEBOOK_APP_PERMISSIONS=public_profile,email,pages_manage_posts,pages_show_list,pages_read_engagement
    FACEBOOK_APP_CALL_BACK=https://example.com/callback/
    FACEBOOK_APP_API_VERSION=v12.0
    STORAGE_FOLDER=C:/xampp/htdocs/LaravelProject/storage/app/
```
that is all you need to use this tool.
# Note
the callback link in the env file should be listed in your Facebook login feature as a callback.

See you in another project.
