<?php

class ViewProfile extends ViewPrivate {
    private $recommendations;
    private $userPosts;

    public function __construct($userData, $recos, $userPosts = []) {
        parent::__construct($userData);
        $this->pageTitle = $this->t('menu_profile') . " - TradiShion";
        $this->recommendations = $recos;
        $this->userPosts = is_array($userPosts) ? $userPosts : [];
    }

    protected function getBodyContent() {
        ob_start();
        $initial = $this->getTextInitial($this->user['display_name']);
        $bannerUrl = !empty($this->user['banner_url']) ? htmlspecialchars($this->user['banner_url']) : 'assets/images/baniere_default.jpg';
        ?>
        <div style="max-width: 1200px; margin: 30px auto; padding: 0 20px;">
            
            <div class="card" style="padding:0; overflow:hidden; position:relative; margin-bottom:30px;">
                <div style="height:200px; background:url('<?php echo $bannerUrl; ?>') center/cover;"></div>
                <div style="padding: 0 40px 30px; display:flex; align-items:flex-end; gap:20px;">
                    <div style="margin-top:-60px; width:140px; height:140px; background:#fff; border:5px solid white; border-radius:50%; box-shadow:0 4px 10px rgba(0,0,0,0.1); display:flex; justify-content:center; align-items:center; font-size:3.5rem; font-weight:bold; color:#A64B35; z-index:10; overflow:hidden; flex-shrink:0;">
                        <?php if (!empty($this->user['avatar_url'])): ?>
                            <img src="<?php echo htmlspecialchars($this->user['avatar_url']); ?>" style="width:100%; height:100%; object-fit:cover;" alt="Avatar">
                        <?php else: ?>
                            <?php echo $initial; ?>
                        <?php endif; ?>
                    </div>
                    <div style="flex:1; padding-bottom:10px;">
                        <h1 style="margin:8px 0 2px; font-size:2rem; color:#333;"><?php echo htmlspecialchars($this->user['display_name']); ?></h1>
                        <?php if (!empty($this->user['username'])): ?>
                            <p style="margin:0; font-size:0.92rem; color:#8a8a8a; font-weight:600;">@<?php echo htmlspecialchars(ltrim($this->user['username'], '@')); ?></p>
                        <?php endif; ?>
                        
                        <p style="color:#666; margin:5px 0;">🌍 <?php echo $this->formatOrigins($this->user['origins'] ?? []); ?></p>

                    </div>
                    <div style="padding-bottom:10px;">
                        <button onclick="document.getElementById('edit-profile-form').style.display='flex';" class="btn-outline" style="border-color:#ccc; background:white; padding:10px 20px; border-radius:25px; cursor:pointer;">
                            ✏️ <?php echo $this->t('prof_edit'); ?>
                        </button>
                    </div>
                </div>
            </div>

            <div style="display:flex; flex-direction:column; gap:24px;">
                <div class="card" style="margin-bottom:0;">
                    <h3 style="border-bottom:2px solid #FFF0ED; padding-bottom:10px; font-size:1rem; color:#333;"><?php echo $this->t('prof_about'); ?></h3>
                    <p style="color:#555; font-size:0.95rem; white-space:pre-wrap; margin-top:15px;"><?php echo htmlspecialchars($this->user['bio_free'] ?? $this->t('prof_no_bio')); ?></p>
                </div>

                <div style="display:grid; grid-template-columns:minmax(340px, 1fr) minmax(520px, 1.6fr); gap:24px; align-items:stretch;">
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; align-content:start;">
                        <div class="card" style="margin-bottom:0;">
                            <h3 style="border-bottom:2px solid #FFF0ED; padding-bottom:10px; font-size:1rem; color:#333;"><?php echo $this->t('prof_contact'); ?></h3>
                            <ul style="list-style:none; padding:0; margin-top:15px; font-size:0.85rem; color:#555; display:flex; flex-direction:column; gap:12px;">
                                <li style="display:flex; gap:10px; align-items:center;">✉️ <?php echo htmlspecialchars($this->user['contact_email'] ?? $this->t('prof_not_provided')); ?></li>
                                <li style="display:flex; gap:10px; align-items:center;">🌐 <?php echo htmlspecialchars($this->user['website'] ?? $this->t('prof_not_provided')); ?></li>
                                <li style="display:flex; gap:10px; align-items:center;">📸 <?php echo htmlspecialchars($this->user['social_link'] ?? $this->t('prof_not_provided')); ?></li>
                            </ul>
                        </div>

                        <div class="card" style="margin-bottom:0;">
                            <h3 style="border-bottom:2px solid #FFF0ED; padding-bottom:10px; font-size:1rem; color:#333;"><?php echo $this->t('prof_interests'); ?></h3>
                            <div style="display:flex; flex-wrap:wrap; gap:8px; margin-top:15px;">
                                <?php
                                $interests = array_filter(array_map('trim', explode(',', $this->user['interests'] ?? '')));
                                if(empty($interests)): ?>
                                    <p style="color:#888; font-size:0.85rem;"><?php echo $this->t('prof_no_interests'); ?></p>
                                <?php else:
                                    foreach($interests as $int): ?>
                                        <span style="background:#f4f6f8; padding:6px 12px; border-radius:20px; font-size:0.8rem; color:#555; font-weight:bold;">#<?php echo htmlspecialchars($int); ?></span>
                                    <?php endforeach;
                                endif; ?>
                            </div>
                        </div>

                        <div class="card" style="margin-bottom:0; grid-column:1 / span 2;">
                            <h3 style="border-bottom:2px solid #FFF0ED; padding-bottom:10px; font-size:1rem; color:#333;"><?php echo $this->t('prof_skills'); ?></h3>
                            <div style="display:flex; flex-direction:column; gap:12px; margin-top:15px;">
                                <?php
                                $skills = array_filter(array_map('trim', explode(',', $this->user['skills'] ?? '')));
                                if(empty($skills)): ?>
                                    <p style="color:#888; font-size:0.85rem;"><?php echo $this->t('prof_no_skills'); ?></p>
                                <?php else:
                                    foreach($skills as $skill): ?>
                                        <div>
                                            <div style="display:flex; justify-content:space-between; font-size:0.85rem; color:#555; margin-bottom:5px;">
                                                <span><?php echo htmlspecialchars($skill); ?></span>
                                                <strong style="color:#A64B35;"><?php echo $this->t('prof_advanced'); ?></strong>
                                            </div>
                                            <div style="height:6px; background:#eee; border-radius:3px;">
                                                <div style="height:100%; width:80%; background:#A64B35; border-radius:3px;"></div>
                                            </div>
                                        </div>
                                    <?php endforeach;
                                endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="card" style="margin-bottom:0; min-height:100%;">
                        <h3 style="border-bottom:2px solid #FFF0ED; padding-bottom:10px; margin-bottom:20px; color:#333;"><?php echo $this->t('prof_creations'); ?></h3>
                        <?php if (empty($this->userPosts)): ?>
                            <div style="min-height:420px; color:#888; text-align:center; padding-top:50px;"><?php echo $this->t('prof_no_creations'); ?></div>
                        <?php else: ?>
                            <div id="profile-posts-feed" style="display:flex; flex-direction:column; gap:16px;">
                                <?php foreach ($this->userPosts as $post): ?>
                                    <div class="feed-post" style="margin:0; padding:20px; border:1px solid #eee; border-radius:14px;">
                                        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px;">
                                            <div>
                                                <strong style="color:#333;"><?php echo htmlspecialchars($post['author']); ?></strong>
                                                <?php if (!empty($post['username'])): ?>
                                                    <span style="font-size:0.84rem; color:#8f827c; margin-left:6px;">@<?php echo htmlspecialchars(ltrim($post['username'], '@')); ?></span>
                                                <?php endif; ?>
                                                <div style="font-size:0.8rem; color:#8a8a8a;">🌍 <?php echo $this->formatOrigins($post['origins'] ?? ''); ?></div>
                                            </div>
                                            <small style="color:#aaa;"><?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?></small>
                                        </div>
                                        <p style="margin:0 0 10px; color:#444; line-height:1.5;"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                                        <?php if (!empty($post['image_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($post['image_url']); ?>" style="width:100%; height:320px; object-fit:cover; border-radius:10px; border:1px solid #eee; margin-bottom:12px;">
                                        <?php endif; ?>
                                        <?php
                                            $likeIcon = (int)$post['user_liked'] > 0 ? '❤️' : '🤍';
                                            $likeCountText = (int)$post['likes_count'] > 0 ? (int)$post['likes_count'] : $this->t('post_like');
                                        ?>
                                        <div class="post-actions" style="border-top:1px solid #f0f0f0; padding-top:12px; display:flex; gap:20px; color:#666;">
                                            <span style="cursor:pointer; display:flex; align-items:center; gap:8px;" onclick="toggleLike(<?php echo (int)$post['id_post']; ?>, this)">
                                                <span class="like-icon"><?php echo $likeIcon; ?></span>
                                                <span class="like-count" style="font-weight:700;"><?php echo $likeCountText; ?></span>
                                            </span>
                                            <span style="cursor:pointer; display:flex; align-items:center; gap:8px; font-weight:700;" onclick="toggleComments(<?php echo (int)$post['id_post']; ?>)">💬 <?php echo $this->t('post_comment'); ?></span>
                                        </div>
                                        <div id="comments-section-<?php echo (int)$post['id_post']; ?>" style="display:none; margin-top:16px; border-top:1px dashed #eee; padding-top:14px; background:#fafafa; border-radius:10px; padding:14px;">
                                            <div id="comments-list-<?php echo (int)$post['id_post']; ?>" style="max-height:250px; overflow-y:auto; margin-bottom:12px;"></div>
                                            <div style="display:flex; gap:8px; align-items:center;">
                                                <input type="text" id="comment-input-<?php echo (int)$post['id_post']; ?>" placeholder="<?php echo $this->t('add_comment'); ?>" style="flex:1; padding:10px 14px; border:1px solid #ddd; border-radius:20px; outline:none;">
                                                <button onclick="addComment(<?php echo (int)$post['id_post']; ?>)" style="background:#FFF0ED; border:none; color:#A64B35; width:36px; height:36px; border-radius:50%; cursor:pointer;">➤</button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <script>
                function toggleLike(postId, element) {
                    const fd = new FormData();
                    fd.append('id_post', postId);
                    fetch('index.php?page=api_like_post', { method: 'POST', body: fd })
                    .then(res => res.json()).then(data => {
                        const iconSpan = element.querySelector('.like-icon');
                        const countSpan = element.querySelector('.like-count');
                        const currentCount = parseInt(countSpan.innerText) || 0;
                        if (data.status === 'liked') {
                            iconSpan.innerText = '❤️';
                            countSpan.innerText = currentCount + 1;
                        } else if (data.status === 'unliked') {
                            iconSpan.innerText = '🤍';
                            const next = currentCount - 1;
                            countSpan.innerText = next > 0 ? next : window.Lang.post_like;
                        }
                    });
                }

                function toggleComments(postId) {
                    const section = document.getElementById('comments-section-' + postId);
                    if (section.style.display === 'none') {
                        section.style.display = 'block';
                        loadComments(postId);
                    } else {
                        section.style.display = 'none';
                    }
                }

                function loadComments(postId) {
                    const list = document.getElementById('comments-list-' + postId);
                    list.innerHTML = `<p style="font-size:0.85rem; color:#888; text-align:center;">${window.Lang.loading}</p>`;
                    fetch('index.php?page=api_get_comments&id_post=' + postId)
                    .then(res => res.json()).then(data => {
                        list.innerHTML = '';
                        if (!data || data.length === 0) {
                            list.innerHTML = '<p style="font-size:0.85rem; color:#888; text-align:center;">Aucun commentaire.</p>';
                            return;
                        }
                        data.forEach(c => {
                            const av = c.avatar_url ? `<img src="${c.avatar_url}" style="width:30px; height:30px; border-radius:50%; object-fit:cover;">` : `<div style="width:30px; height:30px; border-radius:50%; background:#eee; display:flex; align-items:center; justify-content:center; font-weight:bold; color:#333;">${Array.from((c.author || '').trim())[0] || '?'}</div>`;
                            list.innerHTML += `<div style="display:flex; gap:10px; margin-bottom:12px;">${av}<div style="background:white; padding:8px 11px; border-radius:10px; border:1px solid #eee; flex:1;"><strong style="font-size:0.82rem; color:#333;">${c.author}</strong><p style="margin:0; color:#555;">${c.content}</p></div></div>`;
                        });
                    });
                }

                function addComment(postId) {
                    const input = document.getElementById('comment-input-' + postId);
                    if (!input || !input.value.trim()) return;
                    const fd = new FormData();
                    fd.append('id_post', postId);
                    fd.append('content', input.value.trim());
                    fetch('index.php?page=api_add_comment', { method: 'POST', body: fd })
                    .then(res => res.json()).then(data => {
                        if (data.success) {
                            input.value = '';
                            loadComments(postId);
                        } else {
                            showToast("Erreur d'ajout.", false);
                        }
                    });
                }
            </script>
        </div>

        <div id="edit-profile-form" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center; padding: 20px 0;">
            <div class="card" style="width:100%; max-width:600px; padding:30px; background:white; border-radius:12px; max-height:90vh; overflow-y:auto; margin:auto;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <h3 style="margin:0; color:#333; font-family: 'Segoe UI', sans-serif; font-size: 1.3rem;"><?php echo $this->t('prof_edit_title'); ?></h3>
                    <button onclick="document.getElementById('edit-profile-form').style.display='none';" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:#888;">✕</button>
                </div>
                
                <form action="index.php?page=profile" method="POST" enctype="multipart/form-data" style="display:flex; flex-direction:column; gap:15px;">
                    <input type="hidden" name="update_profile" value="1">
                    <input type="hidden" name="avatar_crop_ready" id="avatar_crop_ready" value="0">
                    <input type="hidden" name="avatar_crop_x" id="avatar_crop_x" value="0">
                    <input type="hidden" name="avatar_crop_y" id="avatar_crop_y" value="0">
                    <input type="hidden" name="avatar_crop_w" id="avatar_crop_w" value="0">
                    <input type="hidden" name="avatar_crop_h" id="avatar_crop_h" value="0">
                    <input type="hidden" name="banner_crop_ready" id="banner_crop_ready" value="0">
                    <input type="hidden" name="banner_crop_x" id="banner_crop_x" value="0">
                    <input type="hidden" name="banner_crop_y" id="banner_crop_y" value="0">
                    <input type="hidden" name="banner_crop_w" id="banner_crop_w" value="0">
                    <input type="hidden" name="banner_crop_h" id="banner_crop_h" value="0">
                    
                    <div style="display:flex; gap:15px;">
                        <div style="flex:1;">
                            <label style="font-size:0.85rem; font-weight:bold; color:#555;"><?php echo $this->t('prof_avatar'); ?></label>
                            <input type="file" id="avatar-input" name="avatar" accept="image/*" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px; margin-top:5px; background:#fafafa;">
                            <small id="avatar-crop-status" style="display:block; margin-top:6px; color:#888;"><?php echo $this->t('prof_crop_auto'); ?></small>
                        </div>
                        <div style="flex:1;">
                            <label style="font-size:0.85rem; font-weight:bold; color:#555;"><?php echo $this->t('prof_banner'); ?></label>
                            <input type="file" id="banner-input" name="banner" accept="image/*" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px; margin-top:5px; background:#fafafa;">
                            <small id="banner-crop-status" style="display:block; margin-top:6px; color:#888;"><?php echo $this->t('prof_crop_auto'); ?></small>
                        </div>
                    </div>

                    <div style="display:flex; gap:15px;">
                        <div style="flex:1;">
                            <label style="font-size:0.85rem; font-weight:bold; color:#555;"><?php echo $this->t('prof_display_name'); ?></label>
                            <input type="text" name="display_name" value="<?php echo htmlspecialchars($this->user['display_name']); ?>" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; margin-top:5px; outline:none;">
                        </div>
                        <div style="flex:1;">
                            <label style="font-size:0.85rem; font-weight:bold; color:#555;"><?php echo $this->t('prof_about_me'); ?></label>
                            <textarea name="bio_free" rows="2" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; margin-top:5px; resize:none; outline:none; font-family:inherit;"><?php echo htmlspecialchars($this->user['bio_free'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <div class="origin-picker" data-max="4">
                        <label style="font-size:0.85rem; font-weight:bold; color:#555;"><?php echo $this->t('prof_origins'); ?></label>
                        <div class="origin-picker-chips"></div>
                        <input type="text" id="origin-search" class="origin-picker-search" placeholder="<?php echo $this->t('prof_search_country'); ?>" style="padding:8px; border:1px solid #ddd; border-radius:6px; width:100%; outline:none;">
                        <select name="origins[]" id="origins" multiple class="origin-picker-select" style="display:none;">
                            <?php
                            $countriesJson = @file_get_contents('data/countries.json');
                            if($countriesJson) {
                                $countriesArr = json_decode($countriesJson, true);
                                foreach ($countriesArr as $code => $name) {
                                    $selected = in_array($code, $this->user['origins'] ?? []) ? 'selected' : '';
                                    echo "<option value=\"" . htmlspecialchars($code) . "\" $selected>" . htmlspecialchars($name) . "</option>";
                                }
                            }
                            ?>
                        </select>
                        <div class="origin-picker-list">
                            <?php
                            if($countriesJson) {
                                foreach ($countriesArr as $code => $name) {
                                    $active = in_array($code, $this->user['origins'] ?? []) ? 'active' : '';
                                    echo '<button type="button" class="origin-picker-option ' . $active . '" data-value="' . htmlspecialchars($code) . '">' . $this->getCountryFlag($code) . ' ' . htmlspecialchars($name) . '</button>';
                                }
                            }
                            ?>
                        </div>
                        <div class="origin-picker-status"></div>
                    </div>

                    <div style="display:flex; gap:15px;">
                        <div style="flex:1;">
                            <label style="font-size:0.85rem; font-weight:bold; color:#555;"><?php echo $this->t('prof_skills_label'); ?></label>
                            <input type="text" name="skills" value="<?php echo htmlspecialchars($this->user['skills'] ?? ''); ?>" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; margin-top:5px; outline:none;">
                        </div>
                        <div style="flex:1;">
                            <label style="font-size:0.85rem; font-weight:bold; color:#555;"><?php echo $this->t('prof_interests_label'); ?></label>
                            <input type="text" name="interests" value="<?php echo htmlspecialchars($this->user['interests'] ?? ''); ?>" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; margin-top:5px; outline:none;">
                        </div>
                    </div>

                    <div style="display:flex; gap:15px;">
                        <div style="flex:1;">
                            <label style="font-size:0.85rem; font-weight:bold; color:#555;"><?php echo $this->t('prof_pro_email'); ?></label>
                            <input type="email" name="contact_email" value="<?php echo htmlspecialchars($this->user['contact_email'] ?? ''); ?>" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; margin-top:5px; outline:none;">
                        </div>
                        <div style="flex:1;">
                            <label style="font-size:0.85rem; font-weight:bold; color:#555;"><?php echo $this->t('prof_social'); ?></label>
                            <input type="text" name="social_link" value="<?php echo htmlspecialchars($this->user['social_link'] ?? ''); ?>" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; margin-top:5px; outline:none;">
                        </div>
                    </div>
                    <div>
                        <label style="font-size:0.85rem; font-weight:bold; color:#555;"><?php echo $this->t('prof_website'); ?></label>
                        <input type="text" name="website" value="<?php echo htmlspecialchars($this->user['website'] ?? ''); ?>" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; margin-top:5px; outline:none;">
                    </div>

                    <button type="submit" class="btn-submit" style="width:100%; padding:12px; border-radius:8px; font-size:1rem; margin-top:15px;"><?php echo $this->t('btn_save_changes'); ?></button>
                </form>
            </div>
        </div>

        <div id="crop-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.72); z-index:2000; align-items:center; justify-content:center; padding:20px;">
            <div style="width:min(92vw, 860px); background:#fff; border-radius:16px; padding:20px; box-shadow:0 16px 40px rgba(0,0,0,0.28);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                    <h3 id="crop-title" style="margin:0; color:#333; font-size:1.1rem;"><?php echo $this->t('crop_title'); ?></h3>
                    <button type="button" id="crop-cancel" style="border:none; background:#f1f1f1; width:34px; height:34px; border-radius:50%; cursor:pointer; font-size:1.05rem;">✕</button>
                </div>

                <div style="background:#f7f7f7; border-radius:12px; padding:16px; border:1px solid #e9e9e9;">
                    <div id="crop-viewport" style="width:100%; max-width:640px; margin:0 auto; position:relative; overflow:hidden; background:#101010; user-select:none; touch-action:none; border-radius:10px;">
                        <img id="crop-image" alt="Aperçu recadrage" style="position:absolute; left:0; top:0; transform-origin:top left; cursor:grab;">
                        <div id="crop-overlay" style="position:absolute; inset:0; pointer-events:none; box-sizing:border-box;"></div>
                    </div>
                </div>

                <div style="display:flex; align-items:center; gap:12px; margin:16px 0 8px;">
                    <label for="crop-zoom" style="font-size:0.9rem; font-weight:bold; color:#555; min-width:50px;"><?php echo $this->t('crop_zoom'); ?></label>
                    <input id="crop-zoom" type="range" min="1" max="3" step="0.01" value="1" style="flex:1;">
                    <span id="crop-zoom-value" style="font-size:0.9rem; color:#666; min-width:52px; text-align:right;">100%</span>
                </div>

                <p style="margin:0; color:#777; font-size:0.86rem;"><?php echo $this->t('crop_desc'); ?></p>

                <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:18px;">
                    <button type="button" id="crop-skip" class="btn-outline" style="padding:10px 14px;"><?php echo $this->t('btn_skip'); ?></button>
                    <button type="button" id="crop-apply" class="btn-submit" style="padding:10px 18px;"><?php echo $this->t('btn_apply_crop'); ?></button>
                </div>
            </div>
        </div>

        <script>
        (function() {
            const modal = document.getElementById('crop-modal');
            const titleEl = document.getElementById('crop-title');
            const viewport = document.getElementById('crop-viewport');
            const cropImage = document.getElementById('crop-image');
            const overlay = document.getElementById('crop-overlay');
            const zoomInput = document.getElementById('crop-zoom');
            const zoomValue = document.getElementById('crop-zoom-value');
            const btnCancel = document.getElementById('crop-cancel');
            const btnSkip = document.getElementById('crop-skip');
            const btnApply = document.getElementById('crop-apply');

            const uploadConfigs = {
                avatar: {
                    label: 'photo de profil',
                    input: document.getElementById('avatar-input'),
                    status: document.getElementById('avatar-crop-status'),
                    ready: document.getElementById('avatar_crop_ready'),
                    x: document.getElementById('avatar_crop_x'),
                    y: document.getElementById('avatar_crop_y'),
                    w: document.getElementById('avatar_crop_w'),
                    h: document.getElementById('avatar_crop_h'),
                    viewportWidth: 320,
                    viewportHeight: 320,
                    shape: 'circle'
                },
                banner: {
                    label: 'banniere',
                    input: document.getElementById('banner-input'),
                    status: document.getElementById('banner-crop-status'),
                    ready: document.getElementById('banner_crop_ready'),
                    x: document.getElementById('banner_crop_x'),
                    y: document.getElementById('banner_crop_y'),
                    w: document.getElementById('banner_crop_w'),
                    h: document.getElementById('banner_crop_h'),
                    viewportWidth: 640,
                    viewportHeight: 240,
                    shape: 'rect'
                }
            };

            const cropState = { activeKey: null, objectUrl: null, naturalWidth: 0, naturalHeight: 0, baseScale: 1, scale: 1, offsetX: 0, offsetY: 0, dragging: false, dragStartX: 0, dragStartY: 0, dragBaseX: 0, dragBaseY: 0 };

            function activeConfig() { return uploadConfigs[cropState.activeKey] || null; }

            function clampOffsets() {
                const config = activeConfig();
                if (!config) return;
                const renderedW = cropState.naturalWidth * cropState.scale;
                const renderedH = cropState.naturalHeight * cropState.scale;
                const minX = config.viewportWidth - renderedW;
                const minY = config.viewportHeight - renderedH;
                cropState.offsetX = Math.min(0, Math.max(minX, cropState.offsetX));
                cropState.offsetY = Math.min(0, Math.max(minY, cropState.offsetY));
            }

            function draw() {
                const config = activeConfig();
                if (!config) return;
                cropImage.style.width = (cropState.naturalWidth * cropState.scale) + 'px';
                cropImage.style.height = (cropState.naturalHeight * cropState.scale) + 'px';
                cropImage.style.transform = 'translate(' + cropState.offsetX + 'px,' + cropState.offsetY + 'px)';
                zoomValue.textContent = Math.round((cropState.scale / cropState.baseScale) * 100) + '%';
            }

            function computeAndStoreCrop() {
                const config = activeConfig();
                if (!config) return;
                const x = Math.max(0, Math.round((-cropState.offsetX) / cropState.scale));
                const y = Math.max(0, Math.round((-cropState.offsetY) / cropState.scale));
                const w = Math.max(1, Math.round(config.viewportWidth / cropState.scale));
                const h = Math.max(1, Math.round(config.viewportHeight / cropState.scale));
                config.x.value = String(x); config.y.value = String(y); config.w.value = String(Math.min(w, cropState.naturalWidth - x)); config.h.value = String(Math.min(h, cropState.naturalHeight - y));
                config.ready.value = '1'; config.status.textContent = 'Recadrage enregistre.'; config.status.style.color = '#2d7a43';
            }

            function clearCrop(config) {
                config.ready.value = '0'; config.x.value = '0'; config.y.value = '0'; config.w.value = '0'; config.h.value = '0';
                config.status.textContent = window.Lang.prof_crop_auto; config.status.style.color = '#888';
            }

            function closeModal() {
                modal.style.display = 'none'; document.body.style.overflow = ''; cropState.dragging = false;
                if (cropState.objectUrl) { URL.revokeObjectURL(cropState.objectUrl); }
                cropState.objectUrl = null; cropImage.removeAttribute('src');
            }

            function openCropper(type, file) {
                const config = uploadConfigs[type];
                if (!config || !file) return;
                clearCrop(config); cropState.activeKey = type;
                viewport.style.width = config.viewportWidth + 'px'; viewport.style.height = config.viewportHeight + 'px';
                if (config.shape === 'circle') { overlay.style.border = '0'; overlay.style.boxShadow = 'inset 0 0 0 2px rgba(255,255,255,0.85)'; overlay.style.borderRadius = '50%'; } 
                else { overlay.style.border = '0'; overlay.style.boxShadow = 'inset 0 0 0 2px rgba(255,255,255,0.88)'; overlay.style.borderRadius = '10px'; }
                cropState.objectUrl = URL.createObjectURL(file);
                cropImage.onload = function() {
                    cropState.naturalWidth = cropImage.naturalWidth; cropState.naturalHeight = cropImage.naturalHeight;
                    cropState.baseScale = Math.max(config.viewportWidth / cropState.naturalWidth, config.viewportHeight / cropState.naturalHeight);
                    cropState.scale = cropState.baseScale;
                    cropState.offsetX = (config.viewportWidth - cropState.naturalWidth * cropState.scale) / 2;
                    cropState.offsetY = (config.viewportHeight - cropState.naturalHeight * cropState.scale) / 2;
                    zoomInput.value = '1'; clampOffsets(); draw();
                };
                cropImage.src = cropState.objectUrl;
                modal.style.display = 'flex'; document.body.style.overflow = 'hidden';
            }

            Object.keys(uploadConfigs).forEach(function(key) {
                const config = uploadConfigs[key];
                if (!config.input) return;
                config.input.addEventListener('change', function() {
                    if (!this.files || !this.files[0]) { clearCrop(config); return; }
                    openCropper(key, this.files[0]);
                });
            });

            zoomInput.addEventListener('input', function() {
                const config = activeConfig();
                if (!config) return;
                const oldScale = cropState.scale; const nextScale = cropState.baseScale * parseFloat(zoomInput.value);
                const centerX = (config.viewportWidth / 2 - cropState.offsetX) / oldScale; const centerY = (config.viewportHeight / 2 - cropState.offsetY) / oldScale;
                cropState.scale = nextScale; cropState.offsetX = config.viewportWidth / 2 - centerX * nextScale; cropState.offsetY = config.viewportHeight / 2 - centerY * nextScale;
                clampOffsets(); draw();
            });

            function startDrag(clientX, clientY) { cropState.dragging = true; cropState.dragStartX = clientX; cropState.dragStartY = clientY; cropState.dragBaseX = cropState.offsetX; cropState.dragBaseY = cropState.offsetY; cropImage.style.cursor = 'grabbing'; }
            function moveDrag(clientX, clientY) { if (!cropState.dragging) return; cropState.offsetX = cropState.dragBaseX + (clientX - cropState.dragStartX); cropState.offsetY = cropState.dragBaseY + (clientY - cropState.dragStartY); clampOffsets(); draw(); }
            function endDrag() { cropState.dragging = false; cropImage.style.cursor = 'grab'; }

            viewport.addEventListener('mousedown', function(e) { e.preventDefault(); startDrag(e.clientX, e.clientY); });
            window.addEventListener('mousemove', function(e) { moveDrag(e.clientX, e.clientY); });
            window.addEventListener('mouseup', function() { endDrag(); });
            viewport.addEventListener('touchstart', function(e) { if (!e.touches || !e.touches[0]) return; startDrag(e.touches[0].clientX, e.touches[0].clientY); }, { passive: true });
            window.addEventListener('touchmove', function(e) { if (!cropState.dragging || !e.touches || !e.touches[0]) return; moveDrag(e.touches[0].clientX, e.touches[0].clientY); }, { passive: true });
            window.addEventListener('touchend', function() { endDrag(); }, { passive: true });

            btnApply.addEventListener('click', function() { computeAndStoreCrop(); closeModal(); });
            btnSkip.addEventListener('click', function() { const config = activeConfig(); if (config) { clearCrop(config); } closeModal(); });
            btnCancel.addEventListener('click', function() { const config = activeConfig(); if (config) { clearCrop(config); } closeModal(); });
            modal.addEventListener('click', function(e) { if (e.target === modal) { const config = activeConfig(); if (config) { clearCrop(config); } closeModal(); } });
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}
?>