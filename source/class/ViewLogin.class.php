<?php

class ViewLogin extends View {
    // Property to store authentication errors
    private $error_message;

    public function __construct($error_message = "") {
        parent::__construct();
        $this->pageTitle = "Tradishion - " . $this->t('nav_login');
        $this->bodyClass = "login-page";
        $this->error_message = $error_message;
    }

    protected function getBodyContent() {
        ob_start();
        ?>
        <div class="login-container">
            <header class="login-header">
                <h1>TRADISHION</h1>
                <p><?php echo $this->t('login_desc'); ?></p>
            </header>

            <main class="login-card">
                <?php if ($this->error_message): ?>
                    <div class="error-message">
                        <?php echo $this->error_message; ?>
                    </div>
                <?php endif; ?>

                <form action="index.php?page=login" method="POST">
                    <div class="form-group">
                        <label for="email"><?php echo $this->t('form_email'); ?></label>
                        <input type="email" name="email" id="email" placeholder="votre@email.com" required>
                    </div>

                    <div class="form-group">
                        <label for="password"><?php echo $this->t('form_password'); ?></label>
                        <input type="password" name="password" id="password" placeholder="********" required>
                    </div>

                    <button type="submit" class="btn-submit"><?php echo strtoupper($this->t('nav_login')); ?></button>
                    
                    <a href="#" class="forgot-password"><?php echo $this->t('forgot_password'); ?></a>
                </form>

                <div class="separator"></div>

                <div class="register-link">
                    <p><?php echo $this->t('no_account'); ?></p>
                    <a href="index.php?page=signup" class="btn-outline"><?php echo strtoupper($this->t('nav_signup')); ?></a>
                </div>
            </main>

            <footer class="login-footer">
                <a href="index.php">← <?php echo $this->t('back_public'); ?></a>
            </footer>
        </div>
        <?php
        return ob_get_clean(); // Return the buffered output
    } 
}
?>