<?php

class ViewExplorer extends ViewPrivate {
    private $recommendations;
    private $posts;

    public function __construct($userData, $recos, $posts) {
        parent::__construct($userData);
        $this->pageTitle = $this->t('menu_explorer') . " - TradiShion";
        $this->recommendations = $recos;
        $this->posts = $posts;
    }

    protected function getBodyContent() {
        $trendScores = [];
        foreach ($this->posts as $post) {
            if (preg_match_all('/#([\p{L}\p{N}_-]{2,30})/u', (string) ($post['content'] ?? ''), $matches)) {
                foreach ($matches[1] as $tag) {
                    $key = function_exists('mb_strtolower') ? mb_strtolower($tag, 'UTF-8') : strtolower($tag);
                    if (!isset($trendScores[$key])) {
                        $trendScores[$key] = ['tag' => $tag, 'count' => 0];
                    }
                    $trendScores[$key]['count']++;
                }
            }
        }
        usort($trendScores, function($a, $b) {
            return $b['count'] <=> $a['count'];
        });
        $trendScores = array_slice($trendScores, 0, 8);

        $t_just_now = $this->t('time_just_now');
        $t_ago_prefix = $this->t('time_ago_prefix');
        $t_min = $this->t('time_ago_min');
        $t_h = $this->t('time_ago_h');
        $t_d = $this->t('time_ago_d');

        $formatRelativeDate = function($dateRaw) use ($t_just_now, $t_ago_prefix, $t_min, $t_h, $t_d) {
            $timestamp = strtotime((string) $dateRaw);
            if (!$timestamp) return '';
            $diff = time() - $timestamp;
            if ($diff < 60) return $t_just_now;
            if ($diff < 3600) return $t_ago_prefix . floor($diff / 60) . $t_min;
            if ($diff < 86400) return $t_ago_prefix . floor($diff / 3600) . $t_h;
            if ($diff < 604800) return $t_ago_prefix . floor($diff / 86400) . $t_d;
            return date('d/m/Y H:i', $timestamp);
        };

        ob_start();
        ?>
        <style> html { overflow-y: scroll; } </style>

        <div class="dash-container" style="display:grid; gap: 34px; padding-top: 24px; max-width: 1500px; margin: 0 auto;">
            
            <aside class="dash-left" style="display:none;">
                </aside>

            <main class="dash-center" style="display:flex; flex-direction:column; gap:25px; min-height: 800px;">
                
                <form action="index.php?page=explorer" method="POST" enctype="multipart/form-data" class="card" style="margin-bottom: 5px; display:flex; flex-direction:column; gap:15px; border:none; box-shadow:0 4px 20px rgba(0,0,0,0.03); border-radius:16px; padding:25px;">
                    <input type="hidden" name="action" value="new_post">
                    <div style="display:flex; gap:15px; align-items:flex-start;">
                        <div class="avatar-large" style="width:50px; height:50px; margin:0; overflow:hidden; font-size:1.2rem;">
                            <?php if (!empty($this->user['avatar_url'])): ?>
                                <img src="<?php echo htmlspecialchars($this->user['avatar_url']); ?>" style="width:100%; height:100%; object-fit:cover;">
                            <?php else: ?>
                                <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-weight:bold; background:#eee; color:#333;"><?php echo $this->getTextInitial($this->user['display_name']); ?></div>
                            <?php endif; ?>
                        </div>
                        <textarea name="content" placeholder="<?php echo $this->t('expl_whats_new'); ?>" style="flex:1; border:none; resize:none; outline:none; font-family:inherit; min-height:70px; padding:15px; background:#f4f6f8; border-radius:12px; font-size:1rem;"></textarea>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #f0f0f0; padding-top:15px;">
                        <label style="cursor:pointer; color:#666; font-size:0.95rem; font-weight:bold; display:flex; align-items:center; gap:8px;">
                            <span style="font-size:1.2rem;">🖼️</span> <?php echo $this->t('expl_add_photo'); ?>
                            <input type="file" name="post_image" style="display:none;" accept="image/*" onchange="document.getElementById('post-file-name').innerText = this.files[0].name;">
                        </label>
                        <span id="post-file-name" style="font-size:0.85rem; color:#A64B35; flex:1; margin-left:15px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"></span>
                        <button type="submit" class="btn-submit" style="padding:10px 25px; border-radius:25px; width:auto; background:#A64B35; color:white; border:none; font-weight:bold; cursor:pointer;"><?php echo $this->t('btn_publish'); ?></button>
                    </div>
                </form>

                <div id="posts-feed" style="display:flex; flex-direction:column; gap:25px;">
                    <?php if (empty($this->posts)): ?>
                        <div style="text-align:center; padding: 60px 20px; background:white; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.03);">
                            <span style="font-size:3rem; opacity:0.5; display:block; margin-bottom:15px;">🏜️</span>
                            <p style="color:#666; font-size:1.1rem; margin:0;"><?php echo $this->t('expl_empty_feed'); ?></p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($this->posts as $post): ?>
                            <div class="feed-post card" style="margin:0; padding:25px; border:none; box-shadow:0 4px 20px rgba(0,0,0,0.04); border-radius:16px;">
                                <div class="post-header" style="display:flex; align-items:center; gap:15px; margin-bottom:15px;">
                                    <a href="index.php?page=profile&id=<?php echo $post['id_author']; ?>" style="text-decoration:none;">
                                        <div class="post-avatar" style="overflow:hidden; width:50px; height:50px; border-radius:50%;">
                                            <?php if (!empty($post['avatar_url'])): ?>
                                                <img src="<?php echo htmlspecialchars($post['avatar_url']); ?>" style="width:100%; height:100%; object-fit:cover;">
                                            <?php else: ?>
                                                <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-weight:bold; background:#eee; color:#333; font-size:1.2rem;"><?php echo $this->getTextInitial($post['author']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </a>
                                    <div class="post-meta" style="display:flex; flex-direction:column; gap:2px;">
                                        <a href="index.php?page=profile&id=<?php echo $post['id_author']; ?>" style="color:#333; text-decoration:none; font-size:1.05rem;">
                                            <strong><?php echo htmlspecialchars($post['author']); ?></strong>
                                            <?php if (!empty($post['username'])): ?>
                                                <span style="font-size:0.86rem; color:#8e817b; font-weight:600; margin-left:6px;">@<?php echo htmlspecialchars(ltrim($post['username'], '@')); ?></span>
                                            <?php endif; ?>
                                        </a>
                                        <span style="color:#888; font-size:0.84rem;">🌍 <?php echo $this->formatOrigins($post['origins'] ?? ''); ?></span>
                                        <span style="color:#9a8f89; font-size:0.8rem;"><?php echo htmlspecialchars($formatRelativeDate($post['created_at'])); ?></span>
                                    </div>
                                </div>
                                
                                <p id="post-content-<?php echo $post['id_post']; ?>" style="margin-bottom:5px; color:#444; line-height:1.6; font-size:0.95rem;"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                                <div style="margin-bottom:15px; font-size:0.85rem; display:flex; align-items:center; gap:8px;">
                                    <select id="translate-lang-post-<?php echo $post['id_post']; ?>" style="border-radius:4px; border:1px solid #ddd; padding:2px; font-size:0.8rem; outline:none;">
                                        <option value="vi">Tiếng Việt</option>
                                        <option value="fr">Français</option>
                                        <option value="sq">Shqip</option>
                                        <option value="en">English</option>
                                    </select>
                                    <span style="color:#A64B35; cursor:pointer; font-weight:bold;" onclick="translatePost(<?php echo $post['id_post']; ?>)">Translate</span>
                                </div>
                                
                                <?php if (!empty($post['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($post['image_url']); ?>" style="width:100%; height:350px; object-fit:cover; border-radius:12px; margin-bottom:15px; border:1px solid #eee;">
                                <?php endif; ?>
                                
                                <div class="post-actions" style="border-top:1px solid #f0f0f0; padding-top:15px; color:#666; font-size:0.95rem; display:flex; gap:25px;">
                                    <?php 
                                        $likeIcon = $post['user_liked'] > 0 ? '❤️' : '🤍';
                                        $likeCountText = $post['likes_count'] > 0 ? $post['likes_count'] : $this->t('post_like');
                                    ?>
                                    <span style="cursor:pointer; display:flex; align-items:center; gap:8px;" onclick="toggleLike(<?php echo $post['id_post']; ?>, this)">
                                        <span class="like-icon"><?php echo $likeIcon; ?></span>
                                        <span class="like-count" style="transition:0.2s; font-weight:bold;" onmouseover="this.style.color='#A64B35'" onmouseout="this.style.color='#666'"><?php echo $likeCountText; ?></span>
                                    </span>
                                    <span style="cursor:pointer; transition: 0.2s; display:flex; align-items:center; gap:8px; font-weight:bold;" onmouseover="this.style.color='#A64B35'" onmouseout="this.style.color='#666'" onclick="toggleComments(<?php echo $post['id_post']; ?>)">
                                        <span>💬</span> <?php echo $this->t('post_comment'); ?>
                                    </span>
                                </div>

                                <div id="comments-section-<?php echo $post['id_post']; ?>" style="display:none; margin-top:20px; border-top:1px dashed #eee; padding-top:20px; background: #fafafa; padding: 20px; border-radius: 12px;">
                                    <div id="comments-list-<?php echo $post['id_post']; ?>" style="max-height:250px; overflow-y:auto; margin-bottom:15px; padding-right: 5px;"></div>
                                    <div style="display:flex; gap:10px; align-items:center; border-top: 1px solid #ddd; padding-top: 15px;">
                                        <input type="text" id="comment-input-<?php echo $post['id_post']; ?>" placeholder="<?php echo $this->t('add_comment'); ?>" style="flex:1; padding:12px 18px; border:1px solid #ddd; border-radius:25px; outline:none; font-size:0.9rem; background:white;">
                                        <button onclick="addComment(<?php echo $post['id_post']; ?>)" style="background:#FFF0ED; border:none; color:#A64B35; cursor:pointer; font-size:1.2rem; display:flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:50%; transition:0.2s;" onmouseover="this.style.background='#A64B35'; this.style.color='white';" onmouseout="this.style.background='#FFF0ED'; this.style.color='#A64B35';">➤</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </main>

            <aside class="dash-right" style="display:flex; justify-content:center; align-self:start;">
                <div class="card recommendations" style="position:sticky; top:96px; border:none; box-shadow:0 4px 20px rgba(0,0,0,0.03); border-radius:16px; padding:25px; width:100%; max-width:340px; max-height:calc(100vh - 118px); overflow-y:auto;">
                    <h4 style="color:#333; border-bottom:2px solid #FFF0ED; padding-bottom:10px; margin-bottom:20px; font-size:0.95rem; text-transform:uppercase;"><?php echo $this->t('expl_trends'); ?></h4>
                    <?php if (empty($trendScores)): ?>
                        <p style="color:#888; font-size:0.9rem; margin:0;"><?php echo $this->t('expl_no_trends'); ?></p>
                    <?php else: ?>
                        <?php foreach ($trendScores as $idx => $trend): ?>
                            <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; padding:10px 0; border-bottom:1px solid #f2edea;">
                                <div>
                                    <div style="font-size:0.75rem; color:#9a8a83;"><?php echo $this->t('expl_trend_number'); ?> <?php echo $idx + 1; ?></div>
                                    <strong style="color:#333; font-size:0.98rem;">#<?php echo htmlspecialchars($trend['tag']); ?></strong>
                                </div>
                                <span style="font-size:0.82rem; color:#A64B35; font-weight:700;"><?php echo (int) $trend['count']; ?> <?php echo $this->t('expl_posts_count'); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </aside>
        </div>

        <script>
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
                        list.innerHTML += `<div style="display:flex; gap:12px; margin-bottom:15px; align-items:flex-start;">${av}<div style="background:white; padding:10px 15px; border-radius:12px; border: 1px solid #eee; flex:1;"><strong style="font-size:0.85rem; color:#333;">${c.author}</strong><p id="comment-content-${c.id_comment}" style="margin:0; font-size:0.9rem; color:#555;">${c.content}</p><div style="margin-top:5px; display:flex; align-items:center; gap:5px;"><select id="translate-lang-comment-${c.id_comment}" style="border-radius:4px; border:1px solid #ddd; padding:1px; font-size:0.75rem; outline:none;"><option value="vi">Tiếng Việt</option><option value="fr">Français</option><option value="sq">Shqip</option><option value="en">English</option></select><span style="color:#A64B35; cursor:pointer; font-size:0.75rem; font-weight:bold;" onclick="translateComment(${c.id_comment})">Translate</span></div></div></div>`;
                    });
                });
            }

            function addComment(postId) {
                const input = document.getElementById('comment-input-' + postId);
                if(!input.value.trim()) return;
                const fd = new FormData(); fd.append('id_post', postId); fd.append('content', input.value.trim());
                fetch('index.php?page=api_add_comment', {method: 'POST', body: fd}).then(res => res.json()).then(data => {
                    if(data.success) { input.value = ''; loadComments(postId); } else { showToast(window.Lang.toast_add_error, false); }
                });
            }

            async function translatePost(postId) {
                const targetLang = document.getElementById('translate-lang-post-' + postId).value;
                const el = document.getElementById('post-content-' + postId);
                if (!el) return;
                const originalHtml = el.innerHTML;
                el.innerText = 'Translating...';
                
                const fd = new FormData();
                fd.append('text', originalHtml.replace(/<br\s*\/?>/ig, '\n'));
                fd.append('target_lang', targetLang);
                
                try {
                    const res = await fetch('index.php?page=api_translate', { method: 'POST', body: fd });
                    const data = await res.json();
                    if (data.success) {
                        el.innerHTML = data.translatedText.replace(/\n/g, '<br>');
                    } else {
                        el.innerHTML = originalHtml;
                        alert('Translation failed');
                    }
                } catch (e) {
                    el.innerHTML = originalHtml;
                }
            }

            async function translateComment(commentId) {
                const targetLang = document.getElementById('translate-lang-comment-' + commentId).value;
                const el = document.getElementById('comment-content-' + commentId);
                if (!el) return;
                const originalText = el.innerText;
                el.innerText = 'Translating...';
                
                const fd = new FormData();
                fd.append('text', originalText);
                fd.append('target_lang', targetLang);
                
                try {
                    const res = await fetch('index.php?page=api_translate', { method: 'POST', body: fd });
                    const data = await res.json();
                    if (data.success) {
                        el.innerText = data.translatedText;
                    } else {
                        el.innerText = originalText;
                    }
                } catch (e) {
                    el.innerText = originalText;
                }
            }
        </script>
        <?php
        return ob_get_clean();
    }
}
?>