$(document).ready(function() {
    $.ajax({
        url: "api/get_videos.php",
        type: "GET",
        dataType: "json",
        success: function (videos) {
            videos.forEach(video => {
                $(".video").append(`
                    <div class="video-card">
                        <video src="${video.video_path}" autoplay muted loop></video>
                    </div>
                `);
            });
        }
    })
})