document.addEventListener("DOMContentLoaded", function() {
    // Llamada fetch al endpoint PHP
    fetch('api/get_videos.php')
        .then(response => response.json())
        .then(videos => {
            if (!videos || videos.length === 0) return;

            // Tomamos el primer proyecto como ejemplo
            const project = videos[0];

            // Reemplazamos el div .video con un video real
            const videoDiv = document.querySelector('.card .video');
            videoDiv.innerHTML = `
                <video src="${project.video_path}" autoplay muted loop playsinline></video>
            `;
        })
        .catch(err => {
            console.error("Error cargando videos:", err);
        });
});

