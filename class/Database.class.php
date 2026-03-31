<?php

class Database {
    // Database credentials
    private $host = 'localhost';
    private $db   = 'tradishion';
    private $user = 'tradishion';
    private $pass = '!*tradishion2026';
    
    // PHP Data Object (PDO) instance
    private $pdo;

    public function __construct() {
        try {
            // Establish the database connection
            $this->pdo = new PDO("mysql:host={$this->host};dbname={$this->db};charset=utf8", $this->user, $this->pass);
            // Set error mode to exception to handle SQL errors properly
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Kill the script if the connection fails
            die("Database connection error: " . $e->getMessage());
        }
    }

    // ---------- AUTHENTICATION METHODS ----------

<<<<<<< HEAD
    public function authenticateUser($email, $password) {
=======
    /**
     * Authenticates a user and creates a session if credentials are correct
     */
    public function authenticateUser($email, $password) {
        // Prepare the SQL statement to find the user by email
>>>>>>> 4ebf829a978694ca016c9b8d60ba9e45bb8fa771
        $stmt = $this->pdo->prepare("SELECT id_user, password_hash FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

<<<<<<< HEAD
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['id_user'] = $user['id_user'];
            return true; 
        }
        return false; 
    }

    public function registerUser($nom, $email, $password) {
        $stmt = $this->pdo->prepare("SELECT id_user FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return "Cette adresse email est déjà utilisée."; 
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare("INSERT INTO users (display_name, email, password_hash) VALUES (?, ?, ?)");
        if ($stmt->execute([$nom, $email, $hash])) {
            return true; 
        }
        
        return "Une erreur est survenue lors de l'inscription."; 
    }

    // ---------- FEED & MAP METHODS ----------

    /**
     * Récupère les posts pour le fil d'actualité et la carte
     * Si $countryCode vaut 'all', on renvoie les derniers posts globaux.
     */
    public function getFeedPosts($countryCode = 'all') {
        if ($countryCode === 'all' || empty($countryCode)) {
            $query = "SELECT u.display_name as author, u.location, p.title, p.body as content, h.slug as tag 
                      FROM posts p
                      JOIN users u ON p.id_author = u.id_user
                      LEFT JOIN hashtags h ON p.id_hashtag = h.id_hashtag
                      ORDER BY p.created_at DESC LIMIT 10";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
        } else {
            $query = "SELECT u.display_name as author, u.location, p.title, p.body as content, h.slug as tag 
                      FROM posts p
                      JOIN users u ON p.id_author = u.id_user
                      LEFT JOIN hashtags h ON p.id_hashtag = h.id_hashtag
                      WHERE u.location = ?
                      ORDER BY p.created_at DESC";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$countryCode]);
        }
=======
        // Verify the provided password against the stored hash
        if ($user && password_verify($password, $user['password_hash'])) {
            // Save the user ID in the session variables
            $_SESSION['id_user'] = $user['id_user'];
            return true; // Authentication successful
        }
        return false; // Authentication failed
    }

    /**
     * Registers a new user into the database securely
     */
    public function registerUser($nom, $email, $password) {
        // 1. Check if the email is already taken
        $stmt = $this->pdo->prepare("SELECT id_user FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return "Cette adresse email est déjà utilisée."; // Email exists
        }

        // 2. Hash the password (CRITICAL FOR SECURITY)
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // 3. Insert the new user into the database
        // (Using display_name to match the map query requirements)
        $stmt = $this->pdo->prepare("INSERT INTO users (display_name, email, password_hash) VALUES (?, ?, ?)");
        if ($stmt->execute([$nom, $email, $hash])) {
            return true; // Registration successful
        }
        
        return "Une erreur est survenue lors de l'inscription."; // Registration failed
    }

    // ---------- OTHER METHODS ----------

    /**
     * Retrieves posts for the interactive map based on the user's country
     */
    public function getPostsByCountry($countryCode) {
        $query = "SELECT u.display_name as author, p.title, p.body as content, h.tag_name as tag 
                  FROM posts p
                  JOIN users u ON p.id_author = u.id_user
                  LEFT JOIN user_hashtags uh ON u.id_user = uh.id_user
                  LEFT JOIN hashtags h ON uh.id_hashtag = h.id_hashtag
                  WHERE u.location = ?";
                  
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$countryCode]);
>>>>>>> 4ebf829a978694ca016c9b8d60ba9e45bb8fa771
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>