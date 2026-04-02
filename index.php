<?php
session_start();

error_reporting(E_ALL & ~E_DEPRECATED);
ini_set("display_errors", 1);

define("CHARGE_AUTOLOAD", true);
require_once("inc/poo.inc.php"); 

$errorMessageLogin = "";
$errorMessageSignup = "";

$page = isset($_GET["page"]) ? $_GET["page"] : "landing";

// ---------- POST FORMS PROCESSING ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database(); 

    if ($page === 'login' && isset($_POST['email']) && isset($_POST['password'])) {
        if ($db->authenticateUser(trim($_POST['email']), $_POST['password'])) {
            header("Location: index.php?page=dashboard");
            exit();
        } else {
            $errorMessageLogin = "Adresse email ou mot de passe incorrect.";
        }
    }

    if ($page === 'signup' && isset($_POST['nom']) && isset($_POST['email']) && isset($_POST['password'])) {
        $resultatInscription = $db->registerUser(trim($_POST['nom']), trim($_POST['email']), $_POST['password']);
        if ($resultatInscription === true) {
            $db->authenticateUser(trim($_POST['email']), $_POST['password']);
            header("Location: index.php?page=dashboard");
            exit();
        } else {
            $errorMessageSignup = $resultatInscription;
        }
    }
}

