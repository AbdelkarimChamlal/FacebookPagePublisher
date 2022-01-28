@if($connected)
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">FPP</a>
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
          <li><a href="/pages">Pages</a></li>
          <li><a href="#">Account</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li><a href="#">{{session('name')}}</a></li>
            <li><a href="/logout">Sign out</a></li>
        </ul>
      </div>
    </div>
  </nav>
@else
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">FPP</a>
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
          <li><a href="/login">Connect</a></li>
        </ul>
      </div>
    </div>
  </nav>
@endif