<?php

abstract class ViewPrivate extends View {
    protected $user;
    protected $bodyStyle = ''; 

    public function __construct($userData) {
        $this->user = $userData;
    }

    protected function formatOrigins($originsData) {
        if (empty($originsData)) return $this->t('no_location');
        
        $originsArray = is_array($originsData) ? $originsData : array_filter(array_map('trim', explode(',', $originsData)));

        $countriesJson = @file_get_contents('data/countries.json');
        $countries = $countriesJson ? (json_decode($countriesJson, true) ?: []) : [];

        $names = array_map(function($code) use ($countries) {
            $code = strtoupper(trim((string) $code));
            $name = isset($countries[$code]) ? $countries[$code] : $code;
            return '<span class="origin-flag-item">' . $this->getCountryFlag($code) . ' ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</span>';
        }, $originsArray);

        return implode(', ', $names);
    }

    protected function getCountryFlag($countryCode) {
        $countryCode = strtoupper(trim((string) $countryCode));
        if (!preg_match('/^[A-Z]{2}$/', $countryCode)) {
            return '<span class="origin-flag-fallback">🌍</span>';
        }

        $countryCodeLower = strtolower($countryCode);
        return '<img class="country-flag-img" src="https://flagcdn.com/20x15/' . $countryCodeLower . '.png" alt="' . htmlspecialchars($countryCode, ENT_QUOTES, 'UTF-8') . '" loading="lazy" decoding="async" referrerpolicy="no-referrer">';
    }

    protected function getTextInitial($value, $fallback = '?') {
        $text = trim((string) $value);
        if ($text === '') {
            return $fallback;
        }

        if (function_exists('mb_substr')) {
            $initial = mb_substr($text, 0, 1, 'UTF-8');
            return function_exists('mb_strtoupper') ? mb_strtoupper($initial, 'UTF-8') : $initial;
        }

        return strtoupper(substr($text, 0, 1));
    }

    protected function getTextExcerpt($value, $length) {
        $text = trim((string) $value);
        if ($text === '') {
            return '';
        }

        if (function_exists('mb_substr')) {
            return mb_substr($text, 0, $length, 'UTF-8');
        }

        return substr($text, 0, $length);
    }

    protected function getActivePrivatePage() {
        $allowed = ['dashboard', 'explorer', 'events', 'messages', 'network', 'profile'];
        $page = isset($_GET['page']) ? (string) $_GET['page'] : 'dashboard';
        return in_array($page, $allowed, true) ? $page : 'dashboard';
    }

    protected function getSidebarRecommendations() {
        if (empty($this->user['id_user'])) {
            return [];
        }

        try {
            $db = new Database();
            $rows = $db->getRecommendations($this->user['id_user']);
            return is_array($rows) ? $rows : [];
        } catch (Exception $e) {
            return [];
        }
    }

