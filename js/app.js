document.addEventListener("DOMContentLoaded", function() {
    // Llamada fetch al endpoint PHP
    fetch('api/get_videos.php', {
        method: 'POST' // Asegurarse de que sea POST como en el PHP
    })
        .then(response => response.json())
        .then(videos => {
            if (!videos || videos.length === 0) {
                console.log("No hay videos disponibles");
                return;
            }

            // Tomamos el primer proyecto como ejemplo
            const project = videos[0];
            console.log("Proyecto cargado:", project);
            console.log("Ruta del video:", project.video_path);

            // Reemplazamos el div .video con un video real
            const videoDiv = document.querySelector('.card .video');
            videoDiv.innerHTML = `
                <video src="${project.video_path}" controls style="width: 100%; height: 100%; object-fit: cover;"></video>
            `;
        })
        .catch(err => {
            console.error("Error cargando videos:", err);
        });
});

