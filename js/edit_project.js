// edit_project.js - Manejo del formulario de creación de proyecto

let allCategories = [];
let selectedOrganizerTags = new Set();
let selectedPartnerTags = new Set();
let currentModalMode = 'organizer'; // 'organizer' o 'partner'
let userProfileImage = null;

document.addEventListener('DOMContentLoaded', async function() {
    try {
        // Cargar categorías disponibles
        await loadCategories();
        
        // Cargar datos del usuario (tags predeterminados)
        await loadUserData();
        
        // Configurar event listeners
        setupEventListeners();
        
        // Configurar preview de imagen
        setupImagePreview();
        
        // Configurar validación de video
        setupVideoValidation();
    } catch (err) {
        console.error("Error inicializando formulario:", err);
        showMessage("Error cargando el formulario", 'error');
    }
});

async function loadCategories() {
    try {
        const res = await fetch('api/get_categories.php');
        if (!res.ok) {
            throw new Error('No se pudieron cargar las categorías');
        }
        allCategories = await res.json();
    } catch (err) {
        console.error("Error cargando categorías:", err);
        showMessage("Error cargando las categorías", 'error');
    }
}

async function loadUserData() {
    try {
        const res = await fetch('api/get_profile.php');
        if (!res.ok) {
            throw new Error('No autorizado');
        }
        const data = await res.json();
        
        // Guardar imagen de perfil para usarla por defecto
        userProfileImage = data.user.profile_image || null;
        
        // Cargar tags predeterminados del usuario
        if (data.tags && data.tags.length > 0) {
            data.tags.forEach(tag => {
                selectedOrganizerTags.add(tag);
            });
            updateOrganizerTagsDisplay();
        }
    } catch (err) {
        console.error("Error cargando datos del usuario:", err);
    }
}

function setupEventListeners() {
    // Botones para añadir tags
    document.getElementById('btn-add-organizer-tags').addEventListener('click', (e) => {
        e.preventDefault();
        currentModalMode = 'organizer';
        openTagsModal();
    });
    
    document.getElementById('btn-add-partner-tags').addEventListener('click', (e) => {
        e.preventDefault();
        currentModalMode = 'partner';
        openTagsModal();
    });
    
    // Modal controls
    document.getElementById('modal-close').addEventListener('click', closeTagsModal);
    document.getElementById('modal-cancel').addEventListener('click', closeTagsModal);
    document.getElementById('modal-confirm').addEventListener('click', confirmTagsSelection);
    
    // Cerrar modal al hacer click fuera
    document.getElementById('tags-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeTagsModal();
        }
    });
    
    // Form submission
    document.getElementById('project-form').addEventListener('submit', handleFormSubmit);
}

function setupImagePreview() {
    const imageInput = document.getElementById('project-image');
    const previewImg = document.getElementById('preview-img');
    const previewPlaceholder = document.getElementById('preview-placeholder');
    
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                previewImg.src = event.target.result;
                previewImg.style.display = 'block';
                previewPlaceholder.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    });
}

function setupVideoValidation() {
    const videoInput = document.getElementById('project-video');
    const videoInfo = document.getElementById('video-info');
    const MAX_SIZE = 200 * 1024 * 1024; // 200 MB en bytes
    
    videoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        videoInfo.innerHTML = '';
        
        if (file) {
            // Validar tamaño
            if (file.size > MAX_SIZE) {
                videoInfo.innerHTML = '<div class="notification notification--error">El vídeo és massa gran. Màxim 200 MB.</div>';
                videoInput.value = '';
                return;
            }
            
            // Mostrar información del archivo
            const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
            videoInfo.innerHTML = `<div class="notification notification--info">Vídeo: ${file.name} (${sizeMB} MB)</div>`;
        }
    });
}

