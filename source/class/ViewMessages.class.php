<?php

class ViewMessages extends View {

    public function __construct() {
        $this->pageTitle = "Messages - TradiShion";
    }

    protected function getHeadAndHeader() {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo $this->pageTitle; ?></title>
            <link rel="stylesheet" href="style/style.css">
        </head>
        <body class="dashboard-body" style="overflow:hidden;">
            <header class="dash-header">
                <div class="dash-logo">
                    <span style="font-family:'Libre Baskerville', serif; font-size:1.5rem; font-weight:bold; color:#A64B35;">Tradishion</span>
                </div>
                
                <div class="dash-search" style="position:relative;">
                    <input type="text" id="global-search" placeholder="🔍 Rechercher des artisans...">
                    <div id="search-results" style="display:none; position:absolute; top:100%; left:0; width:100%; background:white; border:1px solid #eee; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.1); z-index:100; max-height:300px; overflow-y:auto;"></div>
                </div>

                <div class="dash-user-nav" style="display: flex; gap: 20px; align-items: center;">
                    <a href="index.php?page=dashboard" style="color:#555; text-decoration:none; font-weight:bold;">🏠 Accueil</a>
                    <a href="index.php?page=explorer" style="color:#555; text-decoration:none; font-weight:bold;">🧭 Explorateur</a>
                    <a href="index.php?page=network" style="color:#555; text-decoration:none; font-weight:bold;">👥 Réseau</a>
                    <span class="notification-icon" style="cursor:pointer;">🔔</span>
                    <a href="index.php?page=logout" class="btn-outline" style="border-color:#ccc; color:#333; padding: 5px 15px; border-radius: 20px;">Déconnexion</a>
                </div>
            </header>
        <?php
        return ob_get_clean();
    }

