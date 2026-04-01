<?php

class ViewExplorer extends View {
    private $user;
    private $recommendations;
    private $posts;

    public function __construct($userData, $recos, $posts) {
        $this->pageTitle = "Explorateur - TradiShion";
        $this->user = $userData;
        $this->recommendations = $recos;
        $this->posts = $posts;
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
        <body class="dashboard-body">
            <header class="dash-header">
                <div class="dash-logo">
                    <span style="font-family:'Libre Baskerville', serif; font-size:1.5rem; font-weight:bold; color:#A64B35;">Tradishion</span>
                </div>
                <div class="dash-search" style="position:relative;">
                    <input type="text" id="global-search" placeholder="🔍 Rechercher des artisans...">
                    <div id="search-results" style="display:none; position:absolute; top:100%; left:0; width:100%; background:white; border:1px solid #eee; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.1); z-index:100; max-height:300px; overflow-y:auto;"></div>
                </div>
                <div class="dash-user-nav">
                    <span>AL | <strong style="color:#A64B35;">FR</strong> | GB</span>
                    <span class="notification-icon">🔔</span>
                    <a href="index.php?page=logout" class="logout-link">Se déconnecter</a>
                </div>
            </header>
        <?php
        return ob_get_clean();
    }

    protected function getBodyContent() {
        ob_start();
        ?>
        <div class="dash-container" style="grid-template-columns: 280px 1fr 320px;">
            
            <aside class="dash-left">
                <div class="card profile-overview">
                    <a href="index.php?page=profile" style="text-decoration:none; color:inherit;">
                        <div class="avatar-large" style="overflow:hidden;">
                            <?php if (!empty($this->user['avatar_url'])): ?>
                                <img src="<?php echo htmlspecialchars($this->user['avatar_url']); ?>" style="width:100%; height:100%; object-fit:cover;" alt="Avatar">
                            <?php else: ?>
                                <?php echo strtoupper(substr($this->user['display_name'], 0, 1)); ?>
                            <?php endif; ?>
                        </div>
                        <h3><?php echo htmlspecialchars($this->user['display_name']); ?></h3>
                    </a>
                </div>

                <div class="card nav-menu">
                   <ul>
                        <li><a href="index.php?page=dashboard">🏠 Accueil</a></li>
                        <li><a href="index.php?page=explorer" class="active" style="background:#FFF0ED; color:#A64B35;">🧭 Explorateur</a></li>
                        <li><a href="index.php?page=messages">💬 Mes Messages</a></li>
                        <li><a href="index.php?page=network">👥 Mon Réseau</a></li>
                    </ul>
                </div>
            </aside>

            <main class="dash-center">
                
                <form action="index.php?page=explorer" method="POST" enctype="multipart/form-data" class="card" style="margin-bottom: 20px; display:flex; flex-direction:column; gap:10px;">
                    <input type="hidden" name="action" value="new_post">
                    <div style="display:flex; gap:15px; align-items:flex-start;">
                        <div class="avatar-large" style="width:40px; height:40px; margin:0; overflow:hidden; font-size:1rem;">
                            <?php if (!empty($this->user['avatar_url'])): ?>
                                <img src="<?php echo htmlspecialchars($this->user['avatar_url']); ?>" style="width:100%; height:100%; object-fit:cover;">
                            <?php else: ?>
                                <?php echo strtoupper(substr($this->user['display_name'], 0, 1)); ?>
                            <?php endif; ?>
                        </div>
                        <textarea name="content" placeholder="Quoi de neuf dans votre atelier ?" style="flex:1; border:none; resize:none; outline:none; font-family:inherit; min-height:60px; padding:10px; background:#fdfdfd; border-radius:8px;"></textarea>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #eee; padding-top:10px;">
                        <label style="cursor:pointer; color:#888; font-size:0.9rem;">
                            🖼️ Ajouter une photo
                            <input type="file" name="post_image" style="display:none;" accept="image/*" onchange="document.getElementById('post-file-name').innerText = this.files[0].name;">
                        </label>
                        <span id="post-file-name" style="font-size:0.8rem; color:#A64B35; flex:1; margin-left:10px;"></span>
                        <button type="submit" class="btn-submit" style="padding:8px 20px; width:auto;">Publier</button>
                    </div>
                </form>

                <div id="posts-feed">
                    <?php if (empty($this->posts)): ?>
                        <p style="text-align:center; color:#888; padding:20px;">Le fil d'actualité est vide. Soyez le premier à publier !</p>
                    <?php else: ?>
                        <?php foreach ($this->posts as $post): ?>
                            <div class="feed-post card" style="margin-bottom:15px;">
                                <div class="post-header" style="display:flex; align-items:center; gap:10px; margin-bottom:15px;">
                                    <div class="post-avatar" style="overflow:hidden;">
                                        <?php if (!empty($post['avatar_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($post['avatar_url']); ?>" style="width:100%; height:100%; object-fit:cover;">
                                        <?php else: ?>
                                            <?php echo strtoupper(substr($post['author'], 0, 1)); ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="post-meta">
                                        <strong><?php echo htmlspecialchars($post['author']); ?></strong> 
                                        <span style="color:#aaa; font-size:0.75rem; margin-left:10px;"><?php echo date("d/m/Y H:i", strtotime($post['created_at'])); ?></span><br>
                                        <span style="color:#888; font-size:0.8rem;">📍 <?php echo htmlspecialchars($post['location'] ?? 'Inconnu'); ?></span>
                                    </div>
                                </div>
                                <p style="margin-bottom:15px; color:#333; line-height:1.5;"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                                
                                <?php if (!empty($post['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($post['image_url']); ?>" style="width:100%; border-radius:8px; margin-bottom:15px; border:1px solid #eee;">
                                <?php endif; ?>
                                
                                <div class="post-actions" style="border-top:1px solid #eee; padding-top:15px; color:#888; font-size:0.9rem; display:flex; gap:20px;">
                                    <span style="cursor:pointer;">❤️ J'aime</span>
                                    <span style="cursor:pointer;">💬 Commenter</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </main>

            <aside class="dash-right">
                <div class="card recommendations">
                    <h4>SUGGESTIONS D'ARTISANS</h4>
                    <br>
                    <?php foreach ($this->recommendations as $rec): ?>
                        <?php $recInit = strtoupper(substr($rec['display_name'], 0, 1)); ?>
                        <div class="rec-user" style="display:flex; align-items:center; gap:10px; margin-bottom:15px;">
                            <div class="rec-avatar" style="background:#eee; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; overflow:hidden;">
                                <?php if (!empty($rec['avatar_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($rec['avatar_url']); ?>" style="width:100%; height:100%; object-fit:cover;">
                                <?php else: ?>
                                    <?php echo $recInit; ?>
                                <?php endif; ?>
                            </div>
                            <div class="rec-info" style="flex:1; font-size:0.85rem;">
                                <strong><?php echo htmlspecialchars($rec['display_name']); ?></strong><br>
                                <span><?php echo htmlspecialchars(substr($rec['bio_free'] ?? 'Artisan', 0, 20)) . '...'; ?></span>
                            </div>
                            <button onclick="addFriendGlobal(<?php echo $rec['id_user']; ?>)" class="btn-outline" style="border-color:#333; color:#333; border-radius:20px; padding:5px 15px; font-size:0.75rem; cursor:pointer;">Suivre</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </aside>

        </div>

        <script>
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
                    if(data.success) { showToast('✅ Invitation envoyée avec succès !'); } 
                    else { showToast('⚠️ Invitation déjà en attente ou contact déjà ajouté.', false); }
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