<?php
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set("display_errors", 1);

define("CHARGE_AUTOLOAD",true);
require_once("inc/poo.inc.php"); 

$page = $_GET["page"] ?? "";

switch($page) {
    case "login":
        $view = new ViewLogin();
        echo $view;
        break;
    default:
        $view = new ViewLandingPage();
        echo $view;
        break;
}

?>