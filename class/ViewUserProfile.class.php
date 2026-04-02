<?php

class ViewUserProfile extends ViewPrivate {
    private $targetUser;
    private $connectionStatus;
    private $userPosts;

    public function __construct($currentUser, $targetUser, $connectionStatus, $userPosts) {
        parent::__construct($currentUser); 
        $this->pageTitle = "Profil de " . htmlspecialchars($targetUser['display_name']) . " - TradiShion";
        $this->targetUser = $targetUser; 
        $this->connectionStatus = $connectionStatus;
        $this->userPosts = $userPosts;
    }

    protected function getBodyContent() {
        ob_start();
        $initial = strtoupper(substr($this->targetUser['display_name'], 0, 1));
        $bannerUrl = !empty($this->targetUser['banner_url']) ? htmlspecialchars($this->targetUser['banner_url']) : 'assets/images/baniere_default.jpg';
        ?>
        <div style="max-width: 1200px; margin: 30px auto; padding: 0 20px;">
            <div class="card" style="padding:0; overflow:hidden; position:relative; margin-bottom:30px;">
                
                <div style="height:200px; background:url('<?php echo $bannerUrl; ?>') center/cover;"></div>
                
                <div style="padding: 0 40px 30px; display:flex; align-items:flex-end; gap:20px;">
                    <div style="margin-top:-60px; width:140px; height:140px; background:#fff; border:5px solid white; border-radius:50%; box-shadow:0 4px 10px rgba(0,0,0,0.1); display:flex; justify-content:center; align-items:center; font-size:3.5rem; font-weight:bold; color:#A64B35; z-index:10; overflow:hidden; flex-shrink:0;">
                        <?php if (!empty($this->targetUser['avatar_url'])): ?>
                            <img src="<?php echo htmlspecialchars($this->targetUser['avatar_url']); ?>" style="width:100%; height:100%; object-fit:cover;" alt="Avatar">
                        <?php else: ?>
                            <?php echo $initial; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div style="flex:1; padding-bottom:10px;">
                        <h1 style="margin:0; font-size:2rem; color:#333;"><?php echo htmlspecialchars($this->targetUser['display_name']); ?></h1>
                        <p style="color:#666; margin:5px 0;">📍 <?php echo htmlspecialchars($this->targetUser['location'] ?? 'Localisation non renseignée'); ?></p>
                    </div>
                    
                    <div style="padding-bottom:10px; display:flex; gap:10px;">
                        <?php if (!$this->connectionStatus): ?>
                            <button onclick="addFriendGlobal(<?php echo $this->targetUser['id_user']; ?>)" class="btn-submit" style="padding:10px 20px; border-radius:25px;">➕ Se connecter</button>
                        <?php elseif ($this->connectionStatus['status'] == 'pending'): ?>
                            <button class="btn-outline" disabled style="border-color:#ccc; color:#888; padding:10px 20px; border-radius:25px; cursor:not-allowed; background:white;">⏳ En attente</button>
                        <?php elseif ($this->connectionStatus['status'] == 'accepted'): ?>
                            <a href="index.php?page=messages&contact=<?php echo $this->targetUser['id_user']; ?>" class="btn-outline" style="border-color:#A64B35; color:#A64B35; padding:10px 20px; border-radius:25px; background:white;">💬 Message</a>
                            <button onclick="removeConnection(<?php echo $this->targetUser['id_user']; ?>)" class="btn-outline" style="border-color:#ccc; color:#888; padding:10px 20px; border-radius:25px; background:white;">💔 Retirer</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 2fr; gap:30px;">
                
                <aside style="display:flex; flex-direction:column; gap:20px;">
                    <div class="card nav-menu" style="margin-bottom:0;">
                       <ul style="list-style: none; padding: 0; margin: 0;">
                            <li><a href="index.php?page=dashboard">🏠 Accueil</a></li>
                            <li><a href="index.php?page=explorer">🧭 Explorateur</a></li>
                            <li><a href="index.php?page=messages">💬 Mes Messages</a></li>
                            <li><a href="index.php?page=network">👥 Mon Réseau</a></li>
                        </ul>
                    </div>

                    <div class="card" style="margin-bottom:0;">
                        <h3 style="border-bottom:2px solid #FFF0ED; padding-bottom:10px; font-size:1rem; color:#333;">À propos</h3>
                        <p style="color:#555; font-size:0.95rem; white-space:pre-wrap; margin-top:15px;"><?php echo htmlspecialchars($this->targetUser['bio_free'] ?? 'Aucune biographie pour le moment.'); ?></p>
                    </div>

                    <div class="card" style="margin-bottom:0;">
                        <h3 style="border-bottom:2px solid #FFF0ED; padding-bottom:10px; font-size:1rem; color:#333;">Maîtrise de techniques</h3>
                        <div style="display:flex; flex-direction:column; gap:12px; margin-top:15px;">
                            <?php 
                            $skills = array_filter(array_map('trim', explode(',', $this->targetUser['skills'] ?? '')));
                            if(empty($skills)): ?>
                                <p style="color:#888; font-size:0.85rem;">Aucune technique renseignée.</p>
                            <?php else: 
                                foreach($skills as $skill): ?>
                                    <div>
                                        <div style="display:flex; justify-content:space-between; font-size:0.85rem; color:#555; margin-bottom:5px;">
                                            <span><?php echo htmlspecialchars($skill); ?></span>
                                            <strong style="color:#A64B35;">Avancé</strong>
                                        </div>
                                        <div style="height:6px; background:#eee; border-radius:3px;">
                                            <div style="height:100%; width:80%; background:#A64B35; border-radius:3px;"></div>
                                        </div>
                                    </div>
                                <?php endforeach; 
                            endif; ?>
                        </div>
                    </div>
                </aside>

                <main style="display:flex; flex-direction:column; gap:30px;">
                    
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:30px;">
                        <div class="card" style="margin-bottom:0;">
                            <h3 style="border-bottom:2px solid #FFF0ED; padding-bottom:10px; font-size:1rem; color:#333;">Centres d'intérêt</h3>
                            <div style="display:flex; flex-wrap:wrap; gap:8px; margin-top:15px;">
                                <?php 
                                $interests = array_filter(array_map('trim', explode(',', $this->targetUser['interests'] ?? '')));
                                if(empty($interests)): ?>
                                    <p style="color:#888; font-size:0.85rem;">Aucun centre d'intérêt.</p>
                                <?php else: 
                                    foreach($interests as $int): ?>
                                        <span style="background:#f4f6f8; padding:6px 12px; border-radius:20px; font-size:0.8rem; color:#555; font-weight:bold;">#<?php echo htmlspecialchars($int); ?></span>
                                    <?php endforeach; 
                                endif; ?>
                            </div>
                        </div>

                        <div class="card" style="margin-bottom:0;">
                            <h3 style="border-bottom:2px solid #FFF0ED; padding-bottom:10px; font-size:1rem; color:#333;">Coordonnées</h3>
                            <ul style="list-style:none; padding:0; margin-top:15px; font-size:0.85rem; color:#555; display:flex; flex-direction:column; gap:12px;">
                                <li style="display:flex; gap:10px; align-items:center;">✉️ <?php echo htmlspecialchars($this->targetUser['contact_email'] ?? 'Non renseigné'); ?></li>
                                <li style="display:flex; gap:10px; align-items:center;">🌐 <?php echo htmlspecialchars($this->targetUser['website'] ?? 'Non renseigné'); ?></li>
                                <li style="display:flex; gap:10px; align-items:center;">📸 <?php echo htmlspecialchars($this->targetUser['social_link'] ?? 'Non renseigné'); ?></li>
                            </ul>
                        </div>
                    </div>

                    <div class="card" style="margin-bottom:0;">
                        <h3 style="border-bottom:2px solid #FFF0ED; padding-bottom:10px; margin-bottom:20px; color:#333;">Créations de <?php echo htmlspecialchars($this->targetUser['display_name']); ?></h3>
                        <?php if (empty($this->userPosts)): ?>
                            <p style="color:#888; text-align:center; padding:40px;">Aucune publication pour le moment.</p>
                        <?php else: ?>
                            <div id="posts-feed">
                                <?php foreach ($this->userPosts as $post): ?>
                                    <div class="feed-post" style="margin-bottom:15px; padding-bottom:20px; border-bottom:1px solid #eee;">
                                        <div class="post-meta" style="margin-bottom:10px;">
                                            <span style="color:#aaa; font-size:0.75rem;"><?php echo date("d/m/Y H:i", strtotime($post['created_at'])); ?></span>
                                        </div>
                                        <p style="color:#333; line-height:1.5; margin-bottom:10px;"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                                        <?php if (!empty($post['image_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($post['image_url']); ?>" style="width:100%; border-radius:8px; margin-bottom:15px; border:1px solid #eee;">
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </main>
            </div>
        </div>

        <script>
            function removeConnection(idContact) {
                if(confirm("Voulez-vous vraiment supprimer cette relation ?")) {
                    const formData = new FormData(); formData.append('id_contact', idContact);
                    fetch('index.php?page=api_remove_connection', { method: 'POST', body: formData })
                    .then(res => res.json()).then(data => { if(data.success) { location.reload(); } });
                }
            }
        </script>
        <?php
        return ob_get_clean();
    }
}
?>