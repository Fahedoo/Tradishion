<?php

class ViewNetwork extends ViewPrivate {
    public function __construct($userData) {
        parent::__construct($userData);
        $this->pageTitle = $this->t('menu_network') . " - TradiShion";
    }

    protected function getBodyContent() {
        ob_start();
        $db = new Database();
        $pendingRequests = $db->getPendingRequests($_SESSION['id_user']);
        $myConnections = $db->getConnections($_SESSION['id_user']);
        ?>
        <style> html { overflow-y: scroll; } </style>

        <div class="dash-container" style="display:grid; gap: 24px; padding-top: 24px; max-width: 1500px; margin: 0 auto;">

            <main class="network-content" style="display:flex; flex-direction:column; gap:30px; min-height: 800px;">
                <div class="card" style="margin:0; border:none; box-shadow:0 4px 18px rgba(0,0,0,0.04); padding:10px; display:flex; gap:10px; align-items:center;">
                    <button id="tab-friends-btn" type="button" class="btn-outline" style="border-color:#A64B35; color:#A64B35; background:#FFF0ED; padding:8px 16px; border-radius:20px;"><?php echo $this->t('net_tab_friends'); ?></button>
                    <button id="tab-invites-btn" type="button" class="btn-outline" style="border-color:#ddd; color:#666; background:white; padding:8px 16px; border-radius:20px;"><?php echo $this->t('net_tab_invites'); ?></button>
                    <div id="friends-sort-wrap" style="margin-left:auto; display:flex; align-items:center; gap:8px;">
                        <label for="friends-sort" style="font-size:0.85rem; color:#666;"><?php echo $this->t('net_sort'); ?></label>
                        <select id="friends-sort" style="padding:7px 10px; border:1px solid #ddd; border-radius:8px;">
                            <option value="name_asc"><?php echo $this->t('net_sort_az'); ?></option>
                            <option value="name_desc"><?php echo $this->t('net_sort_za'); ?></option>
                        </select>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:repeat(3, minmax(180px, 1fr)); gap:14px;">
                    <div class="card" style="margin:0; border:none; box-shadow:0 4px 18px rgba(0,0,0,0.04);">
                        <small style="color:#887971; text-transform:uppercase; letter-spacing:0.04em;"><?php echo $this->t('net_relations'); ?></small>
                        <p style="font-size:1.55rem; color:#2f2521; margin:4px 0 0;"><?php echo count($myConnections); ?></p>
                    </div>
                    <div class="card" style="margin:0; border:none; box-shadow:0 4px 18px rgba(0,0,0,0.04);">
                        <small style="color:#887971; text-transform:uppercase; letter-spacing:0.04em;"><?php echo $this->t('net_tab_invites'); ?></small>
                        <p style="font-size:1.55rem; color:#2f2521; margin:4px 0 0;"><?php echo count($pendingRequests); ?></p>
                    </div>
                    <div class="card" style="margin:0; border:none; box-shadow:0 4px 18px rgba(0,0,0,0.04);">
                        <small style="color:#887971; text-transform:uppercase; letter-spacing:0.04em;"><?php echo $this->t('net_accept_rate'); ?></small>
                        <p style="font-size:1.55rem; color:#2f2521; margin:4px 0 0;"><?php echo count($myConnections) + count($pendingRequests) > 0 ? (int) round((count($myConnections) / (count($myConnections) + count($pendingRequests))) * 100) : 0; ?>%</p>
                    </div>
                </div>

                <div id="tab-invites" class="card pending-requests" style="display:none; border:none; box-shadow:0 4px 20px rgba(0,0,0,0.03); border-radius:16px; padding:30px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:2px solid #FFF0ED; padding-bottom:15px;">
                        <h3 style="margin:0; font-family:'Libre Baskerville', serif; color:#333;"><?php echo $this->t('net_pending'); ?> (<?php echo count($pendingRequests); ?>)</h3>
                    </div>
                    <?php if (empty($pendingRequests)): ?>
                        <p style="color:#888; text-align:center; padding:30px 0; font-size:1rem;"><?php echo $this->t('net_no_pending'); ?></p>
                    <?php else: ?>
                        <?php foreach ($pendingRequests as $req): ?>
                            <?php $initial = $this->getTextInitial($req['display_name']); ?>
                            <div class="request-item" style="display:flex; align-items:center; justify-content:space-between; padding:15px 0; border-bottom:1px solid #f5f5f5;">
                                
                                <a href="index.php?page=profile&id=<?php echo $req['id_follower']; ?>" style="text-decoration:none; color:inherit; display:flex; align-items:center; gap:15px; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                    <div class="avatar-large" style="width:55px; height:55px; margin:0; background:#eee; display:flex; align-items:center; justify-content:center; border-radius:50%; font-size:1.2rem; font-weight:bold; overflow:hidden; color:#333;">
                                        <?php if (!empty($req['avatar_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($req['avatar_url']); ?>" style="width:100%; height:100%; object-fit:cover;">
                                        <?php else: ?>
                                            <?php echo $initial; ?>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <h4 style="margin:0 0 5px 0; font-size:1.05rem; color:#333;"><?php echo htmlspecialchars($req['display_name']); ?></h4>
                                        <span style="font-size:0.85rem; color:#666; display:block; margin-bottom:3px;"><?php echo htmlspecialchars($req['bio_free'] ?? 'Artisan'); ?></span>
                                        <small style="color:#888; font-weight:bold;">🌍 <?php echo $this->formatOrigins($req['origins'] ?? []); ?></small>
                                    </div>
                                </a>

                                <div style="display:flex; gap:10px; align-items:center;">
                                    <button onclick="rejectRequest(<?php echo $req['id_follower']; ?>)" style="background:none; border:none; font-size:1.2rem; cursor:pointer; color:#aaa; transition:color 0.2s;" onmouseover="this.style.color='#e74c3c'" onmouseout="this.style.color='#aaa'">✕</button>
                                    <button onclick="acceptRequest(<?php echo $req['id_follower']; ?>)" class="btn-outline" style="background:#FFF0ED; border:none; color:#A64B35; padding:8px 25px; border-radius:20px; cursor:pointer; font-weight:bold; transition:all 0.2s;" onmouseover="this.style.background='#A64B35'; this.style.color='white';" onmouseout="this.style.background='#FFF0ED'; this.style.color='#A64B35';"><?php echo $this->t('btn_accept'); ?></button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div id="tab-friends" class="card my-connections" style="border:none; box-shadow:0 4px 20px rgba(0,0,0,0.03); border-radius:16px; padding:30px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; border-bottom:2px solid #FFF0ED; padding-bottom:15px;">
                        <h3 style="margin:0; font-family:'Libre Baskerville', serif; color:#333;"><?php echo $this->t('net_my_connections'); ?></h3>
                    </div>
                    <?php if (empty($myConnections)): ?>
                        <div style="text-align:center; padding:40px 0;">
                            <span style="font-size:3rem; opacity:0.5; display:block; margin-bottom:15px;">👥</span>
                            <p style="color:#888; font-size:1rem; margin:0;"><?php echo $this->t('net_no_connections'); ?></p>
                        </div>
                    <?php else: ?>
                        <div id="connections-grid" class="connections-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(220px, 1fr)); gap:25px;">
                            <?php foreach ($myConnections as $conn): ?>
                                <?php $initial = $this->getTextInitial($conn['display_name']); ?>
                                <div class="connection-card" data-name="<?php $dn = $conn['display_name'] ?? ''; echo htmlspecialchars(function_exists('mb_strtolower') ? mb_strtolower($dn, 'UTF-8') : strtolower($dn)); ?>" style="border:1px solid #f0f0f0; border-radius:16px; padding:25px; text-align:center; transition:box-shadow 0.2s ease; box-shadow:0 2px 10px rgba(0,0,0,0.02);" onmouseover="this.style.boxShadow='0 8px 25px rgba(0,0,0,0.06)'" onmouseout="this.style.boxShadow='0 2px 10px rgba(0,0,0,0.02)'">
                                    
                                    <a href="index.php?page=profile&id=<?php echo $conn['id_user']; ?>" style="text-decoration:none; color:inherit;">
                                        <div class="avatar-large" style="margin:0 auto 15px; width:70px; height:70px; background:#fdfaf5; border: 2px solid #A64B35; display:flex; align-items:center; justify-content:center; border-radius:50%; font-size:1.8rem; font-weight:bold; overflow:hidden; color:#333;">
                                            <?php if (!empty($conn['avatar_url'])): ?>
                                                <img src="<?php echo htmlspecialchars($conn['avatar_url']); ?>" style="width:100%; height:100%; object-fit:cover;">
                                            <?php else: ?>
                                                <?php echo $initial; ?>
                                            <?php endif; ?>
                                        </div>
                                        <h4 style="margin:0 0 5px 0; font-size:1.1rem; color:#333; transition: color 0.2s;" onmouseover="this.style.color='#A64B35'" onmouseout="this.style.color='#333'"><?php echo htmlspecialchars($conn['display_name']); ?></h4>
                                    </a>

                                    <small style="color:#888; display:block; margin-bottom:10px; font-weight:bold;">🌍 <?php echo $this->formatOrigins($conn['origins'] ?? []); ?></small>
                                    
                                    <p style="font-size:0.85rem; color:#666; margin:10px 0; height:38px; overflow:hidden; text-overflow:ellipsis; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;"><?php echo htmlspecialchars($conn['bio_free'] ?? ''); ?></p>
                                    
                                    <div style="display:flex; gap: 10px; margin-top: 20px;">
                                        <a href="index.php?page=messages&contact=<?php echo $conn['id_user']; ?>" class="btn-outline" style="flex:1; border:1px solid #A64B35; color:#A64B35; padding:8px; border-radius:8px; font-weight:bold; transition:all 0.2s;" onmouseover="this.style.background='#A64B35'; this.style.color='white';" onmouseout="this.style.background='transparent'; this.style.color='#A64B35';"><?php echo $this->t('btn_message'); ?></a>
                                        <button onclick="openDeleteModal(<?php echo $conn['id_user']; ?>)" class="btn-outline" style="border:1px solid #eee; color:#888; padding: 8px 15px; border-radius:8px; transition:all 0.2s;" title="Retirer de mon réseau" onmouseover="this.style.background='#fff1f0'; this.style.color='#e74c3c'; this.style.borderColor='#ffccc7';" onmouseout="this.style.background='transparent'; this.style.color='#888'; this.style.borderColor='#eee';">✕</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>

        <div id="confirm-delete-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:2000; align-items:center; justify-content:center;">
            <div class="card" style="background:white; padding:40px; border-radius:16px; text-align:center; max-width:400px; width:90%; box-shadow: 0 15px 40px rgba(0,0,0,0.2);">
                <div style="font-size: 3.5rem; margin-bottom: 15px;">💔</div>
                <h3 style="margin-bottom:15px; color:#333; font-family:'Libre Baskerville', serif; font-size:1.4rem;"><?php echo $this->t('net_remove_title'); ?></h3>
                <p style="color:#666; margin-bottom:30px; font-size:0.95rem; line-height:1.5;"><?php echo $this->t('net_remove_desc'); ?></p>
                <div style="display:flex; justify-content:center; gap:15px;">
                    <button onclick="closeDeleteModal()" class="btn-outline" style="flex:1; border:1px solid #ddd; color:#555; padding:12px; border-radius:8px; font-weight:bold; cursor:pointer; transition:0.2s;" onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='transparent'"><?php echo $this->t('btn_cancel'); ?></button>
                    <button onclick="confirmDeleteConnection()" class="btn-submit" style="flex:1; background:#e74c3c; border:none; color:white; padding:12px; border-radius:8px; cursor:pointer; font-weight:bold; transition:0.2s;" onmouseover="this.style.background='#c0392b'" onmouseout="this.style.background='#e74c3c'"><?php echo $this->t('btn_remove'); ?></button>
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

            const tabFriendsBtn = document.getElementById('tab-friends-btn');
            const tabInvitesBtn = document.getElementById('tab-invites-btn');
            const tabFriends = document.getElementById('tab-friends');
            const tabInvites = document.getElementById('tab-invites');
            const friendsSortWrap = document.getElementById('friends-sort-wrap');
            const friendsSort = document.getElementById('friends-sort');

            function setTab(tab) {
                const friendsActive = tab === 'friends';
                tabFriends.style.display = friendsActive ? 'block' : 'none';
                tabInvites.style.display = friendsActive ? 'none' : 'block';
                friendsSortWrap.style.display = friendsActive ? 'flex' : 'none';

                tabFriendsBtn.style.background = friendsActive ? '#FFF0ED' : 'white';
                tabFriendsBtn.style.borderColor = friendsActive ? '#A64B35' : '#ddd';
                tabFriendsBtn.style.color = friendsActive ? '#A64B35' : '#666';

                tabInvitesBtn.style.background = friendsActive ? 'white' : '#FFF0ED';
                tabInvitesBtn.style.borderColor = friendsActive ? '#ddd' : '#A64B35';
                tabInvitesBtn.style.color = friendsActive ? '#666' : '#A64B35';
            }

            function sortFriends() {
                const grid = document.getElementById('connections-grid');
                if (!grid) return;
                const cards = Array.from(grid.querySelectorAll('.connection-card'));
                const mode = friendsSort.value;

                cards.sort((a, b) => {
                    const aName = a.getAttribute('data-name') || '';
                    const bName = b.getAttribute('data-name') || '';
                    return mode === 'name_desc' ? bName.localeCompare(aName) : aName.localeCompare(bName);
                });

                cards.forEach(card => grid.appendChild(card));
            }

            tabFriendsBtn?.addEventListener('click', () => setTab('friends'));
            tabInvitesBtn?.addEventListener('click', () => setTab('invites'));
            friendsSort?.addEventListener('change', sortFriends);

            setTab('friends');
            sortFriends();
        </script>
        <?php
        return ob_get_clean();
    }
}
?>