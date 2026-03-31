<?php

class ViewPrivacy extends View {

    public function __construct() {
        $this->pageTitle = "Privacy Policy - TradiShion";
    }

    protected function getBodyContent() {
        ob_start();
        ?>
        <main class="legal-container">
            <section class="legal-content">
                <h1>PRIVACY POLICY</h1>
                
                <div class="legal-section">
                    <h2>1. Introduction and GDPR Compliance</h2>
                    <p>Welcome to the privacy policy of the “Tradishion” platform. Because our private network handles personal information, it strictly complies with the General Data Protection Regulation (GDPR) and national and international regulations concerning databases. This document explains how Tradishion Corp collects, uses, and protects your personal data.</p>
                </div>

                <div class="legal-section">
                    <h2>2. Data Controller</h2>
                    <p>The data controller for this platform is the <strong>Tradishion Corp</strong> team. The data is processed strictly for the academic purposes of the International Open Bidding Project 2026.</p>
                </div>

                <div class="legal-section">
                    <h2>3. What Data Do We Collect?</h2>
                    <p>To allow you to use the private section of our network, we collect the following personal information when you create an account:</p>
                    <ul>
                        <li><strong>Account Credentials:</strong> Your e-mail address and password used for the authentication screen.</li>
                        <li><strong>Profile Information:</strong> We require registered users to mention the languages they speak, their geographical location, and the experiences they may share.</li>
                        <li><strong>Interaction Data:</strong> Data related to your social relationships (connections), your personal agenda (private, shared, and public events), and internal messages.</li>
                    </ul>
                </div>

                <div class="legal-section">
                    <h2>4. How Do We Use Your Data?</h2>
                    <p>We use your data exclusively to operate the Tradishion platform and connect generations through traditional fashion. Specifically:</p>
                    <ul>
                        <li><strong>Search and Matchmaking:</strong> To populate a searchable database and map to help users find others based on mutual interests.</li>
                        <li><strong>Communication:</strong> Your e-mail address is kept strictly confidential; users communicate via an internal system without disclosing their personal e-mail.</li>
                    </ul>
                </div>

                <div class="legal-section">
                    <h2>5. Data Storage and Security</h2>
                    <p>Your personal data is stored securely on our academic hosting environment (LAMP server at http://international.iut-bobigny.univ-paris13.fr) using <strong>MariaDB</strong> and <strong>PHP</strong>. Technical security is ensured through individual management of server credentials.</p>
                </div>

                <div class="legal-section">
                    <h2>6. Visibility and Data Sharing</h2>
                    <ul>
                        <li><strong>Public Section:</strong> No personal data is required for access.</li>
                        <li><strong>Private Section:</strong> Profile info is only visible to other registered users.</li>
                        <li><strong>Agenda Privacy:</strong> Private events appear only as blocked slots to others. Shared events are visible only to the concerned connected users.</li>
                    </ul>
                </div>

                <div class="legal-section">
                    <h2>7. Your GDPR Rights</h2>
                    <p>Under the GDPR, you have the right to:</p>
                    <ul>
                        <li>• Access, Rectify, or Erase (right to be forgotten) your data.</li>
                        <li>• Restrict or Object to the processing of your data.</li>
                    </ul>
                    <p>To exercise these rights, please contact the Tradishion Corp team through the internal messaging system.</p>
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