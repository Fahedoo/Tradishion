<?php

class ViewLandingPage extends View {
    
    public function __construct() {
        parent::__construct();
        $this->pageTitle = "Tradishion - " . $this->t('landing_title');
        $this->bodyClass = "landing-page";
    }

    protected function getBodyContent() {
        ob_start();
        ?>
        <!-- Hero Section with Background Video -->
        <section class="hero-video-section">
            <video autoplay muted loop class="hero-video">
                <source src="assets/videos/HeroBackground.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <div class="hero-overlay">
                <div class="hero-content-left">
                    <h1 class="logo-text">Tradishion</h1>
                    <h1><?php echo $this->t('landing_hero_title'); ?></h1>
                    <p class="motto"><?php echo $this->t('landing_hero_desc'); ?></p>
                    <a href="?page=login" class="btn btn-signin"><?php echo $this->t('nav_login'); ?></a>
                </div>
            </div>
        </section>

        <!-- 3 Main Features Section -->
        <section class="features-section">
            <div class="container features-grid">
                <div class="feature-card">
                    <h3><?php echo $this->t('landing_feature1_title'); ?></h3>
                    <p><?php echo $this->t('landing_feature1_desc'); ?></p>
                </div>
                <div class="feature-card">
                    <h3><?php echo $this->t('landing_feature2_title'); ?></h3>
                    <p><?php echo $this->t('landing_feature2_desc'); ?></p>
                </div>
                <div class="feature-card">
                    <h3><?php echo $this->t('landing_feature3_title'); ?></h3>
                    <p><?php echo $this->t('landing_feature3_desc'); ?></p>
                </div>
            </div>
        </section>

        <!-- Images Preview Section -->
        <section class="images-preview-section">
            <div class="image-row">
                <img src="assets/images/VietnameseFabric.jpg" alt="Traditional fabric patterns">
                <img src="assets/images/FrenchClothes.webp" alt="People wearing traditional clothing">
                <img src="assets/images/AlbanianClothes.jpeg" alt="Albanian traditional clothing">
            </div>
        </section>
        <?php
        return ob_get_clean();
    }
}
?>
