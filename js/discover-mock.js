const proyectos = [
  {
    id: 1,
    titulo: 'Proyecto A',
    video: 'https://www.w3schools.com/html/mov_bbb.mp4',
    descripcion: 'Descripción del proyecto A',
    etiquetas: ['innovación', 'tecnología']
  },
  {
    id: 2,
    titulo: 'Proyecto B',
    video: 'https://www.w3schools.com/html/movie.mp4',
    descripcion: 'Descripción del proyecto B',
    etiquetas: ['educación', 'social']
  }
];

let actual = 0;

function renderProyecto(idx) {
  const p = proyectos[idx];
  if (!p) return;
  const cont = document.getElementById('proyecto-card');
  cont.innerHTML = `
    <div class="card">

      <video class="video" src="${p.video}" controls></video>

      <div class="details">
        <h2>${p.titulo}</h2>
        <p style="display:none;">${p.descripcion}</p>
        <div class="tags" style="display:none;">
          ${p.etiquetas.map(tag => `<span class="tag">${tag}</span>`).join('')}
        </div>
      </div>

      <div class="actions">
        <button class="nope" id="btn-nope">Nope</button>
        <button class="like" id="btn-like">Like</button>
        <button id="btn-detalles">Detalls</button>
      </div>

      <div class="footer">
        <span>Perfil</span>
        <span>Converses</span>
        <span>Detalls</span>
      </div>

    </div>
  `;

  document.getElementById('btn-like').onclick = () => animarCard('like');
  document.getElementById('btn-nope').onclick = () => animarCard('nope');
  document.getElementById('btn-detalles').onclick = () => toggleDetalles();
}

function animarCard(tipo) {
  const card = document.querySelector('.card');
  card.style.transition = 'opacity 0.5s, transform 0.5s';
  card.style.opacity = '0';
  card.style.transform = tipo === 'like' ? 'translateX(100px)' : 'translateX(-100px)';
  setTimeout(() => {
    actual++;
    if (actual >= proyectos.length) actual = 0; // Loop projects
    renderProyecto(actual);
  }, 500);
}

function toggleDetalles() {
  const desc = document.querySelector('.details p');
  const tags = document.querySelector('.tags');
  desc.style.display = desc.style.display === 'none' ? 'block' : 'none';
  tags.style.display = tags.style.display === 'none' ? 'flex' : 'none';
}

document.addEventListener('DOMContentLoaded', () => {
  renderProyecto(actual);
});
