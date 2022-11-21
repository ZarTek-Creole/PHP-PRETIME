<?PHP
/*
* PHP-PRETIME
* PHP pour les PRETIME, multi-serveurs, utile par exemple pour les autotrades comme slftp afin de fournir les pretimes
* URL: https://github.com/ZarTek-Creole/PHP-PRETIME
* auteur: https://github.com/ZarTek-Creole/
*/
error_reporting(E_ALL);
ini_set("display_errors", 1);
    $rlsname                        = (isset($_GET['rlsname']) && $_GET['rlsname'] != '') ? trim($_GET['rlsname']) : die("Erreur : Besoin de l'argument ?rlsname=rls");
    (@include './config.php') or die('Need rename config.example.php to config.php and edit-it!');
    (@include './functions.php') or die('functions.php is missing. re-download code on https://github.com/ZarTek-Creole/PHP-PRETIME');
    if ( !GetPRETIME( $cfg, $rlsname ) ) {
        // Release no found
        RLS_isMissing( $cfg, $rlsname );
        sendToLEgg( $cfg, $rlsname );
    }
	


?>