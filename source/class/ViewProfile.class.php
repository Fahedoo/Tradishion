<?php

class ViewProfile extends View {
    private $user;
    private $recommendations;

    public function __construct($userData, $recos) {
        $this->pageTitle = "Mon Profil - TradiShion";
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
                <div class="dash-user-nav">
                    <a href="index.php?page=dashboard" class="btn-outline" style="padding: 8px 15px; border-radius: 20px;">Retour au Dashboard</a>
                </div>
            </header>
        <?php
        return ob_get_clean();
    }

    protected function getBodyContent() {
        ob_start();
        $initial = strtoupper(substr($this->user['display_name'], 0, 1));
        ?>
        <div style="max-width: 1200px; margin: 30px auto; padding: 0 20px;">
            
            <div class="card" style="padding:0; overflow:hidden; position:relative; margin-bottom:30px;">
                <div style="height:200px; background:url('assets/images/fabric1.jpg') center/cover;"></div>
                
                <div style="padding: 20px 40px 40px; display:flex; align-items:flex-end; gap:20px; margin-top:-60px;">
                    <div style="width:120px; height:120px; background:#fff; border:4px solid white; border-radius:50%; box-shadow:0 4px 10px rgba(0,0,0,0.1); display:flex; justify-content:center; align-items:center; font-size:3rem; font-weight:bold; color:#A64B35; z-index:10; overflow:hidden;">
                        <?php if (!empty($this->user['avatar_url'])): ?>
                            <img src="<?php echo htmlspecialchars($this->user['avatar_url']); ?>" style="width:100%; height:100%; object-fit:cover;" alt="Avatar">
                        <?php else: ?>
                            <?php echo $initial; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div style="flex:1; padding-bottom:10px;">
                        <h1 style="margin:0; font-size:2rem;"><?php echo htmlspecialchars($this->user['display_name']); ?></h1>
                        <p style="color:#666; margin:5px 0;">📍 <?php 
                            if (!empty($this->user['origins'])) {
                                $countriesJson = file_get_contents('source/data/countries.json');
                                $countries = json_decode($countriesJson, true);
                                $names = array_map(function($code) use ($countries) {
                                    return $countries[$code] ?? $code;
                                }, $this->user['origins']);
                                echo htmlspecialchars(implode(', ', $names));
                            } else {
                                echo htmlspecialchars($this->t('no_location') ?? 'No origin specified');
                            }
                        ?></p>
                    </div>
                    
                    <button onclick="document.getElementById('edit-profile-form').style.display='block';" class="btn-outline" style="border-color:#ccc; padding:10px 20px; border-radius:25px; cursor:pointer;">
                        ✏️ Modifier le profil
                    </button>
                </div>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 2fr; gap:30px;">
                
                <aside style="display:flex; flex-direction:column; gap:20px;">
                    <div class="card">
                        <h3 style="border-bottom:2px solid #FFF0ED; padding-bottom:10px;">À propos</h3>
                        <p style="color:#555; font-size:0.95rem; white-space:pre-wrap;"><?php echo htmlspecialchars($this->user['bio_free'] ?? 'Aucune biographie pour le moment.'); ?></p>
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

                <main>
                    <div class="card">
                        <h3 style="border-bottom:2px solid #FFF0ED; padding-bottom:10px;">Mes Créations</h3>
                        <p style="color:#888; text-align:center; padding:40px;">Vos photos apparaîtront ici.</p>
                    </div>
                </main>
            </div>
        </div>

        <div id="edit-profile-form" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
            <div class="card" style="max-width:500px; margin: 100px auto; padding:30px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <h3>Modifier mes informations</h3>
                    <button onclick="document.getElementById('edit-profile-form').style.display='none';" style="background:none; border:none; font-size:1.5rem; cursor:pointer;">✕</button>
                </div>
                
                <form action="index.php?page=profile" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="update_profile" value="1">
                    
                    <div class="form-group">
                        <label>Photo de profil</label>
                        <input type="file" name="avatar" accept="image/*">
                    </div>

                    <div class="form-group">
                        <label>Nom d'affichage</label>
                        <input type="text" name="display_name" value="<?php echo htmlspecialchars($this->user['display_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label><?php echo $this->t('form_origins'); ?> (Max 4)</label>
                        <input type="text" id="origin-search" placeholder="Search..." style="margin-bottom:5px; padding:8px; border:1px solid #ddd; border-radius:4px; width:100%;">
                        <select name="origins[]" id="origins" multiple class="form-select" style="height: 120px; width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            <?php 
                            $countriesJson = file_get_contents('source/data/countries.json');
                            $countriesArr = json_decode($countriesJson, true);
                            foreach ($countriesArr as $code => $name) {
                                $selected = in_array($code, $this->user['origins'] ?? []) ? 'selected' : '';
                                echo "<option value=\"$code\" $selected>$name</option>";
                            }
                            ?>
                        </select>
                        <script>
                        document.getElementById('origin-search').addEventListener('input', function(e) {
                            const term = e.target.value.toLowerCase();
                            const options = document.getElementById('origins').options;
                            for (let i = 0; i < options.length; i++) {
                                const text = options[i].text.toLowerCase();
                                options[i].style.display = text.includes(term) ? '' : 'none';
                            }
                        });
                        document.getElementById('origins').addEventListener('change', function(e) {
                            if (Array.from(this.selectedOptions).length > 4) {
                                alert("Max 4 countries");
                                Array.from(this.options).forEach(opt => {
                                    if (opt.selected && !window.lastSelected?.includes(opt.value)) {
                                        opt.selected = false;
                                    }
                                });
                            }
                            window.lastSelected = Array.from(this.selectedOptions).map(o => o.value);
                        });
                        window.lastSelected = Array.from(document.getElementById('origins').selectedOptions).map(o => o.value);
                        </script>
                    </div>

                    <div class="form-group">
                        <label>À propos de moi</label>
                        <textarea name="bio_free" rows="4" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;"><?php echo htmlspecialchars($this->user['bio_free'] ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="btn-submit" style="width:100%; margin-top:10px;">Sauvegarder</button>
                </form>
            </div>
        </div>

        <script>
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
        </script>
        <?php
        return ob_get_clean();
    }
}
?>