<?php

class ViewExplorer extends ViewPrivate {
    private $recommendations;
    private $posts;

    public function __construct($userData, $recos, $posts) {
        parent::__construct($userData);
        $this->pageTitle = "Explorateur - TradiShion";
        $this->recommendations = $recos;
        $this->posts = $posts;
    }

    protected function getBodyContent() {
        ob_start();
        ?>
        <div class="dash-container" style="grid-template-columns: 280px 1fr 320px;">
            
            <aside class="dash-left">
                <div class="card profile-overview" style="text-align: center;">
                    <a href="index.php?page=profile" style="text-decoration:none; color:inherit;">
                        <div class="avatar-large" style="margin: 0 auto 10px; overflow:hidden;">
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
                   <ul style="list-style: none;">
                        <li><a href="index.php?page=dashboard">🏠 Accueil</a></li>
                        <li><a href="index.php?page=explorer" class="active" style="background:#FFF0ED; color:#A64B35; font-weight:bold;">🧭 Explorateur</a></li>
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
                                    <a href="index.php?page=profile&id=<?php echo $post['id_author']; ?>" style="text-decoration:none;">
                                        <div class="post-avatar" style="overflow:hidden; width:40px; height:40px; border-radius:50%;">
                                            <?php if (!empty($post['avatar_url'])): ?>
                                                <img src="<?php echo htmlspecialchars($post['avatar_url']); ?>" style="width:100%; height:100%; object-fit:cover;">
                                            <?php else: ?>
                                                <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-weight:bold; background:#eee; color:#333;"><?php echo strtoupper(substr($post['author'], 0, 1)); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </a>
                                    <div class="post-meta">
                                        <a href="index.php?page=profile&id=<?php echo $post['id_author']; ?>" style="color:inherit; text-decoration:none;">
                                            <strong><?php echo htmlspecialchars($post['author']); ?></strong> 
                                        </a>
                                        <span style="color:#aaa; font-size:0.75rem; margin-left:10px;"><?php echo date("d/m/Y H:i", strtotime($post['created_at'])); ?></span><br>
                                        <span style="color:#888; font-size:0.8rem;">📍 <?php echo htmlspecialchars($post['location'] ?? 'Inconnu'); ?></span>
                                    </div>
                                </div>
                                <p style="margin-bottom:15px; color:#333; line-height:1.5;"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                                
                                <?php if (!empty($post['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($post['image_url']); ?>" style="width:100%; border-radius:8px; margin-bottom:15px; border:1px solid #eee;">
                                <?php endif; ?>
                                
                                <div class="post-actions" style="border-top:1px solid #eee; padding-top:15px; color:#888; font-size:0.9rem; display:flex; gap:20px;">
                                    <span style="cursor:pointer; transition: 0.2s;" onmouseover="this.style.color='#A64B35'" onmouseout="this.style.color='#888'">❤️ J'aime</span>
                                    <span style="cursor:pointer; transition: 0.2s;" onmouseover="this.style.color='#A64B35'" onmouseout="this.style.color='#888'" onclick="toggleComments(<?php echo $post['id_post']; ?>)">💬 Commenter</span>
                                </div>

                                <div id="comments-section-<?php echo $post['id_post']; ?>" style="display:none; margin-top:15px; border-top:1px dashed #eee; padding-top:15px; background: #fafafa; padding: 15px; border-radius: 8px;">
                                    <div id="comments-list-<?php echo $post['id_post']; ?>" style="max-height:200px; overflow-y:auto; margin-bottom:10px; padding-right: 5px;"></div>
                                    <div style="display:flex; gap:10px; align-items:center; border-top: 1px solid #ddd; padding-top: 10px;">
                                        <input type="text" id="comment-input-<?php echo $post['id_post']; ?>" placeholder="Ajouter un commentaire..." style="flex:1; padding:10px 15px; border:1px solid #ddd; border-radius:20px; outline:none; font-size:0.85rem; background:white;">
                                        <button onclick="addComment(<?php echo $post['id_post']; ?>)" style="background:none; border:none; color:#A64B35; cursor:pointer; font-size:1.5rem; display:flex; align-items:center; justify-content:center; width:35px; height:35px; border-radius:50%; background:#FFF0ED;">➤</button>
                                    </div>
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
                            <a href="index.php?page=profile&id=<?php echo $rec['id_user']; ?>">
                                <div class="rec-avatar" style="background:#eee; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; overflow:hidden; color:#333;">
                                    <?php if (!empty($rec['avatar_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($rec['avatar_url']); ?>" style="width:100%; height:100%; object-fit:cover;">
                                    <?php else: ?>
                                        <?php echo $recInit; ?>
                                    <?php endif; ?>
                                </div>
                            </a>
                            <div class="rec-info" style="flex:1; font-size:0.85rem;">
                                <a href="index.php?page=profile&id=<?php echo $rec['id_user']; ?>" style="color:inherit; text-decoration:none;">
                                    <strong><?php echo htmlspecialchars($rec['display_name']); ?></strong>
                                </a><br>
                                <span><?php echo htmlspecialchars(substr($rec['bio_free'] ?? 'Artisan', 0, 20)) . '...'; ?></span>
                            </div>
                            <button onclick="addFriendGlobal(<?php echo $rec['id_user']; ?>)" class="btn-outline" style="border-color:#333; color:#333; border-radius:20px; padding:5px 15px; font-size:0.75rem; cursor:pointer;">Suivre</button>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="card agenda-card" style="margin-top:20px;">
                    <h3>L'AGENDA INTERACTIF</h3>
                    <div class="calendar-wrapper">
                        <div class="calendar-header">
                            <button id="prev-month">❮</button>
                            <h4 id="month-year">...</h4>
                            <button id="next-month">❯</button>
                        </div>
                        <div class="calendar-grid" id="calendar-days"></div>
                    </div>
                    <div class="events-list" id="dynamic-events-list"></div>
                    <button class="btn-create-event">+ Créer un événement</button>
                </div>
            </aside>

        </div>

        <div id="create-event-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center; overflow-y:auto; padding: 20px 0;">
            <div class="card" style="width:100%; max-width:600px; padding:40px; background:white; border-radius:12px; margin: auto;">
                <h2 style="margin-bottom:5px; color:#333; font-family:'Libre Baskerville', serif;">Créer un événement</h2>
                <p style="color:#888; font-size:0.9rem; margin-bottom:20px;">Partagez votre savoir-faire ou organisez une rencontre avec la communauté.</p>
                
                <form id="create-event-form" style="display:flex; flex-direction:column; gap:20px;">
                    
                    <label style="border: 2px dashed #ddd; border-radius: 12px; padding: 40px 20px; text-align: center; cursor: pointer; background: #fafafa; display: block;">
                        <span style="font-size: 2rem; color: #ccc;">🖼️</span><br>
                        <strong style="color: #555;">Ajouter une image de couverture</strong><br>
                        <small style="color: #aaa;">JPG, PNG ou GIF. Max 5MB.</small>
                        <input type="file" name="cover_image" accept="image/*" style="display: none;" onchange="document.getElementById('cover-file-name').innerText = this.files[0].name;">
                        <div id="cover-file-name" style="margin-top: 10px; color: #A64B35; font-size: 0.85rem; font-weight: bold;"></div>
                    </label>

                    <div>
                        <label style="font-size:0.85rem; font-weight:bold; color:#333;">Titre de l'événement *</label>
                        <input type="text" name="title" required placeholder="Ex: Atelier d'initiation à la broderie albanaise" style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; margin-top:5px; outline:none; font-size:0.95rem;">
                    </div>

                    <div style="display:flex; gap:15px;">
                        <div style="flex:1;">
                            <label style="font-size:0.85rem; font-weight:bold; color:#333;">Date *</label>
                            <input type="date" name="event_date" required style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; margin-top:5px; outline:none; font-size:0.95rem;">
                        </div>
                        <div style="flex:1; display:flex; gap:10px;">
                            <div style="flex:1;">
                                <label style="font-size:0.85rem; font-weight:bold; color:#333;">Début *</label>
                                <input type="time" name="start_time" required style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; margin-top:5px; outline:none;">
                            </div>
                            <div style="flex:1;">
                                <label style="font-size:0.85rem; font-weight:bold; color:#333;">Fin *</label>
                                <input type="time" name="end_time" required style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; margin-top:5px; outline:none;">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label style="font-size:0.85rem; font-weight:bold; color:#333;">Lieu ou Lien visio *</label>
                        <input type="text" name="meeting_url" required placeholder="📍 Adresse physique ou lien Zoom/Meet" style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; margin-top:5px; outline:none; font-size:0.95rem;">
                    </div>

                    <div>
                        <label style="font-size:0.85rem; font-weight:bold; color:#333;">Visibilité</label>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 5px;">
                            <label class="vis-option" style="border: 1px solid #A64B35; border-radius: 8px; padding: 15px; cursor: pointer; display: flex; align-items: center; gap: 10px; background: #FFF0ED;">
                                <input type="radio" name="visibility" value="public" checked style="accent-color: #A64B35;">
                                <div>
                                    <strong style="color: #333;">🌐 Public</strong><br>
                                    <small style="color: #888;">Visible par tous</small>
                                </div>
                            </label>
                            <label class="vis-option" style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; cursor: pointer; display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="visibility" value="private" style="accent-color: #A64B35;">
                                <div>
                                    <strong style="color: #333;">🔒 Privé</strong><br>
                                    <small style="color: #888;">Sur invitation</small>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label style="font-size:0.85rem; font-weight:bold; color:#333;">Description</label>
                        <textarea name="description" rows="4" placeholder="Décrivez ce que les participants vont apprendre ou découvrir..." style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; margin-top:5px; resize:none; outline:none; font-family:inherit; font-size:0.95rem;"></textarea>
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:15px; margin-top:10px;">
                        <button type="button" onclick="document.getElementById('create-event-modal').style.display='none'" class="btn-outline" style="border-color:#ddd; color:#555; padding:12px 25px; border-radius:8px; font-weight:bold;">Annuler</button>
                        <button type="submit" class="btn-submit" style="width:auto; padding:12px 25px; border-radius:8px; font-size:1rem;">Créer l'événement</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            // --- GESTION DES COMMENTAIRES (AJAX) ---
            function toggleComments(postId) {
                const section = document.getElementById('comments-section-' + postId);
                if(section.style.display === 'none') { section.style.display = 'block'; loadComments(postId); } 
                else { section.style.display = 'none'; }
            }

            function loadComments(postId) {
                const list = document.getElementById('comments-list-' + postId);
                list.innerHTML = '<p style="font-size:0.8rem; color:#888; text-align:center;">Chargement...</p>';
                fetch('index.php?page=api_get_comments&id_post=' + postId).then(res => res.json()).then(data => {
                    list.innerHTML = '';
                    if(data.length === 0) { list.innerHTML = '<p style="font-size:0.8rem; color:#888; text-align:center;">Aucun commentaire. Soyez le premier !</p>'; return; }
                    data.forEach(c => {
                        let av = c.avatar_url ? `<img src="${c.avatar_url}" style="width:28px; height:28px; border-radius:50%; object-fit:cover;">` : `<div style="width:28px; height:28px; border-radius:50%; background:#eee; display:flex; align-items:center; justify-content:center; font-size:0.8rem; font-weight:bold; color:#333;">${c.author.charAt(0).toUpperCase()}</div>`;
                        list.innerHTML += `
                            <div style="display:flex; gap:10px; margin-bottom:12px; align-items:flex-start;">
                                ${av}
                                <div style="background:white; padding:8px 12px; border-radius:12px; border: 1px solid #eee; flex:1;">
                                    <strong style="font-size:0.8rem; color:#333;">${c.author}</strong>
                                    <p style="margin:0; font-size:0.85rem; color:#555;">${c.content}</p>
                                </div>
                            </div>`;
                    });
                });
            }

            function addComment(postId) {
                const input = document.getElementById('comment-input-' + postId);
                if(!input.value.trim()) return;
                const fd = new FormData(); fd.append('id_post', postId); fd.append('content', input.value.trim());
                fetch('index.php?page=api_add_comment', {method: 'POST', body: fd}).then(res => res.json()).then(data => {
                    if(data.success) { input.value = ''; loadComments(postId); } else { showToast("Erreur d'ajout.", false); }
                });
            }

            // GESTION DU CALENDRIER
            const monthNames = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
            const daysContainer = document.getElementById('calendar-days');
            const monthYearText = document.getElementById('month-year');
            const eventsList = document.getElementById('dynamic-events-list');
            let currentDate = new Date(); let navDate = new Date();     
            let selectedDateString = `${currentDate.getFullYear()}-${String(currentDate.getMonth()+1).padStart(2, '0')}-${String(currentDate.getDate()).padStart(2, '0')}`;
            let eventsDB = {};

            function fetchEvents() {
                fetch('index.php?page=api_events').then(res => res.json()).then(data => {
                    eventsDB = {};
                    data.forEach(ev => {
                        let dateStr = ev.event_date;
                        if(!eventsDB[dateStr]) eventsDB[dateStr] = [];
                        let st = ev.start_time ? ev.start_time.split(' ')[1].substring(0,5) : '';
                        let et = ev.end_time ? ev.end_time.split(' ')[1].substring(0,5) : '';
                        let labelMap = { 'private': 'Privé', 'shared': 'Partagé', 'public': 'Public' };
                        eventsDB[dateStr].push({ type: ev.visibility, title: ev.title, time: `${st} - ${et}`, label: labelMap[ev.visibility] || 'Événement' });
                    });
                    renderCalendar(); displayEvents(selectedDateString);
                }).catch(() => { renderCalendar(); });
            }

            function displayEvents(dateString) {
                eventsList.innerHTML = '';
                if (eventsDB[dateString] && eventsDB[dateString].length > 0) {
                    eventsDB[dateString].forEach(ev => { eventsList.innerHTML += `<div class="event-item event-${ev.type}"><span class="badge badge-${ev.type}">${ev.label}</span> ${ev.title}<br><small>${ev.time}</small></div>`; });
                } else { eventsList.innerHTML = `<p style="text-align:center; color:#888; padding:15px; font-style:italic;">Aucun événement prévu à cette date.</p>`; }
            }

            function renderCalendar() {
                const year = navDate.getFullYear(); const month = navDate.getMonth();
                monthYearText.innerText = `${monthNames[month]} ${year}`;
                daysContainer.innerHTML = '';
                ['L', 'M', 'M', 'J', 'V', 'S', 'D'].forEach(day => { daysContainer.innerHTML += `<div class="cal-day-name">${day}</div>`; });

                let firstDayIndex = new Date(year, month, 1).getDay() - 1; if(firstDayIndex === -1) firstDayIndex = 6; 
                const daysInMonth = new Date(year, month + 1, 0).getDate();

                for(let i = 0; i < firstDayIndex; i++) daysContainer.innerHTML += `<div class="cal-day empty"></div>`;

                for(let i = 1; i <= daysInMonth; i++) {
                    let dateString = `${year}-${String(month+1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
                    let isSelected = (dateString === selectedDateString);
                    let dayDiv = document.createElement('div');
                    dayDiv.className = isSelected ? 'cal-day active-day' : 'cal-day';
                    dayDiv.innerText = i;
                    dayDiv.onclick = () => { selectedDateString = dateString; renderCalendar(); displayEvents(selectedDateString); };
                    if (eventsDB[dateString]) dayDiv.innerHTML += `<div style="width:4px; height:4px; background:${isSelected ? 'white' : '#A64B35'}; border-radius:50%; margin: 2px auto 0;"></div>`;
                    daysContainer.appendChild(dayDiv);
                }
            }

            document.getElementById('prev-month').addEventListener('click', () => { navDate.setMonth(navDate.getMonth() - 1); renderCalendar(); });
            document.getElementById('next-month').addEventListener('click', () => { navDate.setMonth(navDate.getMonth() + 1); renderCalendar(); });
            
            renderCalendar(); displayEvents(selectedDateString); fetchEvents();

            // Modal Radio Animation
            const radios = document.querySelectorAll('input[name="visibility"]');
            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    document.querySelectorAll('.vis-option').forEach(el => { el.style.borderColor = '#ddd'; el.style.background = 'white'; });
                    if(this.checked) { this.closest('.vis-option').style.borderColor = '#A64B35'; this.closest('.vis-option').style.background = '#FFF0ED'; }
                });
            });

            // Gestion modal création événement
            const btnCreateEvent = document.querySelector('.btn-create-event');
            const modalEvent = document.getElementById('create-event-modal');
            const formEvent = document.getElementById('create-event-form');
            if(btnCreateEvent && modalEvent && formEvent) {
                btnCreateEvent.addEventListener('click', () => modalEvent.style.display = 'flex');
                formEvent.addEventListener('submit', function(e) {
                    e.preventDefault();
                    fetch('index.php?page=api_create_event', { method: 'POST', body: new FormData(this) })
                    .then(res => res.json()).then(data => {
                        if(data.success) { modalEvent.style.display = 'none'; formEvent.reset(); showToast('✅ Événement créé avec succès !'); setTimeout(() => location.reload(), 1000); } 
                        else { showToast('⚠️ Erreur lors de la création.', false); }
                    });
                });
            }
        </script>
        <?php
        return ob_get_clean();
    }
}
?>