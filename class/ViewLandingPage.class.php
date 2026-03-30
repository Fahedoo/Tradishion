<?php

class ViewLandingPage extends View {
    
    public function __construct() {
        // On peut personnaliser le titre de la page pour cette vue spécifiquement
        $this->pageTitle = "Tradishion - Legacy comes to life";
    }

    protected function getBodyContent() {
        ob_start();
        ?>
        <!-- Hero Section with Background Video -->
        <section class="hero-video-section">
            <video autoplay muted loop class="hero-video">
                <source src="../../assets/video/hero-bg.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <div class="hero-overlay">
                <div class="hero-content-left">
                    <h1 class="logo-text">Tradishion</h1>
                    <p class="motto">Legacy comes to life</p>
                    <a href="?page=login" class="btn btn-signin">Log in</a>
                </div>
            </div>
        </section>

        <!-- 3 Main Features Section -->
        <section class="features-section">
            <div class="container features-grid">
                <div class="feature-card">
                    <h3>Learn about traditional know-how</h3>
                    <p>Dive deep into the roots of unique craftsmanship and discover the methods passed down through generations.</p>
                </div>
                <div class="feature-card">
                    <h3>Discover the legacies</h3>
                    <p>Explore an immersive gallery of cultural heritage, traditional garments, and historical textiles.</p>
                </div>
                <div class="feature-card">
                    <h3>Link with people around the world</h3>
                    <p>Connect with a global community passionate about preserving and sharing their vibrant cultural identities.</p>
                </div>
            </div>
        </section>

        <!-- Images Preview Section -->
        <section class="images-preview-section">
            <div class="image-row">
                <img src="../../assets/images/fabric1.jpg" alt="Traditional fabric patterns">
                <img src="../../assets/images/people1.jpg" alt="People wearing traditional clothing">
                <img src="../../assets/images/fabric2.jpg" alt="Textile weaving process">
            </div>
        </section>
        <?php
        return ob_get_clean();
    }
}
?>
