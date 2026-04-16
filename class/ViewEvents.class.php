<?php

class ViewEvents extends ViewPrivate {
    private $todayPublicEvents;
    private $myOrganizedEvents;
    private $myConnections;
    private $pendingEventInvites;

    public function __construct($userData, $todayEvents, $myEvents, $connections, $pendingEventInvites) {
        parent::__construct($userData);
        $this->pageTitle = $this->t('menu_events') . " - TradiShion";
        $this->todayPublicEvents = $todayEvents;
        $this->myOrganizedEvents = $myEvents;
        $this->myConnections = $connections;
        $this->pendingEventInvites = $pendingEventInvites;
    }

    protected function getBodyContent() {
        ob_start();
        ?>
        <div class="dash-container" style="display:grid; grid-template-columns: minmax(760px, 1fr) 340px; gap: 34px; padding-top: 24px; width: 100%;">
            
            <aside class="dash-left" style="display:none;">
                </aside>

            <main class="dash-center" style="display:flex; flex-direction:column; gap:25px; width:100%; align-items:stretch; min-width:0;">
                
                <div style="display:flex; justify-content:space-between; align-items:flex-end; border-bottom: 2px solid #FFF0ED; padding-bottom: 15px; margin-bottom: 10px; gap:12px;">
                    <div>
                        <h2 style="font-family:'Libre Baskerville', serif; color:#333; margin:0; font-size: 1.6rem;"><?php echo $this->t('ev_today'); ?></h2>
                        <span style="color:#A64B35; font-weight:bold; font-size:1rem;"><?php echo date("d/m/Y"); ?></span>
                    </div>
                    <button id="btn-create-event-events" type="button" class="btn-submit" style="width:auto; border:none; padding:10px 16px; border-radius:12px;"><?php echo $this->t('btn_create_event'); ?></button>
                </div>
                
                <?php if (empty($this->todayPublicEvents)): ?>
                    <div class="card" style="text-align:center; padding:60px 20px; color:#888; background: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); width:100%;">
                        <span style="font-size:3.5rem; display:block; margin-bottom:20px; opacity: 0.5;">🏜️</span>
                        <p style="font-size: 1.1rem; margin:0;"><?php echo $this->t('ev_no_public_today'); ?></p>
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
                                                <?php echo $this->getTextInitial($ev['organizer_name']); ?>
                                            <?php endif; ?>
                                        </div>
                                        <span style="font-size:0.95rem; color:#666;">
                                            <?php echo $this->t('ev_organized_by'); ?> <strong style="color:#333;"><?php echo htmlspecialchars($ev['organizer_name']); ?></strong>
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

            <aside class="dash-right" style="display:flex; flex-direction:column; gap:18px;">
                <div class="card" style="padding:0; background: transparent; box-shadow: none;">
                    <h3 style="border-bottom:2px solid #ddd; padding-bottom:15px; color:#333; margin-bottom:25px; font-size:1.15rem; text-transform:uppercase; letter-spacing: 1px;"><?php echo $this->t('ev_my_events'); ?></h3>
                    
                    <?php if (empty($this->myOrganizedEvents)): ?>
                        <div class="card" style="text-align:center; padding:30px 20px;">
                            <p style="color:#888; font-size:0.95rem; margin:0;"><?php echo $this->t('ev_no_upcoming'); ?></p>
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
                                        <p style="font-size:0.85rem; color:#888; margin-bottom:15px; margin-top:0;"><?php echo $this->t('ev_organized_by'); ?> <?php echo htmlspecialchars($myEv['organizer_name']); ?></p>
                                    <?php endif; ?>
                                    
                                    <div style="font-size:0.9rem; color:#666; margin-bottom:20px; display:flex; flex-direction:column; gap:8px; background: <?php echo $isMine ? '#fdfaf5' : '#f4f9f5'; ?>; padding: 10px 15px; border-radius: 6px;">
                                        <span style="display:flex; align-items:center; gap:8px;">📅 <strong style="color:#555;"><?php echo date("d/m/Y", strtotime($myEv['start_time'])); ?></strong></span>
                                        <span style="display:flex; align-items:center; gap:8px;">🕒 <strong style="color:#555;"><?php echo substr($myEv['start_time'], 11, 5) . ' - ' . substr($myEv['end_time'], 11, 5); ?></strong></span>
                                    </div>
                                    
                                    <?php if ($isMine): ?>
                                        <button onclick="openInviteModal(<?php echo $myEv['id_event']; ?>)" class="btn-outline" style="width:100%; border: 1px solid #A64B35; background: white; color:#A64B35; padding:12px; border-radius:8px; font-size:0.95rem; cursor:pointer; font-weight:bold; transition:all 0.2s; display:flex; align-items:center; justify-content:center; gap:8px;" onmouseover="this.style.background='#FFF0ED'" onmouseout="this.style.background='white'">
                                            <?php echo $this->t('ev_invite_connections'); ?>
                                        </button>
                                    <?php else: ?>
                                        <div style="width:100%; text-align:center; background:#e8f5e9; color:#2e7d32; padding:12px; border-radius:8px; font-size:0.95rem; font-weight:bold; box-sizing: border-box;">
                                            <?php echo $this->t('ev_you_participate'); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card" style="padding:0; background: transparent; box-shadow: none; margin-top:40px;">
                    <h3 style="border-bottom:2px solid #ddd; padding-bottom:15px; color:#333; margin-bottom:25px; font-size:1.15rem; text-transform:uppercase; letter-spacing: 1px;"><?php echo $this->t('ev_invitations'); ?></h3>
                    
                    <?php if (empty($this->pendingEventInvites)): ?>
                        <div class="card" style="text-align:center; padding:30px 20px;">
                            <p style="color:#888; font-size:0.95rem; margin:0;"><?php echo $this->t('ev_no_pending_invites'); ?></p>
                        </div>
                    <?php else: ?>
                        <div style="display:flex; flex-direction:column; gap:20px;">
                            <?php foreach ($this->pendingEventInvites as $inv): ?>
                                <div class="card" style="padding:20px; border-left: 5px solid #4caf50; border-radius: 12px; margin:0; transition: transform 0.2s ease, box-shadow 0.2s ease;">
                                    <h4 style="margin:0 0 5px 0; font-size:1.15rem; color:#333; font-family: 'Libre Baskerville', serif;">
                                        <?php echo htmlspecialchars($inv['title']); ?>
                                    </h4>
                                    <p style="font-size:0.85rem; color:#888; margin-bottom:15px; margin-top:0;"><?php echo $this->t('ev_by'); ?> <?php echo htmlspecialchars($inv['organizer_name']); ?></p>
                                    
                                    <div style="font-size:0.9rem; color:#666; margin-bottom:20px; display:flex; flex-direction:column; gap:8px; background: #f4f9f5; padding: 10px 15px; border-radius: 6px;">
                                        <span style="display:flex; align-items:center; gap:8px;">📅 <strong style="color:#555;"><?php echo date("d/m/Y", strtotime($inv['start_time'])); ?></strong></span>
                                        <span style="display:flex; align-items:center; gap:8px;">🕒 <strong style="color:#555;"><?php echo substr($inv['start_time'], 11, 5) . ' - ' . substr($inv['end_time'], 11, 5); ?></strong></span>
                                    </div>
                                    
                                    <div style="display:flex; gap:10px;">
                                        <button onclick="respondInvite(<?php echo $inv['id_event']; ?>, 'accepted')" class="btn-submit" style="flex:1; padding:10px; border-radius:8px; font-size:0.9rem; background:#4caf50;"><?php echo $this->t('btn_accept'); ?></button>
                                        <button onclick="respondInvite(<?php echo $inv['id_event']; ?>, 'declined')" class="btn-outline" style="flex:1; padding:10px; border-radius:8px; font-size:0.9rem; border-color:#ccc; color:#888;"><?php echo $this->t('btn_decline'); ?></button>
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
                    <h3 style="margin:0; color:#333; font-family:'Libre Baskerville', serif; font-size: 1.3rem;"><?php echo $this->t('ev_invite_modal_title'); ?></h3>
                    <button onclick="closeInviteModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:#888; transition: color 0.2s;" onmouseover="this.style.color='#333'" onmouseout="this.style.color='#888'">✕</button>
                </div>
                
                <div style="max-height:350px; overflow-y:auto; padding-right:10px;">
                    <?php if (empty($this->myConnections)): ?>
                        <p style="color:#888; text-align:center; padding: 20px 0;"><?php echo $this->t('ev_no_relations_to_invite'); ?></p>
                    <?php else: ?>
                        <?php foreach ($this->myConnections as $conn): ?>
                            <div style="display:flex; align-items:center; justify-content:space-between; padding:15px 0; border-bottom:1px solid #f5f5f5;">
                                <div style="display:flex; align-items:center; gap:15px;">
                                    <div style="width:45px; height:45px; border-radius:50%; background:#eee; display:flex; align-items:center; justify-content:center; overflow:hidden; font-weight:bold; color:#333; font-size: 1.1rem;">
                                        <?php if (!empty($conn['avatar_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($conn['avatar_url']); ?>" style="width:100%; height:100%; object-fit:cover;">
                                        <?php else: ?>
                                            <?php echo $this->getTextInitial($conn['display_name']); ?>
                                        <?php endif; ?>
                                    </div>
                                    <strong style="font-size:1rem; color:#333;"><?php echo htmlspecialchars($conn['display_name']); ?></strong>
                                </div>
                                <button onclick="sendInvite(<?php echo $conn['id_user']; ?>, this)" style="background:#FFF0ED; color:#A64B35; border:none; padding:8px 18px; border-radius:20px; font-size:0.9rem; cursor:pointer; font-weight:bold; transition:all 0.2s;" onmouseover="this.style.background='#A64B35'; this.style.color='white';" onmouseout="this.style.background='#FFF0ED'; this.style.color='#A64B35';"><?php echo $this->t('ev_btn_invite'); ?></button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div id="create-event-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:2200; align-items:center; justify-content:center; overflow-y:auto; padding: 20px 0;">
            <div class="card" style="width:100%; max-width:600px; padding:40px; background:white; border-radius:12px; margin: auto;">
                <h2 style="margin-bottom:5px; color:#333; font-family:'Libre Baskerville', serif;"><?php echo $this->t('dash_create_event_title'); ?></h2>
                <form id="create-event-form" style="display:flex; flex-direction:column; gap:20px;">
                    <label style="border: 2px dashed #ddd; border-radius: 12px; padding: 40px 20px; text-align: center; cursor: pointer; background: #fafafa; display: block;">
                        <span style="font-size: 2rem; color: #ccc;">🖼️</span><br>
                        <strong style="color: #555;"><?php echo $this->t('dash_add_cover_image'); ?></strong>
                        <input type="file" name="cover_image" accept="image/*" style="display: none;" onchange="document.getElementById('cover-file-name').innerText = this.files[0] ? this.files[0].name : '';">
                        <div id="cover-file-name" style="margin-top: 10px; color: #A64B35; font-size: 0.85rem; font-weight: bold;"></div>
                    </label>
                    <div>
                        <label style="font-size:0.85rem; font-weight:bold; color:#333;"><?php echo $this->t('dash_event_title_label'); ?></label>
                        <input type="text" name="title" required style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; margin-top:5px; outline:none;">
                    </div>
                    <div style="display:flex; gap:15px;">
                        <div style="flex:1;">
                            <label style="font-size:0.85rem; font-weight:bold; color:#333;"><?php echo $this->t('dash_event_date_label'); ?></label>
                            <input type="date" name="event_date" required style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; margin-top:5px; outline:none;">
                        </div>
                        <div style="flex:1; display:flex; gap:10px;">
                            <div style="flex:1;">
                                <label style="font-size:0.85rem; font-weight:bold; color:#333;"><?php echo $this->t('dash_event_start_label'); ?></label>
                                <input type="time" name="start_time" required style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; margin-top:5px; outline:none;">
                            </div>
                            <div style="flex:1;">
                                <label style="font-size:0.85rem; font-weight:bold; color:#333;"><?php echo $this->t('dash_event_end_label'); ?></label>
                                <input type="time" name="end_time" required style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; margin-top:5px; outline:none;">
                            </div>
                        </div>
                    </div>
                    <div>
                        <label style="font-size:0.85rem; font-weight:bold; color:#333;"><?php echo $this->t('dash_event_location_label'); ?></label>
                        <input type="text" name="meeting_url" required style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; margin-top:5px; outline:none;">
                    </div>
                    <div>
                        <label style="font-size:0.85rem; font-weight:bold; color:#333;"><?php echo $this->t('dash_event_visibility_label'); ?></label>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 5px;">
                            <label class="vis-option" style="border: 1px solid #A64B35; border-radius: 8px; padding: 15px; cursor: pointer; display: flex; align-items: center; gap: 10px; background: #FFF0ED;">
                                <input type="radio" name="visibility" value="public" checked style="accent-color: #A64B35;">
                                <div><strong style="color: #333;"><?php echo $this->t('dash_event_public'); ?></strong></div>
                            </label>
                            <label class="vis-option" style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; cursor: pointer; display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="visibility" value="private" style="accent-color: #A64B35;">
                                <div><strong style="color: #333;"><?php echo $this->t('dash_event_private'); ?></strong></div>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label style="font-size:0.85rem; font-weight:bold; color:#333;"><?php echo $this->t('dash_event_desc_label'); ?></label>
                        <textarea name="description" rows="4" style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; margin-top:5px; resize:none; outline:none;"></textarea>
                    </div>
                    <div style="display:flex; justify-content:flex-end; gap:15px; margin-top:10px;">
                        <button type="button" id="close-event-modal" class="btn-outline" style="border-color:#ddd; color:#555; padding:12px 25px; border-radius:8px; font-weight:bold;"><?php echo $this->t('btn_cancel'); ?></button>
                        <button type="submit" class="btn-submit" style="width:auto; padding:12px 25px; border-radius:8px; font-size:1rem; background:#A64B35; color:white; border:none;"><?php echo $this->t('dash_submit_event'); ?></button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            let currentEventIdToInvite = null;

            function openInviteModal(eventId) {
                currentEventIdToInvite = eventId;
                document.getElementById('invite-modal').style.display = 'flex';
                document.querySelectorAll('#invite-modal button').forEach(btn => {
                    if(btn.innerText === window.Lang.ev_btn_invited) {
                        btn.innerText = window.Lang.ev_btn_invite;
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
                        btnElement.innerText = window.Lang.ev_btn_invited;
                        btnElement.style.background = "#e8f5e9";
                        btnElement.style.color = "#2e7d32";
                        btnElement.style.opacity = "0.7";
                        btnElement.style.pointerEvents = "none";
                        showToast(window.Lang.toast_invite_sent);
                    } else { showToast(window.Lang.toast_invite_failed, false); }
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
                        showToast(status === 'accepted' ? window.Lang.toast_invite_accepted : window.Lang.toast_invite_declined);
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast('⚠️ Erreur.', false);
                    }
                });
            }

            const createEventBtnEvents = document.getElementById('btn-create-event-events');
            const createEventModalEvents = document.getElementById('create-event-modal');
            const closeEventModalEvents = document.getElementById('close-event-modal');
            const createEventFormEvents = document.getElementById('create-event-form');

            createEventBtnEvents?.addEventListener('click', () => {
                createEventModalEvents.style.display = 'flex';
            });
            closeEventModalEvents?.addEventListener('click', () => {
                createEventModalEvents.style.display = 'none';
            });
            createEventModalEvents?.addEventListener('click', (e) => {
                if (e.target === createEventModalEvents) {
                    createEventModalEvents.style.display = 'none';
                }
            });
            createEventFormEvents?.addEventListener('submit', (e) => {
                e.preventDefault();
                const fd = new FormData(createEventFormEvents);
                fetch('index.php?page=api_create_event', { method: 'POST', body: fd })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(window.Lang.toast_event_created);
                        createEventFormEvents.reset();
                        createEventModalEvents.style.display = 'none';
                        setTimeout(() => location.reload(), 600);
                    } else {
                        showToast(window.Lang.toast_event_failed, false);
                    }
                })
                .catch(() => showToast(window.Lang.toast_network_error, false));
            });
        </script>
        <?php
        return ob_get_clean();
    }
}
?>