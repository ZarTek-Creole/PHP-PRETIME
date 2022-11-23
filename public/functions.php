<?PHP
/*
* PHP-PRETIME
* PHP pour les PRETIME, multi-serveurs, utile par exemple pour les autotrades comme slftp afin de fournir les pretimes
* URL: https://github.com/ZarTek-Creole/PHP-PRETIME
* Auteur: https://github.com/ZarTek-Creole/
*/
function Release_ToGroup( ) {
	global $cfg;
	global $Release;
	$Group 				= explode ( "-", $Release['Name'] );
	$Group 				= $Group [count ( $Group ) - 1];
	$Release['Group']	= $Group;
	if ( $cfg['DEBUG'] == 1 ) { echo 'DEBUG: Release_ToGroup('.$Release['Name'].') -> '.$Release['Group']."\n";  }

}
function Release_isMissing( ) {
	global $cfg;
	global $count;
	global $Release;
	if ( $cfg['DEBUG'] == 1 ) { 
		echo 'DEBUG: Release_isMissing('.$Release['Name'].")\n";  
	}
	Release_ToGroup();
	$Release['Timestamp']				= strtotime("now");
	if( isset($cfg['NO_TIME_IS_NOW']) && $cfg['NO_TIME_IS_NOW'] == '1' ) { 
		if ( $cfg['DEBUG']==1 ) { 
			echo "DEBUG: Release_isMissing pretime now is true\n";  
		}
		echo $Release['Name']." ".$Release['Timestamp']." - -\n";
	}
	if( !isset($cfg['NO_RELEASE_INSERT_NOW']) || $cfg['NO_RELEASE_INSERT_NOW'] == '0' ) { 
		if ( $cfg['DEBUG'] == 1 ) { 
			echo "DEBUG: Release_isMissing -> \$cfg['NO_RELEASE_INSERT_NOW'] is false\n"; 
		}
		return 1;
	}
	if (  $count['MySQL'] != 0 )  {
		for ($m = 1; $m <= $count['MySQL']; $m++) {
			if ( $cfg['DEBUG'] == 1 ) { echo 'DEBUG: Release_isMissing LOOP MYSQL n°'.$m."\n"; }
			if( !isset($cfg['MySQL'][$m]['insert']) || $cfg['MySQL'][$m]['insert'] != '1') { continue; }
			
			$SOCKET						= SQL_OPEN($m);
			$INSERT						= SQL_BUILD_INSERT($m);
			$SOCKET->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt 						= $SOCKET->prepare($INSERT);
			if ($cfg['DEBUG']==1) { echo "DEBUG: INSERT MYSQL n°".$m.": ". $INSERT."\n"; }
			if ( preg_match('/:rlstime/', $INSERT) ) {
				if ( !isset($cfg['MySQL'][$m]['time_type']) || $cfg['MySQL'][$m]['time_type'] == '1' ) {
					$col[':rlstime']	= $Release['Timestamp'];
				} else { 
					$col[':rlstime']	= Timestamp_ToDatetime($Release['Timestamp']);
				}
			}
			if ( preg_match('/:rlsname/', $INSERT) ) { $col[':rlsname'] = $Release['Name']; }
			if ( preg_match('/:section/', $INSERT) ) { $col[':section'] = $cfg['NO_RELEASE_SECTION_NAME']; }
			if ( preg_match('/:rlsgrp/', $INSERT) ) { $col[':rlsgrp']	= $Release['Group']; }
			$stmt->execute( $col );
			$SOCKET						= null;
		}
	}
}
function SQL_BUILD_INSERT_OPS( $m ) {
	global $cfg;
	global $Release;
	$INSERT	= SQL_BUILD_INSERT($m);
	if ( $cfg['DEBUG'] == 1 ) { echo "DEBUG: SQL_BUILD_INSERT_OPS MYSQL n°".$m.": ".$INSERT."\n"; }
	if ( preg_match('/:rlstime/', $INSERT) ) {
		if ( !isset($cfg['MySQL'][$m]['time_type']) || $cfg['MySQL'][$m]['time_type'] == '1' ) {
			$col[':rlstime'] 			= $Release['Timestamp'];
		} else { 
			$col[':rlstime'] 			= Timestamp_ToDatetime($Release['Timestamp']);
		}
	}
	if ( preg_match('/:rlsname/', $INSERT) ) { $col[':rlsname']	= $Release['Name']; }
	if ( preg_match('/:section/', $INSERT) ) { $col[':section']	= $cfg['NO_RELEASE_SECTION_NAME']; }
	if ( preg_match('/:rlsgrp/', $INSERT) ) { $col[':rlsgrp']	= $Release['Group']; }
}
function Timestamp_ToDatetime($timestamp) {
	// 2022-11-22 22:11:03
	return date("Y-m-d H:i:s", $timestamp);
}

