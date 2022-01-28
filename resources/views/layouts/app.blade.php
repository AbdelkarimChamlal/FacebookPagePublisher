<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <title>{{config('app.name')}}</title>
</head>
<body>
    @include('layouts.nav')
    @if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    @foreach ($messages as $item)
        <div class="alert alert-warning" role="alert">
            {{$item}}
        </div>
    @endforeach
    @foreach ($warnings as $item)
        <div class="alert alert-warning" role="alert">
            {{$item}}
        </div>    
    @endforeach
    @foreach ($success as $item)
        <div class="alert alert-success" role="alert">
            {{$item}}
        </div>
    @endforeach

    @yield('content')
</body>
</html>