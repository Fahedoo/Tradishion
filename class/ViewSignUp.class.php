<?php

class ViewSignUp extends View {
    // Property to store registration errors (e.g., email already taken)
    private $error_message;

    public function __construct($error_message = "") {
        // Set the page title for the browser tab
        $this->pageTitle = "Inscription - TradiShion";
        $this->error_message = $error_message;
    }

    protected function getBodyContent() {
        ob_start(); // Start output buffering
        ?>
        <div class="login-container">
            <header class="login-header">
                <h1>TRADISHION</h1>
                <p>Rejoignez notre communauté d'artisans</p>
            </header>

            <main class="login-card">
                <?php if ($this->error_message): ?>
                    <div class="error-message">
                        <?php echo $this->error_message; ?>
                    </div>
                <?php endif; ?>

                <form action="index.php?page=signup" method="POST">                    
                    <div class="form-group">
                        <label for="nom">Nom complet</label>
                        <input type="text" name="nom" id="nom" placeholder="Votre nom complet" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Adresse email</label>
                        <input type="email" name="email" id="email" placeholder="votre@email.com" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" name="password" id="password" placeholder="********" required>
                    </div>

                    <button type="submit" class="btn-submit">CRÉER MON COMPTE</button>
                </form>

                <div class="separator"></div>

                <div class="register-link">
                    <p>Vous avez déjà un compte ?</p>
                    <a href="index.php?page=login" class="btn-outline">SE CONNECTER</a>
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