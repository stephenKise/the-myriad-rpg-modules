<?php

// Redone for Xythen.
// Changes:
// Donator Only

function guild_tag_name_getmoduleinfo(){
	$info = array(
		"name"=>"Guild Tag and Name Change",
		"author"=>"`&`bStephen Kise`b`7, edits by `LMaverick`0",
		"category"=>"Lodge",
		"version"=>"1.2",
		"download"=>"nope",
		"settings"=>array(
			"Guild Tag and Name Change Prefs,title",
				"guild_name_cost"=>"Price to change guild name? (DPS),int|300",
				"guild_tag_cost"=>"Price to change guild tag? (DPS),int|300",
			),
		);
	return $info;
}

function guild_tag_name_install(){
//	module_addhook_priority("header-clan","87");
	module_addhook("lodge");
	module_addhook("pointsdesc");
	return true;
}

function guild_tag_name_uninstall(){
	return true;
}

function guild_tag_name_dohook($hookname,$args){
	global $session;
	$name_cost = get_module_setting("guild_name_cost");
	$tag_cost = get_module_setting("guild_tag_cost");
	switch ($hookname){
		case "lodge":
			addnav("Guild");
			addnav(array("Edit Guild Name `@(%s DP)",$name_cost),"runmodule.php?module=guild_tag_name&op=clan_name");
			addnav(array("Edit Guild Tag `@(%s DP)",$tag_cost),"runmodule.php?module=guild_tag_name&op=clan_tag");
			break;
		case "pointsdesc":
			$args['count']++;
			$format = $args['format'];
			$str = translate("Clan Founders can change their Clan Name and Tag.  This costs %s points.");
			$str = sprintf($str, $name_cost);
			output($format, $str, true);
			break;
		}
	return $args;
}

function guild_tag_name_run(){
	global $session;
	$op = httpget('op');
	$name_cost = get_module_setting("guild_name_cost");
	$tag_cost = get_module_setting("guild_tag_cost");
	$pointsavailable = $session['user']['donation'] - $session['user']['donationspent'];
	$name = httppost('name');
	$tag = httppost('tag');
	$myclan = $session['user']['clanid'];
	$myrank = $session['user']['clanrank'];
	page_header("Guild Halls");
	switch ($op){
		case "clan_name":
			if ($session['user']['clanid'] == 0)
			{
				output("Sorry, you are not in a clan. You must be the founder or leader of a clan to proceed.");
			}
			else
			{
				if ($myrank >= 30)
				{
					if ($pointsavailable >= $name_cost)
					{
						output("Enter your new Guild Name:`n");
						$change = translate_inline("Change");
						rawoutput("<form action='runmodule.php?module=guild_tag_name&op=change_name' method='POST'>
							<input id='input' name='name' width='15' maxlength='50'>".htmlentities($name)."</input><br>
							<input type='submit' class='button' value='$change'></form>");
						addnav("","runmodule.php?module=guild_tag_name&op=change_name");
					}
					else
					{
						output("Sorry, you do not have enough points available to select this option.");
					}
				}
				else
				{
					
					debug($session['user']['clanrank']);
					output("Sorry, you are not the founder or leader of the clan you are in.");
				}
			}
		break;

		case "clan_tag":
			if ($session['user']['clanid'] == 0)
			{
				output("Sorry, you are not in a clan. You must be the founder or leader of a clan to proceed.");
			}
			else
			{
				if ($myrank >= 30)
				{
					if ($pointsavailable >= $tag_cost)
					{
						output("Enter your new Guild Tag:`n");
						$change = translate_inline("Change");
						rawoutput("<form action='runmodule.php?module=guild_tag_name&op=change_tag' method='POST'>
							<input id='input' name='tag' width='15' maxlength='50'>".htmlentities($tag)."</input><br>
							<input type='submit' class='button' value='$change'></form>");
						addnav("","runmodule.php?module=guild_tag_name&op=change_tag");
					}
					else
					{
						output("Sorry, you do not have enough points available to select this option.");
					}
				}
				else
				{
					output("Sorry, you are not the founder or leader of the clan you are in.");
				}
			}
		break;

		case "change_name":
			output("It has been changed!");
			$sql = "UPDATE ".db_prefix("clans")." SET clanname = '$name`0' WHERE clanid = $myclan";
			db_query($sql);
			$session['user']['donationspent'] += $name_cost;
		break;

		case "change_tag":
			output("It has been changed!");
			$sql = "UPDATE ".db_prefix("clans")." SET clanshort = '$tag`0' WHERE clanid = $myclan";
			db_query($sql);
			$session['user']['donationspent'] += $tag_cost;
		break;
	}
	addnav("Leave");
	addnav("Return to the Donation Centre","lodge.php");
	page_footer();
}
?>