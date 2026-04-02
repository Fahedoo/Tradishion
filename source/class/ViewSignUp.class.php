<?php

class ViewSignUp extends View {
    // Property to store registration errors (e.g., email already taken)
    private $error_message;

    public function __construct($error_message = "") {
        parent::__construct();
        $this->pageTitle = "Tradishion - " . $this->t('nav_signup');
        $this->error_message = $error_message;
    }

    protected function getBodyContent() {
        ob_start();
        ?>
        <div class="login-container">
            <header class="login-header">
                <h1>TRADISHION</h1>
                <p><?php echo $this->t('signup_desc'); ?></p>
            </header>

            <main class="login-card">
                <?php if ($this->error_message): ?>
                    <div class="error-message">
                        <?php echo $this->error_message; ?>
                    </div>
                <?php endif; ?>

                <form action="index.php?page=signup" method="POST">                    
                    <div class="form-group">
                        <label for="nom"><?php echo $this->t('form_name'); ?></label>
                        <input type="text" name="nom" id="nom" required>
                    </div>

                    <div class="form-group">
                        <label for="email"><?php echo $this->t('form_email'); ?></label>
                        <input type="email" name="email" id="email" required>
                    </div>

                    <div class="form-group">
                        <label for="password"><?php echo $this->t('form_password'); ?></label>
                        <input type="password" name="password" id="password" required>
                    </div>

                    <div class="form-group">
                        <label for="birth_date"><?php echo $this->t('form_birthdate'); ?></label>
                        <input type="date" name="birth_date" id="birth_date" required>
                    </div>

                    <div class="form-group">
                        <label for="origins"><?php echo $this->t('form_origins'); ?> (Max 4)</label>
                        <input type="text" id="origin-search" placeholder="Search country..." style="margin-bottom:5px; padding:8px; border:1px solid #ddd; border-radius:4px; width:100%;">
                        <select name="origins[]" id="origins" multiple class="form-select" style="height: 150px; width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            <?php 
                            $countriesJson = file_get_contents('source/data/countries.json');
                            $countries = json_decode($countriesJson, true);
                            foreach ($countries as $code => $name) {
                                echo "<option value=\"$code\">$name</option>";
                            }
                            ?>
                        </select>
                        <small style="color: #666; font-size: 0.8rem; display: block; margin-top: 5px;">Hold Ctrl (or Cmd) to select multiple</small>
                    </div>

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
                        const selectedOptions = Array.from(this.selectedOptions);
                        if (selectedOptions.length > 4) {
                            alert("You can only select up to 4 countries.");
                            // Deselect last one
                            for (let i = 0; i < this.options.length; i++) {
                                if (this.options[i].selected && !window.lastSelected?.includes(this.options[i].value)) {
                                    if (selectedOptions.indexOf(this.options[i]) === selectedOptions.length - 1) {
                                        this.options[i].selected = false;
                                    }
                                }
                            }
                        }
                        window.lastSelected = Array.from(this.selectedOptions).map(o => o.value);
                    });
                    </script>

                    <button type="submit" class="btn-submit"><?php echo $this->t('btn_create_account'); ?></button>
                </form>

                <div class="separator"></div>

                <div class="register-link">
                    <p><?php echo $this->t('has_account'); ?></p>
                    <a href="index.php?page=login" class="btn-outline"><?php echo strtoupper($this->t('nav_login')); ?></a>
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