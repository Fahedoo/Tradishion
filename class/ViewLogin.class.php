<?php

class ViewLogin extends View {
    // Property to store authentication errors
    private $error_message;

    public function __construct($error_message = "") {
        // Set the page title and body class for CSS targeting
        $this->pageTitle = "Connexion - TradiShion";
        $this->bodyClass = "login-page";
        $this->error_message = $error_message;
    }

    protected function getBodyContent() {
        ob_start(); // Start output buffering
        ?>
        <div class="login-container">
            <header class="login-header">
                <h1>TRADISHION</h1>
                <p>Connectez-vous à votre espace privé</p>
            </header>

            <main class="login-card">
                <?php if ($this->error_message): ?>
                    <div class="error-message">
                        <?php echo $this->error_message; ?>
                    </div>
                <?php endif; ?>

                <form action="index.php?page=login" method="POST">
                    <div class="form-group">
                        <label for="email">Adresse email</label>
                        <input type="email" name="email" id="email" placeholder="votre@email.com" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" name="password" id="password" placeholder="********" required>
                    </div>

                    <button type="submit" class="btn-submit">SE CONNECTER</button>
                    
                    <a href="#" class="forgot-password">Mot de passe oublié ?</a>
                </form>

                <div class="separator"></div>

                <div class="register-link">
                    <p>Vous n'avez pas de compte ?</p>
                    <a href="index.php?page=signup" class="btn-outline">S'INSCRIRE</a>
                </div>
            </main>

            <footer class="login-footer">
                <a href="index.php">← Retour au site public</a>
            </footer>
        </div>
        <?php
        return ob_get_clean(); // Return the buffered output
    } 
}
?>