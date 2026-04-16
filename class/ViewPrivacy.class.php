<?php
class ViewPrivacy extends View {
    public function __construct() {
        $this->pageTitle = $this->t('privacy_title') . " - TradiShion";
    }
    protected function getBodyContent() {
        ob_start();
        ?>
        <main class="legal-container">
            <section class="legal-content">
                <h1><?php echo $this->t('privacy_title'); ?></h1>
                <div class="legal-section">
                    <h2><?php echo $this->t('priv_s1_title'); ?></h2>
                    <p><?php echo $this->t('priv_s1_desc'); ?></p>
                </div>
                <div class="legal-section">
                    <h2><?php echo $this->t('priv_s2_title'); ?></h2>
                    <p><?php echo $this->t('priv_s2_desc'); ?></p>
                </div>
                <div class="legal-section">
                    <h2><?php echo $this->t('priv_s3_title'); ?></h2>
                    <p><?php echo $this->t('priv_s3_desc'); ?></p>
                    <ul>
                        <li><?php echo $this->t('priv_s3_li1'); ?></li>
                        <li><?php echo $this->t('priv_s3_li2'); ?></li>
                        <li><?php echo $this->t('priv_s3_li3'); ?></li>
                    </ul>
                </div>
                <div class="legal-section">
                    <h2><?php echo $this->t('priv_s4_title'); ?></h2>
                    <p><?php echo $this->t('priv_s4_desc'); ?></p>
                    <ul>
                        <li><?php echo $this->t('priv_s4_li1'); ?></li>
                        <li><?php echo $this->t('priv_s4_li2'); ?></li>
                    </ul>
                </div>
                <div class="legal-section">
                    <h2><?php echo $this->t('priv_s5_title'); ?></h2>
                    <p><?php echo $this->t('priv_s5_desc'); ?></p>
                </div>
                <div class="legal-section">
                    <h2><?php echo $this->t('priv_s6_title'); ?></h2>
                    <ul>
                        <li><?php echo $this->t('priv_s6_li1'); ?></li>
                        <li><?php echo $this->t('priv_s6_li2'); ?></li>
                        <li><?php echo $this->t('priv_s6_li3'); ?></li>
                    </ul>
                </div>
                <div class="legal-section">
                    <h2><?php echo $this->t('priv_s7_title'); ?></h2>
                    <p><?php echo $this->t('priv_s7_desc1'); ?></p>
                    <ul>
                        <li><?php echo $this->t('priv_s7_li1'); ?></li>
                        <li><?php echo $this->t('priv_s7_li2'); ?></li>
                    </ul>
                    <p><?php echo $this->t('priv_s7_desc2'); ?></p>
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