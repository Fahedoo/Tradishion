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
                                // Gère l'avatar (Image ou initiale)
                                let avatarHtml = post.avatar_url 
                                    ? `<img src="${post.avatar_url}" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">` 
                                    : `<div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-weight:bold; background:#eee; border-radius:50%;">${post.author.charAt(0).toUpperCase()}</div>`;

                                // Gère l'image du post
                                let imageHtml = post.image_url 
                                    ? `<img src="${post.image_url}" style="width:100%; border-radius:8px; margin-bottom:15px; border:1px solid #eee;">` 
                                    : '';
                                    
                                // Gère la date
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

            // --- GESTION DE L'AGENDA ---
            const monthNames = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
            const daysContainer = document.getElementById('calendar-days');
            const monthYearText = document.getElementById('month-year');
            const eventsList = document.getElementById('dynamic-events-list');
            let currentDate = new Date(); let navDate = new Date();     
            let selectedDateString = `${currentDate.getFullYear()}-${String(currentDate.getMonth()+1).padStart(2, '0')}-${String(currentDate.getDate()).padStart(2, '0')}`;

            const eventsDB = {
                [selectedDateString]: [{ type: "private", title: "Acheter du fil rouge", time: "18:00 - 19:00", label: "Privé" }],
                "2026-04-02": [{ type: "shared", title: "Atelier raccommodage", time: "14:00 - 16:00", label: "Partagé" }],
                "2026-05-26": [{ type: "public", title: "Conférence Soie", time: "14:00 - 17:00", label: "Public" }]
            };

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
            renderCalendar(); displayEvents(selectedDateString);

            // --- SYSTEME DE TOAST & RECHERCHE HARMONISÉ ---
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
                    if(data.success) {
                        showToast('✅ Invitation envoyée avec succès !');
                    } else {
                        showToast('⚠️ Invitation déjà en attente ou contact déjà ajouté.', false);
                    }
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