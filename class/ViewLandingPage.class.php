<?php

class ViewLandingPage extends View {
    
    public function __construct() {
        parent::__construct();
        $this->pageTitle = $this->t('landing_title');
        $this->bodyClass = "landing-page";
    }

    protected function getBodyContent() {
        ob_start();
        ?>
        <section class="hero-video-section">
            <video autoplay muted loop class="hero-video">
                <source src="assets/videos/HeroBackground.mp4?v=<?php echo @filemtime('assets/videos/HeroBackground.mp4'); ?>" type="video/mp4">
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

        <section class="features-section">
            <div class="container features-grid">
                <div class="feature-card">
                    <div class="feature-icon" aria-hidden="true">&#10024;</div>
                    <h3><?php echo $this->t('landing_feature1_title'); ?></h3>
                    <p><?php echo $this->t('landing_feature1_desc'); ?></p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" aria-hidden="true">&#128214;</div>
                    <h3><?php echo $this->t('landing_feature2_title'); ?></h3>
                    <p><?php echo $this->t('landing_feature2_desc'); ?></p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" aria-hidden="true">&#127760;</div>
                    <h3><?php echo $this->t('landing_feature3_title'); ?></h3>
                    <p><?php echo $this->t('landing_feature3_desc'); ?></p>
                </div>
            </div>
        </section>

        <section class="story-video-section">
            <div class="container">
                <h2><?php echo $this->t('landing_video_title'); ?></h2>
                <p><?php echo $this->t('landing_video_desc'); ?></p>
                <video class="story-video" controls preload="metadata">
                    <source src="assets/videos/video_fashion.mp4?v=<?php echo @filemtime('assets/videos/video_fashion.mp4'); ?>" type="video/mp4">
                    <track kind="subtitles" srclang="fr" label="Français" src="assets/videos/tradishion_story.fr.vtt" default>
                    <track kind="subtitles" srclang="sq" label="Shqip" src="assets/videos/tradishion_story.sq.vtt">
                    <track kind="subtitles" srclang="vi" label="Tiếng Việt" src="assets/videos/tradishion_story.vi.vtt">
                    <track kind="subtitles" srclang="en" label="English" src="assets/videos/tradishion_story.en.vtt">
                    Your browser does not support the video tag.
                </video>
            </div>
        </section>

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