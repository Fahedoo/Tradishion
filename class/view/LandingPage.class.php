<?php

class LandingPage {
    
    public function __toString() {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Tradishion - Legacy comes to life</title>
            <link rel="stylesheet" href="style/style.css">
        </head>
        <body>
            <!-- Header -->
            <header class="main-header">
                <div class="header-logo">
                    <!-- Placeholder for the logo image if you have one -->
                    <span class="logo-text-small">Tradishion</span>
                </div>
                <div class="header-controls">
                    <div class="header-languages">
                        <select name="language" id="language-select" class="lang-select">
                            <option value="en">English</option>
                            <option value="sq">Albanese</option>
                            <option value="fr">French</option>
                            <option value="vi">Vietnamese</option>
                        </select>
                    </div>
                    <div class="header-auth">
                        <a href="login.php" class="btn btn-login">Log In</a>
                        <a href="signin.php" class="btn btn-signin">Sign In</a>
                    </div>
                </div>
            </header>

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
                        <a href="login.php" class="btn btn-signin">Sign In</a>
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

            <!-- Footer -->
            <footer class="footer">
                <div class="footer-content">
                    <ul class="footer-links">
                        <li><a href="#privacy">Privacy policy</a></li>
                        <li><a href="#qa">Q&A</a></li>
                        <li><a href="#about">About us</a></li>
                        <li><a href="#legal">Legal mentions</a></li>
                        <li><a href="#contact">Contact</a></li>
                        <li><a href="#gcu">GCU</a></li>
                    </ul>
                    <p>&copy; <?php echo date('Y'); ?> Tradishion. All rights reserved.</p>
                </div>
            </footer>

        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}
?>