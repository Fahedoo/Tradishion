<?php

class ViewSignUp extends View {
    private $error_message;

    public function __construct($error_message = "") {
        $this->pageTitle = "Tradishion - " . $this->t('nav_signup');
        $this->error_message = $error_message;
    }

    protected function getBodyContent() {
        ob_start();
        ?>
        <div class="login-container signup-container">
            <header class="login-header">
                <h1><?php echo $this->t('signup_desc'); ?></h1>
            </header>

            <main class="login-card signup-card">
                <?php if ($this->error_message): ?>
                    <div class="error-message">
                        <?php echo $this->error_message; ?>
                    </div>
                <?php endif; ?>

                <?php $maxBirthDate = date('Y-m-d', strtotime('-14 years')); ?>

                <div class="signup-layout">
                    <div class="signup-form-area">
                        <form id="signup-form" class="signup-form" action="index.php?page=signup" method="POST" novalidate>
                    <div class="signup-grid signup-grid-1">
                        <div class="form-group">
                            <label for="email"><?php echo $this->t('form_email'); ?></label>
                            <input type="email" name="email" id="email" required>
                            <p class="field-error" id="email-error"></p>
                        </div>
                    </div>

                    <div class="signup-grid signup-grid-2">
                        <div class="form-group">
                            <label for="password"><?php echo $this->t('form_password'); ?></label>
                            <input type="password" name="password" id="password" minlength="8" required>
                            <p class="field-error" id="password-error"></p>
                            <div class="password-strength-wrapper">
                                <div class="password-strength-inline">
                                    <div class="password-strength-bar" role="progressbar" aria-label="Force du mot de passe" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                        <span id="password-strength-fill"></span>
                                    </div>
                                    <p class="password-strength-text" id="password-strength-text">Tres faible</p>
                                </div>
                                <ul class="password-requirements" id="password-requirements">
                                    <li data-rule="length">8+ caracteres</li>
                                    <li data-rule="uppercase">1 majuscule</li>
                                    <li data-rule="lowercase">1 minuscule</li>
                                    <li data-rule="digit">1 chiffre</li>
                                </ul>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password_confirm"><?php echo $this->t('form_password_confirm'); ?></label>
                            <input type="password" name="password_confirm" id="password_confirm" minlength="8" required>
                            <p class="field-error" id="password-confirm-error"></p>
                        </div>
                    </div>

                    <div class="signup-grid signup-grid-1">
                        <div class="form-group">
                            <label for="birth_date"><?php echo $this->t('form_birthdate'); ?></label>
                            <input type="date" name="birth_date" id="birth_date" max="<?php echo $maxBirthDate; ?>" required>
                            <small class="signup-help-text"><?php echo $this->t('signup_age_req'); ?></small>
                            <p class="field-error" id="birth-date-error"></p>
                        </div>
                    </div>

                    <div class="form-group form-group-checkbox">
                        <label for="accept_cgu" class="checkbox-label checkbox-card">
                            <input
                                type="checkbox"
                                name="accept_cgu"
                                id="accept_cgu"
                                value="1"
                                <?php echo isset($_POST['accept_cgu']) ? 'checked' : ''; ?>
                                required>
                            <span class="checkbox-copy"><?php echo $this->t('signup_accept'); ?> <a href="index.php?page=gcu" target="_blank" rel="noopener"><?php echo $this->t('signup_cgu_link'); ?></a>.</span>
                        </label>
                        <p class="field-error" id="accept-cgu-error"></p>
                    </div>

                    <script>
                    const form = document.getElementById('signup-form');
                    const passwordInput = document.getElementById('password');
                    const passwordConfirmInput = document.getElementById('password_confirm');
                    const emailInput = document.getElementById('email');
                    const birthDateInput = document.getElementById('birth_date');
                    const acceptCguInput = document.getElementById('accept_cgu');
                    const emailError = document.getElementById('email-error');
                    const passwordError = document.getElementById('password-error');
                    const passwordConfirmError = document.getElementById('password-confirm-error');
                    const birthDateError = document.getElementById('birth-date-error');
                    const acceptCguError = document.getElementById('accept-cgu-error');
                    const cguCard = document.querySelector('.checkbox-card');
                    const maxBirthDate = '<?php echo $maxBirthDate; ?>';
                    const strengthFill = document.getElementById('password-strength-fill');
                    const strengthText = document.getElementById('password-strength-text');
                    const strengthBar = document.querySelector('.password-strength-bar');
                    const requirementItems = document.querySelectorAll('#password-requirements li');

                    function showError(input, errorNode, message) {
                        errorNode.textContent = message;
                        errorNode.classList.add('is-visible');
                        if (input) {
                            input.classList.add('is-invalid');
                        }
                    }

                    function clearError(input, errorNode) {
                        errorNode.textContent = '';
                        errorNode.classList.remove('is-visible');
                        if (input) {
                            input.classList.remove('is-invalid');
                        }
                    }

                    function getPasswordChecks(password) {
                        return {
                            length: password.length >= 8,
                            uppercase: /[A-Z]/.test(password),
                            lowercase: /[a-z]/.test(password),
                            digit: /\d/.test(password)
                        };
                    }

                    function updatePasswordStrength() {
                        const password = passwordInput.value;
                        const checks = getPasswordChecks(password);
                        const score = Object.values(checks).filter(Boolean).length;
                        const percent = Math.round((score / 4) * 100);

                        strengthFill.style.width = percent + '%';
                        strengthBar.setAttribute('aria-valuenow', String(percent));

                        let label = 'Tres faible';
                        if (score === 2) { label = 'Faible'; }
                        if (score === 3) { label = 'Moyenne'; }
                        if (score === 4) { label = 'Forte'; }
                        strengthText.textContent = label;

                        strengthBar.classList.remove('strength-1', 'strength-2', 'strength-3', 'strength-4');
                        if (score > 0) {
                            strengthBar.classList.add('strength-' + score);
                        }

                        requirementItems.forEach(function(item) {
                            const rule = item.getAttribute('data-rule');
                            const valid = !!checks[rule];
                            item.classList.toggle('valid', valid);
                            item.classList.toggle('invalid', !valid);
                        });
                    }

                    function validatePasswordConfirmation() {
                        if (passwordConfirmInput.value === '') {
                            return false;
                        }

                        return passwordInput.value === passwordConfirmInput.value;
                    }

                    function validateEmailField() {
                        const value = emailInput.value.trim();
                        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!value) {
                            showError(emailInput, emailError, 'Veuillez renseigner votre adresse email.');
                            return false;
                        }
                        if (!emailPattern.test(value)) {
                            showError(emailInput, emailError, 'Format d\'email invalide.');
                            return false;
                        }
                        clearError(emailInput, emailError);
                        return true;
                    }

