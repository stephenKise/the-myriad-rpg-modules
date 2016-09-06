<?php
global $session;
page_header("Rakei's Forge");
$op=httpget('op');
$level = get_module_pref("level");
$levela = get_module_pref("levela");
$cost = get_module_setting("cost");
if ($session['user']['donation'] > 10000) $cost = get_module_setting("cost")-1;
if ($op=="weaponhofc"){
	page_header("Weapon Forge HOF");
	$clanid = $session['user']['clanid'];
	$acc = db_prefix("accounts");
	$mp = db_prefix("module_userprefs");
	$sql = "SELECT $acc.name AS name,
	$acc.acctid AS acctid,
	$mp.value AS level,
	$mp.userid FROM $mp INNER JOIN $acc
	ON $acc.acctid = $mp.userid
	WHERE $acc.clanid = $clanid
	AND $mp.modulename = 'clanforge'
	AND $mp.setting = 'level'
	AND $mp.value > 0 ORDER BY ($mp.value+0)
	DESC limit ".get_module_setting("list")."";
	$result = db_query($sql);
	$rank = translate_inline("Level");
	$name = translate_inline("Name");
	output("`n`b`c`4Weapon Forge HoF`n`n`c`b");
	rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center'>");
	rawoutput("<tr class='trhead'><td align=center>$name</td><td align=center>$rank</td></tr>");
	for ($i=0;$i < db_num_rows($result);$i++){
		$row = db_fetch_assoc($result);
		if ($row['name']==$session['user']['name']) rawoutput("<tr class='trhilight'><td>");
		else rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td align=left>");
		output_notl("%s",$row['name']);
		rawoutput("</td><td align=right>");
		output_notl("%s",$row['level']);
		rawoutput("</td></tr>");
	}
	rawoutput("</table>");
	addnav("Guild ", "clan.php");
	villagenav();
	page_footer();
}

if ($op=="armorhofc"){
	page_header("Armor Forge HOF");
	$clanid = $session['user']['clanid'];
	$acc = db_prefix("accounts");
	$mp = db_prefix("module_userprefs");
	$sql = "SELECT $acc.name AS name,
	$acc.acctid AS acctid,
	$mp.value AS level,
	$mp.userid FROM $mp INNER JOIN $acc
	ON $acc.acctid = $mp.userid
	WHERE $acc.clanid = $clanid
	AND $mp.modulename = 'clanforge'
	AND $mp.setting = 'levela'
	AND $mp.value > 0 ORDER BY ($mp.value+0)
	DESC limit ".get_module_setting("list")."";
	$result = db_query($sql);
	$rank = translate_inline("Level");
	$name = translate_inline("Name");
	output("`n`b`c`4Armor Forge Guild HoF`n`n`c`b");
	rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center'>");
	rawoutput("<tr class='trhead'><td align=center>$name</td><td align=center>$rank</td></tr>");
	for ($i=0;$i < db_num_rows($result);$i++){
		$row = db_fetch_assoc($result);
		if ($row['name']==$session['user']['name']) rawoutput("<tr class='trhilight'><td>");
		else rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td align=left>");
		output_notl("%s",$row['name']);
		rawoutput("</td><td align=right>");
		output_notl("%s",$row['level']);
		rawoutput("</td></tr>");
	}
	rawoutput("</table>");
	addnav("Guild ", "clan.php");
	villagenav();
	page_footer();
}

if ($op=="weaponhof"){
	page_header("Weapon Forge HOF");
	$acc = db_prefix("accounts");
	$mp = db_prefix("module_userprefs");
	$sql = "SELECT $acc.name AS name,
	$acc.acctid AS acctid,
	$mp.value AS level,
	$mp.userid FROM $mp INNER JOIN $acc
	ON $acc.acctid = $mp.userid
	WHERE $mp.modulename = 'clanforge'
	AND $mp.setting = 'level'
	AND $mp.value > 0 ORDER BY ($mp.value+0)
	DESC limit ".get_module_setting("list")."";
	$result = db_query($sql);
	$rank = translate_inline("Level");
	$name = translate_inline("Name");
	output("`n`b`c`4Weapon Forge HoF`n`n`c`b");
	rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center'>");
	rawoutput("<tr class='trhead'><td align=center>$name</td><td align=center>$rank</td></tr>");
	for ($i=0;$i < db_num_rows($result);$i++){
		$row = db_fetch_assoc($result);
		if ($row['name']==$session['user']['name']) rawoutput("<tr class='trhilight'><td>");
		else rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td align=left>");
		output_notl("%s",$row['name']);
		rawoutput("</td><td align=right>");
		output_notl("%s",$row['level']);
		rawoutput("</td></tr>");
	}
	rawoutput("</table>");
	addnav("Back to HoF", "hof.php");
	villagenav();
	page_footer();
}

