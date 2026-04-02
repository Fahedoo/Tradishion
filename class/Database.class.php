<?php

class Database {
    private $host = 'localhost';
    private $db   = 'tradishion';
    private $user = 'tradishion';
    private $pass = '!*tradishion2026';
    
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO("mysql:host={$this->host};dbname={$this->db};charset=utf8", $this->user, $this->pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection error: " . $e->getMessage());
        }
    }

    public function authenticateUser($email, $password) {
        $stmt = $this->pdo->prepare("SELECT id_user, password_hash FROM users WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['id_user'] = $user['id_user'];
            return true; 
        }
        return false; 
    }

    public function registerUser($nom, $email, $password) {
        $stmt = $this->pdo->prepare("SELECT id_user FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) { return "Cette adresse email est déjà utilisée."; }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (display_name, email, password_hash) VALUES (?, ?, ?)");
        if ($stmt->execute([$nom, $email, $hash])) { return true; }
        return "Une erreur est survenue lors de l'inscription."; 
    }

    public function getFeedPosts($countryCode = 'all') {
        // AJOUT DE p.id_author POUR LES LIENS VERS LE PROFIL
        $sql = "SELECT p.id_post, p.id_author, u.display_name as author, u.avatar_url, u.location, p.title, p.body as content, p.created_at, med.file_path as image_url 
                FROM posts p 
                JOIN users u ON p.id_author = u.id_user 
                LEFT JOIN post_media pm ON p.id_post = pm.id_post
                LEFT JOIN media med ON pm.id_media = med.id_media ";
        
        if ($countryCode !== 'all' && !empty($countryCode)) {
            $sql .= " WHERE u.location = ? ";
        }
        $sql .= " ORDER BY p.created_at DESC LIMIT 20";
        
        $stmt = $this->pdo->prepare($sql);
        if ($countryCode !== 'all' && !empty($countryCode)) {
            $stmt->execute([$countryCode]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createPost($id_author, $content, $fileInfo = null) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO posts (id_author, body) VALUES (?, ?)");
            $stmt->execute([$id_author, $content]);
            $idPost = $this->pdo->lastInsertId();

            if ($fileInfo && $fileInfo['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($fileInfo['name'], PATHINFO_EXTENSION);
                $uuid = uniqid() . '.' . $ext;
                $uploadDir = 'assets/images/';
                if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
                $filePath = $uploadDir . $uuid;

                if (move_uploaded_file($fileInfo['tmp_name'], $filePath)) {
                    $stmtMedia = $this->pdo->prepare("INSERT INTO media (id_uploader, file_name, original_name, file_path, media_type, is_public) VALUES (?, ?, ?, ?, 'image', 1)");
                    $stmtMedia->execute([$id_author, $uuid, $fileInfo['name'], $filePath]);
                    $idMedia = $this->pdo->lastInsertId();

                    $stmtPivot = $this->pdo->prepare("INSERT INTO post_media (id_post, id_media) VALUES (?, ?)");
                    $stmtPivot->execute([$idPost, $idMedia]);
                }
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getComments($id_post) {
        $query = "SELECT c.content, c.created_at, u.display_name as author, u.avatar_url 
                  FROM comments c
                  JOIN users u ON c.id_author = u.id_user
                  WHERE c.id_post = ?
                  ORDER BY c.created_at ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$id_post]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addComment($id_post, $id_author, $content) {
        try {
            $query = "INSERT INTO comments (id_post, id_author, content) VALUES (?, ?, ?)";
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute([$id_post, $id_author, $content]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function sendConnectionRequest($idFollower, $idFollowed) {
        $stmt = $this->pdo->prepare("SELECT * FROM connections WHERE (id_follower = ? AND id_followed = ?) OR (id_follower = ? AND id_followed = ?)");
        $stmt->execute([$idFollower, $idFollowed, $idFollowed, $idFollower]);
        if($stmt->fetch()) { return false; } 
        
        $query = "INSERT INTO connections (id_follower, id_followed, status) VALUES (?, ?, 'pending')";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$idFollower, $idFollowed]);
    }

    public function getPendingRequests($userId) {
        $query = "SELECT c.id_follower, u.display_name, u.bio_free, u.location, u.avatar_url FROM connections c JOIN users u ON c.id_follower = u.id_user WHERE c.id_followed = ? AND c.status = 'pending' ORDER BY c.created_at DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getConnections($userId) {
        $query = "SELECT u.id_user, u.display_name, u.bio_free, u.location, u.avatar_url FROM connections c JOIN users u ON (u.id_user = c.id_follower OR u.id_user = c.id_followed) WHERE (c.id_follower = ? OR c.id_followed = ?) AND c.status = 'accepted' AND u.id_user != ? ORDER BY c.updated_at DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$userId, $userId, $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function acceptConnection($idFollower, $idFollowed) {
        $stmt = $this->pdo->prepare("SELECT * FROM connections WHERE id_follower = ? AND id_followed = ?");
        $stmt->execute([$idFollower, $idFollowed]);
        if($stmt->fetch()) {
            $query = "UPDATE connections SET status = 'accepted' WHERE id_follower = ? AND id_followed = ?";
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute([$idFollower, $idFollowed]);
        } else {
            $query = "INSERT INTO connections (id_follower, id_followed, status) VALUES (?, ?, 'accepted')";
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute([$idFollower, $idFollowed]);
        }
    }

    public function rejectConnection($idFollower, $idFollowed) {
        $query = "DELETE FROM connections WHERE id_follower = ? AND id_followed = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$idFollower, $idFollowed]);
    }

    public function deleteConnection($user1, $user2) {
        $query = "DELETE FROM connections WHERE (id_follower = ? AND id_followed = ?) OR (id_follower = ? AND id_followed = ?)";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$user1, $user2, $user2, $user1]);
    }

    public function getChatHistory($user1, $user2) {
        $query = "SELECT m.id_sender, m.content, m.sent_at, med.file_path 
                  FROM messages m 
                  LEFT JOIN message_media mm ON m.id_message = mm.id_message 
                  LEFT JOIN media med ON mm.id_media = med.id_media 
                  WHERE (m.id_sender = ? AND m.id_receiver = ?) 
                     OR (m.id_sender = ? AND m.id_receiver = ?) 
                  ORDER BY m.sent_at ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$user1, $user2, $user2, $user1]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function canSendMessage($idSender, $idReceiver) {
        $queryConn = "SELECT status FROM connections WHERE (id_follower = ? AND id_followed = ?) OR (id_follower = ? AND id_followed = ?)";
        $stmtConn = $this->pdo->prepare($queryConn);
        $stmtConn->execute([$idSender, $idReceiver, $idReceiver, $idSender]);
        $conn = $stmtConn->fetch(PDO::FETCH_ASSOC);

        if ($conn && $conn['status'] === 'accepted') { return true; }

        $queryMsg = "SELECT id_message FROM messages WHERE id_sender = ? AND id_receiver = ? AND is_first_message = 1";
        $stmtMsg = $this->pdo->prepare($queryMsg);
        $stmtMsg->execute([$idSender, $idReceiver]);
        if ($stmtMsg->fetch()) { return false; }
        return true; 
    }

    public function sendMessage($idSender, $idReceiver, $content, $fileInfo = null) {
        if (!$this->canSendMessage($idSender, $idReceiver)) { return false; }
        $isFirst = 1;
        $stmtCheck = $this->pdo->prepare("SELECT id_message FROM messages WHERE id_sender = ? AND id_receiver = ?");
        $stmtCheck->execute([$idSender, $idReceiver]);
        if ($stmtCheck->fetch()) { $isFirst = 0; }

        try {
            $query = "INSERT INTO messages (id_sender, id_receiver, content, is_first_message) VALUES (?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$idSender, $idReceiver, $content, $isFirst]);
            $idMessage = $this->pdo->lastInsertId();

            if ($fileInfo && $fileInfo['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($fileInfo['name'], PATHINFO_EXTENSION);
                $uuid = uniqid() . '.' . $ext;
                $uploadDir = 'assets/images/';
                if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
                $filePath = $uploadDir . $uuid;

                if (move_uploaded_file($fileInfo['tmp_name'], $filePath)) {
                    $stmtMedia = $this->pdo->prepare("INSERT INTO media (id_uploader, file_name, original_name, file_path, media_type, is_public) VALUES (?, ?, ?, ?, 'image', 0)");
                    $stmtMedia->execute([$idSender, $uuid, $fileInfo['name'], $filePath]);
                    $idMedia = $this->pdo->lastInsertId();

                    $stmtPivot = $this->pdo->prepare("INSERT INTO message_media (id_message, id_media) VALUES (?, ?)");
                    $stmtPivot->execute([$idMessage, $idMedia]);
                }
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getUser($id_user) {
        $stmt = $this->pdo->prepare("SELECT id_user, display_name, location, bio_free, avatar_url FROM users WHERE id_user = ?");
        $stmt->execute([$id_user]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getRecommendations($id_user) {
        $query = "SELECT id_user, display_name, bio_free, location, avatar_url FROM users WHERE id_user != ? AND id_user NOT IN (SELECT id_followed FROM connections WHERE id_follower = ?) LIMIT 3";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$id_user, $id_user]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateProfile($id_user, $displayName, $location, $bio) {
        $query = "UPDATE users SET display_name = ?, location = ?, bio_free = ? WHERE id_user = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$displayName, $location, $bio, $id_user]);
    }

    public function searchUsers($term, $myId) {
        $query = "SELECT id_user, display_name, bio_free, location, avatar_url FROM users WHERE display_name LIKE ? AND id_user != ? LIMIT 5";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(["%$term%", $myId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateAvatar($id_user, $fileInfo) {
        $ext = pathinfo($fileInfo['name'], PATHINFO_EXTENSION);
        $uuid = uniqid() . '.' . $ext;
        $uploadDir = 'assets/images/';
        
        if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
        $filePath = $uploadDir . $uuid;

        if (move_uploaded_file($fileInfo['tmp_name'], $filePath)) {
            $stmt = $this->pdo->prepare("INSERT INTO media (id_uploader, file_name, original_name, file_path, media_type, is_public) VALUES (?, ?, ?, ?, 'image', 1)");
            $stmt->execute([$id_user, $uuid, $fileInfo['name'], $filePath]);
            
            $stmt2 = $this->pdo->prepare("UPDATE users SET avatar_url = ? WHERE id_user = ?");
            $stmt2->execute([$filePath, $id_user]);
            return true;
        }
        return false;
    }

    public function createEvent($id_organizer, $title, $date, $start_time, $end_time, $description, $visibility, $meeting_url, $fileInfo = null) {
        try {
            $start_datetime = $date . ' ' . (!empty($start_time) ? $start_time : '00:00') . ':00';
            $end_datetime = $date . ' ' . (!empty($end_time) ? $end_time : '23:59') . ':00';

            $query = "INSERT INTO events (id_organizer, title, description, visibility, start_time, end_time, meeting_url) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$id_organizer, $title, $description, $visibility, $start_datetime, $end_datetime, $meeting_url]);
            $idEvent = $this->pdo->lastInsertId();

            if ($fileInfo && $fileInfo['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($fileInfo['name'], PATHINFO_EXTENSION);
                $uuid = uniqid() . '.' . $ext;
                $uploadDir = 'assets/images/';
                if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
                $filePath = $uploadDir . $uuid;

                if (move_uploaded_file($fileInfo['tmp_name'], $filePath)) {
                    $stmtMedia = $this->pdo->prepare("INSERT INTO media (id_uploader, file_name, original_name, file_path, media_type, is_public) VALUES (?, ?, ?, ?, 'image', 1)");
                    $stmtMedia->execute([$id_organizer, $uuid, $fileInfo['name'], $filePath]);
                    $idMedia = $this->pdo->lastInsertId();

                    $stmtPivot = $this->pdo->prepare("INSERT INTO event_media (id_event, id_media, role) VALUES (?, ?, 'cover')");
                    $stmtPivot->execute([$idEvent, $idMedia]);
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getEventsForUser($userId) {
        $query = "SELECT e.id_event, e.title, e.start_time, e.end_time, e.visibility, e.description, DATE(e.start_time) as event_date 
                  FROM events e 
                  WHERE e.visibility = 'public' 
                     OR e.id_organizer = ? 
                     OR (e.visibility = 'shared' AND (
                         e.id_organizer IN (SELECT id_followed FROM connections WHERE id_follower = ? AND status = 'accepted')
                         OR 
                         e.id_organizer IN (SELECT id_follower FROM connections WHERE id_followed = ? AND status = 'accepted')
                     ))";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$userId, $userId, $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- NOUVEAU : STATUT DES RELATIONS ---
    public function getConnectionStatus($user1, $user2) {
        $stmt = $this->pdo->prepare("SELECT status, id_follower FROM connections WHERE (id_follower = ? AND id_followed = ?) OR (id_follower = ? AND id_followed = ?)");
        $stmt->execute([$user1, $user2, $user2, $user1]);
        $conn = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($conn) {
            return ['status' => $conn['status'], 'is_follower' => ($conn['id_follower'] == $user1)];
        }
        return false;
    }

    // --- NOUVEAU : RECUPERER LES CREATIONS D'UN UTILISATEUR ---
    public function getUserPosts($userId) {
        $sql = "SELECT p.id_post, p.id_author, u.display_name as author, u.avatar_url, u.location, p.title, p.body as content, p.created_at, med.file_path as image_url 
                FROM posts p 
                JOIN users u ON p.id_author = u.id_user 
                LEFT JOIN post_media pm ON p.id_post = pm.id_post
                LEFT JOIN media med ON pm.id_media = med.id_media 
                WHERE p.id_author = ? 
                ORDER BY p.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>