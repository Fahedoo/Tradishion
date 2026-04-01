<?php
// Start the session first to keep the user logged in
session_start();

// Error reporting configuration (useful for debugging)
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set("display_errors", 1);

// Define the constant for the autoloader and require it
define("CHARGE_AUTOLOAD", true);
require_once("inc/poo.inc.php");

// Variables to store potential error messages for the views
$errorMessageLogin = "";
$errorMessageSignup = "";

// Detect the requested page (default is the landing page)
$page = $_GET["page"] ?? "landing";

// ---------- POST FORMS PROCESSING ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();

    // LOGIN Form Processing
    if ($page === 'login' && isset($_POST['email']) && isset($_POST['password'])) {
        if ($db->authenticateUser(trim($_POST['email']), $_POST['password'])) {
            header("Location: index.php?page=dashboard");
            exit();
        } else {
            $errorMessageLogin = "Adresse email ou mot de passe incorrect.";
        }
    }

    // SIGNUP Form Processing
    if ($page === 'signup' && isset($_POST['email']) && isset($_POST['password'])) {
        $displayName = trim($_POST['nom'] ?? $_POST['name'] ?? $_POST['username'] ?? '');

        if ($displayName === '') {
            $errorMessageSignup = "Merci de renseigner un nom d'utilisateur.";
        } else {
            $resultatInscription = $db->registerUser($displayName, trim($_POST['email']), $_POST['password']);

            if ($resultatInscription === true) {
                $db->authenticateUser(trim($_POST['email']), $_POST['password']);
                header("Location: index.php?page=dashboard");
                exit();
            } else {
                $errorMessageSignup = $resultatInscription;
            }
        }
    }
}

// ---------- ROUTING AND VIEWS DISPLAY ----------
switch ($page) {
    case "login":
        $view = new ViewLogin($errorMessageLogin);
        echo $view;
        break;

    case "signup":
        $view = new ViewSignUp($errorMessageSignup);
        echo $view;
        break;

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

    case "dashboard":
        if (!isset($_SESSION['id_user'])) {
            header("Location: index.php?page=login");
            exit();
        }
        $view = new ViewDashboard();
        echo $view;
        break;

    case "api_posts":
        header('Content-Type: application/json; charset=utf-8');
        $db = new Database();
        $country = $_GET['country'] ?? 'all';
        echo json_encode($db->getFeedPosts($country));
        break;

    case "logout":
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit();

    default:
        $view = new ViewLandingPage();
        echo $view;
        break;
}
?>
