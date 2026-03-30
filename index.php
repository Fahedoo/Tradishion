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
$page = isset($_GET["page"]) ? $_GET["page"] : "landing";

// ---------- POST FORMS PROCESSING (Database) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // The autoloader will automatically load Database.class.php
    $db = new Database(); 

    // LOGIN Form Processing
    if ($page === 'login' && isset($_POST['email']) && isset($_POST['password'])) {
        // Attempt to authenticate the user
        if ($db->authenticateUser(trim($_POST['email']), $_POST['password'])) {
            // Success: redirect to the private dashboard
            header("Location: index.php?page=dashboard");
            exit();
        } else {
            // Failure: set the error message
            $errorMessageLogin = "Adresse email ou mot de passe incorrect.";
        }
    }

    // SIGNUP Form Processing
    if ($page === 'signup' && isset($_POST['nom']) && isset($_POST['email']) && isset($_POST['password'])) {
        // Attempt to register the new user in the database
        $resultatInscription = $db->registerUser(trim($_POST['nom']), trim($_POST['email']), $_POST['password']);
        
        if ($resultatInscription === true) {
            // Registration successful: log the user in automatically for better UX
            $db->authenticateUser(trim($_POST['email']), $_POST['password']);
            // Redirect to the private dashboard
            header("Location: index.php?page=dashboard");
            exit();
        } else {
            // Failure: retrieve the specific error message (e.g., email already taken)
            $errorMessageSignup = $resultatInscription;
        }
    }
}

// ---------- ROUTING AND VIEWS DISPLAY ----------
switch($page) {
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
        $view = new ViewLandingPage();
        echo $view;
        break;
}
?>