if ($op=="armorhof"){
	page_header("Armor Forge HOF");
	$acc = db_prefix("accounts");
	$mp = db_prefix("module_userprefs");
	$sql = "SELECT $acc.name AS name,
	$acc.acctid AS acctid,
	$mp.value AS level,
	$mp.userid FROM $mp INNER JOIN $acc
	ON $acc.acctid = $mp.userid
	WHERE $mp.modulename = 'clanforge'
	AND $mp.setting = 'levela'
	AND $mp.value > 0 ORDER BY ($mp.value+0)
	DESC limit ".get_module_setting("list")."";
	$result = db_query($sql);
	$rank = translate_inline("Level");
	$name = translate_inline("Name");
	output("`n`b`c`4Armor Forge HoF`n`n`c`b");
	rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center'>");
	rawoutput("<tr class='trhead'><td align=center>$name</td><td align=center>$rank</td></tr>");
	for ($i=0;$i < db_num_rows($result);$i++){
		$row = db_fetch_assoc($result);
		if ($row['name']==$session['user']['name']) rawoutput("<tr class='trhilight'><td>");
		else rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td align=left>");
		output_notl("%s",$row['name']);
		rawoutput("</td><td align=right>");
		output_notl("%s",$row['level']);
		rawoutput("</td></tr>");
	}
	rawoutput("</table>");
	addnav("Back to HoF", "hof.php");
	villagenav();
	page_footer();
}

if ($op=="enter"){
	output("`b`c`i`b`&R`b`7ak`#ei`&'`7s`i Forge`b`c");
	output_notl("`n`n");
	output("`$ You enter the Guild Forge, you approach your `)Stone Anvil`$ and set to work creating...");
	output_notl("`n`n`n");
	clear_module_pref("paid");
	clear_module_pref("paida");
	$wlevel=get_module_pref("level");
	$alevel=get_module_pref("levela");
	output("You are on Level %s for Weapons and Level %s for Armor",$wlevel,$alevel);
	output_notl("`n`n");
	output("`7Note: Forging weapons or armor will cost you `^%s `7gems per attempt, you may also only forge levels of 5 times your Tk eg. 10 Tks forge up to level 50.",$cost);
	output_notl("`n`n");
	output("`b`&The Forge will now only take gems per attempt/try at making a weapon or armor.`b");
	addnav("Weapons","runmodule.php?module=clanforge&op=weapon");
	addnav("Armor","runmodule.php?module=clanforge&op=armor");
	modulehook("one-clickclanforge");
	addnav("Forget It","village.php");
}

if ($op=="weapon"){
	output("`b`c`4Weapons`b`c");
	output_notl("`n`n");
	$dk = $session['user']['dragonkills'];
	$lvlmax = $dk*5+2;
	if ($dk<=0) $lvlmax = 5;
	if ($level>=$lvlmax){
		output("Sorry you have forged to your current maximum level, please try again after you've destroyed the Tentromech");
		addnav("Forget It","village.php");
	}else{
		output("`$ Picking up a large hammer you begin to work on your new weapon, you are currently at level %s.",$level);
		rawoutput("<form id='weapons' action='runmodule.php?module=clanforge&op=cweapon' method='POST'>");
		rawoutput("<table cellpadding='0' cellspacing='0' border='0' width='200'>");
		rawoutput("<tr><td>");
		output("Enter Custom Name, Limit 50 characters");
		rawoutput("</td><td>");
		rawoutput("<input id='wname' name='wname' size='50' maxlength='50' autofocus>");
		rawoutput("</td></tr>");
		$click = translate_inline("Create");
		rawoutput("<input type='submit' class='button' value='$click'>");
		rawoutput("</table>");
		rawoutput("</form>");
		addnav("","runmodule.php?module=clanforge&op=cweapon");
		villagenav();
	}
}

