<?php
//1.2 moved clan kills and added clan points per player to clanhof instead of clanpyramids file
function clanhof_getmoduleinfo(){
	$info = array(
		"name" => "Clan HoF",
		"author" => "`b`&Ka`6laza`&ar`b",
		"version" => "1.2",
		"download" => "http://dragonprime.net/index.php?module=Downloads;catd=20",
		"category" => "Clan",
		"description" => "HOF for Clan Pyramid Points",
		"settings"=>array(
			"Clan HoF Settings,title",
				"list"=>"How many should be shown in HoF?,int|25",
		),
		"prefs"=>array(
		"Clan HoF Prefs, title",
		"kills"=>"pyramids Kills,int|",
		"cp"=>"Clan Points per player,int|",
		),
		"requires"=>array("clanpyramid"=>"Clan Pyramid by Kalazaar",
		),
		);
	return $info;
}
function clanhof_install(){
	require_once("modules/clanhof/install.php");
}
function clanhof_uninstall(){
	return true;
}
function clanhof_dohook($hookname,$args){
	global $session;
	$op=httpget('op');
	switch ($hookname){
		case "hof":
			require_once("modules/clanhof/dohook/hof.php");
			break;
		case "footer-clan":
			require_once("modules/clanhof/dohook/footer-clan.php");
			break;
	}
	return $args;
}
function clanhof_run(){
	global $session;
	$op=httpget('op');
if ($op=="clanhof"){
	require_once("modules/clanhof/clanhof.php");
}
if ($op=="clanhofc"){
	require_once("modules/clanhof/clanhofc.php");	
}
if ($op=="clankillhof"){
	require_once("modules/clanhof/clankillhof.php");
}
if ($op=="clankills"){
	require_once("modules/clanhof/clankills.php");
}
if ($op=="playerhof"){
	require_once("modules/clanhof/playerhof.php");
}
if ($op=="playerhofc"){
	require_once("modules/clanhof/playerhofc.php");
}
}

?>