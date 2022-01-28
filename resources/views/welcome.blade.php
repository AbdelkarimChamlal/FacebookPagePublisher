
@extends('layouts.app')

@section('content')
<div class="container text-center">
    @if ($connected)
        <h1>Welcome {{session('name')}}</h1>
    @else
        <h1>Welcome to FPP tool</h1>
        <h5>to use this tool please connect your facebook account</h5>

        <div id="link-btn"><a href="/login">Get Started</a><br></div>

    @endif
</div>
@endsection