<?PHP
/*
* PHP-PRETIME
* PHP pour les PRETIME, multi-serveurs, utile par exemple pour les autotrades comme slftp afin de fournir les pretimes
* URL: https://github.com/ZarTek-Creole/PHP-PRETIME
* auteur: https://github.com/ZarTek-Creole/
*/
    // Quelque chose va mal? ou envie de testé ? Mettre la valeur sur 1
    $cfg['DEBUG']                       = 1;
    // Si la release est introuvable:
    //      ont dit quelle est nouvelle (donne le temps actuelle en reponse)
	$cfg['RELEASE_MISSING_IS_NOW']		= 1;
    //  Si la release est introuvable: 
    //      Nous ajoutons aux base de données qui ont $cfg['MySQL'][$m]['insert'] renseigner
	$cfg['RELEASE_MISSING_INSERT']		= 1;

    //  Si la release est introuvable et que RELEASE_MISSING_INSERT vaut 1: 
    //      Nous ajoutons la release dans la section suivante :
	$cfg['RELEASE_MISSING_SECTION']		= 'AUTOADD';


	// Congiguration du/des serveur(s) MySQL
	$m = 0;

/** MySQL 1 */ 
	$m++;
	$cfg['MySQL'][$m]['select']         =   1;
    $cfg['MySQL'][$m]['insert']         =   1;
    $cfg['MySQL'][$m]['time_type']      =   1; // unixtime = 1, datatime 2
	$cfg['MySQL'][$m]['host']			= 'localhost';
	$cfg['MySQL'][$m]['user']			= 'root';
	$cfg['MySQL'][$m]['password']   	= '';
	$cfg['MySQL'][$m]['port'] 			= '3306';
    $cfg['MySQL'][$m]['db'] 		    = 'pretime';
    $cfg['MySQL'][$m]['table'] 		    = 'scene';
    $cfg['MySQL'][$m]['col_section']    = 'section';
	$cfg['MySQL'][$m]['col_rlsname']    = 'rlsname';
	$cfg['MySQL'][$m]['col_rlstime']    = 'time';
	$cfg['MySQL'][$m]['col_rlsgrp']   	= 'grp';

/** MySQL 2 */
	/** 
	$m++;
	$cfg['MySQL'][$m]['select']         =   1;
    $cfg['MySQL'][$m]['insert']         =   0; // not insert within this db
    $cfg['MySQL'][$m]['time_type']      =   2; // unixtime = 1, datatime 2
	$cfg['MySQL'][$m]['host']			= 'localhost';
	$cfg['MySQL'][$m]['user']			= 'root';
	$cfg['MySQL'][$m]['password']   	= '';
	$cfg['MySQL'][$m]['db'] 			= 'dbpre';
    $cfg['MySQL'][$m]['table'] 		    = 'release';
    $cfg['MySQL'][$m]['col_section']    = 'sect';
	$cfg['MySQL'][$m]['col_rlsname']    = 'release';
	$cfg['MySQL'][$m]['col_rlstime']    = 'datatime';
	$cfg['MySQL'][$m]['col_rlsgrp']   	= 'group';
// Serveur 3
// $m++;
// .....
*/

	// Congiguration du/des serveur(s) Eggdrops
	$e = 0;
	/** Eggdrop LEGG 1 */ 
	$e++;
	$cfg['Eggdrop'][$e]['host']	 	= '127.0.0.1';
	$cfg['Eggdrop'][$e]['port']		= '6666';
	$cfg['Eggdrop'][$e]['password'] = 'MyPasswordLEgg';
	$cfg['Eggdrop'][$e]['message']  = '!addpre %rlsname% AUTOADD';
	$cfg['Eggdrop'][$e]['sleep']	= 1;
	
/** Eggdrop LEGG 2
	$e++;
	$cfg['Eggdrop'][$e]['host']	 = 'Remote';
	$cfg['Eggdrop'][$e]['port']	 = '7777';
	$cfg['Eggdrop'][$e]['password'] = 'MyPasswordLEgg';
	$cfg['Eggdrop'][$e]['message']  = '!newdir %rlsname% -';
    $cfg['Eggdrop'][$e]['timeout']  = 45;
?>
*/ 
?>