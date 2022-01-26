
@extends('layouts.app')

@section('content')
    @if ($logedIn)
        <h1>Welcome {{session('name')}}</h1>
    @else
        <h1>Welcome to FPP tool</h1>
        <h5>to use this tool please connect your facebook account</h5>
        <a href="/login">connect</a>
    @endif
@endsection