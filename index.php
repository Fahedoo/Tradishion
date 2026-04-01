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
<<<<<<< HEAD
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

    // NOUVELLE ROUTE : EXPLORATEUR (Fil d'actualité)
    case "explorer":
        if (!isset($_SESSION['id_user'])) { header("Location: index.php?page=login"); exit(); }
        $db = new Database();
        
        // Si l'utilisateur poste un nouveau message
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
        $posts = $db->getFeedPosts('all'); // Récupère tous les posts de la BDD
        $view = new ViewExplorer($userData, $recommendations, $posts);
        echo $view;
        break;

    case "profile":
        if (!isset($_SESSION['id_user'])) { header("Location: index.php?page=login"); exit(); }
        $db = new Database();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
            $db->updateProfile($_SESSION['id_user'], $_POST['display_name'], $_POST['location'], $_POST['bio_free']);
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $db->updateAvatar($_SESSION['id_user'], $_FILES['avatar']);
            }
            header("Location: index.php?page=profile");
            exit();
        }
        $userData = $db->getUser($_SESSION['id_user']);
        $recommendations = $db->getRecommendations($_SESSION['id_user']);
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
        $view = new ViewMessages();
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
        echo json_encode($db->getFeedPosts($country));
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

    default:
=======
    case "login":
        // Instantiate the login view and pass any error message
        $view = new ViewLogin($errorMessageLogin);
        echo $view;
        break;

    case "signup":
        // Instantiate the signup view and pass any error message
        $view = new ViewSignUp($errorMessageSignup);
        echo $view;
        break;

    // --- KAINA'S LEGAL PAGES ---
    case "legal":
        $view = new ViewLegal();
        echo $view;
        break;

    case "privacy":
        $view = new ViewPrivacy();
        echo $view;
        break;

    case "gcu":
        $view = new ViewCGU();
        echo $view;
        break;

    // --- PRIVATE AREA ---
    case "dashboard":
        // SECURITY CHECK: Block access if the user is not logged in
        if (!isset($_SESSION['id_user'])) {
            header("Location: index.php?page=login");
            exit();
        }
        // Temporary display for the dashboard
        echo "<div style='text-align:center; padding:50px;'>";
        echo "<h1>Espace Membre Tradishion</h1>";
        echo "<p>Tu es bien connecté !</p>";
        echo "<a href='logout.php' style='color: red; font-weight: bold;'>Se déconnecter</a>";
        echo "</div>";
        break;

    default:
        // Default route: display the public landing page
>>>>>>> dae1e28b82f50cb6a29be9f90de1e7a0baa5406c
        $view = new ViewLandingPage();
        echo $view;
        break;
}
?>