const loginForm = document.getElementById('loginForm');
const forgotPasswordForm = document.getElementById('forgotPasswordForm');
const forgotPasswordLink = document.getElementById('forgotPasswordLink');
const backToLoginLink = document.getElementById('backToLoginLink');
const formTitle = document.getElementById('formTitle');

forgotPasswordLink.addEventListener('click', function(e) {
  e.preventDefault();
  loginForm.classList.add('hidden');
  forgotPasswordForm.classList.remove('hidden');
  formTitle.textContent = 'Recuperar Contrasenya';
  forgotPasswordLink.classList.add('hidden');
  backToLoginLink.classList.remove('hidden');
});

backToLoginLink.addEventListener('click', function(e) {
  e.preventDefault();
  loginForm.classList.remove('hidden');
  forgotPasswordForm.classList.add('hidden');
  formTitle.textContent = 'Iniciar Sessió';
  forgotPasswordLink.classList.remove('hidden');
  backToLoginLink.classList.add('hidden');
});

loginForm.addEventListener('submit', function(e) {
  // Validación opcional, pero HTML required lo maneja
});

// Manejar envío de código de recuperación
forgotPasswordForm.addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const email = document.getElementById('recover-email').value;
  const submitBtn = forgotPasswordForm.querySelector('button[type="submit"]');
  
  submitBtn.disabled = true;
  submitBtn.textContent = 'Enviando...';
  
  try {
    const formData = new FormData();
    formData.append('email', email);
    
    const response = await fetch('api/send_reset_code.php', {
      method: 'POST',
      body: formData
    });
    
    const data = await response.json();
    
    if (data.success) {
      // Redirigir a la página de verificación de código
      window.location.href = 'forgot_password.php?step=2';
    } else {
      alert(data.message || 'Error al enviar el código');
    }
  } catch (error) {
    console.error('Error:', error);
    alert('Error al enviar el código');
  } finally {
    submitBtn.disabled = false;
    submitBtn.textContent = 'Enviar codi';
  }
});
