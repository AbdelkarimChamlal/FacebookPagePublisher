@extends('../layouts.app')

@section('content')
<div class="container">
    <h3> CREATE NEW POST </h3>
    <form action="/pages/{{$id}}/create" method="POST" class="postCreator row" enctype="multipart/form-data">
        <div class="gridContainer">
            <div>
                POST TYPE
            </div>
            <div>
                <select id="postType" class="form-control" name="postOptions">
                    <option value="text" default>Text</option>
                    <option value="picture">Picture</option>
                    <option value="video">Video</option>
                </select>
            </div>
        </div>

        <div id="text-form" >
            <div class="gridContainer">
                <div>TEXT</div>
                <textarea name="textMessage" class="form-control" placeholder="Whats on your mind?"></textarea>
            </div>
        </div>
        <div id="picture-form" style="display:none;">
            <div class="gridContainer">
                <div>Picture Description</div>
                <textarea class="form-control" name="pictureMessage" placeholder="What does this picture capture?"></textarea><br>
            </div>
            <div class="gridContainer">
                <div>Picture file</div>
                <input type="file" name="image"/>
            </div>

        </div>
        <div id="video-form" style="display:none;">
            <div class="gridContainer">
                <div>Video Title</div>
                <input type="text" class="form-control" name = "videoTitle" placeholder="What should this video be called?"/><br>
            </div>
            <div class="gridContainer">
                <div>Video Description</div>
                <textarea class="form-control" name="videoDescription" placeholder="What does this video Capture?"></textarea><br>
            </div>
            <div class="gridContainer">
                <div>Video file</div>
                <input type="file"  name="video"/>
            </div>
        </div>
        <br>
        <div class="gridContainer">
            <div>Publish Date</div>
            <select id="postTime" class="form-control" name="postTimeOptions">
                <option value="now" default>Now</option>
                <option value="schedule">Schedule</option>
            </select>
        </div>

        <div id="schedule" style="display:none;">
            <div class="gridContainer">
                <div>Schedule Date</div>
                <input type="text" class="form-control" name="scheduleTime" placeholder="yyyy-mm-dd hh:mm"/>
            </div>
        </div>
        <input class="btn btn-primary" type="submit" value="send"/>
    </form>
</div>

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