<?php
// Utilisation d'un autoloader en PHP>5
if (defined("CHARGE_AUTOLOAD"))
{ // Chargement de l'autoload si et seulement si la constante a été définie
  set_autoload();
}
else
	die(""); // Arrêt en erreur, en mode silence (pas de message)


// fonction de chargement de classe en fonction de la version de php

function set_autoload()
{


		function my_autoloader($classname) 
		{
			// Les classes DOIVENT être dans le répertoire class et avoir comme extension .class.php
			$filename = './class/' . $classname . '.class.php';
			 if (file_exists($filename))
				include_once($filename); // on ne charge le fichier que si le fichier existe
				else
				  die("Erreur fichier inconnu : ".$filename);
		}

		spl_autoload_register('my_autoloader'); //PHP > 5

}

	
?>