// ---------- ROUTING AND VIEWS DISPLAY ----------
switch($page) {
    case "login": $view = new ViewLogin($errorMessageLogin); echo $view; break;
    case "signup": $view = new ViewSignUp($errorMessageSignup); echo $view; break;
    case "legal": $view = new ViewLegal(); echo $view; break;
    case "privacy": $view = new ViewPrivacy(); echo $view; break;
    case "gcu": $view = new ViewCGU(); echo $view; break;

    case "dashboard":
        if (!isset($_SESSION['id_user'])) { header("Location: index.php?page=login"); exit(); }
        $db = new Database();
        $userData = $db->getUser($_SESSION['id_user']);
        $recommendations = $db->getRecommendations($_SESSION['id_user']);
        $view = new ViewDashboard($userData, $recommendations);
        echo $view;
        break;

    case "explorer":
        if (!isset($_SESSION['id_user'])) { header("Location: index.php?page=login"); exit(); }
        $db = new Database();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'new_post') {
            $content = $_POST['content'] ?? '';
            $fileInfo = (isset($_FILES['post_image']) && $_FILES['post_image']['error'] === UPLOAD_ERR_OK) ? $_FILES['post_image'] : null;
            if ($content !== '' || $fileInfo) {
                $db->createPost($_SESSION['id_user'], $content, $fileInfo);
            }
            header("Location: index.php?page=explorer");
            exit();
        }

        $userData = $db->getUser($_SESSION['id_user']);
        $recommendations = $db->getRecommendations($_SESSION['id_user']);
        $posts = $db->getFeedPosts('all', $_SESSION['id_user']); 
        $view = new ViewExplorer($userData, $recommendations, $posts);
        echo $view;
        break;

    case "profile":
        if (!isset($_SESSION['id_user'])) { header("Location: index.php?page=login"); exit(); }
        $db = new Database();
        $myId = $_SESSION['id_user'];
        
        if (isset($_GET['id']) && $_GET['id'] != $myId) {
            $targetId = intval($_GET['id']);
            $targetUser = $db->getUser($targetId);
            
            if (!$targetUser) { header("Location: index.php?page=dashboard"); exit(); }
            
            $currentUser = $db->getUser($myId);
            $connectionStatus = $db->getConnectionStatus($myId, $targetId);
            $userPosts = $db->getUserPosts($targetId, $myId);
            
            $view = new ViewUserProfile($currentUser, $targetUser, $connectionStatus, $userPosts);
            echo $view;
            break;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
            $db->updateProfile($myId, $_POST['display_name'], $_POST['location'], $_POST['bio_free']);
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $db->updateAvatar($myId, $_FILES['avatar']);
            }
            header("Location: index.php?page=profile");
            exit();
        }
        $userData = $db->getUser($myId);
        $recommendations = $db->getRecommendations($myId);
        $view = new ViewProfile($userData, $recommendations);
        echo $view;
        break;

    case "network":
        if (!isset($_SESSION['id_user'])) { header("Location: index.php?page=login"); exit(); }
        $db = new Database();
        $userData = $db->getUser($_SESSION['id_user']);
        $view = new ViewNetwork($userData);
        echo $view;
        break;

    case "messages":
        if (!isset($_SESSION['id_user'])) { header("Location: index.php?page=login"); exit(); }
        $db = new Database();
        $userData = $db->getUser($_SESSION['id_user']);
        $view = new ViewMessages($userData);
        echo $view;
        break;

    case "logout":
        session_destroy();
        header("Location: index.php");
        exit();
        break;

    // --- ROUTES API ---
    case "api_posts":
        ob_clean(); header('Content-Type: application/json');
        $db = new Database();
        $country = isset($_GET['country']) ? $_GET['country'] : 'all';
        echo json_encode($db->getFeedPosts($country, $_SESSION['id_user']));
        exit();
        break;

    // NOUVELLE ROUTE : Like de post
    case "api_like_post":
        ob_clean(); header('Content-Type: application/json');
        if(isset($_SESSION['id_user']) && isset($_POST['id_post'])) {
            $db = new Database();
            echo json_encode($db->toggleLike($_POST['id_post'], $_SESSION['id_user']));
        } else { echo json_encode(['status' => 'error']); }
        exit();
        break;

    case "api_send_request":
        ob_clean(); header('Content-Type: application/json');
        if(isset($_SESSION['id_user']) && isset($_POST['id_followed'])) {
            $db = new Database();
            $success = $db->sendConnectionRequest($_SESSION['id_user'], $_POST['id_followed']);
            echo json_encode(['success' => $success]);
        } else { echo json_encode(['success' => false]); }
        exit();
        break;

    case "api_accept":
        ob_clean(); header('Content-Type: application/json');
        if(isset($_SESSION['id_user']) && isset($_POST['id_follower'])) {
            $db = new Database();
            $success = $db->acceptConnection($_POST['id_follower'], $_SESSION['id_user']);
            echo json_encode(['success' => $success]);
        } else { echo json_encode(['success' => false]); }
        exit();
        break;

    case "api_reject":
        ob_clean(); header('Content-Type: application/json');
        if(isset($_SESSION['id_user']) && isset($_POST['id_follower'])) {
            $db = new Database();
            $success = $db->rejectConnection($_POST['id_follower'], $_SESSION['id_user']);
            echo json_encode(['success' => $success]);
        } else { echo json_encode(['success' => false]); }
        exit();
        break;

    case "api_remove_connection":
        ob_clean(); header('Content-Type: application/json');
        if(isset($_SESSION['id_user']) && isset($_POST['id_contact'])) {
            $db = new Database();
            $success = $db->deleteConnection($_SESSION['id_user'], $_POST['id_contact']);
            echo json_encode(['success' => $success]);
        } else { echo json_encode(['success' => false]); }
        exit();
        break;

    case "api_get_comments":
        ob_clean(); header('Content-Type: application/json');
        if(isset($_GET['id_post'])) {
            $db = new Database();
            echo json_encode($db->getComments($_GET['id_post']));
        } else { echo json_encode([]); }
        exit();
        break;

    case "api_add_comment":
        ob_clean(); header('Content-Type: application/json');
        if(isset($_SESSION['id_user']) && isset($_POST['id_post']) && isset($_POST['content'])) {
            $db = new Database();
            $success = $db->addComment($_POST['id_post'], $_SESSION['id_user'], $_POST['content']);
            echo json_encode(['success' => $success]);
        } else { echo json_encode(['success' => false]); }
        exit();
        break;

    case "api_message":
        ob_clean(); header('Content-Type: application/json');
        if(isset($_SESSION['id_user']) && isset($_POST['id_receiver'])) {
            $db = new Database();
            $content = isset($_POST['content']) ? $_POST['content'] : '';
            $fileInfo = (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) ? $_FILES['image'] : null;
            
            if ($content === '' && !$fileInfo) {
                echo json_encode(['success' => false]);
                exit();
            }
            
            $success = $db->sendMessage($_SESSION['id_user'], $_POST['id_receiver'], $content, $fileInfo);
            echo json_encode(['success' => $success]);
        } else { echo json_encode(['success' => false]); }
        exit();
        break;

    case "api_get_chat":
        ob_clean(); header('Content-Type: application/json');
        if(isset($_SESSION['id_user']) && isset($_GET['contact'])) {
            $db = new Database();
            echo json_encode($db->getChatHistory($_SESSION['id_user'], $_GET['contact']));
        } else { echo json_encode([]); }
        exit();
        break;

    case "api_search":
        ob_clean(); header('Content-Type: application/json');
        if(isset($_SESSION['id_user']) && isset($_GET['q'])) {
            $db = new Database();
            echo json_encode($db->searchUsers($_GET['q'], $_SESSION['id_user']));
        } else { echo json_encode([]); }
        exit();
        break;

    case "api_create_event":
        ob_clean(); header('Content-Type: application/json');
        if(isset($_SESSION['id_user']) && isset($_POST['title']) && isset($_POST['event_date'])) {
            $db = new Database();
            $fileInfo = (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) ? $_FILES['cover_image'] : null;
            
            $success = $db->createEvent(
                $_SESSION['id_user'],
                $_POST['title'],
                $_POST['event_date'],
                $_POST['start_time'] ?? null,
                $_POST['end_time'] ?? null,
                $_POST['description'] ?? '',
                $_POST['visibility'] ?? 'shared',
                $_POST['meeting_url'] ?? null,
                $fileInfo
            );
            echo json_encode(['success' => $success]);
        } else { echo json_encode(['success' => false]); }
        exit();
        break;

    case "api_events":
        ob_clean(); header('Content-Type: application/json');
        if(isset($_SESSION['id_user'])) {
            $db = new Database();
            echo json_encode($db->getEventsForUser($_SESSION['id_user']));
        } else { echo json_encode([]); }
        exit();
        break;

    default:
        $view = new ViewLandingPage();
        echo $view;
        break;
}
?>