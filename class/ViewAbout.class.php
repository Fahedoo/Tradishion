<?php
class ViewAbout extends View {
    public function __construct() {
        $this->pageTitle = $this->t('about_title') . " - TradiShion";
    }
    protected function getBodyContent() {
        ob_start();
        ?>
        <main class="legal-container">
            <section class="legal-content">
                <h1><?php echo $this->t('about_title'); ?></h1>
                <p class="legal-intro"><?php echo $this->t('about_intro'); ?></p>
                <div class="legal-section">
                    <h2><?php echo $this->t('about_s1_title'); ?></h2>
                    <p><?php echo $this->t('about_s1_desc'); ?></p>
                </div>
                <div class="legal-section">
                    <h2><?php echo $this->t('about_s2_title'); ?></h2>
                    <p><?php echo $this->t('about_s2_desc'); ?></p>
                </div>
                <div class="legal-section">
                    <h2><?php echo $this->t('about_s3_title'); ?></h2>
                    <ul>
                        <li><?php echo $this->t('about_s3_li1'); ?></li>
                        <li><?php echo $this->t('about_s3_li2'); ?></li>
                        <li><?php echo $this->t('about_s3_li3'); ?></li>
                        <li><?php echo $this->t('about_s3_li4'); ?></li>
                    </ul>
                </div>
                <div class="legal-section">
                    <h2><?php echo $this->t('about_s4_title'); ?></h2>
                    <p><?php echo $this->t('about_s4_desc1'); ?></p>
                    <p><?php echo $this->t('about_s4_desc2'); ?></p>
                    <p><?php echo $this->t('about_s4_desc3'); ?></p>
                </div>
                <div class="legal-section">
                    <h2><?php echo $this->t('about_s5_title'); ?></h2>
                    <p><?php echo $this->t('about_s5_desc'); ?></p>
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