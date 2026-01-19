let videos = [];
let actual = 0;

document.addEventListener("DOMContentLoaded", function() {
    // Llamada fetch al endpoint PHP
    fetch('./api/get_videos.php', {
        method: 'POST'
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

    fetch('./api/get_labels.php', {
        method: 'POST'
    })
        .then(response => response.json())
        .then(labelsData => {
            console.log("Etiquetas cargadas:", labelsData);
        })
        .catch(err => {
            console.error("Error cargando etiquetas:", err);
        });
});

function renderProyecto(idx) {
    const p = videos[idx];
    if (!p) return;
    const cont = document.querySelector('.card');

    // Construimos el HTML de las categorías si existen
    let labelsHtml = '';
    if (p.categories && Array.isArray(p.categories) && p.categories.length > 0) {
        labelsHtml = '<div class="tags">';
        p.categories.forEach(cat => {
            labelsHtml += `<span class=\"tag\">${cat}</span>`;
        });
        labelsHtml += '</div>';
    }

    cont.innerHTML = `
        <video class="video" src="${p.video_path}" controls playsinline></video>
        <div class="details">
            <h2>${p.title}</h2>
            <div class="desc" style="display:none;">
                <p>${p.description}</p>
                ${labelsHtml}
            </div>
        </div>
        <div class="actions">
            <button class="nope" id="btn-nope">No m'interesa</button>
            <button class="like" id="btn-like">M'agrada</button>
        </div>
        <div class="footer">
            <span>Perfil</span>
            <span>Converses</span>
            <span id="footer-detalls" style="cursor:pointer;">Detalls</span>
        </div>
    `;

    // Resetear estilos después de la animación
    cont.style.opacity = '1';
    cont.style.transform = 'translateX(0)';
    cont.style.transition = 'opacity 0.5s, transform 0.5s';

    document.getElementById('btn-like').onclick = () => animarCard('like');
    document.getElementById('btn-nope').onclick = () => animarCard('nope');
    document.getElementById('footer-detalls').onclick = () => toggleDetalles();
}

function animarCard(tipo) {
    const card = document.querySelector('.card');
    const p = videos[actual];
    card.style.transition = 'opacity 0.5s, transform 0.5s';
    card.style.opacity = '0';
    card.style.transform = tipo === 'like' ? 'translateX(100px)' : 'translateX(-100px)';
    logAction(tipo, p.project_id);
    if (tipo === 'like') {
        showLikeNotification();
    }
    setTimeout(() => {
        actual++;
        if (actual >= videos.length) actual = 0; // Loop videos
        renderProyecto(actual);
    }, 500);
}

function toggleDetalles() {
    const desc = document.querySelector('.details .desc');
    if (desc) {
        desc.style.display = desc.style.display === 'none' ? 'block' : 'none';
    }
}

async function logAction(action, proyectoId) {
    try {
        await fetch('./api/like_project.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action, proyectoId })
        });
    } catch (error) {
        console.error('Error logging action:', error);
    }
}

function showLikeNotification() {
    const notification = document.createElement('div');
    notification.className = 'like-notification';
    notification.innerHTML = `
        <span class="span-xat">Anar al Xat</span>
        <button class="go-to-chat">Anar</button>
        <button class="close-notification" title="Tancar">&times;</button>
    `;

    document.querySelector(".info").appendChild(notification);

    notification.querySelector('.go-to-chat').onclick = () => {
        window.location.href = 'chat.php';
    };

    notification.querySelector('.close-notification').onclick = () => {
        notification.remove();
    };
}