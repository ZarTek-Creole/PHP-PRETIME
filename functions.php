<?PHP
/*
* PHP-PRETIME
* PHP pour les PRETIME, multi-serveurs, utile par exemple pour les autotrades comme slftp afin de fournir les pretimes
* URL: https://github.com/ZarTek-Creole/PHP-PRETIME
* auteur: https://github.com/ZarTek-Creole/
*/
function ReleaseToGroup($rlsname) {
	global $cfg;
	$grp = explode ( "-", $rlsname );
	$grp = $grp [count ( $grp ) - 1];
	if ($cfg['DEBUG']==1) { echo 'DEBUG: ReleaseToGroup('.$rlsname.') -> '.$grp."\n";  }
	return $grp;
}
function RLS_isMissing( $rlsname ) {
	global $cfg;
	global $MySQL_COUNT;
	if ( $cfg['DEBUG'] == 1 ) { 
		echo 'DEBUG: RLS_isMissing('.$rlsname.")\n";  
	}
	$rlsgrp						= ReleaseToGroup($rlsname);

	$rlstime					= strtotime("now");
	if( isset($cfg['RELEASE_MISSING_IS_NOW']) && $cfg['RELEASE_MISSING_IS_NOW'] == '1' ) { 
		if ( $cfg['DEBUG']==1 ) { 
			echo "DEBUG: RLS_isMissing pretime now is true\n";  
		}
		echo $rlsname." ".$rlstime." - -\n";
	}
	if( !isset($cfg['RELEASE_MISSING_INSERT']) || $cfg['RELEASE_MISSING_INSERT'] == '0' ) { 
		if ( $cfg['DEBUG'] == 1 ) { 
			echo "DEBUG: RLS_isMissing -> \$cfg['RELEASE_MISSING_INSERT'] is false\n"; 
		}
		return 1;
	}
	if (  $MySQL_COUNT != 0 )  {
		for ($m = 1; $m <= $MySQL_COUNT; $m++) {
			if ($cfg['DEBUG']==1) { echo 'DEBUG: RLS_isMissing LOOP MYSQL n°'.$m."\n"; }
			if( !isset($cfg['MySQL'][$m]['insert']) || $cfg['MySQL'][$m]['insert'] != '1') { continue; }
			
			$PIPE	= Open_SQL($m);
		
			$INSERT	= Build_SQL_INSERT($m);
			$PIPE->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt 	= $PIPE->prepare($INSERT);
			if ($cfg['DEBUG']==1) { echo $INSERT; }
			if ( preg_match('/:rlstime/', $INSERT) ) {
				if ( !isset($cfg['MySQL'][$m]['time_type']) || $cfg['MySQL'][$m]['time_type'] == '1' ) {
					$col[':rlstime'] = $rlstime;
				} else { 
					$col[':rlstime'] = timestamp2datatime($rlstime);
				}
			}
			if ( preg_match('/:rlsname/', $INSERT) ) { $col[':rlsname'] = $rlsname; }
			if ( preg_match('/:section/', $INSERT) ) { $col[':section'] = $cfg['RELEASE_MISSING_SECTION']; }
			if ( preg_match('/:rlsgrp/', $INSERT) ) { $col[':rlsgrp'] = $rlsgrp; }
			$stmt->execute( $col );
			$PIPE=null;
		}
	}
}
function timestamp2datatime($timestamp) {
	// 2022-11-22 22:11:03
	return date("Y-m-d H:i:s", $timestamp);
}



