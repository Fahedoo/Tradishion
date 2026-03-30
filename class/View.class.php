<?php

abstract class View {
    protected $pageTitle = 'Tradishion';

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
        </head>
        <body>
            <!-- Header global -->
            <header class="main-header">
                <div class="header-logo">
                    <!-- Placeholder for the logo image if you have one -->
                    <span class="logo-text-small">Tradishion</span>
                </div>
                <div class="header-controls">
                    <div class="header-languages">
                        <select name="language" id="language-select" class="lang-select">
                            <option value="en">English</option>
                            <option value="sq">Albanese</option>
                            <option value="fr">French</option>
                            <option value="vi">Vietnamese</option>
                        </select>
                    </div>
                    <div class="header-auth">
                        <a href="?page=login" class="btn btn-login">Log In</a>
                        <a href="?page=signup" class="btn btn-signup">Sign Up</a>
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
                        <li><a href="?page=privacy">Privacy policy</a></li>
                        <li><a href="?page=faq">Q&A</a></li>
                        <li><a href="?page=about">About us</a></li>
                        <li><a href="?page=legal">Legal information</a></li>
                        <li><a href="?page=contact">Contact</a></li>
                        <li><a href="?page=gcu">GCU</a></li>
                    </ul>
                    <p>&copy; <?php echo date('Y'); ?> Tradishion. All rights reserved.</p>
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