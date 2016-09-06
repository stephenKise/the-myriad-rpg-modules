<?php

function donatormounts_getmoduleinfo(){
	$info = array(
		"name"=>"Custom Mounts for Donators",
		"author"=>"`&Stephen Kise`3, some code from `^mounts.php",
		"version"=>"1.0",
		"category"=>"Lodge",
		"download"=>"nope",
		"prefs"=>array(
			"Custom Mounts Prefs,title",
			"isset"=>"Does this player have their mount created already?,bool|0",
			"mountid"=>"This player's SPECIFIC mount id:,int"
			)
		);
	return $info;
}


function donatormounts_install(){
	module_addhook("dragonkill");
	module_addhook_priority("pointsdesc","15");
	module_addhook_priority("lodge","15");
	return true;
}


function donatormounts_uninstall(){
	return true;
}


function donatormounts_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "dragonkill":
			if ($session['user']['hashorse'] == get_module_pref("mountid")){
				$modifiedmath = round((1+($session['user']['dragonkills']*0.01)),2);
				$sql = db_query("SELECT * FROM mounts where mountid = ".$session['user']['hashorse']);
				$row = db_fetch_assoc($sql);
				$row['mountbuff'] = unserialize($row['mountbuff']);
				$row['mountbuff']['atkmod'] = $modifiedmath;
				$row['mountbuff']['defmod'] = $modifiedmath;
				$row['mountbuff'] = serialize($row['mountbuff']);
				db_query("UPDATE mounts SET mountbuff = '".addslashes($row['mountbuff'])."' WHERE mountid = ".$session['user']['hashorse']);
				output("`^Your mount has grown stronger!`n");
			}
		break;
		case "pointsdesc":
			$args['count']++;
			output("`\$- `^The ability to have your own custom mount - that progresses with your stats!`n`\$- `^Even more chance to find an extra `%gem`^ in the the forest.`n`n");
		break;
		case "lodge":

$pointsavailable =
	$session['user']['donation']-$session['user']['donationspent'];
			if ($pointsavailable >= 1000){
				addnav("Use Points");
				if (get_module_pref('isset') == "1") addnav("Edit Custom Mount `@(1000 DP)","runmodule.php?module=donatormounts&op=edit");
					else addnav("Custom Mount `@(1000 DP)","runmodule.php?module=donatormounts&op=create");
			}
		break;
	}
	return $args;
}

