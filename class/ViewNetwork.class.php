<?php

class ViewNetwork extends ViewPrivate {
    public function __construct($userData) {
        parent::__construct($userData);
        $this->pageTitle = "Mon Réseau - TradiShion";
    }

    protected function getBodyContent() {
        ob_start();
        $db = new Database();
        $pendingRequests = $db->getPendingRequests($_SESSION['id_user']);
        $myConnections = $db->getConnections($_SESSION['id_user']);
        ?>
        <div class="dash-container" style="grid-template-columns: 280px 1fr;">
            <aside class="dash-left">
                <div class="card profile-overview" style="display:flex; align-items:center; gap:15px; text-align:left;">
                    <a href="index.php?page=profile" style="text-decoration:none; color:inherit; display:flex; align-items:center; gap:15px;">
                        <div class="avatar-large" style="width:60px; height:60px; margin:0; overflow:hidden;">
                            <?php if (!empty($this->user['avatar_url'])): ?>
                                <img src="<?php echo htmlspecialchars($this->user['avatar_url']); ?>" style="width:100%; height:100%; object-fit:cover;" alt="Avatar">
                            <?php else: ?>
                                <?php echo strtoupper(substr($this->user['display_name'], 0, 1)); ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h4 style="margin:0;"><?php echo htmlspecialchars($this->user['display_name']); ?></h4>
                            <p class="location" style="margin:0; font-size:0.8rem;">📍 <?php echo htmlspecialchars($this->user['location'] ?? 'Inconnu'); ?></p>
                        </div>
                    </a>
                </div>
                <div class="card" style="padding: 15px; font-size: 0.9rem; display:flex; justify-content:space-between;">
                    <span style="color:#666;">Relations totales</span><strong><?php echo count($myConnections); ?></strong>
                </div>
                <div class="card nav-menu">
                   <ul style="list-style: none; padding: 0; margin: 0;">
    <li><a href="index.php?page=dashboard">🏠 Accueil</a></li>
    <li><a href="index.php?page=explorer">🧭 Explorateur</a></li>
    <li><a href="index.php?page=events">📅 Événements</a></li>
    <li><a href="index.php?page=messages">💬 Mes Messages</a></li>
    <li><a href="index.php?page=network">👥 Mon Réseau</a></li>
</ul>
                </div>
            </aside>

            <main class="network-content">
                <div class="card pending-requests">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                        <h3>Invitations en attente (<?php echo count($pendingRequests); ?>)</h3>
                    </div>
                    <?php if (empty($pendingRequests)): ?>
                        <p style="color:#888; text-align:center; padding:20px 0;">Vous n'avez aucune invitation en attente.</p>
                    <?php else: ?>
                        <?php foreach ($pendingRequests as $req): ?>
                            <?php $initial = strtoupper(substr($req['display_name'], 0, 1)); ?>
                            <div class="request-item" style="display:flex; align-items:center; justify-content:space-between; padding:15px 0; border-bottom:1px solid #eee;">
                                
                                <a href="index.php?page=profile&id=<?php echo $req['id_follower']; ?>" style="text-decoration:none; color:inherit; display:flex; align-items:center; gap:15px; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                    <div class="avatar-large" style="width:50px; height:50px; margin:0; background:#eee; display:flex; align-items:center; justify-content:center; border-radius:50%; font-size:1.2rem; font-weight:bold; overflow:hidden; color:#333;">
                                        <?php if (!empty($req['avatar_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($req['avatar_url']); ?>" style="width:100%; height:100%; object-fit:cover;">
                                        <?php else: ?>
                                            <?php echo $initial; ?>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <h4 style="margin:0;"><?php echo htmlspecialchars($req['display_name']); ?></h4>
                                        <span style="font-size:0.85rem; color:#666;"><?php echo htmlspecialchars($req['bio_free'] ?? 'Artisan'); ?></span><br>
                                        <small style="color:#888;">📍 <?php echo htmlspecialchars($req['location'] ?? 'Monde'); ?></small>
                                    </div>
                                </a>

                                <div style="display:flex; gap:10px; align-items:center;">
                                    <button onclick="rejectRequest(<?php echo $req['id_follower']; ?>)" style="background:none; border:none; font-size:1.2rem; cursor:pointer; color:#888;">✕</button>
                                    <button onclick="acceptRequest(<?php echo $req['id_follower']; ?>)" class="btn-outline" style="border-color:#A64B35; color:#A64B35; padding:8px 20px; border-radius:20px; cursor:pointer;">Accepter</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="card my-connections">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                        <h3>Mes relations</h3>
                    </div>
                    <?php if (empty($myConnections)): ?>
                        <p style="color:#888; text-align:center; padding:20px 0;">Vous n'avez pas encore de relations. Explorez la carte pour en trouver !</p>
                    <?php else: ?>
                        <div class="connections-grid" style="display:grid; grid-template-columns:repeat(3, 1fr); gap:20px;">
                            <?php foreach ($myConnections as $conn): ?>
                                <?php $initial = strtoupper(substr($conn['display_name'], 0, 1)); ?>
                                <div class="connection-card" style="border:1px solid #eee; border-radius:12px; padding:20px; text-align:center;">
                                    
                                    <a href="index.php?page=profile&id=<?php echo $conn['id_user']; ?>" style="text-decoration:none; color:inherit;">
                                        <div class="avatar-large" style="margin:0 auto 10px; width:60px; height:60px; background:#fdfaf5; border: 2px solid #A64B35; display:flex; align-items:center; justify-content:center; border-radius:50%; font-size:1.5rem; font-weight:bold; overflow:hidden; color:#333;">
                                            <?php if (!empty($conn['avatar_url'])): ?>
                                                <img src="<?php echo htmlspecialchars($conn['avatar_url']); ?>" style="width:100%; height:100%; object-fit:cover;">
                                            <?php else: ?>
                                                <?php echo $initial; ?>
                                            <?php endif; ?>
                                        </div>
                                        <h4 style="margin:0; transition: color 0.2s;" onmouseover="this.style.color='#A64B35'" onmouseout="this.style.color='inherit'"><?php echo htmlspecialchars($conn['display_name']); ?></h4>
                                    </a>

                                    <p style="font-size:0.8rem; color:#666; margin:5px 0; height:35px; overflow:hidden;"><?php echo htmlspecialchars($conn['bio_free'] ?? ''); ?></p>
                                    <small style="color:#888; display:block; margin-bottom:15px;">📍 <?php echo htmlspecialchars($conn['location'] ?? 'Inconnu'); ?></small>
                                    
                                    <div style="display:flex; gap: 10px; margin-top: 10px;">
                                        <a href="index.php?page=messages&contact=<?php echo $conn['id_user']; ?>" class="btn-outline" style="flex:1; border-color:#A64B35; color:#A64B35;">Message</a>
                                        <button onclick="openDeleteModal(<?php echo $conn['id_user']; ?>)" class="btn-outline" style="border-color:#ccc; color:#888; padding: 10px;" title="Retirer de mon réseau">✕</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>

        <div id="confirm-delete-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:2000; align-items:center; justify-content:center;">
            <div class="card" style="background:white; padding:30px; border-radius:12px; text-align:center; max-width:400px; width:90%; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
                <div style="font-size: 3rem; margin-bottom: 10px;">💔</div>
                <h3 style="margin-bottom:15px; color:#333; font-family:'Libre Baskerville', serif;">Retirer cette relation ?</h3>
                <p style="color:#666; margin-bottom:25px; font-size:0.95rem;">Êtes-vous sûr de vouloir retirer cet artisan de votre réseau ? Vous n'aurez plus accès à ses événements partagés.</p>
                <div style="display:flex; justify-content:center; gap:15px;">
                    <button onclick="closeDeleteModal()" class="btn-outline" style="border-color:#ddd; color:#555; padding:10px 20px; border-radius:8px; font-weight:bold; cursor:pointer;">Annuler</button>
                    <button onclick="confirmDeleteConnection()" class="btn-submit" style="background:#e74c3c; padding:10px 20px; border-radius:8px; width:auto; cursor:pointer;">Oui, retirer</button>
                </div>
            </div>
        </div>

        <script>
            function acceptRequest(idFollower) {
                const formData = new FormData(); formData.append('id_follower', idFollower);
                fetch('index.php?page=api_accept', { method: 'POST', body: formData }).then(res => res.json()).then(data => { if(data.success) { location.reload(); } });
            }

            function rejectRequest(idFollower) {
                const formData = new FormData(); formData.append('id_follower', idFollower);
                fetch('index.php?page=api_reject', { method: 'POST', body: formData }).then(res => res.json()).then(data => { if(data.success) { location.reload(); } });
            }

            let contactToDelete = null;
            function openDeleteModal(idContact) { contactToDelete = idContact; document.getElementById('confirm-delete-modal').style.display = 'flex'; }
            function closeDeleteModal() { contactToDelete = null; document.getElementById('confirm-delete-modal').style.display = 'none'; }
            function confirmDeleteConnection() {
                if(contactToDelete) {
                    const formData = new FormData(); formData.append('id_contact', contactToDelete);
                    fetch('index.php?page=api_remove_connection', { method: 'POST', body: formData }).then(res => res.json()).then(data => { 
                        if(data.success) { location.reload(); } else { showToast('Erreur lors de la suppression', false); }
                    });
                }
            }
        </script>
        <?php
        return ob_get_clean();
    }
}
?>