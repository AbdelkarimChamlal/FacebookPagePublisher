@extends('../layouts.app')

@section('content')
<div class="container">
    <h3>Connected Pages</h3>
    <table class="table table-sm">
        <thead>
            <tr>
                <th>Page Name</th>
                <th>Posts</th>
            </tr>
        </thead>
        <tbody>

        @foreach($pages as $page)
        <tr>
            <td>{{$page['name']}}</td>
            <td><a href="/pages/{{$page['id']}}">view</a></td>
        </tr>
        @endforeach
        </tbody>
    </table>
    
</div>
@endsection