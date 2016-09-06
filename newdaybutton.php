<?php

function newdaybutton_getmoduleinfo() {
	$info = array(
		"name"=>"Newday Button",
		"author"=>"`\$Inach`0",
		"version"=>"1.1",
		"category"=>"Lodge",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1395",
		"override_forced_nav"=>true,
		"settings"=>array(
			"Newday Button For Donators Settings,title",
			"cost"=>"How much does the newday button cost?,int|5000",
			"times"=>"How many times is the player allowed to get a newday?,int|50",
		),
		"prefs"=>array(
			"Newday Button For Donators Preferences,title",
				"hasbutton"=>"Does this player have the newday button?,bool|0",
				"buttonuse"=>"Number of times this player has used the button,int|0",
		)
	);
	return $info;
}

function newdaybutton_install() {
	module_addhook("lodge");
	module_addhook("pointsdesc");
	module_addhook("shades");
	module_addhook("village");
	return true;
}

function newdaybutton_uninstall() {
	return true;
}

function newdaybutton_dohook($hookname,$args) {
	global $session;
	$op = httpget('op');
    $cost = get_module_setting("cost");
    $times = get_module_setting("times");
    $hasbutton = get_module_pref("hasbutton");
    $buttonuse = get_module_pref("buttonuse");
	switch($hookname) {
		case "pointsdesc":
			$args['count']++;
			$format = $args['format'];
			$str = translate("The newday button, granting newdays to the purchaser.  This costs %s points.");
			$str = sprintf($str, $cost);
			output($format, $str, true);
			break;
		case "lodge":
			addnav("Gameplay Advantage");
			addnav(array("Newday Button `@(%s DP)",$cost),"runmodule.php?module=newdaybutton&op=start");
			break;
		case "shades":
           		if ($hasbutton && ($buttonuse <= $times)) {
				addnav("Donation Perks");
				addnav("`b`@USE NEWDAY`b","runmodule.php?module=newdaybutton&op=increment");
				//increment_module_pref("buttonuse");
			}
			break;
		case "village":
			if ($hasbutton && ($buttonuse <= $times)) {
				addnav("Donation Perks");
				addnav("`@USE NEWDAY","runmodule.php?module=newdaybutton&op=increment");
				//increment_module_pref("buttonuse");
			}
			if ($buttonuse > $times)
			{
				set_module_pref("buttonuse",0);
				set_module_pref("hasbutton",false);
			}
//			debug(get_module_pref("buttonuse"));
			break;
	}
	return $args;
}

function newdaybutton_run() {
	global $session;
	page_header("Hunter's Lodge");
    $cost = get_module_setting("cost");
    $times = get_module_setting("times");
    $hasbutton = get_module_pref("hasbutton");
    $buttonuse = get_module_pref("buttonuse");
	$op = httpget('op');
	switch ($op) {
		case "start":
			$pointsavailable = $session['user']['donation'] - $session['user']['donationspent'];
			if ($pointsavailable >= $cost && $hasbutton == 0){
				output("`QJ.C.P. walks you into a room cluttered high with a lot of odd things. He leads you around a table where you see an odd device that says `\$Newday Button `Qon it. You ask J.C.P. what it could be.");
				output("After explaining to you for a few minutes that this device allows you to grant yourself %s newdays, he asks you whether you would like to purchase it.",$times);
				output("`n`nThe cost is %s donator points",$cost);
				addnav("Choices");
				addnav("Yes","runmodule.php?module=newdaybutton&op=yes");
				addnav("No","village.php");
			} else {
				output("`2J.C.P. stares at you for a moment then looks away as you realize that you don't have enough points to purchase this item.");
			}
			break;
		case "yes":
			output("`2J. C. Peterson hands you the stand mechanism carefully.`n`n");
			output("`\$'This is one thing that will really help you out in travels, especially when you get in danger.'`n`n");
			output("`2Gingerly you place the device in your pocket and leave the room.`n`n");
			$session['user']['donationspent'] += $cost;
			set_module_pref("hasbutton",1);
			break;
		case "increment":
			increment_module_pref("buttonuse");
			require_once("lib/redirect.php");
			redirect("newday.php");
			break;
	}
	addnav("Return");
	addnav("L?Return to the Lodge","lodge.php");
	page_footer();
}
?>