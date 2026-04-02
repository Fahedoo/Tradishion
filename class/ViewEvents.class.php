<?php

class ViewEvents extends ViewPrivate {
    private $todayPublicEvents;
    private $myOrganizedEvents;
    private $myConnections;
    private $pendingEventInvites;

    public function __construct($userData, $todayEvents, $myEvents, $connections, $pendingEventInvites) {
        parent::__construct($userData);
        $this->pageTitle = "Événements - TradiShion";
        $this->todayPublicEvents = $todayEvents;
        $this->myOrganizedEvents = $myEvents;
        $this->myConnections = $connections;
        $this->pendingEventInvites = $pendingEventInvites;
    }

    protected function getBodyContent() {
        ob_start();
        ?>
        <div class="dash-container" style="display:grid; grid-template-columns: 280px minmax(600px, 1fr) 320px; gap: 40px; padding-top: 40px; width: 100%;">
            
            <aside class="dash-left">
                <div class="card profile-overview" style="text-align: center; padding: 30px 20px;">
                    <a href="index.php?page=profile" style="text-decoration:none; color:inherit;">
                        <div class="avatar-large" style="margin: 0 auto 15px; width: 90px; height: 90px; overflow:hidden;">
                            <?php if (!empty($this->user['avatar_url'])): ?>
                                <img src="<?php echo htmlspecialchars($this->user['avatar_url']); ?>" style="width:100%; height:100%; object-fit:cover;" alt="Avatar">
                            <?php else: ?>
                                <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#eee; color:#333; font-size:2rem; font-weight:bold;">
                                    <?php echo strtoupper(substr($this->user['display_name'], 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <h3 style="font-size: 1.2rem; margin-bottom: 5px;"><?php echo htmlspecialchars($this->user['display_name']); ?></h3>
                    </a>
                </div>

                <div class="card nav-menu">
                   <ul style="list-style: none; padding: 0; margin: 0;">
                        <li><a href="index.php?page=dashboard">🏠 Accueil</a></li>
                        <li><a href="index.php?page=explorer">🧭 Explorateur</a></li>
                        <li><a href="index.php?page=events" class="active" style="background:#FFF0ED; color:#A64B35; font-weight:bold; padding: 12px 15px; border-radius: 8px;">📅 Événements</a></li>
                        <li><a href="index.php?page=messages">💬 Mes Messages</a></li>
                        <li><a href="index.php?page=network">👥 Mon Réseau</a></li>
                    </ul>
                </div>
            </aside>

            <main class="dash-center" style="display:flex; flex-direction:column; gap:25px; width:100%; align-items:stretch;">
                
                <div style="display:flex; justify-content:space-between; align-items:flex-end; border-bottom: 2px solid #FFF0ED; padding-bottom: 15px; margin-bottom: 10px;">
                    <h2 style="font-family:'Libre Baskerville', serif; color:#333; margin:0; font-size: 1.6rem;">Aujourd'hui</h2>
                    <span style="color:#A64B35; font-weight:bold; font-size:1rem;"><?php echo date("d/m/Y"); ?></span>
                </div>
                
                <?php if (empty($this->todayPublicEvents)): ?>
                    <div class="card" style="text-align:center; padding:60px 20px; color:#888; background: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); width:100%;">
                        <span style="font-size:3.5rem; display:block; margin-bottom:20px; opacity: 0.5;">🏜️</span>
                        <p style="font-size: 1.1rem; margin:0;">Aucun événement public prévu pour aujourd'hui.</p>
                    </div>
                <?php else: ?>
                    <div style="display:flex; flex-direction:column; gap:30px; width:100%;">
                        <?php foreach ($this->todayPublicEvents as $ev): ?>
                            <div class="card" style="padding:0; overflow:hidden; border:none; box-shadow:0 8px 25px rgba(0,0,0,0.04); border-radius: 16px; width:100%;">
                                <?php 
                                    $coverImage = !empty($ev['image_url']) ? htmlspecialchars($ev['image_url']) : 'assets/images/events.jpg'; 
                                ?>
                                <div style="width:100%; height:300px; background:url('<?php echo $coverImage; ?>') center/cover;"></div>
                                
                                <div style="padding:30px;">
                                    <h3 style="margin:0 0 20px 0; color:#A64B35; font-size:1.6rem; font-family:'Libre Baskerville', serif;">
                                        <?php echo htmlspecialchars($ev['title']); ?>
                                    </h3>
                                    
                                    <div style="display:flex; flex-direction:column; gap:12px; font-size:1rem; color:#555; margin-bottom:25px; background: #fcfcfc; padding: 15px; border-radius: 8px; border: 1px solid #f0f0f0;">
                                        <span style="display:flex; align-items:center; gap:10px;">
                                            🕒 <strong style="color: #333;"><?php echo substr($ev['start_time'], 11, 5) . ' - ' . substr($ev['end_time'], 11, 5); ?></strong>
                                        </span>
                                        <span style="display:flex; align-items:center; gap:10px;">
                                            📍 <span style="color: #444;"><?php echo htmlspecialchars($ev['meeting_url']); ?></span>
                                        </span>
                                    </div>

                                    <div style="display:flex; align-items:center; gap:12px; padding-bottom:20px; border-bottom:1px solid #eee;">
                                        <div style="width:40px; height:40px; border-radius:50%; background:#ddd; display:flex; align-items:center; justify-content:center; overflow:hidden; font-size:1rem; color:#333; font-weight:bold;">
                                            <?php if (!empty($ev['avatar_url'])): ?>
                                                <img src="<?php echo htmlspecialchars($ev['avatar_url']); ?>" style="width:100%; height:100%; object-fit:cover;">
                                            <?php else: ?>
                                                <?php echo strtoupper(substr($ev['organizer_name'], 0, 1)); ?>
                                            <?php endif; ?>
                                        </div>
                                        <span style="font-size:0.95rem; color:#666;">
                                            Organisé par <strong style="color:#333;"><?php echo htmlspecialchars($ev['organizer_name']); ?></strong>
                                        </span>
                                    </div>

                                    <?php if (!empty($ev['description'])): ?>
                                        <p style="font-size:1rem; color:#444; line-height:1.7; margin:20px 0 0 0;">
                                            <?php echo nl2br(htmlspecialchars($ev['description'])); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </main>

            <aside class="dash-right">
                <div class="card" style="padding:0; background: transparent; box-shadow: none;">
                    <h3 style="border-bottom:2px solid #ddd; padding-bottom:15px; color:#333; margin-bottom:25px; font-size:1.15rem; text-transform:uppercase; letter-spacing: 1px;">Mes Événements</h3>
                    
                    <?php if (empty($this->myOrganizedEvents)): ?>
                        <div class="card" style="text-align:center; padding:30px 20px;">
                            <p style="color:#888; font-size:0.95rem; margin:0;">Vous n'avez aucun événement à venir.</p>
                        </div>
                    <?php else: ?>
                        <div style="display:flex; flex-direction:column; gap:20px;">
                            <?php foreach ($this->myOrganizedEvents as $myEv): ?>
                                <?php $isMine = ($myEv['id_organizer'] == $this->user['id_user']); ?>
                                
                                <div class="card" style="padding:20px; border-left: 5px solid <?php echo $isMine ? '#A64B35' : '#4caf50'; ?>; border-radius: 12px; margin:0; transition: transform 0.2s ease, box-shadow 0.2s ease;">
                                    
                                    <h4 style="margin:0 0 <?php echo $isMine ? '15px' : '5px'; ?> 0; font-size:1.15rem; color:#333; font-family: 'Libre Baskerville', serif;">
                                        <?php echo htmlspecialchars($myEv['title']); ?>
                                    </h4>
                                    
                                    <?php if (!$isMine): ?>
                                        <p style="font-size:0.85rem; color:#888; margin-bottom:15px; margin-top:0;">Organisé par <?php echo htmlspecialchars($myEv['organizer_name']); ?></p>
                                    <?php endif; ?>
                                    
                                    <div style="font-size:0.9rem; color:#666; margin-bottom:20px; display:flex; flex-direction:column; gap:8px; background: <?php echo $isMine ? '#fdfaf5' : '#f4f9f5'; ?>; padding: 10px 15px; border-radius: 6px;">
                                        <span style="display:flex; align-items:center; gap:8px;">📅 <strong style="color:#555;"><?php echo date("d/m/Y", strtotime($myEv['start_time'])); ?></strong></span>
                                        <span style="display:flex; align-items:center; gap:8px;">🕒 <strong style="color:#555;"><?php echo substr($myEv['start_time'], 11, 5) . ' - ' . substr($myEv['end_time'], 11, 5); ?></strong></span>
                                    </div>
                                    
                                    <?php if ($isMine): ?>
                                        <button onclick="openInviteModal(<?php echo $myEv['id_event']; ?>)" class="btn-outline" style="width:100%; border: 1px solid #A64B35; background: white; color:#A64B35; padding:12px; border-radius:8px; font-size:0.95rem; cursor:pointer; font-weight:bold; transition:all 0.2s; display:flex; align-items:center; justify-content:center; gap:8px;" onmouseover="this.style.background='#FFF0ED'" onmouseout="this.style.background='white'">
                                            ✉️ Inviter mes relations
                                        </button>
                                    <?php else: ?>
                                        <div style="width:100%; text-align:center; background:#e8f5e9; color:#2e7d32; padding:12px; border-radius:8px; font-size:0.95rem; font-weight:bold; box-sizing: border-box;">
                                            ✅ Vous participez
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card" style="padding:0; background: transparent; box-shadow: none; margin-top:40px;">
                    <h3 style="border-bottom:2px solid #ddd; padding-bottom:15px; color:#333; margin-bottom:25px; font-size:1.15rem; text-transform:uppercase; letter-spacing: 1px;">Invitations</h3>
                    
                    <?php if (empty($this->pendingEventInvites)): ?>
                        <div class="card" style="text-align:center; padding:30px 20px;">
                            <p style="color:#888; font-size:0.95rem; margin:0;">Vous n'avez aucune invitation en attente.</p>
                        </div>
                    <?php else: ?>
                        <div style="display:flex; flex-direction:column; gap:20px;">
                            <?php foreach ($this->pendingEventInvites as $inv): ?>
                                <div class="card" style="padding:20px; border-left: 5px solid #4caf50; border-radius: 12px; margin:0; transition: transform 0.2s ease, box-shadow 0.2s ease;">
                                    <h4 style="margin:0 0 5px 0; font-size:1.15rem; color:#333; font-family: 'Libre Baskerville', serif;">
                                        <?php echo htmlspecialchars($inv['title']); ?>
                                    </h4>
                                    <p style="font-size:0.85rem; color:#888; margin-bottom:15px; margin-top:0;">Par <?php echo htmlspecialchars($inv['organizer_name']); ?></p>
                                    
                                    <div style="font-size:0.9rem; color:#666; margin-bottom:20px; display:flex; flex-direction:column; gap:8px; background: #f4f9f5; padding: 10px 15px; border-radius: 6px;">
                                        <span style="display:flex; align-items:center; gap:8px;">📅 <strong style="color:#555;"><?php echo date("d/m/Y", strtotime($inv['start_time'])); ?></strong></span>
                                        <span style="display:flex; align-items:center; gap:8px;">🕒 <strong style="color:#555;"><?php echo substr($inv['start_time'], 11, 5) . ' - ' . substr($inv['end_time'], 11, 5); ?></strong></span>
                                    </div>
                                    
                                    <div style="display:flex; gap:10px;">
                                        <button onclick="respondInvite(<?php echo $inv['id_event']; ?>, 'accepted')" class="btn-submit" style="flex:1; padding:10px; border-radius:8px; font-size:0.9rem; background:#4caf50;">Accepter</button>
                                        <button onclick="respondInvite(<?php echo $inv['id_event']; ?>, 'declined')" class="btn-outline" style="flex:1; padding:10px; border-radius:8px; font-size:0.9rem; border-color:#ccc; color:#888;">Refuser</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </aside>
        </div>

        <div id="invite-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:2000; align-items:center; justify-content:center;">
            <div class="card" style="width:100%; max-width:450px; padding:35px; background:white; border-radius:16px; box-shadow:0 15px 40px rgba(0,0,0,0.2);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
                    <h3 style="margin:0; color:#333; font-family:'Libre Baskerville', serif; font-size: 1.3rem;">Inviter des relations</h3>
                    <button onclick="closeInviteModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:#888; transition: color 0.2s;" onmouseover="this.style.color='#333'" onmouseout="this.style.color='#888'">✕</button>
                </div>
                
                <div style="max-height:350px; overflow-y:auto; padding-right:10px;">
                    <?php if (empty($this->myConnections)): ?>
                        <p style="color:#888; text-align:center; padding: 20px 0;">Vous n'avez pas encore de relations à inviter.</p>
                    <?php else: ?>
                        <?php foreach ($this->myConnections as $conn): ?>
                            <div style="display:flex; align-items:center; justify-content:space-between; padding:15px 0; border-bottom:1px solid #f5f5f5;">
                                <div style="display:flex; align-items:center; gap:15px;">
                                    <div style="width:45px; height:45px; border-radius:50%; background:#eee; display:flex; align-items:center; justify-content:center; overflow:hidden; font-weight:bold; color:#333; font-size: 1.1rem;">
                                        <?php if (!empty($conn['avatar_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($conn['avatar_url']); ?>" style="width:100%; height:100%; object-fit:cover;">
                                        <?php else: ?>
                                            <?php echo strtoupper(substr($conn['display_name'], 0, 1)); ?>
                                        <?php endif; ?>
                                    </div>
                                    <strong style="font-size:1rem; color:#333;"><?php echo htmlspecialchars($conn['display_name']); ?></strong>
                                </div>
                                <button onclick="sendInvite(<?php echo $conn['id_user']; ?>, this)" style="background:#FFF0ED; color:#A64B35; border:none; padding:8px 18px; border-radius:20px; font-size:0.9rem; cursor:pointer; font-weight:bold; transition:all 0.2s;" onmouseover="this.style.background='#A64B35'; this.style.color='white';" onmouseout="this.style.background='#FFF0ED'; this.style.color='#A64B35';">Inviter</button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <script>
            let currentEventIdToInvite = null;

            function openInviteModal(eventId) {
                currentEventIdToInvite = eventId;
                document.getElementById('invite-modal').style.display = 'flex';
                document.querySelectorAll('#invite-modal button').forEach(btn => {
                    if(btn.innerText === "Envoyé ✓") {
                        btn.innerText = "Inviter";
                        btn.style.opacity = "1";
                        btn.style.pointerEvents = "auto";
                        btn.style.background = "#FFF0ED";
                        btn.style.color = "#A64B35";
                    }
                });
            }

            function closeInviteModal() {
                currentEventIdToInvite = null;
                document.getElementById('invite-modal').style.display = 'none';
            }

            function sendInvite(idGuest, btnElement) {
                if(!currentEventIdToInvite) return;
                const fd = new FormData();
                fd.append('id_event', currentEventIdToInvite);
                fd.append('id_guest', idGuest);

                fetch('index.php?page=api_invite_event', { method: 'POST', body: fd })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        btnElement.innerText = "Envoyé ✓";
                        btnElement.style.background = "#e8f5e9";
                        btnElement.style.color = "#2e7d32";
                        btnElement.style.opacity = "0.7";
                        btnElement.style.pointerEvents = "none";
                        showToast('✅ Invitation envoyée avec succès !');
                    } else { showToast('⚠️ Erreur ou utilisateur déjà invité.', false); }
                });
            }

            function respondInvite(eventId, status) {
                const fd = new FormData();
                fd.append('id_event', eventId);
                fd.append('status', status); 

                fetch('index.php?page=api_respond_event_invite', { method: 'POST', body: fd })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        showToast(status === 'accepted' ? '✅ Invitation acceptée !' : '❌ Invitation refusée.');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast('⚠️ Erreur.', false);
                    }
                });
            }
        </script>
        <?php
        return ob_get_clean();
    }
}
?>