<?php

class ViewCGU extends View {

    public function __construct() {
        $this->pageTitle = "Terms of Service (CGU) - TradiShion";
    }

    protected function getBodyContent() {
        ob_start();
        ?>
        <main class="legal-container">
            <section class="legal-content">
                <h1>USER AGREEMENT AND TERMS OF SERVICE (CGU)</h1>
                
                <p class="legal-intro">Welcome to Tradishion!</p>
                <p>Tradishion is a cross-generational platform created by <strong>Tradishion Corp</strong>, dedicated to sharing skills, experiences, and life histories related to traditional fashion around the world.</p>
                <p>By ticking the acceptance box before subscribing to the platform, you officially agree to the following User Agreement and Terms of Service.</p>

                <div class="legal-section">
                    <h2>1. Purpose of the Platform</h2>
                    <p>Tradishion aims to bridge the gap between different generations and cultures by allowing users to find others based on mutual interests, get in contact, and organize online meetings. Access to the private section of this network is strictly reserved for users who have subscribed to the platform and explicitly agreed to these legal conditions.</p>
                </div>

                <div class="legal-section">
                    <h2>2. Registration and Account Usage</h2>
                    <p>To use the private features, such as the internal messaging system, the user database, and the personal agenda, you must create an account. You agree to provide accurate information regarding the languages you speak, your geographical location, and the traditional fashion experiences you wish to share.</p>
                </div>

                <div class="legal-section">
                    <h2>3. Code of Conduct and Good Practices</h2>
                    <p>Tradishion is a space for cultural celebration and respectful cross-generational exchange. As a user, you agree that all discussions and contents you share must strictly comply with your country's regulations.</p>
                    <p>Furthermore, users must adhere to our strict rules of good practice. Tradishion enforces a zero-tolerance policy regarding the following:</p>
                    <ul>
                        <li>• Calling for hate, violence, or murder.</li>
                        <li>• Expressing racist, xenophobic, or sexist views.</li>
                        <li>• Harassment, bullying, or abusive behavior toward any other user.</li>
                        <li>• Disrespecting the cultural heritage or traditional attire shared by others.</li>
                    </ul>
                </div>

                <div class="legal-section">
                    <h2>4. Internal Messaging and Online Meetings</h2>
                    <p>Our platform provides an internal messaging system allowing you to exchange messages without needing to disclose your e-mail address initially. The delivery of messages is submitted to social networking constraints (e.g., connection requests must be accepted to fully communicate). Users are responsible for their behavior during online meetings and text chats.</p>
                </div>

                <div class="legal-section">
                    <h2>5. Content and Intellectual Property</h2>
                    <p>Users retain ownership of the personal content they share on Tradishion (texts, photos of traditional clothing, videos of sewing skills, etc.). However, by posting content publicly or sharing it within the network, you grant Tradishion Corp the right to display it within the platform's ecosystem. You must ensure you have the rights to any image or video you upload.</p>
                </div>

                <div class="legal-section">
                    <h2>6. Moderation and Account Termination</h2>
                    <p>Tradishion Corp reserves the right to moderate public and shared events. If a user violates these Terms of Service, especially the Code of Conduct, we reserve the right to immediately suspend or permanently delete their account without prior notice.</p>
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