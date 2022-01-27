@extends('../layouts.app')

@section('content')
    @foreach($pages as $page)
        <a href="/pages/{{$page['id']}}">{{$page['name']}}</a>
    @endforeach
@endsection