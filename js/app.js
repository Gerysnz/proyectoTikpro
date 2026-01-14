let videos = [];
let actual = 0;

document.addEventListener("DOMContentLoaded", function() {
    // Llamada fetch al endpoint PHP
    fetch('./api/get_videos.php', {
        method: 'POST' // Asegurarse de que sea POST como en el PHP
    })
        .then(response => response.json())
        .then(data => {
            videos = data;
            if (!videos || videos.length === 0) {
                console.log("No hay videos disponibles");
                return;
            }
            renderProyecto(actual);
        })
        .catch(err => {
            console.error("Error cargando videos:", err);
        });
});

function renderProyecto(idx) {
    const p = videos[idx];
    if (!p) return;
    const cont = document.querySelector('.card');
    cont.innerHTML = `
        <video class="video" src="${p.video_path}" controls style="width: 100%; height: 100%; object-fit: cover;"></video>

        <div class="details">
            <h2>${p.title}</h2>
            <p style="display:none;">${p.description}</p>
        </div>

        <div class="actions">
            <button class="nope" id="btn-nope">Dislike</button>
            <button class="like" id="btn-like">Like</button>
            <button id="btn-detalles">Detalls</button>
        </div>

        <div class="footer">
            <span>Perfil</span>
            <span>Converses</span>
            <span>Detalls</span>
        </div>
    `;

    // Resetear estilos después de la animación
    cont.style.opacity = '1';
    cont.style.transform = 'translateX(0)';
    cont.style.transition = 'opacity 0.5s, transform 0.5s';

    document.getElementById('btn-like').onclick = () => animarCard('like');
    document.getElementById('btn-nope').onclick = () => animarCard('dislike');
    document.getElementById('btn-detalles').onclick = () => toggleDetalles();
}

function animarCard(tipo) {
    const card = document.querySelector('.card');
    const p = videos[actual];
    card.style.transition = 'opacity 0.5s, transform 0.5s';
    card.style.opacity = '0';
    card.style.transform = tipo === 'like' ? 'translateX(100px)' : 'translateX(-100px)';
    // Aqui debe ejecutarse el logAction antes de cambiar el proyecto
    logAction(tipo, p.id);
    setTimeout(() => {
        actual++;
        if (actual >= videos.length) actual = 0; // Loop videos
        renderProyecto(actual);
    }, 500);
}

function toggleDetalles() {
    const desc = document.querySelector('.details p');
    desc.style.display = desc.style.display === 'none' ? 'block' : 'none';
}

async function logAction(action, proyectoId) {
  try {
    await fetch('./api/log_action.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action, proyectoId })
    });
  } catch (error) {
    console.error('Error logging action:', error);
  }
}

