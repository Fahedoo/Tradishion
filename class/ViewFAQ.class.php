<?php
class ViewFAQ extends View {
    public function __construct() {
        $this->pageTitle = $this->t('faq_title') . " - TradiShion";
    }
    protected function getBodyContent() {
        ob_start();
        ?>
        <main class="legal-container">
            <section class="legal-content">
                <h1><?php echo $this->t('faq_title'); ?></h1>
                <p class="legal-intro"><?php echo $this->t('faq_intro'); ?></p>
                <div class="legal-section">
                    <h2><?php echo $this->t('faq_q1'); ?></h2>
                    <p><?php echo $this->t('faq_a1'); ?></p>
                </div>
                <div class="legal-section">
                    <h2><?php echo $this->t('faq_q2'); ?></h2>
                    <p><?php echo $this->t('faq_a2'); ?></p>
                </div>
                <div class="legal-section">
                    <h2><?php echo $this->t('faq_q3'); ?></h2>
                    <p><?php echo $this->t('faq_a3'); ?></p>
                </div>
                <div class="legal-section">
                    <h2><?php echo $this->t('faq_q4'); ?></h2>
                    <p><?php echo $this->t('faq_a4'); ?></p>
                </div>
                <div class="legal-section">
                    <h2><?php echo $this->t('faq_q5'); ?></h2>
                    <p><?php echo $this->t('faq_a5'); ?></p>
                </div>
                <div class="legal-section">
                    <h2><?php echo $this->t('faq_q6'); ?></h2>
                    <p><?php echo $this->t('faq_a6'); ?></p>
                </div>
            </section>
        </main>
        <footer class="login-footer">
            <a href="index.php">← <?php echo $this->t('back_public'); ?></a>
        </footer>
        <?php
        return ob_get_clean();
    }
}
?>