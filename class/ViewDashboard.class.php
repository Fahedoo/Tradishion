<?php

class ViewDashboard extends ViewPrivate {
    private $recommendations;
    private $needsOnboarding;
    private $onboardingError;

    public function __construct($userData, $recos, $needsOnboarding = false, $onboardingError = '') {
        parent::__construct($userData);
        $this->pageTitle = "Dashboard - TradiShion";
        $this->recommendations = $recos;
        $this->needsOnboarding = $needsOnboarding;
        $this->onboardingError = $onboardingError;
    }

    protected function getBodyContent() {
        ob_start();
        ?>
        <style> html { overflow-y: scroll; } </style>

        <?php if ($this->needsOnboarding): ?>
            <?php
            $countriesJson = @file_get_contents('data/countries.json');
            $countries = $countriesJson ? json_decode($countriesJson, true) : [];
            ?>
            <div id="onboarding-overlay" style="position:fixed; inset:0; background:rgba(20, 15, 12, 0.62); z-index:2000; display:flex; align-items:center; justify-content:center; padding:20px;">
                <div style="width:100%; max-width:760px; background:#fff; border-radius:20px; box-shadow:0 30px 80px rgba(0,0,0,0.35); overflow:hidden;">
                    <div style="padding:26px 28px; background:linear-gradient(135deg, #fff3ef 0%, #fffdfb 100%); border-bottom:1px solid rgba(166, 75, 53, 0.18);">
                        <h2 style="margin:0; color:#2f221d; font-family:'Libre Baskerville', serif;"><?php echo $this->t('dash_welcome_title'); ?></h2>
                        <p style="margin:8px 0 0; color:#6d5a51;"><?php echo $this->t('dash_welcome_desc'); ?></p>
                    </div>

                    <form id="onboarding-form" action="index.php?page=dashboard" method="POST" style="padding:26px 28px; display:grid; gap:16px;">
                        <input type="hidden" name="action" value="complete_onboarding">

                        <?php if (!empty($this->onboardingError)): ?>
                            <div style="background:#fff4f4; color:#8a2231; border:1px solid rgba(138, 34, 49, 0.2); border-radius:12px; padding:12px 14px; font-size:0.9rem;">
                                <?php echo htmlspecialchars($this->onboardingError); ?>
                            </div>
                        <?php endif; ?>

                        <div>
                            <label for="onboarding-username" style="display:block; margin-bottom:7px; font-size:0.82rem; text-transform:uppercase; letter-spacing:0.04em; color:#5b463c; font-weight:700;"><?php echo $this->t('dash_username_label'); ?></label>
                            <input id="onboarding-username" type="text" name="username" required minlength="3" maxlength="30" pattern="[a-z0-9._]{3,30}" value="<?php echo htmlspecialchars($_POST['username'] ?? ($this->user['username'] ?? '')); ?>" style="width:100%; padding:12px 14px; border:1px solid #e6d9d3; border-radius:12px;">
                            <small style="display:block; margin-top:6px; color:#7f6f67;"><?php echo $this->t('dash_username_help'); ?></small>
                        </div>

                        <div>
                            <label for="onboarding-origin-search" style="display:block; margin-bottom:7px; font-size:0.82rem; text-transform:uppercase; letter-spacing:0.04em; color:#5b463c; font-weight:700;"><?php echo $this->t('dash_origins_label'); ?></label>
                            <?php $selectedOrigins = isset($_POST['origins']) && is_array($_POST['origins']) ? $_POST['origins'] : ($this->user['origins'] ?? []); ?>
                            <div class="origin-picker" data-max="4">
                                <div class="origin-picker-chips"></div>
                                <input type="text" id="onboarding-origin-search" class="origin-picker-search" placeholder="<?php echo $this->t('prof_search_country'); ?>" style="width:100%; padding:12px 14px; border:1px solid #e6d9d3; border-radius:12px;">
                                <select name="origins[]" id="onboarding-origins" multiple required class="origin-picker-select" style="display:none;">
                                    <?php foreach ($countries as $code => $name): ?>
                                        <?php $selected = in_array($code, $selectedOrigins, true) ? 'selected' : ''; ?>
                                        <option value="<?php echo htmlspecialchars($code); ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="origin-picker-list">
                                    <?php foreach ($countries as $code => $name): ?>
                                        <?php $active = in_array($code, $selectedOrigins, true) ? 'active' : ''; ?>
                                        <button type="button" class="origin-picker-option <?php echo $active; ?>" data-value="<?php echo htmlspecialchars($code); ?>"><?php echo $this->getCountryFlag($code) . ' ' . htmlspecialchars($name); ?></button>
                                    <?php endforeach; ?>
                                </div>
                                <div class="origin-picker-status"></div>
                            </div>
                        </div>

                        <div>
                            <label for="onboarding-display-name" style="display:block; margin-bottom:7px; font-size:0.82rem; text-transform:uppercase; letter-spacing:0.04em; color:#5b463c; font-weight:700;"><?php echo $this->t('dash_display_name_label'); ?></label>
                            <div style="display:flex; gap:10px; align-items:center;">
                                <input id="onboarding-display-name" type="text" name="display_name" maxlength="128" value="<?php echo htmlspecialchars($_POST['display_name'] ?? ''); ?>" style="flex:1; padding:12px 14px; border:1px solid #e6d9d3; border-radius:12px;">
                                <button type="button" id="onboarding-skip-display" style="border:1px solid rgba(166, 75, 53, 0.35); background:#fff; color:#A64B35; border-radius:12px; padding:11px 14px; cursor:pointer; font-weight:700;"><?php echo $this->t('dash_skip'); ?></button>
                            </div>
                            <small style="display:block; margin-top:6px; color:#7f6f67;"><?php echo $this->t('dash_display_name_help'); ?></small>
                        </div>

                        <button type="submit" style="margin-top:6px; border:none; background:linear-gradient(135deg, #A64B35 0%, #7c3d2c 100%); color:#fff; padding:14px 16px; border-radius:12px; font-weight:800; letter-spacing:0.03em; cursor:pointer;"><?php echo $this->t('dash_finalize_profile'); ?></button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <div class="dash-container" style="display:grid; gap: 34px; padding-top: 24px; max-width: 1500px; margin: 0 auto;">
            
            <aside class="dash-left" style="display:none;">
                <div class="card profile-overview" style="text-align: center; padding: 30px 20px; border:none; box-shadow:0 4px 20px rgba(0,0,0,0.03); border-radius:16px;">
                    <a href="index.php?page=profile" style="text-decoration:none; color:inherit;">
                        <div class="avatar-large" style="margin: 0 auto 15px; width:90px; height:90px; overflow:hidden;">
                            <?php if (!empty($this->user['avatar_url'])): ?>
                                <img src="<?php echo htmlspecialchars($this->user['avatar_url']); ?>" style="width:100%; height:100%; object-fit:cover;" alt="Avatar">
                            <?php else: ?>
                                <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#eee; color:#333; font-size:2rem; font-weight:bold;">
                                    <?php echo $this->getTextInitial($this->user['display_name']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <h3 style="font-size:1.2rem; margin-bottom:5px; color:#333;"><?php echo htmlspecialchars($this->user['display_name']); ?></h3>
                        
                        <p class="location" style="color: #888; font-size: 0.9rem; margin:0;">🌍 <?php echo $this->formatOrigins($this->user['origins'] ?? []); ?></p>
                    </a>
                </div>

                <div class="card nav-menu" style="border:none; box-shadow:0 4px 20px rgba(0,0,0,0.03); border-radius:16px; padding:20px;">
                   <ul style="list-style: none; padding: 0; margin: 0;">
                        <li><a href="index.php?page=dashboard" class="active" style="background:#FFF0ED; color:#A64B35; font-weight:bold; padding: 12px 15px; border-radius: 8px;">🏠 <?php echo $this->t('menu_home'); ?></a></li>
                        <li><a href="index.php?page=explorer" style="padding: 12px 15px; display:block;">🧭 <?php echo $this->t('menu_explorer'); ?></a></li>
                        <li><a href="index.php?page=events" style="padding: 12px 15px; display:block;">📅 <?php echo $this->t('menu_events'); ?></a></li>
                        <li><a href="index.php?page=messages" style="padding: 12px 15px; display:block;">💬 <?php echo $this->t('menu_messages'); ?></a></li>
                        <li><a href="index.php?page=network" style="padding: 12px 15px; display:block;">👥 <?php echo $this->t('menu_network'); ?></a></li>
                    </ul>
                </div>

                <div class="card recommendations" style="border:none; box-shadow:0 4px 20px rgba(0,0,0,0.03); border-radius:16px; padding:25px;">
                    <h4 style="color:#333; border-bottom:2px solid #FFF0ED; padding-bottom:10px; margin-bottom:20px; font-size:0.95rem;"><?php echo $this->t('recos_title'); ?></h4>
                    <?php foreach ($this->recommendations as $rec): ?>
                        <?php $recInit = $this->getTextInitial($rec['display_name']); ?>
                        <div class="rec-user" style="display:flex; align-items:center; gap:12px; margin-bottom:15px;">
                            <a href="index.php?page=profile&id=<?php echo $rec['id_user']; ?>">
                                <div class="rec-avatar" style="background:#eee; width:45px; height:45px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; overflow:hidden; color:#333;">
                                    <?php if (!empty($rec['avatar_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($rec['avatar_url']); ?>" style="width:100%; height:100%; object-fit:cover;">
                                    <?php else: ?>
                                        <?php echo $recInit; ?>
                                    <?php endif; ?>
                                </div>
                            </a>
                            <div class="rec-info" style="flex:1; font-size:0.85rem;">
                                <a href="index.php?page=profile&id=<?php echo $rec['id_user']; ?>" style="color:inherit; text-decoration:none;">
                                    <strong style="color:#333; font-size:0.95rem;"><?php echo htmlspecialchars($rec['display_name']); ?></strong>
                                </a><br>
                                <span style="color:#666;"><?php echo htmlspecialchars($this->getTextExcerpt($rec['bio_free'] ?? 'Artisan', 20)) . '...'; ?></span>
                            </div>
                            <button onclick="addFriendGlobal(<?php echo $rec['id_user']; ?>)" class="add-btn" style="background:#FFF0ED; border:none; color:#A64B35; border-radius:50%; width:30px; height:30px; cursor:pointer; font-size:1rem;">➕</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </aside>

            <main class="dash-center" style="display:flex; flex-direction:column; gap:30px; min-height: 800px; width:100%; min-width:0; align-self:stretch;">
                <div class="card map-card" style="width:100%; min-width:0; border:none; box-shadow:0 4px 20px rgba(0,0,0,0.03); border-radius:16px; padding:25px;">
                    <h3 style="font-family:'Libre Baskerville', serif; color:#333; margin-top:0; margin-bottom:15px;"><?php echo $this->t('map_title'); ?></h3>
                    <div class="map-window" style="position:relative; height:450px; background:#eef2f7; border-radius:12px; overflow:hidden; user-select:none;">
                        <div class="zoom-controls" style="position:absolute; top:15px; left:15px; display:flex; flex-direction:column; gap:5px; z-index:10;">
                            <button id="zoom-in" style="width:35px; height:35px; background:white; border:none; border-radius:6px; box-shadow:0 2px 10px rgba(0,0,0,0.1); cursor:pointer; font-size:1.2rem; color:#555;">+</button>
                            <button id="zoom-out" style="width:35px; height:35px; background:white; border:none; border-radius:6px; box-shadow:0 2px 10px rgba(0,0,0,0.1); cursor:pointer; font-size:1.2rem; color:#555;">-</button>
                        </div>
                        <div id="svg-container" style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; cursor:grab; max-width:96%; margin:0 auto;"></div>
                    </div>
                </div>

                <div class="card feed-card" style="width:100%; min-width:0; border:none; box-shadow:none; padding:0; background:transparent; display:flex; flex-direction:column; align-self:stretch;">
                    <h3 id="feed-title" style="font-family:'Libre Baskerville', serif; color:#333; margin-bottom:20px; border-bottom:2px solid #ddd; padding-bottom:10px;"><?php echo $this->t('feed_title'); ?></h3>
                    <div id="posts-content" style="min-height: 500px; width:100%; display:flex; flex-direction:column; align-items:stretch; gap:25px; overflow:visible;">
                        <p style="text-align:center; padding: 20px; color:#888;"><?php echo $this->t('loading'); ?></p>
                    </div>
                    <div id="posts-expand-wrap" style="display:none; text-align:center; margin-top:10px;">
                        <button id="posts-expand-btn" type="button" class="btn-outline" style="border-color:#cdbdb5; color:#5c4b43; padding:9px 18px; border-radius:20px;"><?php echo $this->t('show_more'); ?></button>
                    </div>
                </div>
            </main>

            <aside class="dash-right" style="display:flex; justify-content:center; align-self:start;">
                <div class="card agenda-card" style="position:sticky; top:96px; border:none; box-shadow:0 4px 20px rgba(0,0,0,0.03); border-radius:16px; padding:25px; width:100%; max-width:360px; max-height:calc(100vh - 118px); overflow-y:auto;">
                    <h3 style="border-bottom:2px solid #FFF0ED; padding-bottom:15px; color:#333; margin-bottom:20px; font-size:1.1rem; text-transform:uppercase;"><?php echo $this->t('agenda_title'); ?></h3>
                    <div class="calendar-wrapper">
                        <div class="calendar-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                            <button id="prev-month" style="background:none; border:none; font-size:1.2rem; cursor:pointer; color:#555;">❮</button>
                            <h4 id="month-year" style="margin:0; color:#333; font-size:1.1rem;">...</h4>
                            <button id="next-month" style="background:none; border:none; font-size:1.2rem; cursor:pointer; color:#555;">❯</button>
                        </div>
                        <div class="calendar-grid" id="calendar-days" style="display:grid; grid-template-columns:repeat(7, 1fr); gap:8px; text-align:center;"></div>
                    </div>
                    <div class="events-list" id="dynamic-events-list" style="margin-top:20px;"></div>
                    <button id="btn-create-event" class="btn-create-event" style="width:100%; background:#A64B35; color:white; border:none; padding:12px; border-radius:8px; font-weight:bold; cursor:pointer; margin-top:20px; font-size:1rem; transition:0.2s;" onmouseover="this.style.background='#8b3d2b'" onmouseout="this.style.background='#A64B35'"><?php echo $this->t('btn_create_event'); ?></button>
                </div>
            </aside>

        </div>

        <div id="create-event-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center; overflow-y:auto; padding: 20px 0;">
            <div class="card" style="width:100%; max-width:600px; padding:40px; background:white; border-radius:12px; margin: auto;">
                <h2 style="margin-bottom:5px; color:#333; font-family:'Libre Baskerville', serif;"><?php echo $this->t('dash_create_event_title'); ?></h2>
                <form id="create-event-form" style="display:flex; flex-direction:column; gap:20px;">
                    <label style="border: 2px dashed #ddd; border-radius: 12px; padding: 40px 20px; text-align: center; cursor: pointer; background: #fafafa; display: block;">
                        <span style="font-size: 2rem; color: #ccc;">🖼️</span><br>
                        <strong style="color: #555;"><?php echo $this->t('dash_add_cover_image'); ?></strong>
                        <input type="file" name="cover_image" accept="image/*" style="display: none;" onchange="document.getElementById('cover-file-name').innerText = this.files[0].name;">
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
                        <button type="button" onclick="document.getElementById('create-event-modal').style.display='none'" class="btn-outline" style="border-color:#ddd; color:#555; padding:12px 25px; border-radius:8px; font-weight:bold;"><?php echo $this->t('btn_cancel'); ?></button>
                        <button type="submit" class="btn-submit" style="width:auto; padding:12px 25px; border-radius:8px; font-size:1rem; background:#A64B35; color:white; border:none;"><?php echo $this->t('dash_submit_event'); ?></button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            const feedTitle = document.getElementById('feed-title');
            const postsContent = document.getElementById('posts-content');

            <?php if ($this->needsOnboarding): ?>
            const onboardingForm = document.getElementById('onboarding-form');
            const onboardingUsername = document.getElementById('onboarding-username');
            const onboardingDisplayName = document.getElementById('onboarding-display-name');
            const onboardingSkipDisplay = document.getElementById('onboarding-skip-display');

            if (onboardingSkipDisplay) {
                onboardingSkipDisplay.addEventListener('click', function() {
                    onboardingDisplayName.value = onboardingUsername.value.trim();
                    onboardingDisplayName.dispatchEvent(new Event('input'));
                });
            }

            if (onboardingForm) {
                onboardingForm.addEventListener('submit', function() {
                    if (onboardingDisplayName.value.trim() === '') {
                        onboardingDisplayName.value = onboardingUsername.value.trim();
                    }
                });
            }
            <?php endif; ?>

            function loadPosts(countryCode = 'all') {
                if(countryCode === 'all') { feedTitle.innerText = window.Lang.feed_title; } 
                else { feedTitle.innerText = `${window.Lang.dash_posts_for_country}${countryCode}`; }
                
                postsContent.innerHTML = `<div class="loading-state" style="width:100%; box-sizing:border-box; display:flex; align-items:center; justify-content:center; min-height:500px; text-align:center; color:#888; margin-top:0;">${window.Lang.dash_loading_posts}</div>`;

                fetch(`index.php?page=api_posts&country=${countryCode}`)
                    .then(res => res.json())
                    .then(data => {
                        postsContent.innerHTML = ''; 
                        if (data && data.length > 0) {
                            data.forEach(post => {
                                let avatarHtml = post.avatar_url ? `<img src="${post.avatar_url}" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">` : `<div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-weight:bold; background:#eee; border-radius:50%; color:#333;">${Array.from((post.author || '').trim())[0] || '?'}</div>`;
                                
                                let imageHtml = post.image_url ? `<img src="${post.image_url}" style="width:100%; height:350px; object-fit:cover; border-radius:12px; margin:15px 0;">` : '';
                                
                                let dateObj = new Date(post.created_at);
                                let dateString = dateObj.toLocaleDateString('fr-FR', {day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute:'2-digit'});
                                
                                let likeIcon = post.user_liked > 0 ? '❤️' : '🤍';
                                let likeCountText = post.likes_count > 0 ? post.likes_count : window.Lang.post_like;

                                postsContent.innerHTML += `
                                    <div class="feed-post card" style="margin-bottom:25px; padding:25px; border:none; box-shadow:0 4px 20px rgba(0,0,0,0.04); border-radius:16px;">
                                        <div class="post-header" style="display:flex; align-items:center; gap:15px; margin-bottom:15px;">
                                            <a href="index.php?page=profile&id=${post.id_author}" style="text-decoration:none;">
                                                <div class="post-avatar" style="width:50px; height:50px;">${avatarHtml}</div>
                                            </a>
                                            <div class="post-meta">
                                                <a href="index.php?page=profile&id=${post.id_author}" style="text-decoration:none; color:#333; font-size:1.05rem;"><strong>${post.author}</strong></a> 
                                                <span class="post-tag" style="color:#A64B35; font-size:0.85rem; margin-left:10px; font-weight:bold;">#${post.tag || 'Tradition'}</span><br>
                                                
                                                <span style="color:#888; font-size:0.85rem;">🌍 ${post.location_html || post.location || window.Lang.no_location} • ${dateString}</span>
                                            </div>
                                        </div>
                                        
                                        <h4 style="margin:0 0 10px 0; color:#333; font-size:1.1rem;">${post.title || ''}</h4>
                                        <p style="color:#444; line-height:1.6; font-size:0.95rem;">${post.content}</p>
                                        
                                        ${imageHtml}
                                        
                                        <div class="post-actions" style="border-top:1px solid #f0f0f0; padding-top:15px; color:#666; font-size:0.95rem; display:flex; gap:25px;">
                                            <button type="button" class="like-toggle" data-post-id="${post.id_post}" style="cursor:pointer; display:flex; align-items:center; gap:8px; background:none; border:none; color:inherit; padding:0;">
                                                <span class="like-icon">${likeIcon}</span>
                                                <span class="like-count" style="transition:0.2s; font-weight:bold;" onmouseover="this.style.color='#A64B35'" onmouseout="this.style.color='#666'">${likeCountText}</span>
                                            </button>
                                            <button type="button" class="comment-toggle" data-post-id="${post.id_post}" style="cursor:pointer; transition: 0.2s; display:flex; align-items:center; gap:8px; font-weight:bold; background:none; border:none; color:inherit; padding:0;" onmouseover="this.style.color='#A64B35'" onmouseout="this.style.color='#666'">
                                                <span>💬</span> ${window.Lang.post_comment}
                                            </button>
                                        </div>
                                        
                                        <div id="comments-section-${post.id_post}" style="display:none; margin-top:20px; border-top:1px dashed #eee; padding-top:20px; background: #fafafa; padding: 20px; border-radius: 12px;">
                                            <div id="comments-list-${post.id_post}" style="max-height:250px; overflow-y:auto; margin-bottom:15px; padding-right: 5px;"></div>
                                            <div style="display:flex; gap:10px; align-items:center; border-top: 1px solid #ddd; padding-top: 15px;">
                                                <input type="text" id="comment-input-${post.id_post}" placeholder="${window.Lang.add_comment}" style="flex:1; padding:12px 18px; border:1px solid #ddd; border-radius:25px; outline:none; font-size:0.9rem; background:white;">
                                                <button type="button" class="comment-send" data-post-id="${post.id_post}" style="background:#FFF0ED; border:none; color:#A64B35; cursor:pointer; font-size:1.2rem; display:flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:50%; transition:0.2s;" onmouseover="this.style.background='#A64B35'; this.style.color='white';" onmouseout="this.style.background='#FFF0ED'; this.style.color='#A64B35';">➤</button>
                                            </div>
                                        </div>
                                    </div>`;
                            });

                            const postCards = postsContent.querySelectorAll('.feed-post');
                            const expandWrap = document.getElementById('posts-expand-wrap');
                            const expandBtn = document.getElementById('posts-expand-btn');
                            if (postCards.length > 3) {
                                postCards.forEach((card, idx) => {
                                    if (idx > 2) card.classList.add('hidden-post');
                                });
                                expandWrap.style.display = 'block';
                                expandBtn.textContent = window.Lang.show_more;
                                expandBtn.onclick = () => {
                                    const hidden = postsContent.querySelectorAll('.hidden-post');
                                    const isExpanding = hidden.length > 0;
                                    if (isExpanding) {
                                        hidden.forEach(el => el.classList.remove('hidden-post'));
                                        expandBtn.textContent = window.Lang.show_less;
                                    } else {
                                        const allCards = postsContent.querySelectorAll('.feed-post');
                                        allCards.forEach((card, idx) => {
                                            if (idx > 2) card.classList.add('hidden-post');
                                        });
                                        expandBtn.textContent = window.Lang.show_more;
                                        window.scrollTo({ top: postsContent.offsetTop - 120, behavior: 'smooth' });
                                    }
                                };
                            } else {
                                expandWrap.style.display = 'none';
                            }
                        } else { 
                            postsContent.innerHTML = `
                            <div class="no-posts-state" style="width:100%; box-sizing:border-box; display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:500px; text-align:center; padding: 60px 20px; background:white; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.03); align-self:stretch;">
                                <span style="font-size:3rem; opacity:0.5; display:block; margin-bottom:15px;">🏜️</span>
                                <p style="color:#666; font-size:1.1rem; margin:0;">${window.Lang.no_posts}</p>
                            </div>`; 
                        }
                    });
            }

            function toggleLike(postId, element) {
                const fd = new FormData(); fd.append('id_post', postId);
                fetch('index.php?page=api_like_post', {method: 'POST', body: fd}).then(res => res.json()).then(data => {
                    let iconSpan = element.querySelector('.like-icon');
                    let countSpan = element.querySelector('.like-count');
                    let currentCount = parseInt(countSpan.innerText) || 0;
                    if(data.status === 'liked') { iconSpan.innerText = '❤️'; countSpan.innerText = currentCount + 1; } 
                    else if(data.status === 'unliked') { iconSpan.innerText = '🤍'; let newCount = currentCount - 1; countSpan.innerText = newCount > 0 ? newCount : window.Lang.post_like; }
                });
            }

            function toggleComments(postId) {
                const section = document.getElementById('comments-section-' + postId);
                if(section.style.display === 'none') { section.style.display = 'block'; loadComments(postId); } 
                else { section.style.display = 'none'; }
            }

            function loadComments(postId) {
                const list = document.getElementById('comments-list-' + postId);
                list.innerHTML = `<p style="font-size:0.85rem; color:#888; text-align:center;">${window.Lang.loading}</p>`;
                fetch('index.php?page=api_get_comments&id_post=' + postId).then(res => res.json()).then(data => {
                    list.innerHTML = '';
                    if(data.length === 0) { list.innerHTML = `<p style="font-size:0.85rem; color:#888; text-align:center;">${window.Lang.dash_no_comments}</p>`; return; }
                    data.forEach(c => {
                        let av = c.avatar_url ? `<img src="${c.avatar_url}" style="width:32px; height:32px; border-radius:50%; object-fit:cover;">` : `<div style="width:32px; height:32px; border-radius:50%; background:#eee; display:flex; align-items:center; justify-content:center; font-size:0.85rem; font-weight:bold; color:#333;">${Array.from((c.author || '').trim())[0] || '?'}</div>`;
                        list.innerHTML += `<div style="display:flex; gap:12px; margin-bottom:15px; align-items:flex-start;">${av}<div style="background:white; padding:10px 15px; border-radius:12px; border: 1px solid #eee; flex:1;"><strong style="font-size:0.85rem; color:#333;">${c.author}</strong><p style="margin:0; font-size:0.9rem; color:#555;">${c.content}</p></div></div>`;
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

            postsContent.addEventListener('click', function(e) {
                const likeBtn = e.target.closest('.like-toggle');
                if (likeBtn) {
                    e.preventDefault();
                    toggleLike(parseInt(likeBtn.dataset.postId, 10), likeBtn);
                    return;
                }

                const commentToggleBtn = e.target.closest('.comment-toggle');
                if (commentToggleBtn) {
                    e.preventDefault();
                    toggleComments(parseInt(commentToggleBtn.dataset.postId, 10));
                    return;
                }

                const commentSendBtn = e.target.closest('.comment-send');
                if (commentSendBtn) {
                    e.preventDefault();
                    addComment(parseInt(commentSendBtn.dataset.postId, 10));
                }
            });

            loadPosts('all');

            // --- CARTE ---
            fetch('assets/worldMoroccoLow.svg').then(res => res.text()).then(svg => {
                document.getElementById('svg-container').innerHTML = svg;
                
                const paths = document.querySelectorAll('#svg-container svg path');
                paths.forEach(path => {
                    path.addEventListener('click', function() {
                        const c = this.getAttribute('id'); if (!c) return;
                        paths.forEach(p => p.classList.remove('active')); this.classList.add('active'); loadPosts(c);
                    });
                });

                const mapElement = document.querySelector('#svg-container svg');
                const container = document.getElementById('svg-container');
                if(mapElement) {
                    mapElement.style.width = "100%";
                    mapElement.style.height = "auto";
                    mapElement.style.transition = "transform 0.1s ease";
                    
                    let scale = 1, pointX = 0, pointY = 0, panning = false, startX = 0, startY = 0;
                    const setTransform = () => { mapElement.style.transform = `translate(${pointX}px, ${pointY}px) scale(${scale})`; };
                    
                    document.getElementById('zoom-in').onclick = () => { scale *= 1.3; setTransform(); };
                    document.getElementById('zoom-out').onclick = () => { scale /= 1.3; setTransform(); };
                    
                    container.onmousedown = (e) => { 
                        e.preventDefault(); panning = true; 
                        startX = e.clientX - pointX; startY = e.clientY - pointY; 
                        container.style.cursor = 'grabbing'; 
                    };
                    window.onmouseup = () => { panning = false; container.style.cursor = 'grab'; };
                    container.onmousemove = (e) => { 
                        if (!panning) return; 
                        pointX = e.clientX - startX; pointY = e.clientY - startY; 
                        setTransform(); 
                    };
                }
            });

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
                        let labelMap = { 'private': window.Lang.dash_label_private, 'shared': window.Lang.dash_label_shared, 'public': window.Lang.dash_label_public };
                        eventsDB[dateStr].push({ type: ev.visibility, title: ev.title, time: `${st} - ${et}`, label: labelMap[ev.visibility] || window.Lang.dash_label_event });
                    });
                    renderCalendar(); displayEvents(selectedDateString);
                }).catch(() => { renderCalendar(); });
            }

            function displayEvents(dateString) {
                eventsList.innerHTML = '';
                if (eventsDB[dateString] && eventsDB[dateString].length > 0) {
                    eventsDB[dateString].forEach(ev => { 
                        let bg = ev.type === 'public' ? '#f0f8ff' : (ev.type === 'shared' ? '#fff5f2' : '#f8f9fa');
                        let color = ev.type === 'public' ? '#0d6efd' : (ev.type === 'shared' ? '#A64B35' : '#adb5bd');
                        eventsList.innerHTML += `
                        <div style="background:${bg}; border-left:4px solid ${color}; padding:12px; border-radius:8px; margin-bottom:10px;">
                            <span style="background:${color}; color:white; font-size:0.7rem; padding:3px 6px; border-radius:4px; font-weight:bold; margin-right:5px;">${ev.label}</span>
                            <span style="font-size:0.9rem; font-weight:bold; color:#333;">${ev.title}</span><br>
                            <small style="color:#666; margin-top:5px; display:inline-block;">🕒 ${ev.time}</small>
                        </div>`; 
                    });
                } else { eventsList.innerHTML = `<p style="text-align:center; color:#888; padding:15px; font-style:italic; font-size:0.9rem;">${window.Lang.dash_no_events}</p>`; }
            }

            function renderCalendar() {
                const year = navDate.getFullYear(); const month = navDate.getMonth();
                monthYearText.innerText = `${monthNames[month]} ${year}`;
                daysContainer.innerHTML = '';
                ['L', 'M', 'M', 'J', 'V', 'S', 'D'].forEach(day => { daysContainer.innerHTML += `<div style="font-weight:bold; color:#888; margin-bottom:5px;">${day}</div>`; });

                let firstDayIndex = new Date(year, month, 1).getDay() - 1; if(firstDayIndex === -1) firstDayIndex = 6; 
                const daysInMonth = new Date(year, month + 1, 0).getDate();

                for(let i = 0; i < firstDayIndex; i++) daysContainer.innerHTML += `<div></div>`;

                for(let i = 1; i <= daysInMonth; i++) {
                    let dateString = `${year}-${String(month+1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
                    let isSelected = (dateString === selectedDateString);
                    let dayDiv = document.createElement('div');
                    dayDiv.style.cssText = `padding:5px 0; border-radius:6px; cursor:pointer; font-weight:${isSelected?'bold':'normal'}; background:${isSelected?'#A64B35':'transparent'}; color:${isSelected?'white':'#333'};`;
                    dayDiv.innerText = i;
                    dayDiv.onclick = () => { selectedDateString = dateString; renderCalendar(); displayEvents(selectedDateString); };
                    
                    if (eventsDB[dateString]) {
                        dayDiv.innerHTML += `<div style="width:5px; height:5px; background:${isSelected ? 'white' : '#A64B35'}; border-radius:50%; margin: 3px auto 0;"></div>`;
                    }
                    daysContainer.appendChild(dayDiv);
                }
            }

            document.getElementById('prev-month').addEventListener('click', () => { navDate.setMonth(navDate.getMonth() - 1); renderCalendar(); });
            document.getElementById('next-month').addEventListener('click', () => { navDate.setMonth(navDate.getMonth() + 1); renderCalendar(); });

            const createEventBtn = document.getElementById('btn-create-event');
            const createEventModal = document.getElementById('create-event-modal');
            const createEventForm = document.getElementById('create-event-form');
            createEventBtn?.addEventListener('click', () => {
                createEventModal.style.display = 'flex';
            });
            createEventModal?.addEventListener('click', (e) => {
                if (e.target === createEventModal) {
                    createEventModal.style.display = 'none';
                }
            });
            createEventForm?.addEventListener('submit', (e) => {
                e.preventDefault();
                const fd = new FormData(createEventForm);
                fetch('index.php?page=api_create_event', { method: 'POST', body: fd })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showToast('✅ Événement créé avec succès !');
                            createEventForm.reset();
                            document.getElementById('cover-file-name').innerText = '';
                            createEventModal.style.display = 'none';
                            fetchEvents();
                        } else {
                            showToast('⚠️ Impossible de créer l\'événement.', false);
                        }
                    })
                    .catch(() => showToast('⚠️ Erreur réseau.', false));
            });
            
            renderCalendar(); displayEvents(selectedDateString); fetchEvents();
        </script>
        <?php
        return ob_get_clean();
    }
}
?>