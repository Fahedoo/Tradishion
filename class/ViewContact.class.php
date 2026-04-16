<?php
class ViewContact extends View {
    public function __construct() {
        $this->pageTitle = $this->t('contact_title') . " - TradiShion";
    }
    protected function getBodyContent() {
        ob_start();
        ?>
        <main class="legal-container">
            <section class="legal-content">
                <h1><?php echo $this->t('contact_title'); ?></h1>
                <p class="legal-intro"><?php echo $this->t('contact_intro'); ?></p>
                <div class="legal-section">
                    <h2><?php echo $this->t('contact_s1_title'); ?></h2>
                    <p><strong><?php echo $this->t('contact_s1_email'); ?></strong></p>
                    <p><strong><?php echo $this->t('contact_s1_time'); ?></strong></p>
                </div>
                <div class="legal-section">
                    <h2><?php echo $this->t('contact_s2_title'); ?></h2>
                    <p><?php echo $this->t('contact_s2_desc'); ?></p>
                    <ul>
                        <li><?php echo $this->t('contact_s2_li1'); ?></li>
                        <li><?php echo $this->t('contact_s2_li2'); ?></li>
                        <li><?php echo $this->t('contact_s2_li3'); ?></li>
                    </ul>
                </div>
                <div class="legal-section">
                    <h2><?php echo $this->t('contact_s3_title'); ?></h2>
                    <p><?php echo $this->t('contact_s3_desc'); ?></p>
                </div>
                <div class="legal-section">
                    <h2><?php echo $this->t('contact_s4_title'); ?></h2>
                    <p><?php echo $this->t('contact_s4_desc'); ?></p>
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