function SQL_OPEN($m)
{
	global $cfg;
	$dsn 			= "mysql:host=".$cfg['MySQL'][$m]['host'].";port=".$cfg['MySQL'][$m]['port'].";dbname=".$cfg['MySQL'][$m]['db'];
	try {
		$SOCKET 	= new PDO($dsn, $cfg['MySQL'][$m]['user'], $cfg['MySQL'][$m]['password'], array(PDO::ATTR_PERSISTENT => false));
	} catch (Exception $e) {
		error_log($e->getMessage());
		exit('Server MySQL N°'.$m.' > Connect Error '.$e->getMessage()); //something a user can understand
	}
	$SOCKET->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $SOCKET;
}
function SQL_BUILD_INSERT( $m ) {
	global $cfg;
	if ( isset($cfg['MySQL'][$m]['col_section']) && $cfg['MySQL'][$m]['col_section'] != '') {
		$col_section=$cfg['MySQL'][$m]['col_section'];
		$data[$col_section] 				= ':section';
	}
	if ( isset($cfg['MySQL'][$m]['col_release_name']) && $cfg['MySQL'][$m]['col_release_name'] != '') {
		$col_release_name=$cfg['MySQL'][$m]['col_release_name'];
		$data[$col_release_name] 			= ':rlsname';
	}
	if ( isset($cfg['MySQL'][$m]['col_release_time']) && $cfg['MySQL'][$m]['col_release_time'] != '') {
		$col_release_time=$cfg['MySQL'][$m]['col_release_time'];
		$data[$col_release_time] 			= ':rlstime';
	}
	if ( isset($cfg['MySQL'][$m]['col_release_group']) && $cfg['MySQL'][$m]['col_release_group'] != '') {
		$col_release_group=$cfg['MySQL'][$m]['col_release_group'];
		$data[$col_release_group] 			= ':rlsgrp';
	}
	$key	= array_keys($data);
	$value	= array_values($data);
	return "INSERT IGNORE INTO `".$cfg['MySQL'][$m]['db']."`.`".$cfg['MySQL'][$m]['table']."` (`". implode('` ,`' , $key ) ."`) VALUES(". implode(", " , $value) .")";
}
function SQL_BUILD_SELECT( $m ) {
	global $cfg;
	return "SELECT `".$cfg['MySQL'][$m]['col_release_name']."`, `".$cfg['MySQL'][$m]['col_release_time']."` FROM `".$cfg['MySQL'][$m]['db']."`.`".$cfg['MySQL'][$m]['table']."` WHERE `".$cfg['MySQL'][$m]['col_release_name']."` = :rlsname LIMIT 1;";
}
function RELEASE_GET( ) {
	global $count;
	global $cfg;
	global $Release;
	for ($m = 1; $m <= $count['MySQL']; $m++) {
		if( !isset($cfg['MySQL'][$m]['select']) || $cfg['MySQL'][$m]['select'] != '1') { continue; }
		$SELECT = SQL_BUILD_SELECT($m);
		if ( $cfg['DEBUG'] == 1 ) { 
			echo 'DEBUG: RELEASE_GET N°('.$m.') -> '.$SELECT."\n"; 
		}
		
		$SOCKET 							= SQL_OPEN($m);
		$stmt 								= $SOCKET->prepare($SELECT);
		$stmt->bindParam(':rlsname', $Release['Name']);
		$stmt->execute();
		$results 							= $stmt->fetch(PDO::FETCH_ASSOC);
		$SOCKET								= null;
		if ( empty($results) ) { continue; }
		$col_release_name					= $cfg['MySQL'][$m]['col_release_name'];
		$col_release_time					= $cfg['MySQL'][$m]['col_release_time'];
		if ( !isset($cfg['MySQL'][$m]['time_type']) || $cfg['MySQL'][$m]['time_type'] == '1' ) {
			$Release['Timestamp'] 			= $results[$col_release_time];
		} else { 
			$Release['Timestamp'] 			= strtotime($results[$col_release_time]);
		}
		echo $results[$col_release_name]." ".$Release['Timestamp']." - -\n";
		return 1;
	}
	return 0;
}
function Eggdrop_GetSocket( $e ) {
	global $cfg;
	$EGG_SOCKET		= fsockopen($cfg['Eggdrop'][$e]['host'], $cfg['Eggdrop'][$e]['port'], $errno, $errstr, $cfg['Eggdrop'][$e]['timeout']);
	if ( !$EGG_SOCKET ) {
		die("Eggdrop_GetSocket N°".$e." - Error: ".$errstr." (".$errno.")\n");
	}
	return $EGG_SOCKET;
}
function Eggdrop_SendTo( ) {
	global $Release;
	if ( $Release['Timestamp'] == "" ) { return; }
	global $count;
	global $cfg;
	for ($e = 1; $e <= $count['Eggdrop']; $e++) {
		if ( $cfg['DEBUG'] == 1 ) { 
			echo 'DEBUG: Eggdrop_SendTo N°('.$e.') -> '.$cfg['Eggdrop'][$e]['message']."\n";
		}
		$EGG_SOCKET		= Eggdrop_GetSocket($e);
		$message		= $cfg['Eggdrop'][$e]['password']." ".str_replace('%rlsname%', $Release['Timestamp'], $cfg['Eggdrop'][$e]['message'])."\r\n";
		if ($cfg['DEBUG']==1) {
			echo 'DEBUG: Eggdrop_SendTo N°('.$e.') -> '.$message."\n";
		}
		fwrite($EGG_SOCKET, $message);
		while (!feof($EGG_SOCKET)) {
			$RE 		= "/bad password/i";
			$DATA_LINE 	= fgets($EGG_SOCKET, 128);
			if ( preg_match($RE,  $DATA_LINE) ) {
				die("Bad password for Eggdrop n°".$e);
			}
		}
		fclose($EGG_SOCKET);
	}
}
?>
