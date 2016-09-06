<?php
// Ready to deploy
function academy_getmoduleinfo(){
	$info = array(
		"name"=>"Dycedarg's Academy",
		"author"=>"Chris Vorndran<br>`6Idea by: `QMichael Caternolo<br>`6Changes by: `QAelia",
		"version"=>"1.31",
		"category"=>"Village",
		"description"=>"Hire a Squire. Levels and fights alongside the user.",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=16",
		"vertxtloc"=>"http://dragonprime.net/users/Sichae/",
		"override_forced_nav"=>true,
		"settings"=>array(
			"Dycedarg's Academy Settings,title",
				"cost"=>"Gem cost to indoctrinate a Squire,int|10",
				"re"=>"How much gold to rename Squire?,int|1000",
				"favor"=>"How much favor does it cost to revive a fallen squire?,int|25",
				"gold-revive"=>"How much gold does it cost to revive a fallen squire?,int|500",
				"Set either of the revive-factors to zero to disable. Otherwise both will be enabled.,note",
				"level"=>"How many of the user's level does it take for the squire to level,range,1,15,1|5",
				"knight"=>"At which level does the Squire become a Knight?,int|10",
				"warlord"=>"At which level does the Knight become a Warlord?,int|20",
				"max"=>"Max Level of a Indoctrinated Fighter,int|50",
				"exp"=>"How much EXP (%) is devoted `iin battle`i to the Squire's training,range,0,100,1|5",
				"pvp"=>"Does the Squire work in PVP?,bool|0",
				"training"=>"Does the Squire work in Training?,bool|0",
				"hof"=>"Display an HoF page?,bool|1",
				"academyloc"=>"Location of the Academy?,location|".getsetting("villagename", LOCATION_FIELDS),
			"Squire's Stats,title",
				"names"=>"Available names,text|Sirius Menitiroso,Galahad Gallant,Tim Jones,John Smith,Brogarh Romthor",
				"Parse (seperate) each name with a comma.,note",
				"div"=>"Reduce damage calculation by what percent?,range,1,100,1|50",
				"If you find that people are rapidly going through DKs you may choose to reduce the damage that these entities can inflict. Set to 100 to disable.,note",
				"miss"=>"Max Accuracy (percent),range,0,100,5|85",
				"mindmg"=>"Base Minimum Damage,int|1",
				"is-dmg"=>"Is the minimum damage always 0?,bool|0",
				"This is an override to broaden the range of damage.,note",
				"maxdmg"=>"Base Maximum Damage,int|3",
				"boost-knight"=>"How much of an attack boost (%) does a Knight get,range,100,200,1|115",
				"boost-warlord"=>"How much of an attack boost (%) does a Warlord get,range,100,200,1|115",
			),
		"prefs"=>array(
			"Dycedarg's Academy Prefs,title",
				"active"=>"Has user indoctrinated a Squire?,bool|0",
				"dead"=>"Is user's squire dead?,bool|0",
				"name"=>"Squire's name,text|",
				"lsl"=>"How many levels has the user gained since the Squire leveled?,int|0",
				"level"=>"User's Squire's current Level,int|0",
				"acc"=>"Accuracy (percent)`n`iThis pref will evaluate if a squire has a bad battle or a good one.`i,range,1,100,1|65",
				"tacc"=>"Has user trained accuracy today?,bool|0",
				"class"=>"User's Squire's class,enum,0,Squire,1,Knight,2,Warlord|0",
			"Dycedarg's Academy Prefs,title",
				"user_show"=>"Do you want to view your squires information in your charstats?,bool|0",
				"Only applicable if you HAVE a squire.,note",
			),
		);
	return $info;
}
function academy_install(){
	module_addhook("creatureencounter");
	module_addhook("newday");
	module_addhook("footer-hof");
	module_addhook("battle");
	module_addhook("battle-victory");
	module_addhook("training-victory");
	module_addhook("village");
	module_addhook("dragonkill");
	module_addhook("changesetting");
	module_addhook("charstats");
	module_addhook("shades");
	module_addhook("graveyard");
	module_addhook("footer-news");
	module_addhook("biostat");
	return true;
}
function academy_uninstall(){
	return true;
}
function academy_dohook($hookname,$args){
	global $session;
	require("modules/academy/dohook.php");
	return $args;
}
function academy_run(){
	global $session;
	$op = httpget('op');
	$dead = get_module_pref("dead");
	$active = get_module_pref("active");
	$name = get_module_pref("name");
	$classarray = array(0=>translate_inline("Squire"),1=>translate_inline("Knight"),2=>translate_inline("Warlord"));
	$max = array(0=>get_module_setting("knight"),1=>get_module_setting("warlord"),2=>get_module_setting("max"));
	$class = get_module_pref("class");
	
	page_header("Dycedarg's Academy");
	require_once("modules/academy/case_$op.php");

	if ($op != "hall" && $op != "enter" && $op != "hof" && $op != "dismiss") 
		addnav("Return to Main Hall","runmodule.php?module=academy&op=hall");
	addnav("Leave");
	villagenav();
page_footer();
}
?>