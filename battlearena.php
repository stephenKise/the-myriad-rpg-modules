<?php
//Origial 0.9.8 Conversion by Frederic Hutow
require_once("lib/http.php");
require_once("lib/villagenav.php");

function battlearena_getmoduleinfo(){
	$info = array(
		"name"=>"Battle Arena",
		"version"=>"2.2",
		"author"=>"`#Lonny Luberts",
		"category"=>"Village",
		"download"=>"http://www.pqcomp.com/modules/mydownloads/visit.php?cid=3&lid=34",
		"vertxtloc"=>"http://www.pqcomp.com/",
		"prefs"=>array(
			"Battle Arena User Preference,title",
			"battlepoints"=>"Number of Battle Points Received,int|0",
			"healthtemp"=>"Creature Original Health,int|0",
			"who"=>"Who did they battle last,text",
			"health"=>"Users in battle health,int",
			"crhealth"=>"Creaturs in battle health,int",
			"newfight"=>"newfight flag,bool|0",
		),
		"settings"=>array(
			"Battle Arena Settings,title",
			"leader"=>"Current Arena Leader (userid),int|0",
			"homearena"=>"Arena appears in home town (turn cities-module on!) (otherwise you'd have to specify location), bool|false",
			"fee"=>"How much do you charge for a fight,int|50",
			"arenaloc"=>"Where does the arena appear,location|".getsetting("villagename", LOCATION_FIELDS),
			"allowspecial"=>"Allow specialties in fight?, bool|false",
			"indexstats"=>"Show Leader on Login screen,bool|1",
		)
	);
	return $info;
}

function battlearena_install(){
	if (!is_module_active('battlearena')){
		output("`4Installing Battle Arena Module.`n");
	}else{
		output("`4Updating Battle Arena Module.`n");
	}
	module_addhook("village");
	module_addhook("index");
	module_addhook("dragonkill");
	module_addhook("namechange");
	return true;
}

function battlearena_uninstall(){
	output("`4Un-Installing Battle Arena Module.`n");
	return true;
}

function battlearena_dohook($hookname,$args){
	global $session;
	$leader = get_module_setting("leader");

	switch($hookname){
	case "village":
		$display = true;
		if (is_module_active("cities") && get_module_setting("homearena") == true) {
			$city = getsetting("villagename", LOCATION_FIELDS);
			$home = $session['user']['location']==get_module_pref("homecity", "cities");
			$capital = $session['user']['location']==$city;
			if (!$home && !$capital) {
				$display = false;
			}
		}
		$display = (bool)get_module_setting("homearena");
		if ($session['user']['location'] == get_module_setting("arenaloc"))
			$display = true;
		if ($display) {
			tlschema($args['schemas']['fightnav']);
    		addnav($args['fightnav']);
    		tlschema();
			addnav("`e`bB`b`E`ia`Pt`\$t`i`4`bl`b`4e `\$`bA`b`Pr`p`ie`En`ea`i  ","runmodule.php?module=battlearena");
		}
		break;
   /*	case "index":
		if (get_module_setting("indexstats") == 1){
			if ($leader != 0) {
				$sql = "SELECT name FROM " . db_prefix("accounts") . " WHERE acctid='$leader'";
				$result = db_query_cached($sql, "battleleader");
				$row = db_fetch_assoc($result);
				$leadername = $row['name'];
			}
			if ($leadername) {
				output("`@The current Battle Arena Leader is: `&%s`@.`0`n",$leadername);
			} else {
				output("`@There is `&no`@ leader in the Battle Arena. Will you be the first one?`0`n");
			}
		}
		break;*/
	case "dragonkill":
	case "namechange":
		if ($leader == $session['user']['acctid']) {
			invalidatedatacache("battleleader");
		}
		break;
	}
	return $args;
}

function battlearena_runevent($type){
}

function battlearena_run(){
	global $session;
	require ("modules/lib/battlearena.php");
	}

function battlearena_isnewleader() {
		$currentleader = get_module_setting("leader");
		$sql = "SELECT userid,value FROM " . db_prefix('module_userprefs') . " WHERE modulename='battlearena' AND setting='battlepoints' AND value <> '' ORDER BY value + 0 DESC LIMIT 1";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		if ($row['userid'] != $currentleader) {
			set_module_setting("leader", $row['userid']);
			invalidatedatacache("battleleader");
		}
}
?>