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

    public function getFeedPosts($countryCode = 'all', $myId = 0) {
        $sql = "SELECT p.id_post, p.id_author, u.display_name as author, u.avatar_url, u.location, p.title, p.body as content, p.created_at, med.file_path as image_url,
                (SELECT COUNT(*) FROM post_likes WHERE id_post = p.id_post) as likes_count,
                (SELECT COUNT(*) FROM post_likes WHERE id_post = p.id_post AND id_user = ?) as user_liked
                FROM posts p 
                JOIN users u ON p.id_author = u.id_user 
                LEFT JOIN post_media pm ON p.id_post = pm.id_post
                LEFT JOIN media med ON pm.id_media = med.id_media ";
        
        $params = [$myId];

        if ($countryCode !== 'all' && !empty($countryCode)) {
            $sql .= " WHERE u.location = ? ";
            $params[] = $countryCode;
        }
        $sql .= " ORDER BY p.created_at DESC LIMIT 20";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function toggleLike($id_post, $id_user) {
        $stmt = $this->pdo->prepare("SELECT * FROM post_likes WHERE id_post = ? AND id_user = ?");
        $stmt->execute([$id_post, $id_user]);
        if ($stmt->fetch()) {
            $del = $this->pdo->prepare("DELETE FROM post_likes WHERE id_post = ? AND id_user = ?");
            $del->execute([$id_post, $id_user]);
            return ['status' => 'unliked'];
        } else {
            $ins = $this->pdo->prepare("INSERT INTO post_likes (id_post, id_user) VALUES (?, ?)");
            $ins->execute([$id_post, $id_user]);
            return ['status' => 'liked'];
        }
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
        $stmt = $this->pdo->prepare("SELECT id_user, display_name, location, bio_free, avatar_url, banner_url, contact_email, website, social_link, skills, interests FROM users WHERE id_user = ?");
        $stmt->execute([$id_user]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProfile($id_user, $displayName, $location, $bio, $contactEmail, $website, $socialLink, $skills, $interests) {
        $query = "UPDATE users SET display_name = ?, location = ?, bio_free = ?, contact_email = ?, website = ?, social_link = ?, skills = ?, interests = ? WHERE id_user = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$displayName, $location, $bio, $contactEmail, $website, $socialLink, $skills, $interests, $id_user]);
    }

    public function getRecommendations($id_user) {
        $query = "SELECT id_user, display_name, bio_free, location, avatar_url FROM users WHERE id_user != ? AND id_user NOT IN (SELECT id_followed FROM connections WHERE id_follower = ?) LIMIT 3";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$id_user, $id_user]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    public function updateBanner($id_user, $fileInfo) {
        $ext = pathinfo($fileInfo['name'], PATHINFO_EXTENSION);
        $uuid = uniqid() . '.' . $ext;
        $uploadDir = 'assets/images/';
        
        if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
        $filePath = $uploadDir . $uuid;

        if (move_uploaded_file($fileInfo['tmp_name'], $filePath)) {
            $stmt = $this->pdo->prepare("INSERT INTO media (id_uploader, file_name, original_name, file_path, media_type, is_public) VALUES (?, ?, ?, ?, 'image', 1)");
            $stmt->execute([$id_user, $uuid, $fileInfo['name'], $filePath]);
            
            $stmt2 = $this->pdo->prepare("UPDATE users SET banner_url = ? WHERE id_user = ?");
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

    public function getConnectionStatus($user1, $user2) {
        $stmt = $this->pdo->prepare("SELECT status, id_follower FROM connections WHERE (id_follower = ? AND id_followed = ?) OR (id_follower = ? AND id_followed = ?)");
        $stmt->execute([$user1, $user2, $user2, $user1]);
        $conn = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($conn) {
            return ['status' => $conn['status'], 'is_follower' => ($conn['id_follower'] == $user1)];
        }
        return false;
    }

    public function getUserPosts($userId, $myId = 0) {
        $sql = "SELECT p.id_post, p.id_author, u.display_name as author, u.avatar_url, u.location, p.title, p.body as content, p.created_at, med.file_path as image_url,
                (SELECT COUNT(*) FROM post_likes WHERE id_post = p.id_post) as likes_count,
                (SELECT COUNT(*) FROM post_likes WHERE id_post = p.id_post AND id_user = ?) as user_liked
                FROM posts p 
                JOIN users u ON p.id_author = u.id_user 
                LEFT JOIN post_media pm ON p.id_post = pm.id_post
                LEFT JOIN media med ON pm.id_media = med.id_media 
                WHERE p.id_author = ? 
                ORDER BY p.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$myId, $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- GESTION DES EVENEMENTS & INVITATIONS ---
    public function getTodayPublicEvents() {
        $query = "SELECT e.*, u.display_name as organizer_name, u.avatar_url, med.file_path as image_url
                  FROM events e
                  JOIN users u ON e.id_organizer = u.id_user
                  LEFT JOIN event_media em ON e.id_event = em.id_event AND em.role = 'cover'
                  LEFT JOIN media med ON em.id_media = med.id_media
                  WHERE e.visibility = 'public' AND DATE(e.start_time) = CURDATE()
                  ORDER BY e.start_time ASC";
        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // MISE À JOUR : On récupère NOS événements et ceux où on a DIT OUI !
    public function getMyOrganizedEvents($userId) {
        $query = "SELECT e.*, med.file_path as image_url, u.display_name as organizer_name
                  FROM events e
                  LEFT JOIN event_media em ON e.id_event = em.id_event AND em.role = 'cover'
                  LEFT JOIN media med ON em.id_media = med.id_media
                  LEFT JOIN users u ON e.id_organizer = u.id_user
                  WHERE e.id_organizer = ? 
                     OR e.id_event IN (SELECT id_event FROM event_participants WHERE id_user = ? AND status = 'accepted')
                  ORDER BY e.start_time DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$userId, $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function inviteUserToEvent($id_event, $id_user) {
        try {
            $stmt = $this->pdo->prepare("INSERT IGNORE INTO event_participants (id_event, id_user, status) VALUES (?, ?, 'invited')");
            $stmt->execute([$id_event, $id_user]);
            return true;
        } catch(Exception $e) {
            return false;
        }
    }

    public function getEventsForUser($userId) {
        $query = "SELECT e.id_event, e.title, e.start_time, e.end_time, e.visibility, e.description, DATE(e.start_time) as event_date 
                  FROM events e 
                  LEFT JOIN event_participants ep ON e.id_event = ep.id_event
                  WHERE e.visibility = 'public' 
                     OR e.id_organizer = ? 
                     OR ep.id_user = ? 
                     OR (e.visibility = 'shared' AND (
                         e.id_organizer IN (SELECT id_followed FROM connections WHERE id_follower = ? AND status = 'accepted')
                         OR 
                         e.id_organizer IN (SELECT id_follower FROM connections WHERE id_followed = ? AND status = 'accepted')
                     ))";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$userId, $userId, $userId, $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPendingEventInvitations($userId) {
        $query = "SELECT e.*, u.display_name as organizer_name, ep.status 
                  FROM events e 
                  JOIN event_participants ep ON e.id_event = ep.id_event 
                  JOIN users u ON e.id_organizer = u.id_user 
                  WHERE ep.id_user = ? AND ep.status = 'invited'
                  ORDER BY e.start_time ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function respondToEventInvite($id_event, $id_user, $status) {
        $query = "UPDATE event_participants SET status = ? WHERE id_event = ? AND id_user = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$status, $id_event, $id_user]);
    }
}
?>