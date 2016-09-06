<?php
//Updated using modified buy/sell code from lonnys trading game
//2.0 updated navs
//3.0 updated buy/sell adjustability
//4.0 added ability to limit daily buy/sell
//5.0 added ability to adjust enter and max player levels allowed in shop
//6.0 added ability to control inventory and added flucuation of gems inventory depending on buying and selling from players
//6.5 added debug statements and fixed a error in the entry player level
//7.0 added ability to change the not allowed message for the max player level
//8.0 fixed negative number bug thanks to aes
//8.1 Added admin ability to set village Vessa appears in.
function vessa_getmoduleinfo(){
	$info = array(
		"name"=>"Vessa's Gem Shop",
		"version"=>"8.1",
		"author"=>"Reznarth + Changes by Stark",
		"category"=>"Village Venue",
		"download"=>"http://dragonprime.net/users/Reznarth/vessa98.zip",
		"settings"=>array(
			"Vessa Settings,title",
			"geminventory"=>"Amount of gems in stock,int|1000",
			"gemcost"=>"Gold needed to buy gem,int|2000",
			"gemsell"=>"Gold paid to sell gem,int|500",
			"gemsdailyb"=>"Max gems allowed to buy per new day,int|50",
			"gemsdailys"=>"Max gems allowed to sell per new day,int|50",
			"enterlevel"=>"Min level needed to enter gemshop,int|8",
			"maxlevel"=>"Max level allowed to enter gemshop,int|14",
			"maxmessage"=>"Not allowed max level message,text|Don't you think it's time to kill the dragon?",
 			"vessaloc"=>"Where does Vessa appear,location|".getsetting("villagename", LOCATION_FIELDS)		),		"prefs"=>array(			"Vessa User Preferences,title",			"gemsbuytoday"=>"How many gems bought today?,int|0",			"gemsselltoday"=>"How many gems sold today?,int|0",		),	);	return $info;}
function vessa_install(){
	module_addhook("village");
	module_addhook("newday");
	return true;
}
function vessa_uninstall(){
	return true;
}function vessa_dohook($hookname,$args){
// 	if ($args['old'] == get_module_setting("vessaloc")) {
// 		set_module_setting("vessaloc", $args['new']);
// 	}
	global $session;
	switch($hookname){
		case "newday":
			if ($args['resurrection'] != 'true') {
				set_module_pref("gemsbuytoday",0);
				set_module_pref("gemsselltoday",0);
			}
		break;
		case "village":
			//if ($session['user']['location'] == get_module_setting("vessaloc") || $session['user']['location'] == "Siochanta") {
				tlschema($args['schemas']['marketnav']);
				addnav($args['marketnav']);
				tlschema();
				addnav("`b`i`KV`b`i`ke`gs`Gs`i`ka`i`&'`Ks `b`#G`b`i`ke`Km`i`gs","runmodule.php?module=vessa");
			//}
		break;	}
	return $args;
}
function vessa_run(){
	global $session;
	$op = httpget('op');
	page_header("Vessa's Gem Exchange");
	$geminventory = get_module_setting("geminventory");
	$gemcost = get_module_setting("gemcost");
	$gemsell = get_module_setting("gemsell");
	$gemsdailyb = get_module_setting("gemsdailyb");
	$gemsdailys = get_module_setting("gemsdailys");
	$enterlevel = get_module_setting("enterlevel");
	$maxlevel = get_module_setting("maxlevel");
	$maxmessage = get_module_setting("maxmessage");		$clanid = $session['user']['clanid'];	
	if ($session['user']['level'] < $enterlevel) {
		output("Vessa tells you to come back after you haved gained a few levels.`n`n");
	} else {
		if ($session['user']['level'] <= $maxlevel){

			if ($op == ""){
				output("Vessa has gems for sale, they cost %s gold.",$gemcost);
				output("She will also buy them from you for %s gold.`n",$gemsell);
				output("You can buy as many as %s gems today ",$gemsdailyb);
				output("and you can sell as many as %s gems today.`n",$gemsdailys);
				output("`n There are %s gems in stock.`n",$geminventory);
			}

			if ($op == "gembuy"){
				if (get_module_pref("gemsbuytoday")<get_module_setting("gemsdailyb")){
					output("`%How many gems would you like to buy?`n");
					output("<form action='runmodule.php?module=vessa&op=gembuy2' method='POST'><input name='buy' id='buy'><input type='submit' class='button' value='buy'></form>",true);
					addnav("","runmodule.php?module=vessa&op=gembuy2");
				} else {					output("You can't buy anymore gems today`n");				}
			}
			if ($op == "gembuy2"){
				$max=(get_module_setting("gemsdailyb") - get_module_pref("gemsbuytoday"));
				$stock=(get_module_setting("geminventory"));
				$buy = httppost('buy');
				if ($buy < 0) $buy = 0;
				if ($buy >= $max) $buy = ($max);
				if ($buy >= $stock) $buy = ($stock);
				if ($session['user']['gold'] < ($buy * $gemcost)) {					output("Vessa gives you the finger after you attempt to pay her less than her gems are worth.`n`n");				} else {
					$cost=($buy * $gemcost);
					$session['user']['gold']-=$cost;
					$session['user']['gems']+=$buy;
					set_module_pref("gemsbuytoday",get_module_pref("gemsbuytoday")+$buy);
					set_module_setting("geminventory",get_module_setting("geminventory")-$buy);
					output("Vessa takes your %s gold",$cost);
					output(" and hands you %s gems.",$buy);
					debuglog("spent $cost gold buying $buy gems from Vessa");
				}
			}

			if ($op == "gemsell"){
				if (get_module_pref("gemsselltoday")<get_module_setting("gemsdailys")){
					output("`%How many gems would you like to sell?`n");
					output("<form action='runmodule.php?module=vessa&op=gemsell2' method='POST'><input name='sell' id='sell'><input type='submit' class='button' value='sell'></form>",true);
					addnav("","runmodule.php?module=vessa&op=gemsell2");
				} else {					output("You can't sell anymore gems today`n");				}
			}

			if ($op == "gemsell2"){
				$max=(get_module_setting("gemsdailys") - get_module_pref("gemsselltoday"));
				$sell = httppost('sell');
				if ($sell < 0) $sell = 0;
				if ($sell >= $max) $sell = ($max);
				if ($session['user']['gems'] < $sell) {					output("Vessa raises her fist at you knowing you do not have that many gems.`n`n");				} else {
					$cost=($sell * $gemsell);
					$session['user']['gems']-=$sell;
					$session['user']['gold']+=$cost;
					if ($clanid){
						debuglog('soldgems', $session['user']['acctid'], false, 'vessabug', 10);
					}
					set_module_pref("gemsselltoday",get_module_pref("gemsselltoday")+$sell);
					set_module_setting("geminventory",get_module_setting("geminventory")+$sell);
					output("Vessa gives you %s gold",$cost);
					output(" in return for %s gems.",$sell);
					debuglog("got $cost gold selling $sell gems to Vessa");
				}
			}

		} else {
			output("$maxmessage");		}
	}

	addnav("Buy a gem - $gemcost gp","runmodule.php?module=vessa&op=gembuy");
	addnav("Sell a gem - $gemsell gp","runmodule.php?module=vessa&op=gemsell");
	require_once("lib/villagenav.php");
	villagenav();
	page_footer();
}
?>