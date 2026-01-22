// profile.js - Carga y muestra los datos del perfil

document.addEventListener('DOMContentLoaded', async function() {
    try {
        const res = await fetch('api/get_profile.php');
        if (!res.ok) {
            document.getElementById('profile-data').innerHTML += '<div class="notification notification--error">Error: sessió expirada o no autoritzat.</div>';
            return;
        }
        const data = await res.json();
        cargarDatosUser(data.user);
        cargarCategorias(data.tags);
        cargarProjectes(data.projects);
        
        // Cargar todos los proyectos para poder editarlos
        await cargarTodosProjectes();
    } catch (err) {
        document.getElementById('profile-data').innerHTML += '<div class="notification notification--error">Error de connexió.</div>';
    }
});

async function cargarTodosProjectes() {
    try {
        const res = await fetch('api/get_all_projects.php');
        if (!res.ok) {
            return;
        }
        const projects = await res.json();
        mostrarTodosProjectes(projects);
    } catch (err) {
        console.error("Error cargando todos los proyectos:", err);
    }
}

function cargarDatosUser(user) {
    if (!user) return;
    const html = `
        <label>Nom</label>
        <input type="text" id="input-user_name" value="${user.user_name}">
        <label>Cognoms</label>
        <input type="text" id="input-user_surname" value="${user.user_surname}">
        <label>Nom entitat</label>
        <input type="text" id="input-entity_name" value="${user.entity_name}">
        <label>Email</label>
        <input type="email" id="input-email" value="${user.email}" readonly>
        <label>Telèfon</label>
        <input type="text" id="input-phone_number" value="${user.phone_number || ''}">
        <button class="btn" id="btn-guardar-profile">Guardar</button>
        <div id="profile-msg"></div>
    `;
    document.getElementById('profile-data').innerHTML += html;
    document.getElementById('btn-guardar-profile').onclick = guardarPerfil;
}

async function guardarPerfil() {
    const msg = document.getElementById('profile-msg');
    msg.innerHTML = '';
    const user_name = document.getElementById('input-user_name').value.trim();
    const user_surname = document.getElementById('input-user_surname').value.trim();
    const entity_name = document.getElementById('input-entity_name').value.trim();
    const email = document.getElementById('input-email').value.trim();
    const phone_number = document.getElementById('input-phone_number').value.trim();
    if (!user_name || !user_surname || !entity_name || !email) {
        msg.innerHTML = '<div class="notification notification--error">Tots els camps són obligatoris.</div>';
        return;
    }
    try {
        const res = await fetch('api/put_profile.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_name, user_surname, entity_name, email, phone_number })
        });
        const data = await res.json();
        if (data.success) {
            
            msg.innerHTML = '<div class="notification notification--info">Perfil actualitzat correctament.</div>';
            
        } else {
            msg.innerHTML = `<div class="notification notification--error">${data.error || 'Error actualitzant el perfil.'}</div>`;
        }
    } catch (err) {
        msg.innerHTML = '<div class="notification notification--error">Error de connexió.</div>';
    }
}

function cargarCategorias(tags) {
    if (!tags || tags.length === 0) {
        document.getElementById('profile-tags').innerHTML += '<div class="notification notification--info">Sense etiquetes</div>';
        return;
    }
    tags.forEach(tag => {
        if (window.addLabelToMenu) {
            window.addLabelToMenu(tag);
        }
    });
}

function cargarProjectes(projects) {
    const projCont = document.getElementById('profile-projects');
    if (!projects || projects.length === 0) {
        projCont.innerHTML += '<div class="notification notification--info">Sense projectes</div>';
        return;
    }
    let html = '';
    projects.forEach(p => {
        html += `
        <div class="project-card">
            <a href="project.php?id=${p.project_id}">
                <img src="${p.image_path ? p.image_path : 'default.png'}" alt="Logo projecte">
                <h3>${p.title}</h3>
            </a>
            <div class="project-card-actions">
                <button class="btn-edit-project" data-project-id="${p.project_id}">✏️ Editar</button>
                <button class="btn-delete-project" data-project-id="${p.project_id}" data-project-title="${p.title}">🗑️ Eliminar</button>
            </div>
        </div>`;
    });
    projCont.innerHTML += html;
    
    // Agregar event listeners para editar y eliminar proyectos
    document.querySelectorAll('.btn-edit-project').forEach(btn => {
        btn.addEventListener('click', handleEditProject);
    });
    document.querySelectorAll('.btn-delete-project').forEach(btn => {
        btn.addEventListener('click', handleDeleteProject);
    });
}

