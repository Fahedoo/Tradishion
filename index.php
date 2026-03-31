<?php
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set("display_errors", 1);

define("CHARGE_AUTOLOAD",true);
require_once("inc/poo.inc.php"); 

$page = $_GET["page"] ?? "landing";

switch($page) {
    case "login":
        $view = new ViewLogin();
        echo $view;
        break;
    case "signup":
        $view = new ViewSignUp();
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
    default:
        $view = new ViewLandingPage();
        echo $view;
        break;
}

?>