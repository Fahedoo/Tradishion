<?php

class ViewProfile extends ViewPrivate {
    private $recommendations;

    public function __construct($userData, $recos) {
        parent::__construct($userData);
        $this->pageTitle = "Mon Profil - TradiShion";
        $this->recommendations = $recos;
    }

    protected function getBodyContent() {
        ob_start();
        $initial = strtoupper(substr($this->user['display_name'], 0, 1));
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
                        <h1 style="margin:0; font-size:2rem; color:#333;"><?php echo htmlspecialchars($this->user['display_name']); ?></h1>
                        <p style="color:#666; margin:5px 0;">📍 <?php echo htmlspecialchars($this->user['location'] ?? 'Localisation non renseignée'); ?></p>
                    </div>
                    <div style="padding-bottom:10px;">
                        <button onclick="document.getElementById('edit-profile-form').style.display='flex';" class="btn-outline" style="border-color:#ccc; background:white; padding:10px 20px; border-radius:25px; cursor:pointer;">
                            ✏️ Modifier le profil
                        </button>
                    </div>
                </div>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 2fr; gap:30px;">
                
                <aside style="display:flex; flex-direction:column; gap:20px;">
                    <div class="card nav-menu" style="margin-bottom:0;">
                       <ul style="list-style: none; padding: 0; margin: 0;">
    <li><a href="index.php?page=dashboard">🏠 Accueil</a></li>
    <li><a href="index.php?page=explorer">🧭 Explorateur</a></li>
    <li><a href="index.php?page=events">📅 Événements</a></li>
    <li><a href="index.php?page=messages">💬 Mes Messages</a></li>
    <li><a href="index.php?page=network">👥 Mon Réseau</a></li>
</ul>
                    </div>

                    <div class="card" style="margin-bottom:0;">
                        <h3 style="border-bottom:2px solid #FFF0ED; padding-bottom:10px; font-size:1rem; color:#333;">À propos</h3>
                        <p style="color:#555; font-size:0.95rem; white-space:pre-wrap; margin-top:15px;"><?php echo htmlspecialchars($this->user['bio_free'] ?? 'Aucune biographie pour le moment.'); ?></p>
                    </div>

                    <div class="card" style="margin-bottom:0;">
                        <h3 style="border-bottom:2px solid #FFF0ED; padding-bottom:10px; font-size:1rem; color:#333;">Maîtrise de techniques</h3>
                        <div style="display:flex; flex-direction:column; gap:12px; margin-top:15px;">
                            <?php 
                            $skills = array_filter(array_map('trim', explode(',', $this->user['skills'] ?? '')));
                            if(empty($skills)): ?>
                                <p style="color:#888; font-size:0.85rem;">Aucune technique renseignée.</p>
                            <?php else: 
                                foreach($skills as $skill): ?>
                                    <div>
                                        <div style="display:flex; justify-content:space-between; font-size:0.85rem; color:#555; margin-bottom:5px;">
                                            <span><?php echo htmlspecialchars($skill); ?></span>
                                            <strong style="color:#A64B35;">Avancé</strong>
                                        </div>
                                        <div style="height:6px; background:#eee; border-radius:3px;">
                                            <div style="height:100%; width:80%; background:#A64B35; border-radius:3px;"></div>
                                        </div>
                                    </div>
                                <?php endforeach; 
                            endif; ?>
                        </div>
                    </div>
                </aside>

                <main style="display:flex; flex-direction:column; gap:30px;">
                    <div class="card" style="margin-bottom:0;">
                        <h3 style="border-bottom:2px solid #FFF0ED; padding-bottom:10px; color:#333;">Mes Créations</h3>
                        <p style="color:#888; text-align:center; padding:40px;">Vos photos apparaîtront ici.</p>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:30px;">
                        
                        <div class="card" style="margin-bottom:0;">
                            <h3 style="border-bottom:2px solid #FFF0ED; padding-bottom:10px; font-size:1rem; color:#333;">Centres d'intérêt</h3>
                            <div style="display:flex; flex-wrap:wrap; gap:8px; margin-top:15px;">
                                <?php 
                                $interests = array_filter(array_map('trim', explode(',', $this->user['interests'] ?? '')));
                                if(empty($interests)): ?>
                                    <p style="color:#888; font-size:0.85rem;">Aucun centre d'intérêt.</p>
                                <?php else: 
                                    foreach($interests as $int): ?>
                                        <span style="background:#f4f6f8; padding:6px 12px; border-radius:20px; font-size:0.8rem; color:#555; font-weight:bold;">#<?php echo htmlspecialchars($int); ?></span>
                                    <?php endforeach; 
                                endif; ?>
                            </div>
                        </div>

                        <div class="card" style="margin-bottom:0;">
                            <h3 style="border-bottom:2px solid #FFF0ED; padding-bottom:10px; font-size:1rem; color:#333;">Coordonnées</h3>
                            <ul style="list-style:none; padding:0; margin-top:15px; font-size:0.85rem; color:#555; display:flex; flex-direction:column; gap:12px;">
                                <li style="display:flex; gap:10px; align-items:center;">✉️ <?php echo htmlspecialchars($this->user['contact_email'] ?? 'Non renseigné'); ?></li>
                                <li style="display:flex; gap:10px; align-items:center;">🌐 <?php echo htmlspecialchars($this->user['website'] ?? 'Non renseigné'); ?></li>
                                <li style="display:flex; gap:10px; align-items:center;">📸 <?php echo htmlspecialchars($this->user['social_link'] ?? 'Non renseigné'); ?></li>
                            </ul>
                        </div>
                        
                    </div>

                    <div class="card recommendations" style="margin-bottom:0;">
                        <h4 style="color:#333;">SUGGESTIONS D'ARTISANS</h4>
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
                                <button onclick="addFriendGlobal(<?php echo $rec['id_user']; ?>)" class="btn-outline" style="border-color:#333; color:#333; border-radius:20px; padding:5px 15px; font-size:0.75rem; cursor:pointer; font-weight:bold;">Suivre</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </main>
            </div>
        </div>

        <div id="edit-profile-form" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center; padding: 20px 0;">
            <div class="card" style="width:100%; max-width:600px; padding:30px; background:white; border-radius:12px; max-height:90vh; overflow-y:auto; margin:auto;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <h3 style="margin:0; color:#333; font-family: 'Segoe UI', sans-serif; font-size: 1.3rem;">Modifier mes informations</h3>
                    <button onclick="document.getElementById('edit-profile-form').style.display='none';" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:#888;">✕</button>
                </div>
                
                <form action="index.php?page=profile" method="POST" enctype="multipart/form-data" style="display:flex; flex-direction:column; gap:15px;">
                    <input type="hidden" name="update_profile" value="1">
                    
                    <div style="display:flex; gap:15px;">
                        <div style="flex:1;">
                            <label style="font-size:0.85rem; font-weight:bold; color:#555;">Photo de profil</label>
                            <input type="file" name="avatar" accept="image/*" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px; margin-top:5px; background:#fafafa;">
                        </div>
                        <div style="flex:1;">
                            <label style="font-size:0.85rem; font-weight:bold; color:#555;">Bannière</label>
                            <input type="file" name="banner" accept="image/*" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px; margin-top:5px; background:#fafafa;">
                        </div>
                    </div>

                    <div style="display:flex; gap:15px;">
                        <div style="flex:1;">
                            <label style="font-size:0.85rem; font-weight:bold; color:#555;">Nom d'affichage</label>
                            <input type="text" name="display_name" value="<?php echo htmlspecialchars($this->user['display_name']); ?>" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; margin-top:5px; outline:none;">
                        </div>
                        <div style="flex:1;">
                            <label style="font-size:0.85rem; font-weight:bold; color:#555;">Localisation</label>
                            <input type="text" name="location" value="<?php echo htmlspecialchars($this->user['location'] ?? ''); ?>" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; margin-top:5px; outline:none;">
                        </div>
                    </div>

                    <div>
                        <label style="font-size:0.85rem; font-weight:bold; color:#555;">À propos de moi</label>
                        <textarea name="bio_free" rows="3" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; margin-top:5px; resize:none; outline:none; font-family:inherit;"><?php echo htmlspecialchars($this->user['bio_free'] ?? ''); ?></textarea>
                    </div>

                    <div>
                        <label style="font-size:0.85rem; font-weight:bold; color:#555;">Techniques maîtrisées <small style="font-weight:normal;">(séparées par une virgule)</small></label>
                        <input type="text" name="skills" value="<?php echo htmlspecialchars($this->user['skills'] ?? ''); ?>" placeholder="Broderie, Couture, Dentelle..." style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; margin-top:5px; outline:none;">
                    </div>
                    
                    <div>
                        <label style="font-size:0.85rem; font-weight:bold; color:#555;">Centres d'intérêt <small style="font-weight:normal;">(séparés par une virgule)</small></label>
                        <input type="text" name="interests" value="<?php echo htmlspecialchars($this->user['interests'] ?? ''); ?>" placeholder="HistoireDeLArt, ModeEthique..." style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; margin-top:5px; outline:none;">
                    </div>

                    <div style="display:flex; gap:15px;">
                        <div style="flex:1;">
                            <label style="font-size:0.85rem; font-weight:bold; color:#555;">Email professionnel</label>
                            <input type="email" name="contact_email" value="<?php echo htmlspecialchars($this->user['contact_email'] ?? ''); ?>" placeholder="contact@email.com" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; margin-top:5px; outline:none;">
                        </div>
                        <div style="flex:1;">
                            <label style="font-size:0.85rem; font-weight:bold; color:#555;">Réseau Social</label>
                            <input type="text" name="social_link" value="<?php echo htmlspecialchars($this->user['social_link'] ?? ''); ?>" placeholder="@mon_compte" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; margin-top:5px; outline:none;">
                        </div>
                    </div>
                    <div>
                        <label style="font-size:0.85rem; font-weight:bold; color:#555;">Site Web / Portfolio</label>
                        <input type="text" name="website" value="<?php echo htmlspecialchars($this->user['website'] ?? ''); ?>" placeholder="www.monsite.com" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; margin-top:5px; outline:none;">
                    </div>

                    <button type="submit" class="btn-submit" style="width:100%; padding:12px; border-radius:8px; font-size:1rem; margin-top:15px;">Sauvegarder les modifications</button>
                </form>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
?>