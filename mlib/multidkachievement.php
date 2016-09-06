<?php

function multidkachievement_getmoduleinfo(){
	$info = array(
		"name"=>"DKs per Day Core",
		"author"=>"`&Stephen Kise",
		"version"=>"1.0",
		"download"=>"nope",
		"category"=>"General",
		"prefs"=>array(
			"dkstoday"=>"How many Dks has this player gained today?,int|0",
			"time"=>"When was the most recent reset?,int|0",
			"globalhigh"=>"What is the maximum amount of kills this player has had in 24 hours ever?,int|0",
		)
		);
	return $info;
}

function multidkachievement_install(){
	module_addhook_priority("dragonkill","2");
	module_addhook("newday");
	return TRUE;
}

function multidkachievement_uninstall(){
	return TRUE;
}

function multidkachievement_dohook($hookname,$args){
	global $session;
		switch ($hookname){
			case "dragonkill":
				increment_module_pref("dkstoday");
// 				$acct = $session['user']['acctid'];
// 				$dks = get_module_pref("dkstoday");
//  				$points = get_module_pref("points","achievementcore",$session['user']['acctid'])+($dks/5);
// 				if ($dks == 5 || $dks == 10 || $dks == 25 || $dks == 50 || $dks == 100 || $dks == 150){
// 					set_module_pref("points",$points,"achievementcore");
// 					require_once("modules/achievementcore.php");
// 					addnews($session['user']['name']." `2has cleaned up Camelot by slaying `^".$dks." `@Dragons `2within 24 hours!");
// 					achievementadd("`^".$dks." `@DKs `QTODAY`@!","`2I have accomplished a major task by getting `^".$dks." `@Dragon Kills`2, in under twentyfour hours.",$acct);
// 				}



//				****ABOVE CODE THAT IS REMOVED IS OTR. DON'T UNCOMMENT****
			break;
			case "newday":
				$yesterday = get_module_pref("time");
				$dks = get_module_pref("dkstoday");
				$now = strtotime("now");
				$math = $now-$yesterday;
				if ($dks > get_module_pref("globalhigh")){
					set_module_pref("globalhigh",$dks);
				}
				if($math>86400){
					output("`2Your DKs streak for today has been reset.`n");
					set_module_pref("dkstoday",0);	
					set_module_pref("time",$now);
				}
				if(!get_module_pref("time")){
					set_module_pref("time",$now);
				}
			break;
		}
	return $args;
}
?>