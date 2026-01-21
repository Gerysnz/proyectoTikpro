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
    } catch (err) {
        document.getElementById('profile-data').innerHTML += '<div class="notification notification--error">Error de connexió.</div>';
    }
});

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
    const tagsCont = document.getElementById('profile-tags');
    if (!tags || tags.length === 0) {
        tagsCont.innerHTML += '<div class="notification notification--info">Sense etiquetes</div>';
        return;
    }
    let html = '';
    tags.forEach(tag => {
        html += `<div class="tag">${tag}</div>`;
    });
    tagsCont.innerHTML += html;
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
            <button class="btn-delete-project" data-project-id="${p.project_id}" data-project-title="${p.title}">🗑️ Eliminar</button>
        </div>`;
    });
    projCont.innerHTML += html;
    
    // Agregar event listeners para eliminar proyectos
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
