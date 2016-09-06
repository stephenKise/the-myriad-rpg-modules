<?php
function clanpoints_getmoduleinfo() {
	$info = array(
		"name"=>"Clan Point System",
		"author"=>"`&Stephen Kise", 
		"version"=>"0.2b",
		"category"=>"Clan",
		"settings"=>array(
			"General Settings,title",
				"month"=>"What is the current month that we are in?,viewonly",
				"recent_winner"=>"Who recently won the Clan Points for the month?,viewonly",
			"Guild Points - WIN Point Settings,title",
				"pvp"=>"PVP?,int|10",
				"dk"=>"TentroKills?,int|10",
		),
		"prefs"=>array(
			"Guild Points - Player Earnings,title",
				"pvp"=>"Points earned for PVPs:,int|0",
				"dk"=>"Points earned for TentroKills:,int|0",
				"vaults"=>"Points earned for Vaults:,int|0",
		),
		"prefs-clans"=>array(
			"pvp"=>"PVP,int|0",
			"dk"=>" TentroKills:,int|0",
			"vaults"=>"Vaults:,int|0",
		)
	);
	return $info;
}


function clanpoints_install() {
	module_addhook("newday-runonce");
	module_addhook("pvpwin");
	module_addhook("clanhall");
	module_addhook("dragonkill");
	module_addhook("footer-hof");
	module_addhook("biostat");
	module_addhook("superuser");
		return true;
}

function clanpoints_uninstall() {
	return true;
}

function cp_motd() {
	global $session;
	///Grab all of the clans. For each clan we grab their 'Total Points' by adding up all of their settings.
	$bodystring = ""; //Use this as the body message for the petition.
	$allprefs = array("pvp","dk","vaults");
	$sortclans = array();
	$sql = db_query("SELECT clanid,clanname FROM ".db_prefix("clans")." WHERE clanid != 0");
	while ($row = db_fetch_assoc($sql)){
		$total = 0;
		foreach ($allprefs as $key){
			$total += get_module_objpref("clans",$row['clanid'],$key,"clanpoints");
		}
		$sortclans = array_merge($sortclans,array($row['clanname'] => $total));
	}
	arsort($sortclans);
	return $sortclans;
}

function cp_eom(){
// 	include the motd here,

//	then reset all points.
	db_query("UPDATE module_objprefs SET value = 0 WHERE objtype = 'clans' AND modulename = 'clanpoints'");
	db_query("UPDATE module_userprefs SET value = 0 WHERE modulename = 'clanpoints'");
	
}

function gain_points($type,$amount){
	global $session;
	increment_module_pref($type,$amount);
	increment_module_objpref("clans",$session['user']['clanid'],$type,$amount);
	if ($type != "lose") $gainstring = "`@You have gained `^".$amount." `@points for your Guild!`n";
		else $gainstring = "`4You lose `^".$amount."`4 points for your Guild!`n";
	return $gainstring;
}

