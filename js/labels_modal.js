// Modal de etiquetas para perfil
let modal, input, results, closeBtn;



document.addEventListener('DOMContentLoaded', loadUserLabels);

function loadUserLabels() {
    fetch('/api/get_profile.php')
        .then(r => r.json())
        .then(data => {
            if (data.tags && Array.isArray(data.tags)) {
                data.tags.forEach(label => addLabelToMenu(label));
            }
        });
}

function openLabelsModal() {
    if (!modal) createLabelsModal();
    modal.style.display = 'block';
    input.value = '';
    results.innerHTML = '';
    input.focus();
}

function createLabelsModal() {
    modal = document.createElement('div');
    modal.className = 'modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h2>Buscar etiqueta</h2>
                <button type="button" class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <input type="text" class="label-search" placeholder="Cerca..." maxlength="32" />
                <div class="labels-results"></div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    input = modal.querySelector('.label-search');
    results = modal.querySelector('.labels-results');
    closeBtn = modal.querySelector('.modal-close');
    closeBtn.onclick = () => modal.style.display = 'none';
    input.oninput = handleLabelSearch;
}

function handleLabelSearch() {
    const q = input.value.trim();
    if (q.length < 3) {
        results.innerHTML = '';
        return;
    }
    fetch(`/api/labels_crud.php?q=${encodeURIComponent(q)}`)
        .then(r => r.json())
        .then(data => {
            results.innerHTML = '';
            data.forEach(label => {
                const div = document.createElement('div');
                div.className = 'label-result';
                div.textContent = label.name;
                div.onclick = () => selectLabel(label);
                results.appendChild(div);
            });
        });
}

function selectLabel(label) {
    fetch('/api/labels_crud.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ label_id: label.id })
    })
    .then(r => r.json())
    .then(() => {
        modal.style.display = 'none';
        addLabelToMenu(label);
    });
}

function addLabelToMenu(label) {
    const tagsContainer = document.getElementById('profile-tags');
    // Esperar siempre objeto {id, name}
    const labelName = label && label.name ? label.name : label;
    const labelId = label && label.id ? label.id : null;
    if (!labelName || labelName.trim() === '') return;
    // Evitar duplicados
    if ([...tagsContainer.querySelectorAll('.tag')].some(t => t.textContent.replace('×','').trim() === labelName)) return;
    const tag = document.createElement('span');
    tag.className = 'tag';
    tag.textContent = labelName;
    const removeBtn = document.createElement('span');
    removeBtn.className = 'remove';
    removeBtn.textContent = '×';
    removeBtn.onclick = () => removeLabel({id: labelId, name: labelName}, tag);
    tag.appendChild(removeBtn);
    tagsContainer.appendChild(tag);
}

function removeLabel(label, tagElem) {
    fetch('/api/labels_crud.php', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ label_id: label.id })
    })
    .then(r => r.json())
    .then(() => {
        tagElem.remove();
    });
}

window.openLabelsModal = openLabelsModal;