async function handleDeleteProject(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const projectId = this.dataset.projectId;
    const projectTitle = this.dataset.projectTitle;
    
    // Confirmar eliminación
    if (!confirm(`¿Estás seguro de que quieres eliminar el proyecto "${projectTitle}"?`)) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('project_id', projectId);
        
        const res = await fetch('api/delete_project.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await res.json();
        
        if (data.success) {
            // Recargar perfil
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'No se pudo eliminar el proyecto'));
        }
    } catch (err) {
        alert('Error de conexión');
        console.error(err);
    }
}
function mostrarTodosProjectes(projects) {
    // Crear sección si no existe
    let allProjectsSection = document.getElementById('all-projects-section');
    if (!allProjectsSection) {
        const profileProjects = document.getElementById('profile-projects');
        allProjectsSection = document.createElement('section');
        allProjectsSection.id = 'all-projects-section';
        allProjectsSection.className = 'projects';
        allProjectsSection.innerHTML = '<h2>Tots els projectes</h2>';
        profileProjects.parentNode.insertBefore(allProjectsSection, profileProjects.nextSibling);
    }

    if (!projects || projects.length === 0) {
        allProjectsSection.innerHTML += '<div class="notification notification--info">Sense projectes</div>';
        return;
    }

    let html = '';
    projects.forEach(p => {
        html += `
        <div class="project-card">
            <a href="project.php?id=${p.project_id}">
                <img src="${p.image_path ? p.image_path : 'default.png'}" alt="Logo projecte">
                <h3>${p.title}</h3>
            </a>
            <div class="project-card-actions">
                <button class="btn-edit-project" data-project-id="${p.project_id}">✏️ Editar</button>
            </div>
        </div>`;
    });
    
    allProjectsSection.innerHTML += html;
    
    // Agregar event listeners para botones editar
    allProjectsSection.querySelectorAll('.btn-edit-project').forEach(btn => {
        btn.addEventListener('click', handleEditProject);
    });
}

async function handleEditProject(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const projectId = this.dataset.projectId;
    
    try {
        // Verificar si es propietario antes de navegar
        const res = await fetch(`api/get_project.php?project_id=${projectId}`);
        
        if (!res.ok) {
            const data = await res.json();
            // Mostrar notificación de error igual que en discover.php
            showErrorNotificationProfile(data.error || 'Error cargando el proyecto');
            return;
        }
        
        // Si es propietario, navegar a edit_project.php
        window.location.href = `edit_project.php?project_id=${projectId}`;
    } catch (err) {
        console.error("Error verificando proyecto:", err);
    }
}

function showErrorNotificationProfile(message) {
    const notification = document.createElement('div');
    notification.className = 'like-notification';
    notification.innerHTML = `
        <span class="span-xat" style="color: #e74c3c;">${message}</span>
        <button class="close-notification" title="Tancar">&times;</button>
    `;

    // Calcular posición vertical basada en notificaciones existentes
    const infoDiv = document.querySelector(".info");
    if (!infoDiv) {
        // Si no existe .info, crear uno o usar body
        const tempDiv = document.createElement('div');
        tempDiv.className = 'info';
        document.body.appendChild(tempDiv);
    }

    const existing = document.querySelectorAll('.like-notification');
    const offset = existing.length * 24; 
    notification.style.top = (70 + offset) + 'px';

    const infoElement = document.querySelector(".info") || document.body;
    infoElement.appendChild(notification);

    notification.querySelector('.close-notification').onclick = () => {
        notification.remove();
        // Re-stack
        const notifs = document.querySelectorAll('.like-notification');
        notifs.forEach((notif, i) => {
            notif.style.top = (70 + i * 24) + 'px';
        });
    };
}