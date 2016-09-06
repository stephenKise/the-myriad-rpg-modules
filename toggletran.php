<?php
function toggletran_getmoduleinfo(){
	$info = array(
		"name"=>"Toggle Translations Flag",
		"version"=>"1.0",
		"author"=>"`2Shane",
		"category"=>"Administrative",
		"download"=>"",
		"prefs"=>array(
			"Toggle Translations Flag Prefs,title",
			"istran"=>"Is user a translator?,bool|",
		),
	);
	return $info;
}
function toggletran_install(){
	module_addhook("village");
	module_addhook("superuser");
	return true;
}
function toggletran_uninstall(){
	return true;
}
function toggletran_dohook($hook,$args){
	global $session;
	switch($hook){
		case "village":
			if (get_module_pref("istran") == 1){
				addnav("Superuser");
				addnav("Toggle Translations","runmodule.php?module=toggletran&v=1");
			}
		break;
		case "superuser":
			if (get_module_pref("istran") == 1){
				addnav("Actions");
				addnav("Toggle Translations","runmodule.php?module=toggletran&v=2");
			}
		break;
	}
	return $args;
}

function toggletran_run(){
	global $session;
	page_header();
	
	$session['user']['superuser'] ^= SU_IS_TRANSLATOR;
	if (httpget('v') == 1) redirect('village.php');
	if (httpget('v') == 2) redirect('superuser.php');
	
	page_footer();
}
?>