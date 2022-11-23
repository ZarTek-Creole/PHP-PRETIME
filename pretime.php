<?PHP
/*
* PHP-PRETIME
* PHP pour les PRETIME, multi-serveurs, utile par exemple pour les autotrades comme slftp afin de fournir les pretimes
* URL: https://github.com/ZarTek-Creole/PHP-PRETIME
* auteur: https://github.com/ZarTek-Creole/
*/
	ini_set('max_execution_time', 300);
	$rlsname						= (isset($_GET['rlsname']) && $_GET['rlsname'] != '') ? trim($_GET['rlsname']) : die("Erreur : Besoin de l'argument ?rlsname=rls");
	(@require_once './config.php') 	or die('Need rename config.example.php to config.php and edit-it!');
	if ($cfg['DEBUG']==1) { 
		error_reporting(E_ALL);
		ini_set("display_errors", 1);  
		ini_set('display_startup_errors', 1);
		error_reporting(-1);
	}
    (@include './functions.php')    or die('functions.php is missing. re-download code on https://github.com/ZarTek-Creole/PHP-PRETIME');
	$MySQL_COUNT					= count($cfg['MySQL']);
	$Eggdrop_COUNT  	            = count($cfg['Eggdrop']);
	if ( !GetPRETIME( $rlsname ) ) {
		// Release no found
		RLS_isMissing( $rlsname );
		sendToLEgg( $rlsname );
	}
?>