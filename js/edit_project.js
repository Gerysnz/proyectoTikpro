// edit_project.js - Manejo del formulario de creación y edición de proyecto

let allCategories = [];
let selectedOrganizerTags = new Set();
let selectedPartnerTags = new Set();
let currentModalMode = 'organizer'; // 'organizer' o 'partner'
let userProfileImage = null;
let isEditingProject = false;
let editingProjectId = null;

document.addEventListener('DOMContentLoaded', async function() {
    try {
        // Detectar si se está editando un proyecto
        const urlParams = new URLSearchParams(window.location.search);
        editingProjectId = urlParams.get('project_id');
        
        if (editingProjectId) {
            isEditingProject = true;
            // Cargar datos del proyecto a editar
            await loadProjectData(editingProjectId);
        }
        
        // Cargar categorías disponibles
        await loadCategories();
        
        // Cargar datos del usuario (tags predeterminados) solo si no se está editando
        if (!isEditingProject) {
            await loadUserData();
        }
        
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

async function loadProjectData(projectId) {
    try {
        const res = await fetch(`api/get_project.php?project_id=${projectId}`);
        
        if (!res.ok) {
            const data = await res.json();
            showMessage(data.error || 'Error cargando el proyecto', 'error');
            throw new Error(data.error);
        }
        
        const project = await res.json();
        
        // Actualizar título de la página
        document.getElementById('form-title').textContent = 'Editar Projecte';
        document.getElementById('submit-btn').textContent = 'Guardar cambios';
        
        // Llenar formulario con datos del proyecto
        document.getElementById('project-id').value = projectId;
        document.getElementById('project-title').value = project.title;
        document.getElementById('project-description').value = project.description;
        
        // Mostrar imagen existente
        if (project.image_path) {
            const previewImg = document.getElementById('preview-img');
            const previewPlaceholder = document.getElementById('preview-placeholder');
            previewImg.src = project.image_path;
            previewImg.style.display = 'block';
            previewPlaceholder.style.display = 'none';
        }
        
        // Mostrar información del video existente
        if (project.video_path) {
            const videoInfo = document.getElementById('video-info');
            const fileName = project.video_path.split('/').pop();
            videoInfo.innerHTML = `<div class="notification notification--info">Vídeo actual: ${fileName}</div>`;
        }
        
        // Cargar categorías seleccionadas
        // Dividir entre organizador y partner
        if (project.categories && project.categories.length > 0) {
            const categories = project.categories.map(cat => cat.name);
            const midpoint = Math.ceil(categories.length / 2);
            
            // Primeras categorías como organizador
            selectedOrganizerTags.clear();
            categories.slice(0, midpoint).forEach(cat => {
                selectedOrganizerTags.add(cat);
            });
            
            // Resto como partner
            selectedPartnerTags.clear();
            categories.slice(midpoint).forEach(cat => {
                selectedPartnerTags.add(cat);
            });
            
            // Actualizar visualización de tags
            setTimeout(() => {
                updateOrganizerTagsDisplay();
                updatePartnerTagsDisplay();
            }, 100);
        }
        
    } catch (err) {
        console.error("Error cargando proyecto:", err);
        throw err;
    }
}

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
                selectedOrganizerTags.add(tag.name);
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
        
        // Si se está editando, agregar project_id
        if (isEditingProject) {
            formData.append('project_id', editingProjectId);
        }
        
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
        
        // Determinar endpoint y mensaje
        const endpoint = isEditingProject ? 'api/update_project.php' : 'api/create_project.php';
        const loadingMessage = isEditingProject ? 'Guardando cambios...' : 'Creando proyecto...';
        const successMessage = isEditingProject ? 'Proyecto actualizado correctamente' : 'Proyecto creado correctamente';
        
        // Enviar al servidor
        showMessage(loadingMessage, 'info');
        
        const res = await fetch(endpoint, {
            method: 'POST',
            body: formData
        });
        
        const data = await res.json();
        
        if (data.success) {
            showMessage(successMessage, 'success');
            setTimeout(() => {
                window.location.href = 'profile.php';
            }, 2000);
        } else {
            showMessage(data.error || 'Error procesando el proyecto', 'error');
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
