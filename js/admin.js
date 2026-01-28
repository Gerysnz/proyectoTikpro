// Admin Dashboard Logic
document.addEventListener('DOMContentLoaded', function() {
    initAdminDashboard();
});

function initAdminDashboard() {
    // Inicializar interactividad del dashboard
    initLogoutButton();
    initMenuItems();
    initProjectsList();
}

function initLogoutButton() {
    const logoutBtn = document.querySelector('.logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            if (!confirm('¿Deseas cerrar sesión?')) {
                e.preventDefault();
            }
        });
    }
}

function initMenuItems() {
    const menuItems = document.querySelectorAll('.menu-item');
    menuItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px)';
        });
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
}

function initProjectsList() {
    const projectsList = document.querySelectorAll('.projects-list li');
    projectsList.forEach(item => {
        item.addEventListener('click', function() {
            // Comportamiento opcional al hacer clic en un proyecto
            console.log('Proyecto seleccionado');
        });
    });
}

// Validación de sesión activa (solo aviso, el servidor valida)
function checkSessionActive() {
    // El servidor ya valida la sesión en PHP
    // Esto es solo para aviso al usuario
    const lastActivity = localStorage.getItem('lastAdminActivity');
    const now = new Date().getTime();
    
    if (lastActivity && (now - lastActivity) > 30 * 60 * 1000) { // 30 minutos
        console.warn('Sesión probablemente expirada');
    }
    
    localStorage.setItem('lastAdminActivity', now);
}

// Ejecutar cada 5 minutos
setInterval(checkSessionActive, 5 * 60 * 1000);
