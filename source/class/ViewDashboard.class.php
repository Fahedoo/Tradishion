<?php

class ViewDashboard extends View {
    private $user;
    private $recommendations;

    public function __construct($userData, $recos) {
        $this->pageTitle = "Dashboard - TradiShion";
        $this->user = $userData;
        $this->recommendations = $recos;
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
                    <select name="language" class="lang-select" style="margin-right:15px; border:none; background:transparent; font-weight:bold; cursor:pointer;" onchange="window.location.href='index.php?lang=' + this.value + '&page=dashboard'">
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
        <div class="dash-container">
            
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
                        <p class="location">📍 <?php echo htmlspecialchars($this->user['location'] ?? 'Inconnu'); ?></p>
                    </a>
                    <div class="tags-container">
                        <span class="tag">#Couture</span>
                        <span class="tag">#Broderie</span>
                    </div>
                </div>

                <div class="card nav-menu">
                   <ul>
                        <li><a href="index.php?page=dashboard" class="active" style="background:#FFF0ED; color:#A64B35;">🏠 Accueil</a></li>
                        <li><a href="index.php?page=explorer">🧭 Explorateur</a></li>
                        <li><a href="index.php?page=messages">💬 Mes Messages</a></li>
                        <li><a href="index.php?page=network">👥 Mon Réseau</a></li>
                    </ul>
                </div>

                <div class="card recommendations">
                    <h4>RECOMMANDATIONS</h4>
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
                            <button onclick="addFriendGlobal(<?php echo $rec['id_user']; ?>)" class="add-btn" style="background:#FFF0ED; border:none; color:#A64B35; border-radius:50%; width:25px; height:25px; cursor:pointer;">➕</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </aside>

            <main class="dash-center">
                <div class="card map-card">
                    <h3>LA CARTE ET LES RENCONTRES</h3>
                    <div class="map-window">
                        <div class="zoom-controls">
                            <button id="zoom-in">+</button>
                            <button id="zoom-out">-</button>
                        </div>
                        <div id="svg-container"></div>
                    </div>
                </div>

                <div class="card feed-card">
                    <h3 id="feed-title">À la une de la communauté</h3>
                    <div id="posts-content">
                        <p style="text-align:center; padding: 20px;">Chargement des publications...</p>
                    </div>
                </div>
            </main>

            <aside class="dash-right">
                <div class="card agenda-card">
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
            // --- GESTION DE LA CARTE ET DU FEED ---
            const feedTitle = document.getElementById('feed-title');
            const postsContent = document.getElementById('posts-content');

            function loadPosts(countryCode = 'all') {
                if(countryCode === 'all') { feedTitle.innerText = "À la une de la communauté"; } 
                else { feedTitle.innerText = `Publications pour le pays : ${countryCode}`; }
                postsContent.innerHTML = `<p style="text-align:center; color:#888;">Chargement...</p>`;

                fetch(`index.php?page=api_posts&country=${countryCode}`)
                    .then(res => res.json())
                    .then(data => {
                        postsContent.innerHTML = ''; 
                        if (data && data.length > 0) {
                            data.forEach(post => {
                                let avatarHtml = post.avatar_url 
                                    ? `<img src="${post.avatar_url}" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">` 
                                    : `<div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-weight:bold; background:#eee; border-radius:50%;">${post.author.charAt(0).toUpperCase()}</div>`;

                                let imageHtml = post.image_url 
                                    ? `<img src="${post.image_url}" style="width:100%; border-radius:8px; margin-bottom:15px; border:1px solid #eee;">` 
                                    : '';
                                    
                                let dateObj = new Date(post.created_at);
                                let dateString = dateObj.toLocaleDateString('fr-FR', {day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute:'2-digit'});

                                postsContent.innerHTML += `
                                    <div class="feed-post card" style="margin-bottom:15px; padding:20px; border:1px solid #eee; border-radius:12px;">
                                        <div class="post-header" style="display:flex; align-items:center; gap:10px; margin-bottom:15px;">
                                            <div class="post-avatar" style="width:40px; height:40px; border-radius:50%; overflow:hidden;">
                                                ${avatarHtml}
                                            </div>
                                            <div class="post-meta">
                                                <strong>${post.author}</strong> <span style="color:#aaa; font-size:0.75rem; margin-left:10px;">${dateString}</span><br>
                                                <span style="color:#888; font-size:0.8rem;">📍 ${post.location || 'Inconnu'}</span>
                                            </div>
                                        </div>
                                        <p style="margin-bottom:15px; color:#333; line-height:1.5;">${post.content}</p>
                                        
                                        ${imageHtml}
                                        
                                        <div class="post-actions" style="border-top:1px solid #eee; padding-top:15px; color:#888; font-size:0.9rem; display:flex; gap:20px;">
                                            <span style="cursor:pointer;">❤️ J'aime</span>
                                            <span style="cursor:pointer;">💬 Commenter</span>
                                        </div>
                                    </div>
                                `;
                            });
                        } else {
                            postsContent.innerHTML = `<p style="text-align:center; color:#888;">Aucune publication trouvée.</p>`;
                        }
                    }).catch(err => { postsContent.innerHTML = `<p style="color:red; text-align:center;">Erreur de chargement des posts.</p>`; });
            }

            loadPosts('all');

            fetch('assets/worldMoroccoLow.svg').then(response => response.text()).then(svgContent => {
                document.getElementById('svg-container').innerHTML = svgContent;
                initMapInteractions();
            });

            function initMapInteractions() {
                const paths = document.querySelectorAll('#svg-container svg path');
                paths.forEach(path => {
                    path.addEventListener('click', function() {
                        const countryCode = this.getAttribute('id');
                        if (!countryCode) return;
                        paths.forEach(p => p.classList.remove('active'));
                        this.classList.add('active');
                        loadPosts(countryCode);
                    });
                });
                
                const container = document.getElementById('svg-container');
                let scale = 1, pointX = 0, pointY = 0, panning = false, startX = 0, startY = 0;
                const setTransform = () => { container.style.transform = `translate(${pointX}px, ${pointY}px) scale(${scale})`; };
                document.getElementById('zoom-in').onclick = () => { scale *= 1.3; setTransform(); };
                document.getElementById('zoom-out').onclick = () => { scale /= 1.3; setTransform(); };
                container.onmousedown = (e) => { e.preventDefault(); panning = true; startX = e.clientX - pointX; startY = e.clientY - pointY; container.style.cursor = 'grabbing'; };
                window.onmouseup = () => { panning = false; container.style.cursor = 'grab'; };
                container.onmousemove = (e) => { if (!panning) return; pointX = e.clientX - startX; pointY = e.clientY - startY; setTransform(); };
            }

            // --- GESTION DE L'AGENDA DYNAMIQUE ---
            const monthNames = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
            const daysContainer = document.getElementById('calendar-days');
            const monthYearText = document.getElementById('month-year');
            const eventsList = document.getElementById('dynamic-events-list');
            let currentDate = new Date(); 
            let navDate = new Date();     
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
                            type: ev.visibility,
                            title: ev.title,
                            time: `${st} - ${et}`,
                            label: labelMap[ev.visibility] || 'Événement'
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
                        eventsList.innerHTML += `<div class="event-item event-${ev.type}"><span class="badge badge-${ev.type}">${ev.label}</span> ${ev.title}<br><small>${ev.time}</small></div>`;
                    });
                } else { eventsList.innerHTML = `<p style="text-align:center; color:#888; padding:15px; font-style:italic;">Aucun événement prévu à cette date.</p>`; }
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
            
            // On charge les événements au lieu de juste faire le rendu vide !
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