function openTagsModal() {
    const modal = document.getElementById('tags-modal');
    const modalTitle = document.getElementById('modal-title');
    const tagsList = document.getElementById('tags-list');
    
    // Establecer título del modal
    if (currentModalMode === 'organizer') {
        modalTitle.textContent = 'Etiquetes d\'organitzador';
    } else {
        modalTitle.textContent = 'Etiquetes de partners';
    }
    
    // Generar lista de categorías
    const currentSelection = currentModalMode === 'organizer' ? selectedOrganizerTags : selectedPartnerTags;
    
    let html = '';
    allCategories.forEach(cat => {
        const isSelected = currentSelection.has(cat);
        html += `
            <label class="tag-checkbox">
                <input type="checkbox" data-category="${cat}" ${isSelected ? 'checked' : ''}>
                <span>${cat}</span>
            </label>
        `;
    });
    
    tagsList.innerHTML = html;
    modal.style.display = 'flex';
}

function closeTagsModal() {
    document.getElementById('tags-modal').style.display = 'none';
}

function confirmTagsSelection() {
    const checkboxes = document.querySelectorAll('#tags-list input[type="checkbox"]:checked');
    const selectedTags = new Set();
    
    checkboxes.forEach(checkbox => {
        selectedTags.add(checkbox.dataset.category);
    });
    
    if (currentModalMode === 'organizer') {
        selectedOrganizerTags = selectedTags;
        updateOrganizerTagsDisplay();
    } else {
        selectedPartnerTags = selectedTags;
        updatePartnerTagsDisplay();
    }
    
    closeTagsModal();
}

function updateOrganizerTagsDisplay() {
    const container = document.getElementById('organizer-selected-tags');
    updateTagsDisplay(container, selectedOrganizerTags, 'organizer');
}

function updatePartnerTagsDisplay() {
    const container = document.getElementById('partner-selected-tags');
    updateTagsDisplay(container, selectedPartnerTags, 'partner');
}

function updateTagsDisplay(container, tags, mode) {
    let html = '';
    tags.forEach(tag => {
        html += `
            <div class="tag">
                ${tag}
                <span class="remove" data-tag="${tag}" data-mode="${mode}">&times;</span>
            </div>
        `;
    });
    
    container.innerHTML = html;
    
    // Añadir event listeners para eliminar tags
    container.querySelectorAll('.remove').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const tag = this.dataset.tag;
            const mode = this.dataset.mode;
            
            if (mode === 'organizer') {
                selectedOrganizerTags.delete(tag);
                updateOrganizerTagsDisplay();
            } else {
                selectedPartnerTags.delete(tag);
                updatePartnerTagsDisplay();
            }
        });
    });
}

async function handleFormSubmit(e) {
    e.preventDefault();
    
    const formMessage = document.getElementById('form-message');
    formMessage.innerHTML = '';
    
    // Validar campos obligatorios
    const title = document.getElementById('project-title').value.trim();
    const description = document.getElementById('project-description').value.trim();
    
    if (!title || !description) {
        showMessage('El título y la descripción son obligatorios', 'error');
        return;
    }
    
    try {
        // Preparar FormData para envío
        const formData = new FormData();
        formData.append('title', title);
        formData.append('description', description);
        
        // Imagen
        const imageInput = document.getElementById('project-image');
        if (imageInput.files.length > 0) {
            formData.append('image', imageInput.files[0]);
        }
        
        // Tags
        formData.append('organizer_tags', JSON.stringify(Array.from(selectedOrganizerTags)));
        formData.append('partner_tags', JSON.stringify(Array.from(selectedPartnerTags)));
        
        // Video
        const videoInput = document.getElementById('project-video');
        if (videoInput.files.length > 0) {
            formData.append('video', videoInput.files[0]);
        }
        
        // Enviar al servidor
        showMessage('Creando proyecto...', 'info');
        
        const res = await fetch('api/create_project.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await res.json();
        
        if (data.success) {
            showMessage('Proyecto creado correctamente', 'success');
            setTimeout(() => {
                window.location.href = 'profile.php';
            }, 2000);
        } else {
            showMessage(data.error || 'Error creando el proyecto', 'error');
        }
    } catch (err) {
        console.error("Error enviando formulario:", err);
        showMessage('Error de conexión', 'error');
    }
}

function showMessage(message, type) {
    const messageDiv = document.getElementById('form-message');
    const className = `notification notification--${type}`;
    messageDiv.innerHTML = `<div class="${className}">${message}</div>`;
}
