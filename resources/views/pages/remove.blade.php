@extends('../layouts.app')

@section('content')
<div class="container">
    <h3>Remove Post Confirmation</h3>
    <div class="gridContainer">
        <div>Post Message</div>
        <p id="removePost">{{$post}}</p>
    </div>
    <form action="/pages/{{$page_id}}/{{$post_id}}/remove" method="POST">
        <div class="gridContainer">
            <div>Confirm</div>
            <input class="btn btn-danger" type="submit" name="btn" value="REMOVE"/>
        </div>
    </form>
    <h5>*this action will lead to deleting the post from both our website and your facebook page</h5>
    <h5>**once you confirm, you can not undo this action</h5>




</div>
@endsection