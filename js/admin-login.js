// Admin Login Form Validation
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('adminLoginForm');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            if (!validateLoginForm()) {
                e.preventDefault();
            }
        });
    }
});

function validateLoginForm() {
    const email = document.getElementById('admin_email');
    const password = document.getElementById('admin_password');
    
    // Validar email
    if (!email.value.trim()) {
        showError('Por favor, ingresa el email');
        email.focus();
        return false;
    }
    
    if (!isValidEmail(email.value)) {
        showError('Email inválido');
        email.focus();
        return false;
    }
    
    // Validar contraseña
    if (!password.value) {
        showError('Por favor, ingresa la contraseña');
        password.focus();
        return false;
    }
    
    if (password.value.length < 6) {
        showError('La contraseña debe tener al menos 6 caracteres');
        password.focus();
        return false;
    }
    
    return true;
}

function isValidEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

function showError(message) {
    // El sistema ya tiene un notificador global
    console.warn(message);
}
