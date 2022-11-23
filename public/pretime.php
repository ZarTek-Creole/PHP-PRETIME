<?PHP
/*
* PHP-PRETIME
* PHP pour les PRETIME, multi-serveurs, utile par exemple pour les autotrades comme slftp afin de fournir les pretimes
* URL: https://github.com/ZarTek-Creole/PHP-PRETIME
* Auteur: https://github.com/ZarTek-Creole/
*/
	ini_set('max_execution_time', 300);
	$Release['Name']						= (isset($_GET['rlsname']) && $_GET['rlsname'] != '') ? trim($_GET['rlsname']) : die("Erreur : Besoin de l'argument ?rlsname=rls");
	(@require_once './config.php') 	or die('Need rename config.example.php to config.php and edit-it!');
	if ($cfg['DEBUG']==1) { 
		error_reporting(E_ALL);
		ini_set("display_errors", 1);  
		ini_set('display_startup_errors', 1);
		error_reporting(-1);
	}
    (@include './functions.php')    or die('functions.php is missing. re-download code on https://github.com/ZarTek-Creole/PHP-PRETIME');
	$count['MySQL']					= count($cfg['MySQL']);
	$count['Eggdrop']  	            = count($cfg['Eggdrop']);
	if ( !RELEASE_GET() ) {
		// Release no found in MySQL
		Release_isMissing();
		Eggdrop_SendTo();
	}
?>