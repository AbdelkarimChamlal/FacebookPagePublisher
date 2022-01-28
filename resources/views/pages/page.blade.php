@extends('../layouts.app')

@section('content')
<div class="container">
    <div id="link-btn"><a href="/pages/{{$id}}/create">Create new post</a><br></div>

    <h3>Posts </h3>
    <table class="table table-sm">
        <thead>
            <tr>
                <th>Post</th>
                <th>Type</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($posts as $post)
                <tr>
                    <td>{{$post->post_message}}</td>
                    <td>{{$post->post_type}}</td>
                    <td>{{date('m/d/Y H:i',$post->post_share_time)}}</td>
                    <td><a href="https://facebook.com/{{$post->post_id}}">view</a></td>
                    @if($_SERVER['REQUEST_TIME'] < $post->post_share_time)
                        <td><a href="/pages/{{$id}}/{{$post->post_id}}/publish">publish now</a></td>
                    @endif
                    <td><a href="/pages/{{$id}}/{{$post->post_id}}/edit">edit</a></td>
                    <td><a href="/pages/{{$id}}/{{$post->post_id}}/remove">remove</a></td>


                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection