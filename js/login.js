document.getElementById('loginForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const form = e.target;
  const datos = new FormData(form);

  if (!form.email.value || !form.password.value) {
    document.getElementById('mensaje-login').innerText = 'Completa todos los campos';
    return;
  } else {
    document.getElementById('mensaje-login').innerText = '';
  }

  try {
    const resp = await fetch('login.php', {
      method: 'POST',
      body: datos,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    const texto = await resp.text();
    if (texto.trim() === 'OK') {
      window.location.href = 'discover.php';
    } else {
      document.getElementById('mensaje-login').innerText = texto;
    }
  } catch (error) {
    document.getElementById('mensaje-login').innerText = 'Error de conexión. Intenta de nuevo.';
  }
});
