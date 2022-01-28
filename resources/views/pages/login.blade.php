@extends('../layouts.app')

@section('content')
<div class="container text-center">
    <h3>Connect Your Facebook</h3>
    <p>to use this tool, please connect your facebook account and provide this tool with the requested premessions</p>
   
    <div id="link-btn"> <a href={{$authLink}}>Connect to Facebook</a><br></div>

</div>
@endsection