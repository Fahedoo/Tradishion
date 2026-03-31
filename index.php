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
<<<<<<< HEAD
=======
        // Instantiate the login view and pass any error message
>>>>>>> 4ebf829a978694ca016c9b8d60ba9e45bb8fa771
        $view = new ViewLogin($errorMessageLogin);
        echo $view;
        break;

    case "signup":
<<<<<<< HEAD
=======
        // Instantiate the signup view and pass any error message
>>>>>>> 4ebf829a978694ca016c9b8d60ba9e45bb8fa771
        $view = new ViewSignUp($errorMessageSignup);
        echo $view;
        break;

<<<<<<< HEAD
=======
    // --- KAINA'S LEGAL PAGES ---
>>>>>>> 4ebf829a978694ca016c9b8d60ba9e45bb8fa771
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

<<<<<<< HEAD
    case "dashboard":
=======
    // --- PRIVATE AREA ---
    case "dashboard":
        // SECURITY CHECK: Block access if the user is not logged in
>>>>>>> 4ebf829a978694ca016c9b8d60ba9e45bb8fa771
        if (!isset($_SESSION['id_user'])) {
            header("Location: index.php?page=login");
            exit();
        }
<<<<<<< HEAD
        $view = new ViewDashboard();
        echo $view;
        break;

    // --- GESTION DE LA DÉCONNEXION ICI ---
    case "logout":
        session_destroy(); // On détruit la session
        header("Location: index.php"); // On redirige vers l'accueil
        exit();
        break;

    // --- ROUTE API POUR LA CARTE INTERACTIVE ET LE FEED ---
    case "api_posts":
        // On nettoie tout affichage précédent et on dit au navigateur qu'on envoie du JSON
        ob_clean(); 
        header('Content-Type: application/json');
        
        $db = new Database();
        $country = isset($_GET['country']) ? $_GET['country'] : 'all';
        
        // On récupère les posts et on les transforme en JSON
        echo json_encode($db->getFeedPosts($country));
        exit(); // On arrête l'exécution ici pour ne pas charger les vues HTML
        break;

    default:
=======
        // Temporary display for the dashboard
        echo "<div style='text-align:center; padding:50px;'>";
        echo "<h1>Espace Membre Tradishion</h1>";
        echo "<p>Tu es bien connecté !</p>";
        echo "<a href='logout.php' style='color: red; font-weight: bold;'>Se déconnecter</a>";
        echo "</div>";
        break;

    default:
        // Default route: display the public landing page
>>>>>>> 4ebf829a978694ca016c9b8d60ba9e45bb8fa771
        $view = new ViewLandingPage();
        echo $view;
        break;
}
?>