function clanpoints_dohook($hookname,$args) {
	global $session;
	$yourclanid = $session['user']['clanid'];
	switch ($hookname) {
		case "newday-runonce":
			require_once("lib/systemmail.php");
			if (!get_module_setting("month")){
				set_module_setting("month",date("n"));
			}elseif (get_module_setting("month") != date("n")){
				set_module_setting("month",date("n"));
 				$cparray = cp_motd();
 				$bodystring = "<table width='750px' align='center'><tr><td colspan='2'>Clan Participation</td></tr>";
 				foreach($cparray as $clan => $total){
					if (!defined('winner'))
					{
						define('winner',$clan);
						$winner = $clan;
						set_module_setting('recent_winner',$clan);
					}
	 				$bodystring .= "<tr><td>".$clan."</td><td>".$total."</td></tr>";
 				}
 				$bodystring .= "</table>";
				$file = file_put_contents("clanpointseom.txt",$bodystring);
				$sql = db_query("INSERT INTO petitions (author, date, status, pname, body) VALUES ('779', '".date("Y-m-d H:i:s")."', 0, '`QClan Points End of Month', '`QThe Clan Points for the month have ended and the winner is $winner`Q! You can copy the table data here: <a href=\'clanpointseom.txt\'>Clan Points End of Month</a>')");
				cp_eom();
 			}
			break;
		case "clanhall":
			$prefs = get_all_module_prefs();
			$clan = db_fetch_assoc(db_query("SELECT clanname FROM ".db_prefix("clans")." WHERE clanid = ".$session['user']['clanid']));
			$allprefs = array("pvp","dk","vaults","rp","lose");
//			Fixed your get_module_objpref. - Aaron
//			You had: get_module_objpref($key,$session['user']['clanid']);
//			BUT... You're forgiven, because you don't usually use the function. ;) ily.
			foreach ($allprefs as $key){
				${"clan".$key} = get_module_objpref("clan",$session['user']['clanid'],$key);
			}
			$clantotal = (($clanpvp+$clandk+$clanvaults+$clanrp)-$clanlose);
			output("`n`n");
			output("`c<table width='400px' valign='center' align='center'><tr><td colspan='4' align='center'>`^".$clan['clanname']."'s `&Guild Points</td></tr><tr align='center'><td>TK Points</td><td>PVP Points</td><td>Vault Points</td><td>RP Points</td></tr>".
			"<tr align='center'><td>$clandk</td><td>$clanpvp</td><td>$clanvaults</td><td>$clanrp</td></tr>".
			"<tr><td colspan='4' align='center'>Total Points: $clantotal</td></table>`c",true);
		break;
   		case "dragonkill":
			if (!$yourclanid) break;
			output(gain_points("dk",get_module_setting("dk")));
		break;
   		case "pvpwin":
			if (!$yourclanid) break;
			$id = (int)$args['badguy']['acctid'];
			$sql = db_query("SELECT clanid FROM " . db_prefix("accounts") . " WHERE acctid=$id");
			$row = db_fetch_assoc($sql);
			if ($row['clanid'] != $yourclanid){
				output(gain_points("pvp",get_module_setting("pvp")));
			}
		break;
 		case "footer-hof":
			$module = httpget('module');
 			addnav("Guild Rankings");
 			addnav("Guild Points System","runmodule.php?module=clanpoints&op=hofenter");
			blocknav("runmodule.php?module=clanhof&op=clanhof");
			blocknav("runmodule.php?module=clanhof&op=playerhof");
 		break;
		case "superuser":
		if ($session['user']['superuser'] & SU_EDIT_USERS){
			addnav("Mechanics");
			addnav("Guild Points MoTD","runmodule.php?module=clanpoints&op=motd");
		}
		break;
	}
	
	return $args;
}

function clanpoints_run() {
	global $session;
	require_once("lib/systemmail.php");
	
	$yourclanid = $session['user']['clanid'];
	
	$id = httpget("id");
	$op = httpget("op");
	$type = httpget("type");
	$settings = get_all_module_settings();
	$row = db_fetch_assoc(db_query("SELECT * FROM accounts WHERE acctid = ".$id));
	
	$types = array("pvp"=>"PVP", "dk"=>"TK", "master"=>"Master", "rpfight"=>"RP Fight", "pyramid"=>"Vaults", "total"=>"Total");
	
	switch ($op) {		
		case "hofenter":
			page_header("Guild Points - Enter");
			output("`c`b`i`QGuild Points`i`b`c");
			addnav("Leave");
			addnav("Back to Main HoF","hof.php?");
			addnav("Return to the Village","village.php");

				$allprefs = array("pvp","dk","vaults");
				$sortclans = array();
				$sql = db_query("SELECT clanid,clanname FROM ".db_prefix("clans")." WHERE clanid != 0");
				while ($row = db_fetch_assoc($sql)){
					$i = 0;
					foreach ($allprefs as $key){
						$total[$key] = get_module_objpref("clans",$row['clanid'],$key,"clanpoints");
						$i+=get_module_objpref("clans",$row['clanid'],$key,"clanpoints");
					}
					$total['total'] = $i;
					$points[$row['clanname']] = $total;
				}
				foreach ($points as $key => $val)
				{
					$sorting[$key] = $val['total'];
				}
				arsort($sorting);
				output("<table align='center'><th width='250px'>Guild Name</th><th width='75px' align='center'>PVP</th><th width='75px' align='center'>TK</th><th width='75px' align='center'>Vaults</th><th width='75px' align='center'>Total</th>",true);
				foreach ($sorting as $key => $val)
				{
					output("<tr><td width='250px'>$key</td>",true);
					foreach ($points[$key] as $setting => $amt)
					{
						output("<td width='75px' align='center'>{$amt}</td>",true);
					}
					output("</tr>",true);
				}
				output("</table>",true);
				output("`n<small>`i`2What is this?`i`n`)Guild points are points earned by guilds, meeting varying requirements. For each Tentromech slain, each guild earns a set amount of points for slaying the Tentromech. The values you earn are determined by the staff here at Xythen and can change at anytime, especially during double points days. Alternatively, guilds can also lose points if their members fail to complete certain tasks, such as losing to the Tentromech or slaying another guild member. These points are reset each month, so if the values seem low, that is the reason why.</small>",true);
				addnav("Leave");
				addnav("Refresh","runmodule.php?module=clanpoints&op=hofenter");
		break;
		/*
		case "hofpvp":
		case "hofdk":
		case "hofmaster":
		case "hofrpfight":
		case "hofmisc":
		case "hoftotal":
			if ($type == "player"){
				$sqltype = substr($op, 3); // Remove the 'hof' section, and that's the setting.
				page_header("Guild Points - Player {$types[$sqltype]} Points");
				
				$sql = "SELECT accounts.name AS name, accounts.acctid AS acctid, module_userprefs.value AS kills, module_userprefs.userid FROM module_userprefs INNER JOIN accounts
					ON accounts.acctid = module_userprefs.userid
					WHERE module_userprefs.modulename = 'clanpoints'
					AND module_userprefs.setting = '$sqltype'
					AND module_userprefs.value > 0 ORDER BY (module_userprefs.value+0)
					DESC limit 10";
				$result = db_query($sql);
				$rank = translate_inline("Points");
				$name = translate_inline("Name");
				output("`n`b`c<big>`QPlayer {$types[$sqltype]} Points</big>`c`b`n",true);
				rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center'>");
				rawoutput("<tr class='trhead'><td align=center>$name</td><td align=center>$rank</td></tr>");
				
				while($row = db_fetch_assoc($result)){
					if ($row['name'] == $session['user']['name']){
						rawoutput("<tr class='trdark'><td>");
					} else {
						rawoutput("<tr class='trhighlight'><td align=left>");
					}
					output_notl("%s",$row['name']);
					rawoutput("</td><td align=right>");
					output_notl("%s",$row['kills']);
					rawoutput("</td></tr>");
				}
				rawoutput("</table>");
					
				output("`n`c`)Note: These are the top 10 players. Those under these rankings are not shown.`c");
			} else {
				$sqltype = substr($op, 3); // Remove the 'hof' section, and that's the setting.
				page_header("Guild Points - Guild {$types[$sqltype]} Points");
			
				$sql = "SELECT * FROM  ".db_prefix('clanpoints')." ORDER BY (clanpoints.$sqltype+0) DESC LIMIT 10";
				$result = db_query($sql);
				$rank = translate_inline("Points");
				$name = translate_inline("Name");
				output("`n`b<big>`c`QGuild {$types[$sqltype]} Point Rankings`c</big>`n`n`b",TRUE);
				rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center'>");
				rawoutput("<tr class='trhead'><td align=center>$name</td><td align=center>$rank</td></tr>");
				
				while($row = db_fetch_assoc($result)){
					if ($row['clanid'] == $yourclanid){
						rawoutput("<tr class='trdark'><td>");
					} else {
						rawoutput("<tr class='trhighlight'><td align=left>");
					}
					$nsql = "SELECT clanname FROM clans WHERE clanid = ".$row['clanid'];
					$nres = db_query($nsql);
					$names = db_fetch_assoc($nres);
					output_notl("%s",$names['clanname']);
					rawoutput("</td><td align=right>");
					output_notl("%s",$row[$sqltype]);
					rawoutput("</td></tr>");
				}
				rawoutput("</table>");
					
				output("`n`c`)Note: These are the top 10 Guilds. Those under these rankings are not shown.`c");
			}
			addnav("Leave");
			addnav("Guild Rankings HoF","runmodule.php?module=clanpoints&op=hofenter");
			addnav("Main HoF","hof.php");
			addnav("Village","village.php");
		break;
		*/
	}
	page_footer();
}

?>