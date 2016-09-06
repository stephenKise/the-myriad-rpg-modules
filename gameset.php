<?php
# Robert of maddrio.com
# This module allows for no Race or Specialty to be used on custom LoGD realms.
# Use the Translator to remove Race & Specialty from the Bio and anywhere else
#
# CODERS - feel free to use it - rip it apart - redo it - rewrite it - add on to it
# You also can use this module to add on more custom settings for your realm. 
# Keep it simple with one module to control all your custom game settings

function gameset_getmoduleinfo(){
	$info = array(
	"name"=>"Game Setting (special)",
	"version"=>"1.0",
	"author"=>"`2Robert",
	"category"=>"General",
	"download"=>"",
		"settings"=>array(
			"Game Setting (special) - Settings,title",
			"raceset"=>"Name of everyones race?,|Human",
			"specname"=>"Name of everyones specialty?,|none",
			"specset"=>"Do not change this! (AA by default),|AA",
			// add in your custom settings
		),
		"prefs"=>array(
			"Game Setting (special) - Prefs,title",
			// add in your custom prefs
		),
	 );
	return $info;
}
function gameset_install(){
	if (!is_module_active('gameset')){
		output("`^ Installing: Game Setting (special) `n`0");
	}else{
		output("`^ Up Dating: Game Setting (special) `n`0");
	}
	module_addhook("newday-intercept");
	module_addhook("player-login");
	return true;
}
function gameset_uninstall(){
	$specset=get_module_setting("specset");
	output("`^ Un-installing: Game Setting (special) ");
	$sql = "UPDATE " . db_prefix("accounts") . " SET specialty='' WHERE specialty='$specset'";
	db_query($sql);
	return true;
}
function gameset_dohook($hookname,$args){
	global $session;
	$race=$session['user']['race'];
	$specialty=$session['user']['specialty'];
	$raceset=get_module_setting("raceset");
	$specset=get_module_setting("specset");
	switch ($hookname){
		case "newday-intercept":  // this will re-set the race after a DK
		$dp = count($session['user']['dragonpoints']);
		$dkills = $session['user']['dragonkills'];
		if ($dp < $dkills) {
			$session['user']['race']=$raceset;
			$session['user']['specialty']=$specset;
		}
		break;
		case "player-login":  // new players creating an account or if some module cleared race/specialty
			if (!($race == $raceset)) $session['user']['race']=$raceset;
			if (!($specialty == $specset)) $session['user']['specialty']=$specset;
		break;
	}
	return $args;
}

function gameset_run(){
}
?>