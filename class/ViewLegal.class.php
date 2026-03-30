<?php

class ViewLegal extends View {

    public function __construct() {
        $this->pageTitle = "Legal Information - TradiShion";
    }

    protected function getBodyContent() {
        ob_start();
        ?>
        <main class="legal-container">
            <section class="legal-content">
                <h1>LEGAL INFORMATION</h1>
                <p class="legal-intro">
                    In accordance with the provisions of the French Law No. 2004-575 of June 21, 2004, 
                    regarding confidence in the digital economy (LCEN), users of the "Tradishion" website 
                    are informed of the identity of the various parties involved in its creation and monitoring.
                </p>

                <div class="legal-section">
                    <h2>1. Site Publisher</h2>
                    <p>The present website, accessible at <strong>http://international.iut-bobigny.univ-paris13.fr</strong> (the "Site"), is a student prototype developed as part of the International Open Bidding Project (IOBC) 2026.</p>
                    <p><strong>Published by:</strong> Tradishion Corp</p>
                    <p><strong>Status:</strong> International student team (Bobigny Institute of Technology - Sorbonne-Paris-Nord University, University of Tirana, Hanoi University of Science).</p>
                    <p><strong>Contact:</strong> contact@tradishion.com</p>
                </div>

                <div class="legal-section">
                    <h2>2. Hosting</h2>
                    <p>The Site is hosted by our academic institution:</p>
                    <p><strong>Host:</strong> Bobigny Institute of Technology, Sorbonne-Paris-Nord University</p>
                    <p><strong>Address:</strong> 1 Rue de Chablis, 93000 Bobigny, France</p>
                    <p><strong>Server Management:</strong> The team's server access and credentials are individually managed by Raphael Piechocki.</p>
                </div>

                <div class="legal-section">
                    <h2>3. Publication Director</h2>
                    <p><strong>Director of Publication:</strong> Fahed Ismaili Alaoui (Spokesperson for Tradishion Corp).</p>
                </div>

                <div class="legal-section">
                    <h2>4. Intellectual Property</h2>
                    <p>All content present on this site (texts, images, graphics, source code, logos) is the exclusive property of the Tradishion Corp team, unless otherwise stated. Because this is a university project prototype, any third-party resources used on the platform are utilized strictly for educational purposes and non-profit demonstration. Any reproduction is prohibited without prior authorization.</p>
                </div>

                <div class="legal-section">
                    <h2>5. Personal Data & Privacy</h2>
                    <p>This platform acts as a private social network and complies with the General Data Protection Regulation (GDPR). The data collected (such as languages spoken, geographical location, and fashion-related experiences) is used solely to allow users to find each other based on mutual interests and organize online meetings.</p>
                </div>

                <div class="legal-section">
                    <h2>6. Credits</h2>
                    <ul>
                        <li><strong>Design & Audiovisual:</strong> Sarah Ould Mohammed, Emmanuel Kpatinde, Jonida Paja, Lusiana Dokaj</li>
                        <li><strong>Development:</strong> Fahed Ismaili Alaoui, Raphaël Piechocki, Kaïna Slimani, Mai Hoàng Đức</li>
                        <li><strong>Communication Strategy:</strong> Amaury Fabre, Kylian Giard Ouadifi</li>
                        <li><strong>Technologies used:</strong> PHP, MariaDB, HTML, CSS, Javascript</li>
                    </ul>
                </div>
            </section>
        </main>

            <footer class="login-footer">
                <a href="index.php">← Retour au site public</a>
            </footer>
                    
        <?php
        return ob_get_clean();
    }
}