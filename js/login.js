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
