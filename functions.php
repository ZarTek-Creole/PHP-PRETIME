<?PHP
/*
* PHP-PRETIME
* PHP pour les PRETIME, multi-serveurs, utile par exemple pour les autotrades comme slftp afin de fournir les pretimes
* URL: https://github.com/ZarTek-Creole/PHP-PRETIME
* auteur: https://github.com/ZarTek-Creole/
*/
function ReleaseToGroup($rlsname) {
	$grp = explode ( "-", $rlsname );
	$grp = $grp [count ( $grp ) - 1];
	return $grp;
}
function RLS_isMissing( $cfg, $rlsname ) {
    $rlsgrp                         = ReleaseToGroup($rlsname);
    $rlstime                        = strtotime("now");
	if( isset($RELEASE_MISSING_IS_NOW) && $RELEASE_MISSING_IS_NOW == '1' ) { echo $rlsname . ' ' . $rlstime  .' - -'; }
	if( !isset($RELEASE_MISSING_INSERT) || $RELEASE_MISSING_INSERT == '0' ) { die(); }
	for ($m = 1; $m <= $merverCnt; $m++) {
		$PIPE 						= new mysqli($cfg['MySQL'][$m]['host'], $cfg['MySQL'][$m]['user'], $cfg['MySQL'][$m]['password'], $cfg['MySQL'][$m]['db'], $cfg['MySQL'][$m]['port']);
        if($PIPE->connect_error){ die('Serveur '.$m.' > Connect Error (' . $PIPE->connect_errno . ') '. $PIPE->connect_error); }
		if( !isset($cfg['MySQL'][$m]['insert']) || $cfg['MySQL'][$m]['insert'] == '') { continue; }
		$mtmt						= $PIPE->prepare($cfg['MySQL'][$m]['insert']);
		$mtmt->bind_param("isss", $rlstime, $rlsname, $RELEASE_MISSING_SECTION, $rlsgrp);
		$mtmt->execute();
		$PIPE->close();
	}
}
function GetPRETIME( $cfg, $rlsname ) {
    $MySQL_COUNT					= count($cfg['MySQL']);
	for ($m = 1; $m <= $MySQL_COUNT; $m++) {
		$PIPE 			            = new mysqli($cfg['MySQL'][$m]['host'], $cfg['MySQL'][$m]['user'], $cfg['MySQL'][$m]['password'], $cfg['MySQL'][$m]['db'], $cfg['MySQL'][$m]['port']);
		if($PIPE->connect_error){ die('Serveur '.$m.' > Connect Error (' . $PIPE->connect_errno . ') '. $PIPE->connect_error); }
		$mtmt						= $PIPE->prepare($cfg['MySQL'][$m]['select']); 
		$mtmt->bind_param("s", $rlsname);
		$mtmt->execute();
		$result						= $mtmt->get_result(); // get the mysqli result
		$R							= $result->fetch_assoc(); // fetch data 
		$PIPE->close();
		if ( empty($R) ) { continue; }
		echo $R['rlsname'] . ' '. $R['time'] .' - -';
		return 1;
	}
    return 0;
}
function sendToLEgg( $cfg, $rlsname ) {
	if ($rlsname == "") { return; }
    $Eggdrop_COUNT  	= count($cfg['Eggdrop']);
  	for ($e = 1; $e <= $Eggdrop_COUNT; $e++) {
        $EGG_PIPE       = fsockopen($cfg['Eggdrop'][$e]['host'], $cfg['Eggdrop'][$e]['port'], $errno, $errstr, 45) || die('Eggdrop '.$m.' > Connect Error (' . $errno . ') '. $errstr);
        $message        = str_replace("%rlsname%", $rlsname, $cfg['Eggdrop'][$e]['message']);
        sleep($cfg['Eggdrop'][$e]['sleep']);
        fputs($EGG_PIPE, $bot['pass'] . ' ' . $message . "\n");
        sleep($cfg['Eggdrop'][$e]['sleep']);
        fclose($EGG_PIPE);
    }
}
?>