                    function validatePasswordField() {
                        const checks = getPasswordChecks(passwordInput.value);
                        const valid = Object.values(checks).every(Boolean);
                        if (!valid) {
                            showError(passwordInput, passwordError, 'Le mot de passe doit respecter toutes les conditions ci-dessous.');
                            return false;
                        }
                        clearError(passwordInput, passwordError);
                        return true;
                    }

                    function validatePasswordConfirmField() {
                        if (!passwordConfirmInput.value) {
                            showError(passwordConfirmInput, passwordConfirmError, 'Veuillez confirmer votre mot de passe.');
                            return false;
                        }
                        if (!validatePasswordConfirmation()) {
                            showError(passwordConfirmInput, passwordConfirmError, 'Les mots de passe ne correspondent pas.');
                            return false;
                        }
                        clearError(passwordConfirmInput, passwordConfirmError);
                        return true;
                    }

                    function validateBirthDateField() {
                        if (!birthDateInput.value) {
                            showError(birthDateInput, birthDateError, 'Veuillez renseigner votre date de naissance.');
                            return false;
                        }
                        if (birthDateInput.value > maxBirthDate) {
                            showError(birthDateInput, birthDateError, 'Vous devez avoir au moins 14 ans pour vous inscrire.');
                            return false;
                        }
                        clearError(birthDateInput, birthDateError);
                        return true;
                    }

                    function validateCguField() {
                        if (!acceptCguInput.checked) {
                            acceptCguError.textContent = 'Vous devez accepter les conditions generales d\'utilisation.';
                            acceptCguError.classList.add('is-visible');
                            cguCard.classList.add('is-invalid');
                            return false;
                        }
                        acceptCguError.textContent = '';
                        acceptCguError.classList.remove('is-visible');
                        cguCard.classList.remove('is-invalid');
                        return true;
                    }

                    passwordInput.addEventListener('input', updatePasswordStrength);
                    passwordInput.addEventListener('input', validatePasswordConfirmation);
                    passwordInput.addEventListener('input', validatePasswordField);
                    passwordConfirmInput.addEventListener('input', validatePasswordConfirmation);
                    passwordConfirmInput.addEventListener('input', validatePasswordConfirmField);
                    emailInput.addEventListener('input', validateEmailField);
                    birthDateInput.addEventListener('change', validateBirthDateField);
                    acceptCguInput.addEventListener('change', validateCguField);
                    form.addEventListener('submit', function(event) {
                        updatePasswordStrength();
                        const emailOk = validateEmailField();
                        const passwordOk = validatePasswordField();
                        const passwordConfirmOk = validatePasswordConfirmField();
                        const birthDateOk = validateBirthDateField();
                        const cguOk = validateCguField();

                        if (!(emailOk && passwordOk && passwordConfirmOk && birthDateOk && cguOk)) {
                            event.preventDefault();
                        }
                    });

                    updatePasswordStrength();
                    validatePasswordConfirmation();
                    </script>

                            <button type="submit" class="btn-submit signup-submit"><?php echo $this->t('btn_create_account'); ?></button>
                        </form>
                    </div>

                    <div class="signup-divider" aria-hidden="true"></div>

                    <aside class="signup-side-panel">
                        <div class="register-link register-link-side">
                            <p><?php echo $this->t('has_account'); ?></p>
                            <a href="index.php?page=login" class="btn-outline"><?php echo $this->t('nav_login'); ?></a>
                        </div>
                    </aside>
                </div>
            </main>

            <footer class="login-footer">
                <a href="index.php">← <?php echo $this->t('back_public'); ?></a>
            </footer>
        </div>
        <?php
        return ob_get_clean();
    } 
}
?>