<?php
function coreblocker_getmoduleinfo(){
	$info = array(
		"name"=>"Block Core Programs",
		"version"=>"1.01",
		"author"=>"DaveS, idea by Daddlertl and Nightborn",
		"category"=>"Administrative",
		"download"=>"",
		"settings"=>array(
			"Block Core Programs,title",
			"gardens"=>"Block the Gardens?,bool|0",
			"rock"=>"Block the Curious Looking Rock?,bool|0",
			"gypsy"=>"Block the Gypsy?,bool|0",
			"hof"=>"Block the Hall of Fame?,bool|0",
			"stables"=>"Block the Stables?,bool|0",
			"bank"=>"Block the Bank?,bool|0",
			"inn"=>"Block the Inn?,bool|0",
			"list"=>"Block the List of Players?,bool|0",
			"faq"=>"Block the Faq?,bool|0",
			"lodge"=>"Block the Hunter's Lodge?,bool|0",
			"news"=>"Block the Daily News?,bool|0",
			"mercenarycamp"=>"Block the Mercenary Camp?,bool|0",
			"pvp"=>"Block pvp?,bool|0",
		)
	);
	return $info;
}
function coreblocker_install(){
	module_addhook("village");
	return true;
}
function coreblocker_uninstall(){
	return true;
}
function coreblocker_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "village":
			if (get_module_setting("gardens")==1) blocknav("gardens.php",true);
			if (get_module_setting("rock")==1) blocknav("rock.php",true);
			if (get_module_setting("gypsy")==1) blocknav("gypsy.php",true);
			if (get_module_setting("hof")==1) blocknav("hof.php",true);
			if (get_module_setting("stables")==1) blocknav("stables.php",true);
			if (get_module_setting("bank")==1) blocknav("bank.php",true);
			if (get_module_setting("lodge")==1) blocknav("lodge.php",true);
			if (get_module_setting("news")==1) blocknav("news.php",true);
			if (get_module_setting("inn")==1) blocknav("inn.php",true);
			if (get_module_setting("list")==1) blocknav("list.php",true);
			if (get_module_setting("faq")==1) blocknav("petition.php?op=faq",true);
			if (get_module_setting("mercenarycamp")==1) blocknav("mercenarycamp.php",true);
			if (get_module_setting("pvp")==1) blocknav("pvp.php",true);
		break;
	}
	return $args;
}
function coreblocker_run(){
}
?>