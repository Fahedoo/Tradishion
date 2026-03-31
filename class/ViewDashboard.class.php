<?php

class ViewDashboard extends View {

    public function __construct() {
        $this->pageTitle = "Dashboard - TradiShion";
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
                <div class="dash-search">
                    <input type="text" placeholder="🔍 Rechercher des techniques, des artisans...">
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
                    <div class="avatar-large">👤</div>
                    <h3>Ton Profil</h3>
                    <p class="location">📍 France</p>
                    <div class="tags-container">
                        <span class="tag">#Couture</span>
                        <span class="tag">#Broderie</span>
                    </div>
                </div>

                <div class="card nav-menu">
                    <ul>
                        <li><a href="#" class="active">🏠 Accueil</a></li>
                        <li><a href="#">💬 Mes Messages</a></li>
                        <li><a href="#">👥 Mon Réseau</a></li>
                    </ul>
                </div>

                <div class="card recommendations">
                    <h4>RECOMMANDATIONS</h4>
                    <div class="rec-user">
                        <div class="rec-avatar">👩</div>
                        <div class="rec-info">
                            <strong>Naima (58)</strong><br>
                            <span>Broderie berbère</span>
                        </div>
                        <button class="add-btn">➕</button>
                    </div>
                    <div class="rec-user">
                        <div class="rec-avatar">👨</div>
                        <div class="rec-info">
                            <strong>Marco (35)</strong><br>
                            <span>Couture italienne</span>
                        </div>
                        <button class="add-btn">➕</button>
                    </div>
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
                        <div class="calendar-grid" id="calendar-days">
                            </div>
                    </div>

                    <div class="events-list" id="dynamic-events-list">
                        </div>

                    <button class="btn-create-event">+ Créer un événement</button>
                </div>
            </aside>

        </div>

        <script>
            /* =========================================
               1. GESTION DE LA CARTE SVG ET DU FEED
               ========================================= */
            const feedTitle = document.getElementById('feed-title');
            const postsContent = document.getElementById('posts-content');

            function loadPosts(countryCode = 'all') {
                if(countryCode === 'all') {
                    feedTitle.innerText = "À la une de la communauté";
                } else {
                    feedTitle.innerText = `Publications pour le pays : ${countryCode}`;
                }
                postsContent.innerHTML = `<p style="text-align:center; color:#888;">Chargement...</p>`;

                fetch(`index.php?page=api_posts&country=${countryCode}`)
                    .then(res => res.json())
                    .then(data => {
                        postsContent.innerHTML = ''; 
                        if (data && data.length > 0) {
                            data.forEach(post => {
                                postsContent.innerHTML += `
                                    <div class="feed-post">
                                        <div class="post-header">
                                            <div class="post-avatar">👤</div>
                                            <div class="post-meta">
                                                <strong>${post.author}</strong> <span style="color:#A64B35; font-size:0.8rem;">#${post.tag || 'Tradition'}</span><br>
                                                <span>📍 ${post.location || 'Inconnu'}</span>
                                            </div>
                                        </div>
                                        <h4 style="margin:10px 0; color:#333;">${post.title}</h4>
                                        <p>${post.content}</p>
                                        <div class="post-actions">❤️ J'aime 💬 Commenter</div>
                                    </div>
                                `;
                            });
                        } else {
                            postsContent.innerHTML = `<p style="text-align:center; color:#888;">Aucune publication trouvée.</p>`;
                        }
                    })
                    .catch(err => {
                        postsContent.innerHTML = `<p style="color:red; text-align:center;">Erreur de chargement des posts.</p>`;
                    });
            }

            loadPosts('all');

            fetch('assets/worldMoroccoLow.svg')
                .then(response => response.text())
                .then(svgContent => {
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

            /* =========================================
               2. GESTION DE L'AGENDA (INTERACTIF)
               ========================================= */
            const monthNames = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
            const daysContainer = document.getElementById('calendar-days');
            const monthYearText = document.getElementById('month-year');
            const eventsList = document.getElementById('dynamic-events-list');
            
            let currentDate = new Date(); 
            let navDate = new Date();     
            
            // Format YYYY-MM-DD pour la comparaison
            let selectedDateString = `${currentDate.getFullYear()}-${String(currentDate.getMonth()+1).padStart(2, '0')}-${String(currentDate.getDate()).padStart(2, '0')}`;

            // Dictionnaire factice d'événements pour la Bêta
            const eventsDB = {
                [selectedDateString]: [
                    { type: "private", title: "Acheter du fil rouge", time: "18:00 - 19:00", label: "Privé" }
                ],
                "2026-04-02": [
                    { type: "shared", title: "Atelier raccommodage", time: "14:00 - 16:00", label: "Partagé" }
                ],
                "2026-05-26": [
                    { type: "public", title: "Conférence Soie", time: "14:00 - 17:00", label: "Public" }
                ]
            };

            // Fonction qui met à jour la liste HTML en dessous du calendrier
            function displayEvents(dateString) {
                eventsList.innerHTML = '';
                if (eventsDB[dateString] && eventsDB[dateString].length > 0) {
                    eventsDB[dateString].forEach(ev => {
                        eventsList.innerHTML += `
                            <div class="event-item event-${ev.type}">
                                <span class="badge badge-${ev.type}">${ev.label}</span> ${ev.title}<br>
                                <small>${ev.time}</small>
                            </div>
                        `;
                    });
                } else {
                    eventsList.innerHTML = `<p style="text-align:center; color:#888; padding:15px; font-style:italic;">Aucun événement prévu à cette date.</p>`;
                }
            }

            // Génération de la grille du mois
            function renderCalendar() {
                const year = navDate.getFullYear();
                const month = navDate.getMonth();
                
                monthYearText.innerText = `${monthNames[month]} ${year}`;
                daysContainer.innerHTML = '';

                // En-têtes L, M, M...
                const daysOfWeek = ['L', 'M', 'M', 'J', 'V', 'S', 'D'];
                daysOfWeek.forEach(day => {
                    const div = document.createElement('div');
                    div.className = 'cal-day-name';
                    div.innerText = day;
                    daysContainer.appendChild(div);
                });

                let firstDayIndex = new Date(year, month, 1).getDay() - 1;
                if(firstDayIndex === -1) firstDayIndex = 6; 

                const daysInMonth = new Date(year, month + 1, 0).getDate();

                // Cases vides
                for(let i = 0; i < firstDayIndex; i++) {
                    const div = document.createElement('div');
                    div.className = 'cal-day empty';
                    daysContainer.appendChild(div);
                }

                // Cases des jours cliquables
                for(let i = 1; i <= daysInMonth; i++) {
                    let dateString = `${year}-${String(month+1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
                    let isSelected = (dateString === selectedDateString);
                    
                    let dayDiv = document.createElement('div');
                    dayDiv.className = isSelected ? 'cal-day active-day' : 'cal-day';
                    dayDiv.innerText = i;
                    
                    // Interaction au clic !
                    dayDiv.onclick = () => {
                        selectedDateString = dateString;
                        renderCalendar(); // Met à jour le visuel de la grille (rond rouge)
                        displayEvents(selectedDateString); // Met à jour les événements en dessous
                    };
                    
                    // Petit point rouge si un événement existe ce jour-là
                    if (eventsDB[dateString]) {
                        dayDiv.innerHTML += `<div style="width:4px; height:4px; background:${isSelected ? 'white' : '#A64B35'}; border-radius:50%; margin: 2px auto 0;"></div>`;
                    }

                    daysContainer.appendChild(dayDiv);
                }
            }

            document.getElementById('prev-month').addEventListener('click', () => { navDate.setMonth(navDate.getMonth() - 1); renderCalendar(); });
            document.getElementById('next-month').addEventListener('click', () => { navDate.setMonth(navDate.getMonth() + 1); renderCalendar(); });

            // Initialisation
            renderCalendar();
            displayEvents(selectedDateString);
        </script>
        <?php
        return ob_get_clean();
    }
}
?>