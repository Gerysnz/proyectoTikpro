// Modal de videos para admin-projects
const videoModal = document.getElementById('videoModal');
const modalVideo = document.getElementById('modalVideo');
const videoSource = document.getElementById('videoSource');
const closeBtn = document.querySelector('.video-modal-close');
const videoBtns = document.querySelectorAll('.video-btn');

if (videoBtns.length > 0) {
    // Abrir modal con video
    videoBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const videoPath = this.dataset.video;
            
            console.log('Video path:', videoPath);
            
            // Cargar video solo cuando se hace click
            videoSource.src = videoPath;
            modalVideo.load();
            videoModal.style.display = 'flex';
        });
    });

    // Cerrar modal
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            videoModal.style.display = 'none';
            modalVideo.pause();
            videoSource.src = '';
        });
    }

    // Cerrar modal al hacer click fuera del contenido
    window.addEventListener('click', function(e) {
        if (e.target === videoModal) {
            videoModal.style.display = 'none';
            modalVideo.pause();
            videoSource.src = '';
        }
    });
}
