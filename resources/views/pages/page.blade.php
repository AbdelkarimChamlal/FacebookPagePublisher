@extends('../layouts.app')

@section('content')
    <a href="/pages/{{$id}}/create">add new post</a><br>

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
                        <td><a href="/pages/publish/{{$post->post_id}}">publish now</a></td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection