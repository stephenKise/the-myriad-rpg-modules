<?php

// COMMENTS
// SOME CODE COPIED FROM CLANEDITOR.PHP

function guild_pack_getmoduleinfo(){
	$info = array(
		"name"=>"Guild Pack: Guild Creation and Monies",
		"author"=>"`LMaverick`0",
		"category"=>"Lodge",
		"version"=>"1.0",
		"download"=>"nope",
		);
	return $info;
}

function guild_pack_install(){
	module_addhook("lodge");
//	module_addhook("pointsdesc");
	return true;
}

function guild_pack_uninstall(){
	return true;
}

function guild_pack_dohook($hookname,$args){
	global $session;
	$pack_costs = array(1000,1800,2500);
	$pack_ranks = array("Lvl 1","Lvl 2","Lvl 3");
	switch ($hookname){
		case "lodge":
			addnav("Guild");
			for ($i = 0; $i < count($pack_costs); $i++)
			{
				addnav("Guild Pack: ".$pack_ranks[$i]." `@(".$pack_costs[$i]." DP)","runmodule.php?module=guild_pack&op=pack&rank=".$i."");
			}
			break;
//		case "pointsdesc":
//			$args['count']++;
//			$format = $args['format'];
//			$str = translate("Clan Founders can change their Clan Name and Tag.  This costs %s points.");
//			$str = sprintf($str, $name_cost);
//			output($format, $str, true);
//			break;
		}
	return $args;
}

function guild_pack_run(){
	global $session;
	$op = httpget('op');
	$rank = httpget('rank');
	$pack_costs = array(1000,1800,2500);
	$pack_ranks = array("Bronze","Silver","Gold");
	$base_gold = array("100000","500000", "1000000");
	$base_gems = array("25000","50000","100000");
	$pointsavailable = $session['user']['donation'] - $session['user']['donationspent'];
	page_header("Guild Halls");
	switch ($op)
	{
		case "pack":
			if ($pointsavailable >= $pack_costs[$rank])
			{
				if ($session['user']['clanid'] == 0)
				{
					rawoutput("<form action='runmodule.php?module=guild_pack&op=create&rank=".$rank."' method='POST'>");
					addnav("","runmodule.php?module=guild_pack&op=create&rank=".$rank."");
					output("`@`b`cNew Guild Form`c`b");
					output("Guild Name: ");
					rawoutput("<input name='clanname' maxlength='50' value=\"".htmlentities(stripslashes(httppost('clanname')), ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\">");
					output("`nShort Name: ");
					rawoutput("<input name='clanshort' maxlength='5' size='5' value=\"".htmlentities(stripslashes(httppost('clanshort')), ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\">");
					output("`nNote, color codes are permitted in neither Guild names nor short names.");
					output("The Guild name is shown on player bios and on Guild overview pages while the short name is displayed next to players' names in comment areas and such.`n");
					$apply = translate_inline("Create");
					rawoutput("<input type='submit' class='button' value='$apply'></form>");
				}
				else
				{
					output("Sorry, you cannot make a clan while you are already in one.");
				}
			}
			else
			{
				output("Sorry, you do not have enough points available to select this option.");
			}
		break;

		case "create":
			if ($session['user']['clanid'] == 0)
			{
				$clan_name = httppost("clanname");
				$clan_short = httppost("clanshort");

				$sql = "INSERT INTO " . db_prefix("clans") . " (clanname,clanshort) VALUES ('$clan_name','$clan_short')";
				db_query($sql);

				$clid = db_insert_id();

				$sql = "UPDATE " . db_prefix("accounts") . " SET clanid='$clid',clanrank='".CLAN_FOUNDER."' WHERE acctid='".$session['user']['acctid']."'";
				db_query($sql);

				//set_module_objpref("clans", $session['user']['clanid'], "vaultgold", "clanvault");
				//get_module_objpref("clans", $session['user']['clanid'], "vaultgold", "clanvault");
				
				debug($clid);
				debug($rank);
				
				//set_module_objpref("clans", $session['user']['clanid'], "vaultgold",($gold+$session['user']['gold']),"clanvault");

				set_module_objpref("clans",$clid,"vaultgold",$base_gold[$rank],"clanvault");
				set_module_objpref("clans",$clid,"vaultgems",$base_gems[$rank],"clanvault");

				$session['user']['donationspent'] += $pack_costs[$rank];

				output("Your guild has been created!");
				addnav("Guild Commons");
				addnav("Enter your Guild","clan.php");
			}
			
		break;
	}
	addnav("Leave");
	addnav("Return to the Donation Centre","lodge.php");
	page_footer();
}
?>