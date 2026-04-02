<?php

class ViewLandingPage extends View {
    
    public function __construct() {
        $this->pageTitle = "Tradishion - Legacy comes to life";
        $this->bodyClass = "landing-page";
    }

    protected function getBodyContent() {
        ob_start();
        ?>
        <section class="hero-video-section">
            <video autoplay muted loop class="hero-video">
                <source src="assets/videos/HeroBackground.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <div class="hero-overlay">
                <div class="hero-content-left">
                    <h1 class="logo-text">Tradishion</h1>
                    <h1>Legacy comes to life</h1>
                    <p class="motto">Discover the stories and learn about the hidden craftsmanship behind traditional clothing across generations.</p>
                    <a href="?page=login" class="btn btn-signin">Log in</a>
                </div>
            </div>
        </section>

        <section class="features-section">
            <div class="container features-grid">
                <div class="feature-card">
                    <div class="feature-icon" aria-hidden="true">&#10024;</div>
                    <h3>Learn about traditional know-how</h3>
                    <p>Dive deep into the roots of unique craftsmanship and discover the methods passed down through generations.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" aria-hidden="true">&#128214;</div>
                    <h3>Discover the legacies</h3>
                    <p>Explore an immersive gallery of cultural heritage, traditional garments, and historical textiles.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" aria-hidden="true">&#127760;</div>
                    <h3>Link with people around the world</h3>
                    <p>Connect with a global community passionate about preserving and sharing their vibrant cultural identities.</p>
                </div>
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