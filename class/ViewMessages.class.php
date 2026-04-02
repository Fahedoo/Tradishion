<?php

class ViewMessages extends ViewPrivate {

    public function __construct($userData) {
        parent::__construct($userData);
        $this->pageTitle = "Messages - TradiShion";
        $this->bodyStyle = "overflow:hidden;";
    }

    protected function getBodyContent() {
        ob_start();
        $db = new Database();
        $myConnections = $db->getConnections($_SESSION['id_user']);
        ?>
        <div class="chat-container" style="display:flex; max-width:1600px; margin:20px auto; height:calc(100vh - 120px); gap:20px; padding:0 40px;">
            
            <div style="display:flex; flex-direction:column; gap:20px; width:350px; height:100%;">
                
                <div class="card nav-menu" style="margin-bottom:0; flex-shrink:0;">
                   <ul style="list-style: none; padding: 0; margin: 0;">
                        <li><a href="index.php?page=dashboard">🏠 Accueil</a></li>
                        <li><a href="index.php?page=explorer">🧭 Explorateur</a></li>
                        <li><a href="index.php?page=messages" class="active" style="background:#FFF0ED; color:#A64B35; font-weight:bold;">💬 Mes Messages</a></li>
                        <li><a href="index.php?page=network">👥 Mon Réseau</a></li>
                    </ul>
                </div>

                <aside class="chat-sidebar card" style="flex:1; display:flex; flex-direction:column; padding:0; overflow:hidden; margin-bottom:0;">
                    <div style="padding:20px; border-bottom:1px solid #eee;">
                        <h3 style="margin-bottom:15px; margin-top:0; font-family:'Libre Baskerville', serif;">Messages</h3>
                        <input type="text" placeholder="🔍 Rechercher dans les messages..." style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd; background:#f9f9f9; outline:none;">
                    </div>
                    
                    <div class="contact-list" style="overflow-y:auto; flex:1;">
                        <?php if (empty($myConnections)): ?>
                            <p style="padding:20px; text-align:center; color:#888; font-style:italic;">Vous n'avez pas encore de relations.</p>
                        <?php else: ?>
                            <?php foreach ($myConnections as $contact): ?>
                                <?php $initial = strtoupper(substr($contact['display_name'], 0, 1)); ?>
                                <div class="contact-item" data-id="<?php echo $contact['id_user']; ?>" data-name="<?php echo htmlspecialchars($contact['display_name']); ?>" style="display:flex; align-items:center; gap:15px; padding:15px 20px; border-bottom:1px solid #eee; cursor:pointer;">
                                    <div class="avatar-large" style="width:50px; height:50px; margin:0; position:relative; background:#eee; display:flex; align-items:center; justify-content:center; border-radius:50%; font-size:1.2rem; font-weight:bold; overflow:hidden; color:#333;">
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
                                        <p style="margin:0; font-size:0.85rem; color:#666; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">📍 <?php echo htmlspecialchars($contact['location'] ?? 'Membre du réseau'); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </aside>
            </div>

            <main class="chat-window card" style="flex:1; display:flex; flex-direction:column; padding:0; overflow:hidden;">
                <div style="padding:15px 20px; border-bottom:1px solid #eee; display:flex; justify-content:space-between; align-items:center; background: #fafafa;">
                    <div style="display:flex; align-items:center; gap:15px;">
                        <div>
                            <h4 id="chat-header-name" style="margin:0;">Sélectionnez une discussion</h4>
                            <small style="color:#4caf50;">En ligne</small>
                        </div>
                    </div>
                    <div style="color:#888; font-size:1.2rem; cursor:pointer; display:flex; gap:20px;"><span>⋮</span></div>
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
                if(this.files.length > 0) { fileNameDisplay.innerText = "📎 " + this.files[0].name; } 
                else { fileNameDisplay.innerText = ""; }
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
                        if(messages.length === 0) { chatHistory.innerHTML = '<p style="text-align:center; color:#888; margin-top:20px;">Aucun message. Soyez le premier à briser la glace !</p>'; return; }
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
                                </div>`;
                        });
                        chatHistory.scrollTop = chatHistory.scrollHeight;
                    });
            }

            contacts.forEach(c => { c.addEventListener('click', function() { loadChat(this.getAttribute('data-id'), this.getAttribute('data-name')); }); });

            function sendMessage() {
                const content = chatInput.value.trim();
                const imageFile = imageUploadInput.files[0];
                if(!currentContactId || (content === '' && !imageFile)) return;
                
                const formData = new FormData();
                formData.append('id_receiver', currentContactId);
                formData.append('content', content);
                if (imageFile) formData.append('image', imageFile);

                fetch('index.php?page=api_message', { method: 'POST', body: formData })
                .then(res => res.json()).then(data => {
                    if(data.success) {
                        chatInput.value = ''; imageUploadInput.value = ''; fileNameDisplay.innerText = '';
                        loadChat(currentContactId, chatHeaderName.innerText); 
                    } else { showToast("Action refusée. (Règle Anti-Spam)", false); }
                });
            }

            btnSend.addEventListener('click', sendMessage);
            chatInput.addEventListener('keypress', function (e) { if (e.key === 'Enter') sendMessage(); });

            if(contacts.length > 0) { contacts[0].click(); }
        </script>
        <?php
        return ob_get_clean();
    }
}
?>