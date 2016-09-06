<?php

function dwellings_getmoduleinfo() {
	$info = array(
	return $info;
}

function dwellings_install(){
	global $session;
	require_once("modules/dwellings/install.php");
	return true;
}

function dwellings_uninstall(){
	output("`4Un-Installing dwellings Module.`n");
	$sql = "DROP TABLE IF EXISTS ".db_prefix("dwellings").", ".db_prefix("dwellingkeys").",".db_prefix("dwellingtypes")."";
	db_query($sql);
	$sql = "DELETE FROM ".db_prefix("module_objprefs")." WHERE objtype='dwellings'";
	db_query($sql);
	$sql = "DELETE FROM ".db_prefix("module_userprefs")." WHERE modulename='dwellings'";
	db_query($sql);
	$sql = "DELETE FROM ".db_prefix("commentary")." WHERE section LIKE 'dwellings-%' OR section LIKE 'coffers-%'";
	db_query($sql);
	return true;
}

function dwellings_dohook($hookname,$args){
	global $session;
	require("modules/dwellings/dohook/$hookname.php");
	return $args;
}

function dwellings_run() {
	checkday();
	page_header("Dwellings");
	global $session;
	$op = httpget("op");
	$dwid = httpget('dwid');
	$type = httpget('type');
	debug(get_module_pref("location_saver"));
	if($type == "" && $dwid>0){
		$sql = "SELECT type FROM ".db_prefix("dwellings")." WHERE dwid=$dwid";
		$result = db_query($sql);
		$row = db_fetch_assoc($result); 
		$type = $row['type'];
	}
	$cityid = httpget('cityid');	
	require_once("modules/dwellings/run/case_$op.php");
	if ($op != "list" && $op != ""){
		addnav("Leave");
		addnav("Return to Hamlet","runmodule.php?module=dwellings");
	}else{
		addnav("Navigation");
		villagenav();
	}
	page_footer();
}
?>