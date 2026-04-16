<?php
class ViewLegal extends View {
    public function __construct() {
        $this->pageTitle = $this->t('legal_title') . " - TradiShion";
    }
    protected function getBodyContent() {
        ob_start();
        ?>
        <main class="legal-container">
            <section class="legal-content">
                <h1><?php echo $this->t('legal_title'); ?></h1>
                <p class="legal-intro"><?php echo $this->t('legal_intro'); ?></p>
                <div class="legal-section">
                    <h2><?php echo $this->t('legal_s1_title'); ?></h2>
                    <p><?php echo $this->t('legal_s1_desc1'); ?></p>
                    <p><strong><?php echo $this->t('legal_s1_desc2'); ?></strong></p>
                    <p><strong><?php echo $this->t('legal_s1_desc3'); ?></strong></p>
                </div>
                <div class="legal-section">
                    <h2><?php echo $this->t('legal_s2_title'); ?></h2>
                    <p><?php echo $this->t('legal_s2_desc1'); ?></p>
                    <p><strong><?php echo $this->t('legal_s2_desc2'); ?></strong></p>
                    <p><strong><?php echo $this->t('legal_s2_desc3'); ?></strong></p>
                </div>
                <div class="legal-section">
                    <h2><?php echo $this->t('legal_s3_title'); ?></h2>
                    <p><strong><?php echo $this->t('legal_s3_desc'); ?></strong></p>
                </div>
                <div class="legal-section">
                    <h2><?php echo $this->t('legal_s4_title'); ?></h2>
                    <p><?php echo $this->t('legal_s4_desc'); ?></p>
                </div>
                <div class="legal-section">
                    <h2><?php echo $this->t('legal_s5_title'); ?></h2>
                    <p><?php echo $this->t('legal_s5_desc'); ?></p>
                </div>
                <div class="legal-section">
                    <h2><?php echo $this->t('legal_s6_title'); ?></h2>
                    <ul>
                        <li><?php echo $this->t('legal_s6_li1'); ?></li>
                        <li><?php echo $this->t('legal_s6_li2'); ?></li>
                        <li><?php echo $this->t('legal_s6_li3'); ?></li>
                        <li><?php echo $this->t('legal_s6_li4'); ?></li>
                    </ul>
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