document.getElementById('loginForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const form = e.target;
  const datos = new FormData(form);

  // Validación simple
  if (!form.email.value || !form.password.value) {
    document.getElementById('mensaje').innerText = 'Completa todos los campos';
    return;
  }

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
    document.getElementById('mensaje').innerText = texto;
  }
});