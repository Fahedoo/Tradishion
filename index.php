<?php
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set("display_errors", 1);

define("CHARGE_AUTOLOAD",true);
require_once("inc/poo.inc.php"); 

$vue = new LandingPage();
echo $vue;
?>