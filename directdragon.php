<?php
function directdragon_getmoduleinfo(){
	$info = array(
		"name"=>"SU Direct Dragon",
		"version"=>"1.0",
		"author"=>"`@KaosKaizer`0 for Aeolus",
		"category"=>"Staff Tools",
		"description"=>"Superusers get a handy nav to skip directly to the dragon, regardless of level.",
		"prefs"=>array(
			"SU Direct Dragon,title",
			"canskip"=>"If this user is not a MEGAUSER can they skip to the dragon fight?,bool|0",
		),
	);
	return $info;
}

function directdragon_install(){
	module_addhook("forest");
	return true;
}

function directdragon_uninstall(){
	return true;
}

function directdragon_dohook($hooks,$args){
	global $session;
	if ($session['user']['superuser'] & SU_MEGAUSER || get_module_pref("canskip") == 1){
		addnav("Fight");
		if ($session['user']['level'] < 15) addnav("`@Skip to Dragon Fight`0","runmodule.php?module=directdragon");
	}
	return $args;
}

function directdragon_run(){
	global $session;
	require_once("modules/mlib/levelgain.php");
	require_once("lib/redirect.php");
	if ($session['user']['level'] >= 15) redirect("forest.php?op=dragon");
	$levelgain = 15 - $session['user']['level'];
	adjust_player_level($levelgain);
	$session['user']['hitpoints'] = $session['user']['maxhitpoints'];
	redirect("forest.php?op=dragon");
}