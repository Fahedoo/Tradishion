<?php

abstract class ViewPrivate extends View {
    protected $user;
    protected $bodyStyle = ''; // Permet d'ajouter du style au body (ex: overflow:hidden pour Messages)

    public function __construct($userData) {
        $this->user = $userData;
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
            <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
        </head>
        <body class="dashboard-body" <?php echo !empty($this->bodyStyle) ? 'style="'.htmlspecialchars($this->bodyStyle).'"' : ''; ?>>
            <header class="dash-header">
                <div class="dash-logo">
                    <a href="index.php?page=dashboard">
                        <img src="assets/images/logofinal.webp" alt="Tradishion" style="height: 35px; object-fit: contain;">
                    </a>
                </div>
                <div class="dash-search" style="position:relative;">
                    <input type="text" id="global-search" placeholder="🔍 Rechercher des artisans...">
                    <div id="search-results" style="display:none; position:absolute; top:100%; left:0; width:100%; background:white; border:1px solid #eee; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.1); z-index:100; max-height:300px; overflow-y:auto;"></div>
                </div>
                <div class="dash-user-nav">
                    <span style="color: #666;">AL | <strong style="color:#A64B35;">FR</strong> | GB</span>
                    <span class="notification-icon" style="font-size: 1.2rem; cursor: pointer;">🔔</span>
                    <a href="index.php?page=logout" class="btn-outline" style="border-color:#ccc; color:#333; padding: 5px 15px; border-radius: 20px; font-weight:bold; font-size: 0.8rem;">Se déconnecter</a>
                </div>
            </header>
        <?php
        return ob_get_clean();
    }

    protected function getFooter() {
        ob_start();
        ?>
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
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}
?>