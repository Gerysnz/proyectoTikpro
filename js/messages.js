// JS para cargar y mostrar las conversaciones en messages.php

document.addEventListener('DOMContentLoaded', () => {
    const list = document.getElementById('conversations-list');
    if (!list) return;

    fetch('api/get_conversations.php')
        .then(res => res.json())
        .then(conversations => {
            if (!Array.isArray(conversations) || conversations.length === 0) {
                list.innerHTML = '<div style="padding:2rem;color:var(--gray);text-align:center">No tens converses encara.</div>';
                return;
            }
            list.innerHTML = '';
            conversations.forEach(conv => {
                const row = document.createElement('div');
                row.className = 'conversation-row';
                row.onclick = () => {
                    window.location.href = `chat.php?project_id=${conv.project_id}&partner_id=${conv.partner_id}`;
                };
                row.innerHTML = `
                    <img class="conversation-avatar" src="${conv.project_logo ? conv.project_logo : 'uploads/default-avatar.png'}" alt="avatar">
                    <div class="conversation-info">
                        <div class="conversation-name" title="${conv.partner_entity}">${conv.project_title} — ${conv.partner_entity}</div>
                        <div class="conversation-last-message">${conv.last_message ? conv.last_message : ''}</div>
                    </div>
                    <div class="conversation-date">${conv.last_message_time ? formatDate(conv.last_message_time) : ''}</div>
                `;
                list.appendChild(row);
            });
        })
        .catch(err => {
            list.innerHTML = '<div style="padding:2rem;color:var(--red);text-align:center">Error carregant converses.</div>';
        });
});

function formatDate(dateStr) {
    // Devuelve solo hora y minutos (HH:mm)
    const d = new Date(dateStr.replace(' ', 'T'));
    return d.toLocaleTimeString('ca-ES', { hour: '2-digit', minute: '2-digit' });
}