    protected function getBodyContent() {
        ob_start();
        
        $db = new Database();
        $myConnections = $db->getConnections($_SESSION['id_user']);
        
        ?>
        <div class="chat-container" style="display:flex; max-width:1600px; margin:20px auto; height:calc(100vh - 120px); gap:20px; padding:0 40px;">
            
            <aside class="chat-sidebar card" style="width:350px; display:flex; flex-direction:column; padding:0; overflow:hidden;">
                <div style="padding:20px; border-bottom:1px solid #eee;">
                    <h3 style="margin-bottom:15px;">Messages</h3>
                    <input type="text" placeholder="🔍 Rechercher dans les messages..." style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd; background:#f9f9f9;">
                </div>
                
                <div class="contact-list" style="overflow-y:auto; flex:1;">
                    <?php if (empty($myConnections)): ?>
                        <p style="padding:20px; text-align:center; color:#888; font-style:italic;">
                            Vous n'avez pas encore de relations.
                        </p>
                    <?php else: ?>
                        <?php foreach ($myConnections as $contact): ?>
                            <?php $initial = strtoupper(substr($contact['display_name'], 0, 1)); ?>
                            <div class="contact-item" data-id="<?php echo $contact['id_user']; ?>" data-name="<?php echo htmlspecialchars($contact['display_name']); ?>" style="display:flex; align-items:center; gap:15px; padding:15px 20px; border-bottom:1px solid #eee; cursor:pointer;">
                                <div class="avatar-large" style="width:50px; height:50px; margin:0; position:relative; background:#eee; display:flex; align-items:center; justify-content:center; border-radius:50%; font-size:1.2rem; font-weight:bold; overflow:hidden;">
                                    <?php if (!empty($contact['avatar_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($contact['avatar_url']); ?>" style="width:100%; height:100%; object-fit:cover;">
                                    <?php else: ?>
                                        <?php echo $initial; ?>
                                    <?php endif; ?>
                                    <span style="position:absolute; bottom:0; right:0; width:12px; height:12px; background:#4caf50; border:2px solid white; border-radius:50%;"></span>
                                </div>
                                <div style="flex:1;">
                                    <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                                        <strong style="color:#333;"><?php echo htmlspecialchars($contact['display_name']); ?></strong>
                                    </div>
                                    <p style="margin:0; font-size:0.85rem; color:#666; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                        📍 <?php echo htmlspecialchars($contact['location'] ?? 'Membre du réseau'); ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </aside>

            <main class="chat-window card" style="flex:1; display:flex; flex-direction:column; padding:0; overflow:hidden;">
                
                <div style="padding:15px 20px; border-bottom:1px solid #eee; display:flex; justify-content:space-between; align-items:center; background: #fafafa;">
                    <div style="display:flex; align-items:center; gap:15px;">
                        <div>
                            <h4 id="chat-header-name" style="margin:0;">Sélectionnez une discussion</h4>
                            <small style="color:#4caf50;">En ligne</small>
                        </div>
                    </div>
                    <div style="color:#888; font-size:1.2rem; cursor:pointer; display:flex; gap:20px;">
                        <span>⋮</span>
                    </div>
                </div>

                <div id="chat-history" style="flex:1; padding:20px; overflow-y:auto; background:#fcfcfc; display:flex; flex-direction:column; gap:15px;">
                    <p style="text-align:center; color:#888; margin-top: 50px;">Cliquez sur un contact à gauche pour afficher vos messages.</p>
                </div>

                <div style="padding:15px 20px; border-top:1px solid #eee; display:flex; align-items:center; gap:15px; background:white; position:relative;">
                    
                    <label style="cursor:pointer; margin:0;">
                        <span style="color:#888; font-size:1.2rem;">🖼️</span>
                        <input type="file" id="image-upload" style="display:none;" accept="image/*">
                    </label>
                    <span id="file-name-display" style="font-size:0.8rem; color:#A64B35; max-width: 100px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></span>
                    
                    <input type="text" id="chat-input" placeholder="Écrivez un message..." style="flex:1; padding:12px 20px; border-radius:25px; border:1px solid #eee; background:#f4f5f7; outline:none; font-size:0.95rem;">
                    
                    <button id="btn-send" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:#A64B35;">➤</button>
                </div>
            </main>
        </div>

        <script>
            const myUserId = <?php echo $_SESSION['id_user']; ?>;
            let currentContactId = null;
            
            const chatHistory = document.getElementById('chat-history');
            const chatHeaderName = document.getElementById('chat-header-name');
            const chatInput = document.getElementById('chat-input');
            const imageUploadInput = document.getElementById('image-upload');
            const fileNameDisplay = document.getElementById('file-name-display');
            const btnSend = document.getElementById('btn-send');
            const contacts = document.querySelectorAll('.contact-item');

            imageUploadInput.addEventListener('change', function() {
                if(this.files.length > 0) {
                    fileNameDisplay.innerText = "📎 " + this.files[0].name;
                } else {
                    fileNameDisplay.innerText = "";
                }
            });

            function loadChat(contactId, contactName) {
                currentContactId = contactId;
                chatHeaderName.innerText = contactName;
                chatHistory.innerHTML = '<p style="text-align:center; color:#888; padding:20px;">Chargement...</p>';
                
                contacts.forEach(c => c.classList.remove('active'));
                document.querySelector(`.contact-item[data-id="${contactId}"]`).classList.add('active');

                fetch(`index.php?page=api_get_chat&contact=${contactId}`)
                    .then(res => res.json())
                    .then(messages => {
                        chatHistory.innerHTML = '';
                        if(messages.length === 0) {
                            chatHistory.innerHTML = '<p style="text-align:center; color:#888; margin-top:20px;">Aucun message. Soyez le premier à briser la glace !</p>';
                            return;
                        }

                        messages.forEach(msg => {
                            const isMe = (msg.id_sender == myUserId);
                            const align = isMe ? 'align-self:flex-end; align-items:flex-end;' : 'align-self:flex-start; align-items:flex-start;';
                            const bg = isMe ? 'background:#A64B35; color:white;' : 'background:white; border:1px solid #eee; color:#333;';
                            const radius = isMe ? '15px 0 15px 15px' : '0 15px 15px 15px';
                            const time = new Date(msg.sent_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

                            let contentHtml = msg.content ? `<div>${msg.content}</div>` : '';
                            let imageHtml = msg.file_path ? `<img src="${msg.file_path}" style="max-width:100%; max-height: 250px; border-radius:8px; margin-top:5px; border:1px solid rgba(0,0,0,0.1);">` : '';

                            chatHistory.innerHTML += `
                                <div style="display:flex; flex-direction:column; max-width:70%; ${align} margin-bottom:10px;">
                                    <div style="${bg} padding:12px 18px; border-radius:${radius}; font-size:0.95rem; box-shadow:0 1px 2px rgba(0,0,0,0.05);">
                                        ${contentHtml}
                                        ${imageHtml}
                                    </div>
                                    <small style="color:#aaa; font-size:0.7rem; margin-top:5px;">${time}</small>
                                </div>
                            `;
                        });
                        chatHistory.scrollTop = chatHistory.scrollHeight;
                    });
            }

            contacts.forEach(c => {
                c.addEventListener('click', function() {
                    loadChat(this.getAttribute('data-id'), this.getAttribute('data-name'));
                });
            });

            function sendMessage() {
                const content = chatInput.value.trim();
                const imageFile = imageUploadInput.files[0];
                
                if(!currentContactId || (content === '' && !imageFile)) return;
                
                const formData = new FormData();
                formData.append('id_receiver', currentContactId);
                formData.append('content', content);
                
                if (imageFile) {
                    formData.append('image', imageFile);
                }

                fetch('index.php?page=api_message', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        chatInput.value = '';
                        imageUploadInput.value = '';
                        fileNameDisplay.innerText = '';
                        loadChat(currentContactId, chatHeaderName.innerText); 
                    } else {
                        showToast("Action refusée. (Règle Anti-Spam / Erreur serveur)", false);
                    }
                });
            }

            btnSend.addEventListener('click', sendMessage);
            chatInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') sendMessage();
            });

            if(contacts.length > 0) { contacts[0].click(); }

            function showToast(message, isSuccess = true) {
                let toast = document.createElement('div');
                toast.innerText = message;
                toast.style.cssText = `
                    position: fixed; top: 20px; right: 20px; 
                    background: ${isSuccess ? '#4caf50' : '#e74c3c'}; 
                    color: white; padding: 15px 25px; border-radius: 8px; 
                    box-shadow: 0 4px 15px rgba(0,0,0,0.2); z-index: 9999; 
                    font-weight: bold; opacity: 0; transform: translateY(-20px); 
                    transition: all 0.3s ease;
                `;
                document.body.appendChild(toast);
                setTimeout(() => { toast.style.opacity = '1'; toast.style.transform = 'translateY(0)'; }, 10);
                setTimeout(() => { 
                    toast.style.opacity = '0'; toast.style.transform = 'translateY(-20px)'; 
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }

            function addFriendGlobal(id) {
                const formData = new FormData(); formData.append('id_followed', id);
                fetch('index.php?page=api_send_request', { method:'POST', body:formData })
                .then(res => res.json()).then(data => { 
                    if(data.success) {
                        showToast('✅ Invitation envoyée avec succès !');
                    } else {
                        showToast('⚠️ Invitation déjà en attente ou contact déjà ajouté.', false);
                    }
                });
            }

            document.getElementById('global-search')?.addEventListener('input', function() {
                const query = this.value.trim();
                const resultsDiv = document.getElementById('search-results');
                if (query.length < 2) { resultsDiv.style.display = 'none'; return; }
                
                fetch('index.php?page=api_search&q=' + encodeURIComponent(query))
                .then(res => res.json()).then(data => {
                    resultsDiv.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(user => {
                            resultsDiv.innerHTML += `
                            <div style="padding:10px; border-bottom:1px solid #eee; display:flex; align-items:center; gap:10px; cursor:pointer;" onclick="addFriendGlobal(${user.id_user})">
                                <strong>${user.display_name}</strong>
                                <span style="font-size:0.8rem; color:#888;">📍 ${user.location || ''}</span>
                                <button style="margin-left:auto; background:none; border:none; color:#A64B35; cursor:pointer;">➕ Ajouter</button>
                            </div>`;
                        });
                        resultsDiv.style.display = 'block';
                    } else {
                        resultsDiv.innerHTML = '<div style="padding:10px; color:#888;">Aucun résultat</div>';
                        resultsDiv.style.display = 'block';
                    }
                });
            });
        </script>
        <?php
        return ob_get_clean();
    }
}
?>