if ($op=="armor"){
	output("`b`c`4Armor`b`c");
	output_notl("`n`n");
	$dk = $session['user']['dragonkills'];
	$lvlmax = $dk*5+2;
	if ($levela>=$lvlmax){
		output("Sorry you have forged to your current maximum level, please try again after you've destroyed the Tentromech.");
		addnav("Forget It","village.php");
	}else{
		output("`$ Picking up a large hammer you begin to work on your new armor, you are currently at level %s.",$levela);
		rawoutput("<form id='armors' action='runmodule.php?module=clanforge&op=carmor' method='POST'>");
		rawoutput("<table cellpadding='0' cellspacing='0' border='0' width='200'>");
		rawoutput("<tr><td>");
		output("Enter Custom Name, Limit 50 characters");
		rawoutput("</td><td>");
		rawoutput("<input id='aname' name='aname' size='50' maxlength='50' autofocus>");
		rawoutput("</td></tr>");
		$click = translate_inline("Create");
		rawoutput("<input type='submit' class='button' value='$click'>");
		rawoutput("</table>");
		rawoutput("</form>");
		addnav("","runmodule.php?module=clanforge&op=carmor");
		villagenav();
	}
}

if ($op=="cweapon"){
	$wn=httppost('wname');
	$dk = $session['user']['dragonkills'];
	$lvlmax = $dk*5+2;
	if ($level>=$lvlmax){
		if ($level>$lvlmax) $level = $lvlmax;
		output("Sorry you have forged to your current maximum level, please try again after you've destroyed the Tentromech");
		addnav("Forget It","village.php");
	}else{
		if ($session['user']['gems']<$cost){
			output("Sorry you do not have the required gems onhand to forge anymore");
			addnav("Forget It","village.php");
		}else{
			$session['user']['gems']-=$cost;
			if ($wn=="") $wn = get_module_pref("name");
			output("`c`&You have created your %s`0`&.`c",$wn);
			$batk = round(($level*2)+20);
			$atk=$batk+5;
			$wcost = $atk*690;
			set_module_pref("name",$wn);
			set_module_pref("value",$atk);
			$lvl = $level+1;
			set_module_pref("level",$lvl);
			output("`n`c`\$Attack: %s`c`n",$atk);
			output("`c`i`QYou are now level `^$level`Q in `4Weapon Forging`Q.`i`c`n`n");
			$fname=get_module_pref("name");
			$fvalue=get_module_pref("value");
			$weapondamage=$session['user']['weapondmg'];
			$session['user']['attack']-=$weapondamage;
			$session['user']['attack']+=$fvalue;
			$session['user']['weapon']=$fname;
			$session['user']['weapondmg']=$fvalue;
			addnav("Make Another","runmodule.php?module=clanforge&op=cweapon");
			addnav("Forget It","village.php");
		}
	}
}

if ($op=="carmor"){
	$wn=httppost('aname');
	$dk = $session['user']['dragonkills'];
	$lvlmax = $dk*5+2;
	if ($levela>=$lvlmax){
		if ($levela>$lvlmax) $levela = $lvlmax;
		output("Sorry you have forged to your current maximum level, please try again after you've destroyed the tentromech");
		addnav("Forget It","village.php");
	}else{
		if ($session['user']['gems']<$cost){
			output("Sorry you do not have the required gems onhand to forge");
			addnav("Forget It","village.php");
		}else{
			$session['user']['gems']-=$cost;
			if ($wn=="") $wn = get_module_pref("namea");
			output("`c`&You have created your %s`&.`c",$wn);
			$bdef = round(($levela*2)+20);
			$def=$bdef+5;
			$wcost = $def*690;
			set_module_pref("namea",$wn);
			set_module_pref("value",$def);
			$lvl = $levela+1;
			set_module_pref("levela",$lvl);
			output("`n`c`\$Defense: %s`c`n",$def);
			$fname=get_module_pref("namea");
			$fvalue=get_module_pref("value");
			output("`c`i`QYou are now level `^$levela`Q in `4Armor Forging`Q.`i`c`n`n");
			$armor=$session['user']['armordef'];
			$session['user']['defense']-=$armor;
			$session['user']['defense']+=$fvalue;
			$session['user']['armor']=$fname;
			$session['user']['armordef']=$fvalue;
			addnav("Make Another","runmodule.php?module=clanforge&op=carmor");
			addnav("Forget It","village.php");
		}
	}
}
page_footer();
?>
