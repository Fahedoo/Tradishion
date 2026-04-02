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

    public function registerUser($nom, $email, $password, $birth_date, $origins = []) {
        $stmt = $this->pdo->prepare("SELECT id_user FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) { return "Cette adresse email est déjà utilisée."; }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("INSERT INTO users (display_name, email, password_hash, birth_date) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nom, $email, $hash, $birth_date]);
            $userId = $this->pdo->lastInsertId();

            if (!empty($origins)) {
                $stmtOrigin = $this->pdo->prepare("INSERT INTO user_origins (id_user, country_code) VALUES (?, ?)");
                foreach ($origins as $code) {
                    if (strlen($code) == 2) {
                        $stmtOrigin->execute([$userId, strtoupper($code)]);
                    }
                }
            }
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return "Une erreur est survenue lors de l'inscription.";
        }
    }

    public function getFeedPosts($countryCode = 'all') {
        $sql = "SELECT DISTINCT p.id_post, u.display_name as author, u.avatar_url, p.title, p.body as content, p.created_at, med.file_path as image_url 
                FROM posts p 
                JOIN users u ON p.id_author = u.id_user 
                LEFT JOIN user_origins uo ON u.id_user = uo.id_user
                LEFT JOIN post_media pm ON p.id_post = pm.id_post
                LEFT JOIN media med ON pm.id_media = med.id_media ";
        
        if ($countryCode !== 'all' && !empty($countryCode)) {
            $sql .= " WHERE uo.country_code = ? ";
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

    public function createPost($id_author, $content, $fileInfo = null, $id_hashtag = null) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO posts (id_author, body, id_hashtag) VALUES (?, ?, ?)");
            $stmt->execute([$id_author, $content, $id_hashtag]);
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

    public function getHashtags($idLang = 1) {
        $stmt = $this->pdo->prepare("SELECT h.id_hashtag, hi.label FROM hashtags h JOIN hashtag_i18n hi ON h.id_hashtag = hi.id_hashtag WHERE hi.id_lang = ? ORDER BY hi.label ASC");
        $stmt->execute([$idLang]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function joinEvent($id_user, $id_event) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO event_participants (id_event, id_user, status) VALUES (?, ?, 'accepted') ON DUPLICATE KEY UPDATE status = 'accepted'");
            return $stmt->execute([$id_event, $id_user]);
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
        $stmt = $this->pdo->prepare("SELECT id_user, display_name, bio_free, avatar_url FROM users WHERE id_user = ?");
        $stmt->execute([$id_user]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $user['origins'] = $this->getUserOrigins($id_user);
        }
        return $user;
    }

    public function getUserOrigins($userId) {
        $stmt = $this->pdo->prepare("SELECT country_code FROM user_origins WHERE id_user = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getRecommendations($id_user) {
        $query = "SELECT id_user, display_name, bio_free, location, avatar_url FROM users WHERE id_user != ? AND id_user NOT IN (SELECT id_followed FROM connections WHERE id_follower = ?) LIMIT 3";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$id_user, $id_user]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateProfile($id_user, $displayName, $bio, $origins = []) {
        $this->pdo->beginTransaction();
        try {
            $query = "UPDATE users SET display_name = ?, bio_free = ? WHERE id_user = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$displayName, $bio, $id_user]);

            // Sync origins
            $stmtDel = $this->pdo->prepare("DELETE FROM user_origins WHERE id_user = ?");
            $stmtDel->execute([$id_user]);

            if (!empty($origins)) {
                $stmtIns = $this->pdo->prepare("INSERT INTO user_origins (id_user, country_code) VALUES (?, ?)");
                foreach ($origins as $code) {
                    if (strlen($code) == 2) {
                        $stmtIns->execute([$id_user, strtoupper($code)]);
                    }
                }
            }
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
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

    // --- NOUVEAU : Récupération dynamique des événements pour l'agenda ---
    public function getEventsForUser($userId) {
        // On récupère les événements publics, ceux créés par l'utilisateur, ou ceux partagés par ses relations
        $query = "SELECT e.id_event, e.id_organizer, e.title, e.start_time, e.end_time, e.visibility, e.description, DATE(e.start_time) as event_date,
                         IF((SELECT COUNT(*) FROM event_participants ep WHERE ep.id_event = e.id_event AND ep.id_user = ?) > 0, 1, 0) as is_participating
                  FROM events e 
                  WHERE e.visibility = 'public' 
                     OR e.id_organizer = ? 
                     OR (e.visibility = 'shared' AND (
                         e.id_organizer IN (SELECT id_followed FROM connections WHERE id_follower = ? AND status = 'accepted')
                         OR 
                         e.id_organizer IN (SELECT id_follower FROM connections WHERE id_followed = ? AND status = 'accepted')
                     ))";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$userId, $userId, $userId, $userId, $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>