$(document).ready(function () {
    // Llamamos al endpoint que devuelve los videos
    $.ajax({
        url: "api/get_videos.php",
        type: "GET",
        dataType: "json",
        success: function(videos) {
            if(videos.length === 0) return;

            // Tomamos el primer proyecto como ejemplo
            const project = videos[0];

            // Reemplazamos el div .video con un video real
            $('.card .video').html(`
                <video src="${project.video_path}" autoplay muted loop playsinline></video>
            `);
        },
        error: function(err) {
            console.error("Error cargando videos:", err);
        }
    });
});
