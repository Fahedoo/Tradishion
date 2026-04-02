<?php

abstract class View {
    protected $pageTitle = 'Tradishion';

    public function __construct() {}

    protected function t($key) {
        static $translations = null;
        if ($translations === null) {
            $lang = $_SESSION['lang'] ?? 'fr';
            $file = __DIR__ . "/../lang/{$lang}.json";
            if (file_exists($file)) {
                $translations = json_decode(file_get_contents($file), true) ?: [];
            } else {
                $translations = [];
            }
        }
        return $translations[$key] ?? $key;
    }

    // Méthode abstraite que les enfants devront définir pour ajouter leur contenu
    abstract protected function getBodyContent();

    protected function getHeadAndHeader() {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo $this->pageTitle; ?></title>
            <link rel="stylesheet" href="style/style.css">
            <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
        </head>
        <body>
            <!-- Header global -->
            <header class="main-header">
                <div class="header-logo">
                    <img src="assets/images/logowhite.webp" alt="White_Logo">
                </div>
                <div class="header-controls">
                    <div class="header-languages">
                        <select name="language" id="language-select" class="lang-select" onchange="window.location.href='index.php?lang=' + this.value + '&page=<?php echo isset($_GET['page']) ? htmlspecialchars($_GET['page']) : 'landing'; ?>'">
                            <option value="en" <?php echo (($_SESSION['lang']??'fr')=='en')?'selected':''; ?>>English</option>
                            <option value="al" <?php echo (($_SESSION['lang']??'fr')=='al')?'selected':''; ?>>Albanese</option>
                            <option value="fr" <?php echo (($_SESSION['lang']??'fr')=='fr')?'selected':''; ?>>French</option>
                            <option value="vi" <?php echo (($_SESSION['lang']??'fr')=='vi')?'selected':''; ?>>Vietnamese</option>
                        </select>
                    </div>
                    <div class="header-auth">
                        <a href="?page=login" class="btn btn-login"><?php echo $this->t('nav_login'); ?></a>
                        <a href="?page=signup" class="btn btn-signup"><?php echo $this->t('nav_signup'); ?></a>
                    </div>
                </div>
            </header>
        <?php
        return ob_get_clean();
    }

    protected function getFooter() {
        ob_start();
        ?>
            <!-- Footer global -->
            <footer class="footer">
                <div class="footer-content">
                    <ul class="footer-links">
                        <li><a href="?page=privacy"><?php echo $this->t('footer_privacy'); ?></a></li>
                        <li><a href="?page=faq"><?php echo $this->t('footer_faq'); ?></a></li>
                        <li><a href="?page=about"><?php echo $this->t('footer_about'); ?></a></li>
                        <li><a href="?page=legal"><?php echo $this->t('footer_legal'); ?></a></li>
                        <li><a href="?page=contact"><?php echo $this->t('footer_contact'); ?></a></li>
                        <li><a href="?page=gcu"><?php echo $this->t('footer_gcu'); ?></a></li>
                    </ul>
                    <p>&copy; <?php echo date('Y'); ?> Tradishion. <?php echo $this->t('rights'); ?></p>
                </div>
            </footer>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }

    public function __toString() {
        // La méthode magique assemble le haut, le contenu spécifique à l'enfant, et le bas.
        return $this->getHeadAndHeader() . $this->getBodyContent() . $this->getFooter();
    }
}
?>