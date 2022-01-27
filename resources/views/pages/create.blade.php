@extends('../layouts.app')

@section('content')
    @if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <h3> create new post </h3>
    <form action="/pages/{{$id}}/create" method="POST" enctype="multipart/form-data">
        <select id="postType" name="postOptions">
            <option value="text" default>Text</option>
            <option value="picture">Picture</option>
            <option value="video">Video</option>
        </select>
        <div id="text-form">
            <textarea name="textMessage" placeholder="post content"></textarea>
        </div>
        <div id="picture-form" style="display:none;">
            <textarea name="pictureMessage" placeholder="post content"></textarea><br>
            <input type="file" name="image"/>
        </div>
        <div id="video-form" style="display:none;">
            <input type="text" name = "videoTitle" placeholder="video title"/><br>
            <textarea name="videoDescription" placeholder="video Description"></textarea><br>
            <input type="file" name="video"/>
        </div>
        <select id="postTime" name="postTimeOptions">
            <option value="now" default>Now</option>
            <option value="schedule">Schedule</option>
        </select>
        <div id="schedule" style="display:none;">
            <input type="text" name="scheduleTime" placeholder="yyyy-mm-dd hh:mm"/>
        </div>
        <input type="submit" value="send"/>
    </form>


    <script>
        var postTypes = document.getElementById("postType");
        var textForm = document.getElementById("text-form");
        var pictureForm = document.getElementById("picture-form");
        var videoForm = document.getElementById("video-form");
        var postTime = document.getElementById("postTime");
        var schedule = document.getElementById("schedule");


        postTypes.addEventListener("change", function() {
            if(postTypes.value == "text")
            {
                console.log("text is selected");
                pictureForm.style.display = "none";
                videoForm.style.display = "none";
                textForm.style.display = "block";
            }else if(postTypes.value == "picture"){
                console.log("picture is selected");
                pictureForm.style.display = "block";
                videoForm.style.display = "none";
                textForm.style.display = "none";
            }else if(postTypes.value == "video"){
                console.log("video is selected");
                pictureForm.style.display = "none";
                videoForm.style.display = "block";
                textForm.style.display = "none";
            }
        });

        postTime.addEventListener("change", function() {
            if(postTime.value == "schedule"){
                schedule.style.display = "block";
            }else if(postTime.value == "now"){
                schedule.style.display = "none";
            }
        });

    </script>
@endsection