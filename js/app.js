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
                mostrarFinal();
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
    if (!p) {
        mostrarFinal();
        return;
    }

    const cont = document.querySelector('.card');

    // Construimos el HTML de las categorías si existen
    let labelsHtml = '';
    if (p.categories && Array.isArray(p.categories) && p.categories.length > 0) {
        labelsHtml = '<div class="tags">';
        p.categories.forEach(cat => {
            labelsHtml += `<span class="tag">${cat}</span>`;
        });
        labelsHtml += '</div>';
    }

    // Icono de "possible match" si hay coincidencia
    let matchIconHtml = '';
    if (p.has_match) {
        matchIconHtml = `<div class="match-badge" title="Coincideix amb els teus cicles">
            <span class="match-icon">✓</span>
            <span class="match-text">Possible match</span>
        </div>`;
    }

    cont.style.display = 'block';

    // Si ya fue likeado, mostrar solo botón "Següent" y corazón arriba a la derecha
    if (p.is_liked) {
        cont.innerHTML = `
            ${matchIconHtml}
            <video class="video" src="${p.video_path}" controls playsinline></video>
            <span class="heart-icon-liked" title="Ja t'ha agradat">❤️</span>
            <div class="details">
                <h2>${p.title}</h2>
                <div class="desc" style="display:none;">
                    <p>${p.description}</p>
                    ${labelsHtml}
                </div>
            </div>
            <div class="actions">
                <button class="next-button" id="btn-next">Següent</button>
            </div>
            <div class="footer">
                <span id="footer-perfil">Perfil 👤</span>
                <span>Converses 💬</span>
                <span id="footer-detalls">Detalls ℹ️</span>
            </div>
        `;
    } else {
        cont.innerHTML = `
            ${matchIconHtml}
            <video class="video" src="${p.video_path}" controls playsinline></video>
            <div class="details">
                <h2>${p.title}</h2>
                <div class="desc" style="display:none;">
                    <p>${p.description}</p>
                    ${labelsHtml}
                </div>
            </div>
            <div class="actions">
                <button class="nope" id="btn-nope">No m'interessa</button>
                <button class="like" id="btn-like">M'agrada</button>
            </div>
            <div class="footer">
                <span id="footer-perfil">Perfil 👤</span>
                <span>Converses 💬</span>
                <span id="footer-detalls">Detalls ℹ️</span>
            </div>
        `;
    }

    // Resetear estilos después de la animación
    cont.style.opacity = '1';
    cont.style.transform = 'translateX(0)';
    cont.style.transition = 'opacity 0.5s, transform 0.5s';

    // Event listeners
    if (p.is_liked) {
        document.getElementById('btn-next').onclick = () => animarCard('next');
    } else {
        document.getElementById('btn-like').onclick = () => animarCard('like');
        document.getElementById('btn-nope').onclick = () => animarCard('nope');
    }
    
    document.getElementById('footer-detalls').onclick = () => toggleDetalles();
    document.getElementById('footer-perfil').onclick = () => {
        window.location.href = 'profile.php';
    };
}

function animarCard(tipo) {
    const card = document.querySelector('.card');
    const p = videos[actual];

    card.style.transition = 'opacity 0.5s, transform 0.5s';
    card.style.opacity = '0';
    card.style.transform = tipo === 'like'
        ? 'translateX(100px)'
        : (tipo === 'next' ? 'translateX(0px)' : 'translateX(-100px)');

    if (tipo === 'like') {
        showLikeNotification();
        logAction(tipo, p.project_id);
    } else if (tipo === 'nope') {
        logAction(tipo, p.project_id);
    } else if (tipo === 'next') {
        // No loggear como acción, solo pasar al siguiente
    }

    setTimeout(() => {
        actual++;

        if (actual >= videos.length) {
            mostrarFinal();
            return;
        }

        renderProyecto(actual);
    }, 500);
}

function mostrarFinal() {
    const card = document.querySelector('.card');
    if (card) card.style.display = 'none';

    let endMessage = document.getElementById('end-message');

    if (!endMessage) {
        endMessage = document.createElement('div');
        endMessage.id = 'end-message';
        endMessage.style.textAlign = 'center';
        endMessage.style.marginTop = '40px';

        endMessage.innerHTML = `
            <p>No hi ha més videos per mostrar</p>
            <button onclick="location.reload()">Tornar a carregar</button>
        `;

        document.querySelector('main').appendChild(endMessage);
    }
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

async function verificarYEditarProyecto(projectId) {
    try {
        const res = await fetch(`api/get_project.php?project_id=${projectId}`);
        
        if (!res.ok) {
            const data = await res.json();
            showErrorNotification(data.error || 'Error cargando el proyecto');
            return;
        }
        
        // Si es propietario, navegar a edit_project.php
        window.location.href = `edit_project.php?project_id=${projectId}`;
    } catch (err) {
        console.error("Error verificando proyecto:", err);
        showErrorNotification('Error al verificar el proyecto');
    }
}

function showErrorNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'like-notification';
    notification.innerHTML = `
        <span class="span-xat" style="color: #e74c3c;">${message}</span>
        <button class="close-notification" title="Tancar">&times;</button>
    `;

    // Calcular posición vertical basada en notificaciones existentes
    const existing = document.querySelectorAll('.like-notification');
    const offset = existing.length * 24; 
    notification.style.top = (70 + offset) + 'px';

    document.querySelector(".info").appendChild(notification);

    notification.querySelector('.close-notification').onclick = () => {
        notification.remove();
        // Re-stack
        const notifs = document.querySelectorAll('.like-notification');
        notifs.forEach((notif, i) => {
            notif.style.top = (70 + i * 24) + 'px';
        });
    };
}

function showLikeNotification() {
    const notification = document.createElement('div');
    notification.className = 'like-notification';
    notification.innerHTML = `
        <span class="span-xat">Anar al Xat</span>
        <button class="go-to-chat">Anar</button>
        <button class="close-notification" title="Tancar">&times;</button>
    `;

    // Calcular posición vertical basada en notificaciones existentes, para apilarlas y q se noten
    const existing = document.querySelectorAll('.like-notification');
    const offset = existing.length * 24; 
    notification.style.top = (70 + offset) + 'px';

    document.querySelector(".info").appendChild(notification);

    notification.querySelector('.go-to-chat').onclick = () => {
        window.location.href = 'chat.php';
    };

    notification.querySelector('.close-notification').onclick = () => {
        
        notification.remove();
        // Re-stack, osea ajustar posiciones de las notificaciones restantes
        const notifs = document.querySelectorAll('.like-notification');
        notifs.forEach((notif, i) => {
            notif.style.top = (70 + i * 24) + 'px';
        });
    };
}
