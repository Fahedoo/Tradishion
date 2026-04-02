<?php

class ViewExplorer extends View {
    private $user;
    private $recommendations;
    private $posts;
    private $hashtags;

    public function __construct($userData, $recos, $posts, $hashtags = []) {
        $this->pageTitle = "Explorateur - TradiShion";
        $this->user = $userData;
        $this->recommendations = $recos;
        $this->posts = $posts;
        $this->hashtags = $hashtags;
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
                    <select name="language" class="lang-select" style="margin-right:15px; border:none; background:transparent; font-weight:bold; cursor:pointer;" onchange="window.location.href='index.php?lang=' + this.value + '&page=explorer'">
                        <option value="en" <?php echo (($_SESSION['lang']??'fr')=='en')?'selected':''; ?>>EN</option>
                        <option value="al" <?php echo (($_SESSION['lang']??'fr')=='al')?'selected':''; ?>>AL</option>
                        <option value="fr" <?php echo (($_SESSION['lang']??'fr')=='fr')?'selected':''; ?>>FR</option>
                        <option value="vi" <?php echo (($_SESSION['lang']??'fr')=='vi')?'selected':''; ?>>VI</option>
                    </select>
                    <span class="notification-icon">🔔</span>
                    <a href="index.php?page=logout" class="logout-link"><?php echo $this->t('nav_logout'); ?></a>
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
                        
                        <select name="id_hashtag" style="padding:6px; border-radius:4px; border:1px solid #ddd; margin-right:10px; font-size:0.85rem; outline:none; max-width: 150px;">
                            <option value="">Sélectionnez un tag...</option>
                            <?php foreach($this->hashtags as $ht): ?>
                                <option value="<?php echo $ht['id_hashtag']; ?>"><?php echo htmlspecialchars('#' . $ht['label']); ?></option>
                            <?php endforeach; ?>
                        </select>
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
                                    <div class="post-avatar" style="width:40px; height:40px; border-radius:50%; overflow:hidden;">
                                        <?php if (!empty($post['avatar_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($post['avatar_url']); ?>" style="width:100%; height:100%; object-fit:cover;">
                                        <?php else: ?>
                                            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-weight:bold; background:#eee;"><?php echo strtoupper(substr($post['author'], 0, 1)); ?></div>
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
            // --- GESTION DE L'AGENDA DYNAMIQUE ---
            const monthNames = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
            const daysContainer = document.getElementById('calendar-days');
            const monthYearText = document.getElementById('month-year');
            const eventsList = document.getElementById('dynamic-events-list');
            let currentDate = new Date(); let navDate = new Date();     
            let selectedDateString = `${currentDate.getFullYear()}-${String(currentDate.getMonth()+1).padStart(2, '0')}-${String(currentDate.getDate()).padStart(2, '0')}`;
            let eventsDB = {};

            function fetchEvents() {
                fetch('index.php?page=api_events')
                .then(res => res.json())
                .then(data => {
                    eventsDB = {};
                    data.forEach(ev => {
                        let dateStr = ev.event_date;
                        if(!eventsDB[dateStr]) eventsDB[dateStr] = [];
                        
                        let st = ev.start_time.split(' ')[1].substring(0,5);
                        let et = ev.end_time.split(' ')[1].substring(0,5);
                        let labelMap = { 'private': 'Privé', 'shared': 'Partagé', 'public': 'Public' };
                        
                        eventsDB[dateStr].push({
                            id: ev.id_event,
                            organizer: ev.id_organizer,
                            type: ev.visibility,
                            title: ev.title,
                            time: `${st} - ${et}`,
                            label: labelMap[ev.visibility] || 'Événement',
                            is_participating: ev.is_participating
                        });
                    });
                    renderCalendar();
                    displayEvents(selectedDateString);
                });
            }

            function displayEvents(dateString) {
                eventsList.innerHTML = '';
                if (eventsDB[dateString] && eventsDB[dateString].length > 0) {
                    eventsDB[dateString].forEach(ev => {
                        let btnHtml = '';
                        if (ev.organizer != <?php echo $this->user['id_user']; ?>) {
                           if (ev.is_participating == 1) {
                               btnHtml = `<button disabled style="margin-left:auto; background:#eee; border:none; padding:3px 8px; border-radius:12px; font-size:0.75rem; color:#888;">Inscrit</button>`;
                           } else if (ev.type === 'public' || ev.type === 'shared') {
                               btnHtml = `<button onclick="joinEvent(${ev.id})" style="margin-left:auto; background:#FFF0ED; border:none; color:#A64B35; padding:3px 8px; border-radius:12px; font-size:0.75rem; cursor:pointer;">Participer</button>`;
                           }
                        }
                        eventsList.innerHTML += `<div class="event-item event-${ev.type}" style="display:flex; flex-direction:column; gap:5px;">
                            <div style="display:flex; align-items:center;">
                                <span class="badge badge-${ev.type}">${ev.label}</span> 
                                ${btnHtml}
                            </div>
                            <strong>${ev.title}</strong>
                            <small>${ev.time}</small>
                        </div>`;
                    });
                } else { eventsList.innerHTML = `<p style="text-align:center; color:#888; padding:15px; font-style:italic;">Aucun événement prévu à cette date.</p>`; }
            }

            function joinEvent(id_event) {
                const formData = new FormData(); formData.append('id_event', id_event);
                fetch('index.php?page=api_join_event', { method:'POST', body:formData })
                .then(res => res.json()).then(data => { 
                    if(data.success) { showToast('✅ Vous êtes inscrit à cet événement !'); fetchEvents(); } 
                    else { showToast('⚠️ Erreur lors de l\'inscription.', false); }
                });
            }

            function renderCalendar() {
                const year = navDate.getFullYear(); const month = navDate.getMonth();
                monthYearText.innerText = `${monthNames[month]} ${year}`;
                daysContainer.innerHTML = '';
                const daysOfWeek = ['L', 'M', 'M', 'J', 'V', 'S', 'D'];
                daysOfWeek.forEach(day => {
                    const div = document.createElement('div'); div.className = 'cal-day-name'; div.innerText = day; daysContainer.appendChild(div);
                });

                let firstDayIndex = new Date(year, month, 1).getDay() - 1;
                if(firstDayIndex === -1) firstDayIndex = 6; 
                const daysInMonth = new Date(year, month + 1, 0).getDate();

                for(let i = 0; i < firstDayIndex; i++) {
                    const div = document.createElement('div'); div.className = 'cal-day empty'; daysContainer.appendChild(div);
                }

                for(let i = 1; i <= daysInMonth; i++) {
                    let dateString = `${year}-${String(month+1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
                    let isSelected = (dateString === selectedDateString);
                    let dayDiv = document.createElement('div');
                    dayDiv.className = isSelected ? 'cal-day active-day' : 'cal-day';
                    dayDiv.innerText = i;
                    dayDiv.onclick = () => {
                        selectedDateString = dateString; renderCalendar(); displayEvents(selectedDateString);
                    };
                    if (eventsDB[dateString]) {
                        dayDiv.innerHTML += `<div style="width:4px; height:4px; background:${isSelected ? 'white' : '#A64B35'}; border-radius:50%; margin: 2px auto 0;"></div>`;
                    }
                    daysContainer.appendChild(dayDiv);
                }
            }

            document.getElementById('prev-month').addEventListener('click', () => { navDate.setMonth(navDate.getMonth() - 1); renderCalendar(); });
            document.getElementById('next-month').addEventListener('click', () => { navDate.setMonth(navDate.getMonth() + 1); renderCalendar(); });
            
            // On charge les événements
            fetchEvents();

            // --- ANIMATION BOUTONS RADIOS MODAL ---
            const radios = document.querySelectorAll('input[name="visibility"]');
            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    document.querySelectorAll('.vis-option').forEach(el => {
                        el.style.borderColor = '#ddd';
                        el.style.background = 'white';
                    });
                    if(this.checked) {
                        this.closest('.vis-option').style.borderColor = '#A64B35';
                        this.closest('.vis-option').style.background = '#FFF0ED';
                    }
                });
            });

            // --- GESTION DE LA CRÉATION D'ÉVÉNEMENT ---
            const btnCreateEvent = document.querySelector('.btn-create-event');
            const modalEvent = document.getElementById('create-event-modal');
            const formEvent = document.getElementById('create-event-form');

            if(btnCreateEvent && modalEvent && formEvent) {
                btnCreateEvent.addEventListener('click', () => {
                    modalEvent.style.display = 'flex';
                });

                formEvent.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    
                    fetch('index.php?page=api_create_event', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            modalEvent.style.display = 'none';
                            formEvent.reset();
                            showToast('✅ Événement créé avec succès !');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showToast('⚠️ Erreur lors de la création.', false);
                        }
                    });
                });
            }

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