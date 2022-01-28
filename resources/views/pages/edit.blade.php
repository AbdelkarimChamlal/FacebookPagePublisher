@extends('../layouts.app')

@section('content')
<div class="container">
    <h3> EDIT POST </h3>
    <form action="/pages/{{$page_id}}/{{$post_id}}/edit" method="POST" >
        @if ($post->post_type == "TEXT" || $post->post_type == "PICTURE")
            <div class="gridContainer">
                <div>POST</div>
                <textarea name="message" class="form-control" placeholder="Whats on your mind?">{{$facebookPost->message}}</textarea>
            </div>
        @elseif ($post->post_type == "VIDEO")
            <div class="gridContainer">
                <div>Video Title</div>
                <input type="text" class="form-control" name = "videoTitle" value="{{$facebookPost->title}}"/><br>
            </div>
            <div class="gridContainer">
                <div>Video Description</div>
                <textarea class="form-control" name="videoDescription" >{{$facebookPost->description}}</textarea><br>
            </div>
        @endif
        <input class="btn btn-primary" type="submit" value="UPDATE"/>
    </form>
</div>


@endsection