function Open_SQL($m)
{
	global $cfg;
	$dsn 		= "mysql:host=".$cfg['MySQL'][$m]['host'].";port=".$cfg['MySQL'][$m]['port'].";dbname=".$cfg['MySQL'][$m]['db'];
	try {
		$PIPE 	= new PDO($dsn, $cfg['MySQL'][$m]['user'], $cfg['MySQL'][$m]['password'], array(PDO::ATTR_PERSISTENT => false));
	} catch (Exception $e) {
		error_log($e->getMessage());
		exit('Server MySQL N°'.$m.' > Connect Error '.$e->getMessage()); //something a user can understand
	}
	$PIPE->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $PIPE;
}
function Build_SQL_INSERT( $m ) {
	global $cfg;
	if (isset($cfg['MySQL'][$m]['col_section']) && $cfg['MySQL'][$m]['col_section'] != '') {
		$col_section=$cfg['MySQL'][$m]['col_section'];
		$data[$col_section] = ':section';
	}
	if (isset($cfg['MySQL'][$m]['col_rlsname']) && $cfg['MySQL'][$m]['col_rlsname'] != '') {
		$col_rlsname=$cfg['MySQL'][$m]['col_rlsname'];
		$data[$col_rlsname] = ':rlsname';
	}
	if (isset($cfg['MySQL'][$m]['col_rlstime']) && $cfg['MySQL'][$m]['col_rlstime'] != '') {
		$col_rlstime=$cfg['MySQL'][$m]['col_rlstime'];
		$data[$col_rlstime] = ':rlstime';
	}
	if (isset($cfg['MySQL'][$m]['col_rlsgrp']) && $cfg['MySQL'][$m]['col_rlsgrp'] != '') {
		$col_rlsgrp=$cfg['MySQL'][$m]['col_rlsgrp'];
		$data[$col_rlsgrp] = ':rlsgrp';
	}
	$key	= array_keys($data);
	$value	= array_values($data);
	return "INSERT IGNORE INTO `".$cfg['MySQL'][$m]['db']."`.`".$cfg['MySQL'][$m]['table']."` (`". implode('` ,`' , $key ) ."`) VALUES(". implode(", " , $value) .")";
}
function Build_SQL_Select( $m ) {
	global $cfg;
	return "SELECT `".$cfg['MySQL'][$m]['col_rlsname']."`, `".$cfg['MySQL'][$m]['col_rlstime']."` FROM `".$cfg['MySQL'][$m]['db']."`.`".$cfg['MySQL'][$m]['table']."` WHERE `".$cfg['MySQL'][$m]['col_rlsname']."` = :rlsname LIMIT 1;";
}
function GetPRETIME( $rlsname ) {
	global $MySQL_COUNT;
	global $cfg;
	for ($m = 1; $m <= $MySQL_COUNT; $m++) {
		if( !isset($cfg['MySQL'][$m]['select']) || $cfg['MySQL'][$m]['select'] != '1') { continue; }
		$SELECT = Build_SQL_Select($m);
		if ( $cfg['DEBUG'] == 1 ) { 
			echo 'DEBUG: GetPRETIME N°('.$m.') SELECT -> '.$SELECT."\n"; 
		}
		
		$PIPE 		= Open_SQL($m);
		$stmt 		= $PIPE->prepare($SELECT);
		$stmt->bindParam(':rlsname', $rlsname);
		$stmt->execute();
		$results 	= $stmt->fetch(PDO::FETCH_ASSOC);
		$PIPE		= null;
		if ( empty($results) ) { continue; }
		$col_rlsname=$cfg['MySQL'][$m]['col_rlsname'];
		$col_rlstime=$cfg['MySQL'][$m]['col_rlstime'];
		if ( !isset($cfg['MySQL'][$m]['time_type']) || $cfg['MySQL'][$m]['time_type'] == '1' ) {
			$rlstime = $results[$col_rlstime];
		} else { 
			$rlstime = strtotime($results[$col_rlstime]);
		}
		echo $results[$col_rlsname]." ".$rlstime." - -\n";
		return 1;
	}
	return 0;
}
function sendToLEgg( $rlsname ) {
	if ( $rlsname == "" ) { return; }
	global $Eggdrop_COUNT;
	global $cfg;
	for ($e = 1; $e <= $Eggdrop_COUNT; $e++) {
		if ( $cfg['DEBUG'] == 1 ) { 
			echo 'DEBUG: sendToLEgg N°('.$e.') -> '.$cfg['Eggdrop'][$e]['message']."\n";
		}
		$EGG_PIPE		= fsockopen($cfg['Eggdrop'][$e]['host'], $cfg['Eggdrop'][$e]['port'], $errno, $errstr, $cfg['Eggdrop'][$e]['timeout']);
		if ( !$EGG_PIPE ) {
			echo "$errstr ($errno)<br/>\n";
		}

		$message		=  $cfg['Eggdrop'][$e]['password']." ".str_replace('%rlsname%', $rlsname, $cfg['Eggdrop'][$e]['message'])."\r\n";
		if ($cfg['DEBUG']==1) {
			echo $message;
		}
		fwrite($EGG_PIPE, $message);
		while (!feof($EGG_PIPE)) {
			$pattern 	= "/bad password/i";
			$line 		= fgets($EGG_PIPE, 128);
			if ( preg_match($pattern,  $line) ) {
				die("Bad password for Eggdrop n°".$e);
			}
		}
		fclose($EGG_PIPE);
	}
}
?>
