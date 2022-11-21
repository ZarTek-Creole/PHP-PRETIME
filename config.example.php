<?PHP
/*
* PHP-PRETIME
* PHP pour les PRETIME, multi-serveurs, utile par exemple pour les autotrades comme slftp afin de fournir les pretimes
* URL: https://github.com/ZarTek-Creole/PHP-PRETIME
* auteur: https://github.com/ZarTek-Creole/
*/
    // Si la release est introuvable:
    //      ont dit quelle est nouvelle (donne le temps actuelle en reponse)
	$RELEASE_MISSING_IS_NOW			= 1;
    //  Si la release est introuvable: 
    //      Nous ajoutons aux base de données qui ont $cfg['MySQL'][$m]['insert'] renseigner
	$RELEASE_MISSING_INSERT			= 1;

    //  Si la release est introuvable et que RELEASE_MISSING_INSERT vaut 1: 
    //      Nous ajoutons la release dans la section suivante :
	$RELEASE_MISSING_SECTION		= 'AUTOADD';

    $m = 0;
    // Congiguration du/des serveur(s) MySQL

/** MySQL 1 */ 
    $m++;
	$cfg['MySQL'][$m]['host']		= 'localhost';
	$cfg['MySQL'][$m]['user']		= 'root';
	$cfg['MySQL'][$m]['password']   = '';
	$cfg['MySQL'][$m]['db'] 		= 'predb';
	$cfg['MySQL'][$m]['port'] 	    = '3306';
	$cfg['MySQL'][$m]['select'] 	= 'SELECT `rlsname`, `time` FROM `scene` WHERE `rlsname` = ? LIMIT 1;';
	$cfg['MySQL'][$m]['insert'] 	= "INSERT IGNORE INTO `scene` (`time`, `rlsname`, `section`, `grp`) VALUES (?, ?, ?, ?)";

/** MySQL 2 */
    /** 
	$m++;
    $cfg['MySQL'][$m]['host']		= 'Remote';
	$cfg['MySQL'][$m]['user']		= 'root';
	$cfg['MySQL'][$m]['password']   = '';
	$cfg['MySQL'][$m]['db'] 		= 'dbpre';
	$cfg['MySQL'][$m]['port'] 	    = '3306';
	$cfg['MySQL'][$m]['select'] 	= 'SELECT `rlsname`, `time` FROM `releases` WHERE `rlsname` = ? LIMIT 1;';
	//	$cfg['MySQL'][$m]['insert'] 	= "INSERT IGNORE INTO `scene` (`time`, `rlsname`, `section`, `grp`) VALUES (?, ?, ?, ?)";

// Serveur 3
// $m++;
// .....
*/
    $e = 0;
    // Congiguration du/des serveur(s) Eggdrops

    /** Eggdrop LEGG 1 */ 
    $e++;
    $cfg['Eggdrop'][$e]['host']     = '127.0.0.1';
    $cfg['Eggdrop'][$e]['port']     = '6666';
    $cfg['Eggdrop'][$e]['password'] = 'MyPasswordLEgg';
    $cfg['Eggdrop'][$e]['message']  = '!addpre %rlsname% AUTOADD';
    $cfg['Eggdrop'][$e]['sleep']    = 1;
    
/** Eggdrop LEGG 2
    $e++;
    $cfg['Eggdrop'][$e]['host']     = 'Remote';
    $cfg['Eggdrop'][$e]['port']     = '7777';
    $cfg['Eggdrop'][$e]['password'] = 'MyPasswordLEgg';
    $cfg['Eggdrop'][$e]['message']  = '!newdir %rlsname% -';
    $cfg['Eggdrop'][$e]['sleep']    = 5;
*/ 
?>