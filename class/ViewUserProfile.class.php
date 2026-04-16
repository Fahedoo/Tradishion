<?php

class ViewUserProfile extends ViewPrivate {
    private $targetUser;
    private $connectionStatus;
    private $userPosts;

    public function __construct($currentUser, $targetUser, $connectionStatus, $userPosts) {
        parent::__construct($currentUser); 
        $this->pageTitle = $this->t('prof_of') . " " . htmlspecialchars($targetUser['display_name']) . " - TradiShion";
        $this->targetUser = $targetUser; 
        $this->connectionStatus = $connectionStatus;
        $this->userPosts = $userPosts;
    }

    protected function getBodyContent() {
        ob_start();
        $initial = $this->getTextInitial($this->targetUser['display_name']);
        $bannerUrl = !empty($this->targetUser['banner_url']) ? htmlspecialchars($this->targetUser['banner_url']) : 'assets/images/baniere_default.jpg';
        ?>
        <div style="max-width: 1200px; margin: 30px auto; padding: 0 20px;">
            <div class="card" style="padding:0; overflow:hidden; position:relative; margin-bottom:30px;">
                
                <div style="height:200px; background:url('<?php echo $bannerUrl; ?>') center/cover;"></div>
                
                <div style="padding: 0 40px 30px; display:flex; align-items:flex-end; gap:20px;">
                    <div style="margin-top:-60px; width:140px; height:140px; background:#fff; border:5px solid white; border-radius:50%; box-shadow:0 4px 10px rgba(0,0,0,0.1); display:flex; justify-content:center; align-items:center; font-size:3.5rem; font-weight:bold; color:#A64B35; z-index:10; overflow:hidden; flex-shrink:0;">
                        <?php if (!empty($this->targetUser['avatar_url'])): ?>
                            <img src="<?php echo htmlspecialchars($this->targetUser['avatar_url']); ?>" style="width:100%; height:100%; object-fit:cover;" alt="Avatar">
                        <?php else: ?>
                            <?php echo $initial; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div style="flex:1; padding-bottom:10px;">
                        <h1 style="margin:8px 0 2px; font-size:2rem; color:#333;"><?php echo htmlspecialchars($this->targetUser['display_name']); ?></h1>
                        <?php if (!empty($this->targetUser['username'])): ?>
                            <p style="margin:0; font-size:0.92rem; color:#8a8a8a; font-weight:600;">@<?php echo htmlspecialchars(ltrim($this->targetUser['username'], '@')); ?></p>
                        <?php endif; ?>
                        <p style="color:#666; margin:5px 0;">🌍 <?php echo $this->formatOrigins($this->targetUser['origins'] ?? []); ?></p>
                    </div>
                    
                    <div style="padding-bottom:10px; display:flex; gap:10px;">
                        <?php if (!$this->connectionStatus): ?>
                            <button onclick="addFriendGlobal(<?php echo $this->targetUser['id_user']; ?>)" class="btn-submit" style="padding:10px 20px; border-radius:25px;">➕ <?php echo $this->t('btn_connect'); ?></button>
                        <?php elseif ($this->connectionStatus['status'] == 'pending'): ?>
                            <button class="btn-outline" disabled style="border-color:#ccc; color:#888; padding:10px 20px; border-radius:25px; cursor:not-allowed; background:white;">⏳ <?php echo $this->t('btn_pending'); ?></button>
                        <?php elseif ($this->connectionStatus['status'] == 'accepted'): ?>
                            <a href="index.php?page=messages&contact=<?php echo $this->targetUser['id_user']; ?>" class="btn-outline" style="border-color:#A64B35; color:#A64B35; padding:10px 20px; border-radius:25px; background:white;">💬 <?php echo $this->t('btn_message'); ?></a>
                            <button onclick="removeConnection(<?php echo $this->targetUser['id_user']; ?>)" class="btn-outline" style="border-color:#ccc; color:#888; padding:10px 20px; border-radius:25px; background:white;">💔 <?php echo $this->t('btn_remove'); ?></button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div style="display:flex; flex-direction:column; gap:24px;">
                <div class="card" style="margin-bottom:0;">
                    <h3 style="border-bottom:2px solid #FFF0ED; padding-bottom:10px; font-size:1rem; color:#333;"><?php echo $this->t('prof_about'); ?></h3>
                    <p style="color:#555; font-size:0.95rem; white-space:pre-wrap; margin-top:15px;"><?php echo htmlspecialchars($this->targetUser['bio_free'] ?? $this->t('prof_no_bio')); ?></p>
                </div>

                <div style="display:grid; grid-template-columns:minmax(340px, 1fr) minmax(520px, 1.6fr); gap:24px; align-items:stretch;">
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; align-content:start;">
                        <div class="card" style="margin-bottom:0;">
                            <h3 style="border-bottom:2px solid #FFF0ED; padding-bottom:10px; font-size:1rem; color:#333;"><?php echo $this->t('prof_contact'); ?></h3>
                            <ul style="list-style:none; padding:0; margin-top:15px; font-size:0.85rem; color:#555; display:flex; flex-direction:column; gap:12px;">
                                <li style="display:flex; gap:10px; align-items:center;">✉️ <?php echo htmlspecialchars($this->targetUser['contact_email'] ?? $this->t('prof_not_provided')); ?></li>
                                <li style="display:flex; gap:10px; align-items:center;">🌐 <?php echo htmlspecialchars($this->targetUser['website'] ?? $this->t('prof_not_provided')); ?></li>
                                <li style="display:flex; gap:10px; align-items:center;">📸 <?php echo htmlspecialchars($this->targetUser['social_link'] ?? $this->t('prof_not_provided')); ?></li>
                            </ul>
                        </div>

                        <div class="card" style="margin-bottom:0;">
                            <h3 style="border-bottom:2px solid #FFF0ED; padding-bottom:10px; font-size:1rem; color:#333;"><?php echo $this->t('prof_interests'); ?></h3>
                            <div style="display:flex; flex-wrap:wrap; gap:8px; margin-top:15px;">
                                <?php
                                $interests = array_filter(array_map('trim', explode(',', $this->targetUser['interests'] ?? '')));
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
                                $skills = array_filter(array_map('trim', explode(',', $this->targetUser['skills'] ?? '')));
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
                        <h3 style="border-bottom:2px solid #FFF0ED; padding-bottom:10px; margin-bottom:20px; color:#333;"><?php echo $this->t('prof_creations_of'); ?> <?php echo htmlspecialchars($this->targetUser['display_name']); ?></h3>
                        <?php if (empty($this->userPosts)): ?>
                            <div style="min-height:420px;"></div>
                        <?php else: ?>
                            <div id="posts-feed" style="display:flex; flex-direction:column; gap:16px;">
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
        </div>

        <script>
            function removeConnection(idContact) {
                if(confirm(window.Lang.net_remove_desc)) {
                    const formData = new FormData(); formData.append('id_contact', idContact);
                    fetch('index.php?page=api_remove_connection', { method: 'POST', body: formData })
                    .then(res => res.json()).then(data => { if(data.success) { location.reload(); } });
                }
            }

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
        <?php
        return ob_get_clean();
    }
}
?>