//Function below is by mounts.php, from the DragonPrime team. Cause LE LAZY! XD
//Rights belong to DragonPrime.
//- Stephen
function donatormountform($mount){
	if (!isset($mount['mountname']))
		$mount['mountname'] = "";
	if (!isset($mount['mountid'])) 
		$mount['mountid'] = "";
	if (!isset($mount['mountactive']))
		$mount['mountactive']=0;
	if (!isset($mount['mountdesc']))
		$mount['mountdesc'] = "";
	if (!isset($mount['mountlocation']))
		$mount['mountlocation']  = 'all';
	if (!isset($mount['newday']))
		$mount['newday']  = "";
	if (!isset($mount['recharge']))
		$mount['recharge']  = "";
	if (!isset($mount['partrecharge']))
		$mount['partrecharge']  = "";
	if (!isset($mount['mountbuff']))
		$mount['mountbuff'] = array();
		if (!isset($mount['mountbuff']['name']))
			$mount['mountbuff']['name'] = "";
		if (!isset($mount['mountbuff']['roundmsg']))
			$mount['mountbuff']['roundmsg'] = "";
		if (!isset($mount['mountbuff']['wearoff']))
			$mount['mountbuff']['wearoff'] = "";
		if (!isset($mount['mountbuff']['effectmsg']))
			$mount['mountbuff']['effectmsg'] = "";
		if (!isset($mount['mountbuff']['effectnodmgmsg']))
			$mount['mountbuff']['effectnodmgmsg'] = "";
		if (!isset($mount['mountbuff']['effectfailmsg']))
			$mount['mountbuff']['effectfailmsg'] = "";
	
	//MAKE THE PLAYERS HAVE THEIR STATS DEFAULT AFTER THEY CHANGE THEIR MOUNT'S MESSAGES/INFO!
	//Small price to pay for having to infinitely change the name of your mount.
			
	rawoutput("<form action='runmodule.php?module=donatormounts&op=save&id={$mount['mountid']}' method='POST'>");
	rawoutput("<input type='hidden' name='mount[mountactive]' value=\"".$mount['mountactive']."\">");
	rawoutput("<input type='hidden' name='mount[mountcategory]' value='Donation'>");
	rawoutput("<input type='hidden' name='mount[mountdkcost]' value='0'>");
	rawoutput("<input type='hidden' name='mount[mountcostgems]' value='0'>");
	rawoutput("<input type='hidden' name='mount[mountcostgold]' value='0'>");
	rawoutput("<input type='hidden' name='mount[mountfeedcost]' value='5000'>");
	rawoutput("<input type='hidden' name='mount[mountforestfights]' value='50'>");
	rawoutput("<input type='hidden' name='mount[mountbuff][rounds]' value='100'>");
	rawoutput("<input type='hidden' name='mount[mountbuff][atkmod]' value='1.0'>");
	rawoutput("<input type='hidden' name='mount[mountbuff][defmod]' value='1.0'>");
	rawoutput("<input type='hidden' name='mount[mountbuff][invulnerable]' value='0'>");
	rawoutput("<input type='hidden' name='mount[mountbuff][regen]' value='1.0'>");
	rawoutput("<input type='hidden' name='mount[mountbuff][minioncount]' value='1'>");
	rawoutput("<input type='hidden' name='mount[mountbuff][minbadguydamage]' value=''>");
	rawoutput("<input type='hidden' name='mount[mountbuff][maxbadguydamage]' value=''>");
	rawoutput("<input type='hidden' name='mount[mountbuff][mingoodguydamage]' value=''>");
	rawoutput("<input type='hidden' name='mount[mountbuff][maxgoodguydamage]' value=''>");
	rawoutput("<input type='hidden' name='mount[mountbuff][lifetap]' value='1'>");
	rawoutput("<input type='hidden' name='mount[mountbuff][damageshield]' value=''>");
	rawoutput("<input type='hidden' name='mount[mountbuff][badguydmgmod]' value=''>");
	rawoutput("<input type='hidden' name='mount[mountbuff][badguyatkmod]' value=''>");
	rawoutput("<input type='hidden' name='mount[mountbuff][badguydefmod]' value=''>");
	
	addnav("","runmodule.php?module=donatormounts&op=save&id={$mount['mountid']}");
	
	rawoutput("<table>");
	
	rawoutput("<tr><td nowrap>");
	output("Mount Name:");
	rawoutput("</td><td><input name='mount[mountname]' value=\"".htmlentities($mount['mountname'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\"></td></tr>");
	
	rawoutput("<tr><td nowrap>");
	output("Mount Description:");
	rawoutput("</td><td><input name='mount[mountdesc]' value=\"".htmlentities($mount['mountdesc'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\"></td></tr>");

	rawoutput("<tr><td nowrap>");
	output("Mount Availability:");
	rawoutput("</td><td nowrap>");
	$vname = getsetting('villagename', LOCATION_FIELDS);
	$locs = array($vname => sprintf_translate("The Village of %s", $vname));
	$locs = modulehook("stablelocs", $locs);
	$locs['all'] = translate_inline("Everywhere");
	ksort($locs);
	reset($locs);
	rawoutput("<select name='mount[mountlocation]'>");
	foreach($locs as $loc=>$name) {
		rawoutput("<option value='$loc'".($mount['mountlocation']==$loc?" selected":"").">$name</option>");
	}
	rawoutput("</td></tr>");
	
	rawoutput("<tr><td nowrap colspan='2'>");
	output("`bMount Messages:`b");
	rawoutput("</td></tr>");
	
	rawoutput("<tr><td nowrap>");
	output("New Day:");
	rawoutput("</td><td><input name='mount[newday]' value=\"".htmlentities($mount['newday'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\" size='40'></td></tr>");
	
	rawoutput("<tr><td nowrap>");
	output("Full Recharge:");
	rawoutput("</td><td><input name='mount[recharge]' value=\"".htmlentities($mount['recharge'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\" size='40'></td></tr>");
	
	rawoutput("<tr><td nowrap>");
	output("Partial Recharge:");
	rawoutput("</td><td><input name='mount[partrecharge]' value=\"".htmlentities($mount['partrecharge'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\" size='40'></td></tr>");
	
	rawoutput("<tr><td valign='top' nowrap>");
	output("Mount Buff:");
	rawoutput("</td><td>");
	
	output("Buff name:");
	rawoutput("<input name='mount[mountbuff][name]' value=\"".htmlentities($mount['mountbuff']['name'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\" size='50'><br/>");
	
	output("`bBuff Messages:`b`n");
	output("Each round:");
	rawoutput("<input name='mount[mountbuff][roundmsg]' value=\"".htmlentities($mount['mountbuff']['roundmsg'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\" size='50'><br/>");
	
	output("Wear off:");
	rawoutput("<input name='mount[mountbuff][wearoff]' value=\"".htmlentities($mount['mountbuff']['wearoff'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\" size='50'><br/>");
	
	output("Effect:");
	rawoutput("<input name='mount[mountbuff][effectmsg]' value=\"".htmlentities($mount['mountbuff']['effectmsg'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\" size='50'><br/>");
	
	output("Effect No Damage:");
	rawoutput("<input name='mount[mountbuff][effectnodmgmsg]' value=\"".htmlentities($mount['mountbuff']['effectnodmgmsg'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\" size='50'><br/>");
	
	output("Effect Fail:");
	rawoutput("<input name='mount[mountbuff][effectfailmsg]' value=\"".htmlentities($mount['mountbuff']['effectfailmsg'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\" size='50'><br/>");
	output("(message replacements: {badguy}, {goodguy}, {weapon}, {armor}, {creatureweapon}, and where applicable {damage}.)`n");

	rawoutput("</td></tr></table>");
	rawoutput("<input type='submit' class='button' value='Save'></form>");
	output("`4`iNote that when you save your mount, you automatically equip it!`i");
}


function donatormounts_run(){
	global $session;
	page_header("Donator Lodge");
	output("`Q`c`bCustom Mount Editor`b`c");
	addnav("Back to the Donator Lodge","lodge.php");
	switch(httpget('op')){
		case "create":
			//mounts.php; tybg.
			donatormountform(array());
		break;
		case "edit":
			//Code from mounts.php again, with some minor edits. Main one being the SQL, to grab the player's created mount number.
			$sql = "SELECT * FROM " . db_prefix("mounts") . " WHERE mountid= ".get_module_pref("mountid");
			debug(get_module_pref("mountid"));
			$result = db_query_cached($sql, "mountdata-".get_module_pref("mountid"), 3600);
			if (db_num_rows($result)<=0){
				output("`\$`iERROR! Could not edit this mount! Please send in a petition!`i");
			}else{
					$row = db_fetch_assoc($result);
					$row['mountbuff']=unserialize($row['mountbuff']);
					donatormountform($row);
			}
		break;
		case "save":
			//MOAR! MOAR!! MOAR MOUNTS.PHP!!!		
			$buff = array();
			$mount = httppost('mount');
			if ($mount) {
				reset($mount['mountbuff']);
				while (list($key,$val)=each($mount['mountbuff'])){
					if ($val>""){
						$buff[$key]=stripslashes($val);
					}
				}
				$buff['schema']="mounts";
				httppostset('mount', $buff, 'mountbuff');
	
				list($sql, $keys, $vals) = postparse(false, 'mount');
				if (get_module_pref("isset") == 1){
					$sql="UPDATE " . db_prefix("mounts") .
						" SET $sql WHERE mountid = '".get_module_pref("mountid")."'";
				}else{
					$sql="INSERT INTO " . db_prefix("mounts") .
						" ($keys) VALUES ($vals)";
				}
				debug($sql);
				db_query($sql);
				addnav($session['user']['name']." `^has created their own Donator Mount!");
				invalidatedatacache("mountdata-".get_module_pref("mountid"));
				if (db_affected_rows()){
					output("`^`cMount saved!`c`0`n`2For now, your mount's stats are all set to the lower default. This will make your mount a bit weak for the time being. But it will grow stronger after your next Tentromech kill, and match your stats.");
					$result = db_query("SELECT * FROM ".db_prefix("mounts")." ORDER BY mountid DESC LIMIT 1");
					$row = db_fetch_assoc($result);
					invalidatedatacache("mountdata-".$row['mountid']);
					if (get_module_pref("isset") != 1){
						set_module_pref("mountid",$row['mountid']);
						set_module_pref("isset",1);
						$session['user']['hashorse'] = $row['mountid'];
					}else{
						$session['user']['hashorse'] = get_module_pref("mountid");
					}
					$buff = unserialize($row['mountbuff']);
					if ($buff['schema'] == "") $buff['schema'] = "mounts";
					apply_buff("mount",$buff);
					$session['user']['donationspent'] += 1000;
				}else{
					output("`\$ERROR! `4Mount not saved! Please petition for help!");
				}
			}
		break;
	}
	page_footer();
}



?>