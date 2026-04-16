<?php

abstract class View {
    protected $pageTitle = 'Tradishion';
    protected $bodyClass = '';

    public function __construct() {}

    // LA FAMEUSE FONCTION DE TRADUCTION QUI MANQUAIT !
    protected function t($key) {
        static $translations = null;
        if ($translations === null) {
            $lang = $_SESSION['lang'] ?? 'fr';
            $file = __DIR__ . "/../lang/{$lang}.json"; // On pointe vers le dossier lang/
            if (file_exists($file)) {
                $translations = json_decode(file_get_contents($file), true) ?: [];
            } else {
                $translations = [];
            }
        }
        return $translations[$key] ?? $key;
    }

    // Envoie le dictionnaire entier au Javascript
    protected function getTranslationsJson() {
        $lang = $_SESSION['lang'] ?? 'fr';
        $file = __DIR__ . "/../lang/{$lang}.json";
        if (file_exists($file)) {
            return file_get_contents($file);
        }
        return '{}';
    }

    abstract protected function getBodyContent();

    protected function getHeadAndHeader() {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="<?php echo $_SESSION['lang'] ?? 'fr'; ?>">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo htmlspecialchars($this->pageTitle); ?></title>
            <link rel="stylesheet" href="style/style.css?v=<?php echo @filemtime('style/style.css'); ?>">
            <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
            
            <script>
                window.Lang = <?php echo $this->getTranslationsJson(); ?>;
            </script>
        </head>
        <body class="<?php echo htmlspecialchars($this->bodyClass); ?>">
            <header class="main-header">
                <div class="header-logo">
                    <a href="index.php?page=landing"><img src="assets/images/logowhite.webp" alt="White_Logo" style="height: 35px; object-fit: contain;"></a>
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
                        <a href="index.php?page=login" class="btn btn-login"><?php echo $this->t('nav_login'); ?></a>
                        <a href="index.php?page=signup" class="btn btn-signup"><?php echo $this->t('nav_signup'); ?></a>
                    </div>
                </div>
            </header>
        <?php
        return ob_get_clean();
    }

    protected function getFooter() {
        ob_start();
        ?>
            <footer class="footer">
                <div class="footer-content">
                    <ul class="footer-links">
                        <li><a href="index.php?page=privacy"><?php echo $this->t('footer_privacy'); ?></a></li>
                        <li><a href="index.php?page=faq"><?php echo $this->t('footer_faq'); ?></a></li>
                        <li><a href="index.php?page=about"><?php echo $this->t('footer_about'); ?></a></li>
                        <li><a href="index.php?page=legal"><?php echo $this->t('footer_legal'); ?></a></li>
                        <li><a href="index.php?page=contact"><?php echo $this->t('footer_contact'); ?></a></li>
                        <li><a href="index.php?page=gcu"><?php echo $this->t('footer_gcu'); ?></a></li>
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
        return $this->getHeadAndHeader() . $this->getBodyContent() . $this->getFooter();
    }
}
?>