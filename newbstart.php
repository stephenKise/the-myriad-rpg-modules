<?php
function newbstart_getmoduleinfo(){

	$prefs = array("Newbstart Prefs,title",
		"bonus"=>"Has the user received a bonus?,int|0"
		);

	$info = array(
		"name"=>"Newbie Start",
		"author"=>"`1Middleclaw",
		"version"=>"Build 1.00",
		"category"=>"Administrative",
		"download"=>"",
		"description"=>"Gives a bonus to new players.",
		"prefs"=>$prefs,
    	"settings"=>array(
			"Starting players bonus,title",
			"gems1"=>"Gems to grant a starting player,range,0,50,1|5",
			"favor1"=>"Resurrections to grant a starting player,range,0,25,1|5",
			"gold1"=>"Gold to grant a starting player,range,0,50000,1000|5000",
		),
	);
	return $info;
}

function newbstart_install(){
	module_addhook("newday");
	return true;
}
function newbstart_uninstall(){
	return true;
}

function newbstart_dohook($hookname,$args){
	switch($hookname){
		case "newday":
			if (get_module_pref("bonus")==0) {
				global $session;
				$gems = get_module_setting("gems1");
				$gold = get_module_setting("gold1");
				$favor = get_module_setting("favor1")*100;
				$session['user']['deathpower']+=$favor;
				$session['user']['gems']+=$gems;
				$session['user']['gold']+=$gold;
				set_module_pref("bonus",1);
			}
	}
	return $args;
}
?>