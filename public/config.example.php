<?PHP
/*
* PHP-PRETIME
* PHP pour les PRETIME, multi-serveurs, utile par exemple pour les autotrades comme slftp afin de fournir les pretimes
* URL: https://github.com/ZarTek-Creole/PHP-PRETIME
* Auteur: https://github.com/ZarTek-Creole/
*/
    // Quelque chose va mal? ou envie de testé ? Mettre la valeur sur 1 pour le rendre plus bavard
    $cfg['DEBUG']							= 1;
    // Si la release est introuvable:
    //      ont dit quelle est nouvelle (donne le temps actuelle en reponse)
	$cfg['NO_TIME_IS_NOW']					= 1;
    //  Si la release est introuvable: 
    //      Nous ajoutons aux base de données qui ont $cfg['MySQL'][$m]['insert'] sur 1
	$cfg['NO_RELEASE_INSERT_NOW']			= 1;

    //  Si la release est introuvable et que NO_RELEASE_INSERT_NOW vaut 1: 
    //      Nous ajoutons la release dans la section suivante :
	$cfg['NO_RELEASE_SECTION_NAME']			= 'AUTOADD';


	// Congiguration du/des serveur(s) MySQL
	$m = 0;

/** MySQL 1 */ 
	$m++;
	$cfg['MySQL'][$m]['select']				= 1;
    $cfg['MySQL'][$m]['insert']				= 1;
    $cfg['MySQL'][$m]['time_type']			= 1; // unixtime = 1, datatime 2
	$cfg['MySQL'][$m]['host']				= 'localhost';
	$cfg['MySQL'][$m]['user']				= 'root';
	$cfg['MySQL'][$m]['password']			= '';
	$cfg['MySQL'][$m]['port']				= '3306';
    $cfg['MySQL'][$m]['db']					= 'pretime';
    $cfg['MySQL'][$m]['table']				= 'scene';
    $cfg['MySQL'][$m]['col_section']		= 'section';
	$cfg['MySQL'][$m]['col_release_name']	= 'rlsname';
	$cfg['MySQL'][$m]['col_release_time']	= 'time';
	$cfg['MySQL'][$m]['col_release_group']	= 'grp';

/** MySQL 2 */
	/** 
	$m++;
	$cfg['MySQL'][$m]['select']				=   1;
    $cfg['MySQL'][$m]['insert']				=   0; // not insert within this db
    $cfg['MySQL'][$m]['time_type'] 			=   2; // unixtime = 1, datatime 2
	$cfg['MySQL'][$m]['host']				= 'localhost';
	$cfg['MySQL'][$m]['user']				= 'root';
	$cfg['MySQL'][$m]['password']			= '';
	$cfg['MySQL'][$m]['db']					= 'dbpre';
    $cfg['MySQL'][$m]['table']				= 'release';
    $cfg['MySQL'][$m]['col_section']		= 'sect';
	$cfg['MySQL'][$m]['col_release_name']	= 'release';
	$cfg['MySQL'][$m]['col_release_time']	= 'datatime';
	$cfg['MySQL'][$m]['col_release_group']	= 'group';
// Serveur 3
// $m++;
// .....
*/

	// Congiguration du/des serveur(s) Eggdrops
	$e = 0;
	/** Eggdrop - LEGG 1 */ 
	$e++;
	$cfg['Eggdrop'][$e]['host']				= '127.0.0.1';
	$cfg['Eggdrop'][$e]['port']				= '6666';
	$cfg['Eggdrop'][$e]['password']			= 'MyPasswordLEgg';
	$cfg['Eggdrop'][$e]['message']			= '!addpre %rlsname% AUTOADD';
	$cfg['Eggdrop'][$e]['timeout']			= 45;
	
/** Eggdrop - LEGG 2
	$e++;
	$cfg['Eggdrop'][$e]['host']				= 'Remote';
	$cfg['Eggdrop'][$e]['port']				= '7777';
	$cfg['Eggdrop'][$e]['password']			= 'MyPasswordLEgg';
	$cfg['Eggdrop'][$e]['message']			= '!newdir %rlsname% -';
    $cfg['Eggdrop'][$e]['timeout']			= 45;
?>
*/ 
?>