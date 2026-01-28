// js/chat.js
// Lógica para cargar y renderizar el chat

document.addEventListener('DOMContentLoaded', () => {
    // Obtener project_id y partner_id de la URL
    const urlParams = new URLSearchParams(window.location.search);
    const projectId = urlParams.get('project_id');
    const partnerId = urlParams.get('partner_id');
    if (!projectId || !partnerId) {
        alert('Faltan parámetros de chat.');
        return;
    }

    // Elementos del DOM
    const chatHeader = document.getElementById('chat-header');
    const chatMessages = document.getElementById('chat-messages');
    const chatProject = document.getElementById('chat-project');
    const chatProjectLogo = document.getElementById('chat-project-logo');

    // Función para renderizar mensajes
    function renderMessages(messages, myUserId) {
        chatMessages.innerHTML = '';
        messages.forEach(msg => {
            const msgDiv = document.createElement('div');
            // remitent_id = emisor, destination_id = receptor
            const isSent = msg.remitent_id == myUserId;
            msgDiv.className = 'chat-message ' + (isSent ? 'sent' : 'received');
            msgDiv.innerHTML = `
                <div class="chat-message-content">${msg.content}</div>
                <div class="chat-message-meta">${msg.remitent_name} · ${msg.created_at}</div>
            `;
            chatMessages.appendChild(msgDiv);
        });
        // Scroll al final
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Cargar mensajes y datos del chat
    function loadChat() {
        const myUserId = window.MY_USER_ID || null;
        fetch(`/api/get_chat_messages.php?project_id=${projectId}&partner_id=${partnerId}&user_id=${myUserId}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                // Renderizar header con nombre y avatar del partner
                if (chatHeader && data.partner) {
                    chatHeader.innerHTML = `
                        <span class="chat-partner-name">${data.partner.user_name} (${data.partner.entity_name || ''})</span>
                    `;
                }
                // Renderizar nombre y logo del proyecto
                if (chatProject && data.project) {
                    chatProject.textContent = data.project.title;
                    if (chatProjectLogo && data.project.image_path) {
                        chatProjectLogo.src = data.project.image_path;
                    }
                }
                // Renderizar mensajes
                const myUserId = window.MY_USER_ID || null; // Define esto en PHP
                renderMessages(data.messages, myUserId);
            })
            .catch(err => {
                alert('Error cargando chat: ' + err);
            });
    }

    loadChat();
    // Si quieres polling, descomenta:
    // setInterval(loadChat, 3000);
});