    protected function renderPrivateSidebar() {
        $activePage = $this->getActivePrivatePage();
        $recommendations = $this->getSidebarRecommendations();
        $avatarInitial = $this->getTextInitial($this->user['display_name'] ?? '');
        ob_start();
        ?>
        <aside class="private-sidebar-fixed">
            <div class="private-sidebar-inner">
                <a href="index.php?page=profile" class="private-side-profile" style="text-decoration:none; color:inherit;">
                    <div class="private-side-avatar" style="overflow:hidden;">
                        <?php if (!empty($this->user['avatar_url'])): ?>
                            <img src="<?php echo htmlspecialchars($this->user['avatar_url']); ?>" style="width:100%; height:100%; object-fit:cover;" alt="Avatar">
                        <?php else: ?>
                            <?php echo $avatarInitial; ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <strong style="display:block; color:#2e2521;"><?php echo htmlspecialchars($this->user['display_name'] ?? 'Profil'); ?></strong>
                        <?php if (!empty($this->user['username'])): ?>
                            <small style="display:block; color:#86766e; margin-top:2px;">@<?php echo htmlspecialchars(ltrim($this->user['username'], '@')); ?></small>
                        <?php endif; ?>
                    </div>
                </a>

                <nav class="private-side-nav">
                    <a href="index.php?page=dashboard" class="<?php echo $activePage === 'dashboard' ? 'active' : ''; ?>">🏠 <?php echo $this->t('menu_home'); ?></a>
                    <a href="index.php?page=explorer" class="<?php echo $activePage === 'explorer' ? 'active' : ''; ?>">🧭 <?php echo $this->t('menu_explorer'); ?></a>
                    <a href="index.php?page=events" class="<?php echo $activePage === 'events' ? 'active' : ''; ?>">📅 <?php echo $this->t('menu_events'); ?></a>
                    <a href="index.php?page=messages" class="<?php echo $activePage === 'messages' ? 'active' : ''; ?>">💬 <?php echo $this->t('menu_messages'); ?></a>
                    <a href="index.php?page=network" class="<?php echo $activePage === 'network' ? 'active' : ''; ?>">👥 <?php echo $this->t('menu_network'); ?></a>
                </nav>

                <div class="private-side-recos">
                    <h4><?php echo $this->t('sidebar_suggestions'); ?></h4>
                    <?php if (empty($recommendations)): ?>
                        <p style="font-size:0.84rem; color:#8a7a72; margin:0;"><?php echo $this->t('sidebar_no_suggestions'); ?></p>
                    <?php else: ?>
                        <?php foreach ($recommendations as $rec): ?>
                            <?php $recInitial = $this->getTextInitial($rec['display_name'] ?? ''); ?>
                            <div class="private-side-reco-item">
                                <a href="index.php?page=profile&id=<?php echo (int) $rec['id_user']; ?>" style="display:flex; align-items:center; gap:10px; text-decoration:none; color:inherit; min-width:0;">
                                    <div class="private-side-reco-avatar" style="overflow:hidden;">
                                        <?php if (!empty($rec['avatar_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($rec['avatar_url']); ?>" style="width:100%; height:100%; object-fit:cover;" alt="Avatar">
                                        <?php else: ?>
                                            <?php echo $recInitial; ?>
                                        <?php endif; ?>
                                    </div>
                                    <div style="min-width:0;">
                                        <strong style="display:block; font-size:0.86rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?php echo htmlspecialchars($rec['display_name'] ?? 'Artisan'); ?></strong>
                                        <small style="display:block; color:#8a7a72; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?php echo htmlspecialchars($this->getTextExcerpt($rec['bio_free'] ?? 'Artisan', 24)); ?></small>
                                    </div>
                                </a>
                                <button type="button" onclick="addFriendGlobal(<?php echo (int) $rec['id_user']; ?>)" class="private-side-follow">+</button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </aside>
        <?php
        return ob_get_clean();
    }

    protected function getHeadAndHeader() {
        ob_start();
        $activePage = $this->getActivePrivatePage();
        ?>
        <!DOCTYPE html>
        <html lang="<?php echo $_SESSION['lang'] ?? 'fr'; ?>">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo $this->pageTitle; ?></title>
            <link rel="stylesheet" href="style/style.css?v=<?php echo @filemtime('style/style.css'); ?>">
            <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
            <script> window.Lang = <?php echo $this->getTranslationsJson(); ?>; </script>
        </head>
        <body class="dashboard-body private-page page-<?php echo htmlspecialchars($activePage); ?>" <?php echo !empty($this->bodyStyle) ? 'style="'.htmlspecialchars($this->bodyStyle).'"' : ''; ?>>
            <header class="dash-header">
                <button type="button" class="mobile-burger" id="mobile-burger-btn" aria-label="Menu">☰</button>
                <div class="dash-logo">
                    <a href="index.php?page=dashboard">
                        <img src="assets/images/logofinal.webp" alt="Tradishion" style="height: 35px; object-fit: contain;">
                    </a>
                </div>
                <div class="dash-search" style="position:relative;">
                    <input type="text" id="global-search" placeholder="<?php echo $this->t('search_placeholder'); ?>">
                    <div id="search-results" style="display:none; position:absolute; top:calc(100% + 8px); left:0; width:calc(100% + 40px); max-width:520px; background:white; border:1px solid #eee; border-radius:8px; box-shadow:0 4px 15px rgba(0,0,0,0.1); z-index:100; max-height:350px; overflow-y:auto;"></div>
                </div>
                <div class="dash-user-nav">
                    <select name="language" class="lang-select dash-lang-select" aria-label="Choisir la langue" onchange="window.location.href='index.php?lang=' + this.value + '&page=<?php echo isset($_GET['page']) ? htmlspecialchars($_GET['page']) : 'dashboard'; ?>'">
                        <option value="en" <?php echo (($_SESSION['lang']??'fr')=='en')?'selected':''; ?>>EN</option>
                        <option value="al" <?php echo (($_SESSION['lang']??'fr')=='al')?'selected':''; ?>>AL</option>
                        <option value="fr" <?php echo (($_SESSION['lang']??'fr')=='fr')?'selected':''; ?>>FR</option>
                        <option value="vi" <?php echo (($_SESSION['lang']??'fr')=='vi')?'selected':''; ?>>VI</option>
                    </select>
                    
                    <div style="position: relative;">
                        <span id="notif-bell" style="font-size: 1.3rem; cursor: pointer; position: relative; user-select: none;">
                            🔔
                            <span id="notif-badge" style="display:none; position:absolute; top:-5px; right:-5px; background:#e74c3c; color:white; font-size:0.6rem; font-weight:bold; border-radius:50%; width:16px; height:16px; align-items:center; justify-content:center;">0</span>
                        </span>
                        
                        <div id="notif-dropdown" style="display:none; position:absolute; top:40px; right:-10px; width:320px; background:white; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.1); border:1px solid #eee; z-index:2000; overflow:hidden;">
                            <div style="padding:15px; border-bottom:1px solid #eee; background:#fafafa;">
                                <h4 style="margin:0; color:#333; font-size:0.95rem;"><?php echo $this->t('notifications'); ?></h4>
                            </div>
                            <div id="notif-list" style="max-height:300px; overflow-y:auto; padding:10px;">
                                <p style="text-align:center; color:#888; font-size:0.85rem; padding:20px 0; margin:0;"><?php echo $this->t('loading'); ?></p>
                            </div>
                        </div>
                    </div>

                    <a href="index.php?page=logout" class="btn-outline" style="border-color:#ccc; color:#333; padding: 5px 15px; border-radius: 20px; font-weight:bold; font-size: 0.8rem;"><?php echo $this->t('nav_logout'); ?></a>
                </div>
            </header>
            <div class="mobile-sidebar-overlay" id="mobile-sidebar-overlay"></div>
            <?php echo $this->renderPrivateSidebar(); ?>
            <div class="private-main-content">
        <?php
        return ob_get_clean();
    }

    protected function getFooter() {
        ob_start();
        ?>
            </div>
            <button id="back-to-top-btn" title="Remonter en haut" style="position:fixed; right:26px; bottom:24px; width:48px; height:48px; border:none; border-radius:50%; background:#A64B35; color:white; cursor:pointer; font-size:1.15rem; box-shadow:0 8px 20px rgba(0,0,0,0.18); display:none; z-index:2500;">↑</button>
            <script>
                const notifBell = document.getElementById('notif-bell');
                const notifDropdown = document.getElementById('notif-dropdown');
                const notifBadge = document.getElementById('notif-badge');
                const notifList = document.getElementById('notif-list');

                notifBell.addEventListener('click', (e) => {
                    e.stopPropagation();
                    notifDropdown.style.display = notifDropdown.style.display === 'none' ? 'block' : 'none';
                });

                document.addEventListener('click', (e) => {
                    if (!notifBell.contains(e.target) && !notifDropdown.contains(e.target)) {
                        notifDropdown.style.display = 'none';
                    }
                });

                function fetchNotifications() {
                    fetch('index.php?page=api_notifications')
                    .then(res => res.json())
                    .then(data => {
                        if (data.length > 0) {
                            notifBadge.style.display = 'flex';
                            notifBadge.innerText = data.length;
                            notifList.innerHTML = '';
                            
                            data.forEach(notif => {
                                let av = notif.avatar ? `<img src="${notif.avatar}" style="width:40px; height:40px; border-radius:50%; object-fit:cover;">` : `<div style="width:40px; height:40px; border-radius:50%; background:#eee; display:flex; align-items:center; justify-content:center; font-weight:bold; color:#333;">${notif.initial}</div>`;
                                
                                notifList.innerHTML += `
                                    <a href="${notif.link}" style="display:flex; align-items:center; gap:12px; padding:12px; text-decoration:none; color:inherit; border-radius:8px; transition:background 0.2s;" onmouseover="this.style.background='#f9f9f9'" onmouseout="this.style.background='transparent'">
                                        ${av}
                                        <div style="flex:1; font-size:0.85rem; line-height:1.4;">
                                            <span style="color:#333;">${notif.message}</span>
                                        </div>
                                        <span style="color:#A64B35; font-size:1.2rem;">›</span>
                                    </a>
                                `;
                            });
                        } else {
                            notifBadge.style.display = 'none';
                            notifList.innerHTML = `<p style="text-align:center; color:#888; font-size:0.85rem; padding:20px 0; margin:0;">${window.Lang.no_notifications}</p>`;
                        }
                    });
                }

                fetchNotifications();
                setInterval(fetchNotifications, 30000);

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
                        if(data.success) { showToast(window.Lang.toast_invite_success); fetchNotifications(); } 
                        else { showToast(window.Lang.toast_invite_error, false); }
                    });
                }

                // Initialisation des selecteurs multiples d'origines
                function initOriginPicker(root) {
                    const select = root.querySelector('.origin-picker-select');
                    const search = root.querySelector('.origin-picker-search');
                    const chips = root.querySelector('.origin-picker-chips');
                    const list = root.querySelector('.origin-picker-list');
                    const status = root.querySelector('.origin-picker-status');
                    const max = parseInt(root.dataset.max || '4', 10);

                    if (!select || !chips || !list) return;

                    const options = Array.from(select.options);

                    function updateActiveState() {
                        const selectedValues = Array.from(select.selectedOptions).map(option => option.value);

                        chips.innerHTML = '';
                        selectedValues.forEach(value => {
                            const option = options.find(entry => entry.value === value);
                            if (!option) return;

                            const optionButton = list.querySelector(`.origin-picker-option[data-value="${CSS.escape(value)}"]`);
                            const chip = document.createElement('span');
                            chip.className = 'origin-chip';
                            chip.innerHTML = `<span>${optionButton ? optionButton.innerHTML : option.textContent}</span>`;

                            const removeBtn = document.createElement('button');
                            removeBtn.type = 'button';
                            removeBtn.innerText = '×';
                            removeBtn.addEventListener('click', () => {
                                option.selected = false;
                                updateActiveState();
                            });

                            chip.appendChild(removeBtn);
                            chips.appendChild(chip);
                        });

                        Array.from(list.querySelectorAll('.origin-picker-option')).forEach(button => {
                            button.classList.toggle('active', selectedValues.includes(button.dataset.value));
                        });
                    }

                    function toggleValue(value) {
                        const option = options.find(entry => entry.value === value);
                        if (!option) return;

                        if (option.selected) {
                            option.selected = false;
                            updateActiveState();
                            return;
                        }

                        const selectedCount = Array.from(select.selectedOptions).length;
                        if (selectedCount >= max) {
                            alert(`Maximum ${max} pays !`);
                            return;
                        }

                        option.selected = true;
                        updateActiveState();
                    }

                    if (search) {
                        search.addEventListener('input', function() {
                            const term = this.value.trim().toLowerCase();
                            Array.from(list.querySelectorAll('.origin-picker-option')).forEach(button => {
                                const visible = button.textContent.toLowerCase().includes(term);
                                button.classList.toggle('hidden', !visible);
                            });
                        });
                    }

                    list.addEventListener('click', function(event) {
                        const button = event.target.closest('.origin-picker-option');
                        if (!button) return;
                        toggleValue(button.dataset.value);
                    });

                    updateActiveState();
                }

                document.querySelectorAll('.origin-picker').forEach(initOriginPicker);

                document.getElementById('global-search')?.addEventListener('input', function() {
                    const query = this.value.trim();
                    const resultsDiv = document.getElementById('search-results');
                    
                    if (query.length < 2) { resultsDiv.style.display = 'none'; return; }
                    
                    fetch('index.php?page=api_search&q=' + encodeURIComponent(query))
                    .then(res => res.json()).then(data => {
                        resultsDiv.innerHTML = '';
                        if (data.length > 0) {
                            data.forEach(user => {
                                let avatarHtml = user.avatar_url ? `<img src="${user.avatar_url}" style="width:100%; height:100%; object-fit:cover;">` : (Array.from((user.display_name || '').trim())[0] || '?');
                                resultsDiv.innerHTML += `
                                <div style="padding:10px 15px; border-bottom:1px solid #eee; display:flex; align-items:center; gap:12px; transition: background 0.2s;" onmouseover="this.style.background='#f9f9f9'" onmouseout="this.style.background='transparent'">
                                    <a href="index.php?page=profile&id=${user.id_user}" style="flex:1; text-decoration:none; color:inherit; display:flex; align-items:center; gap:12px;">
                                        <div style="width:35px; height:35px; border-radius:50%; background:#eee; display:flex; justify-content:center; align-items:center; overflow:hidden; font-weight:bold; color:#333; font-size:0.9rem;">
                                            ${avatarHtml}
                                        </div>
                                        <div style="display:flex; flex-direction:column;">
                                            <strong style="font-size:0.95rem; color:#333;">${user.display_name}</strong>
                                            <span style="font-size:0.8rem; color:#888;">🌍 ${user.location || window.Lang.no_location}</span>
                                        </div>
                                    </a>
                                    <button onclick="addFriendGlobal(${user.id_user})" style="background:#FFF0ED; border:none; color:#A64B35; padding:6px 12px; border-radius:15px; cursor:pointer; font-size:0.8rem; font-weight:bold; transition:0.2s;" onmouseover="this.style.background='#A64B35'; this.style.color='white';" onmouseout="this.style.background='#FFF0ED'; this.style.color='#A64B35';">${window.Lang.btn_follow}</button>
                                </div>`;
                            });
                            resultsDiv.style.display = 'block';
                        } else {
                            resultsDiv.innerHTML = '<div style="padding:20px; color:#888; text-align:center; font-size:0.9rem;">Aucun artisan trouvé</div>';
                            resultsDiv.style.display = 'block';
                        }
                    });
                });

                const backTopBtn = document.getElementById('back-to-top-btn');
                const toggleBackToTop = () => {
                    backTopBtn.style.display = window.scrollY > 320 ? 'block' : 'none';
                };
                backTopBtn?.addEventListener('click', () => {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
                window.addEventListener('scroll', toggleBackToTop);
                toggleBackToTop();

                // Mobile sidebar toggle
                const burgerBtn = document.getElementById('mobile-burger-btn');
                const sidebarEl = document.querySelector('.private-sidebar-fixed');
                const sidebarOverlay = document.getElementById('mobile-sidebar-overlay');

                function openMobileSidebar() {
                    if (sidebarEl) sidebarEl.classList.add('mobile-open');
                    if (sidebarOverlay) sidebarOverlay.classList.add('active');
                    document.body.style.overflow = 'hidden';
                }

                function closeMobileSidebar() {
                    if (sidebarEl) sidebarEl.classList.remove('mobile-open');
                    if (sidebarOverlay) sidebarOverlay.classList.remove('active');
                    document.body.style.overflow = '';
                }

                if (burgerBtn) {
                    burgerBtn.addEventListener('click', function() {
                        if (sidebarEl && sidebarEl.classList.contains('mobile-open')) {
                            closeMobileSidebar();
                        } else {
                            openMobileSidebar();
                        }
                    });
                }

                if (sidebarOverlay) {
                    sidebarOverlay.addEventListener('click', closeMobileSidebar);
                }
            </script>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}
?>