<?php
function newbio_getmoduleinfo(){
	$info = array(
		"name" => "Revamped Bios",
		"author" => "`i`)Ae`7ol`&us`i`0",
		"version" => "1.1",
		"category" => "Character",
	);
	return $info;
}

function newbio_install(){
	module_addhook("header-bio");
	return true;
}

function newbio_uninstall(){
	return true;
}

function newbio_dohook($hookname, $args){
	require_once("lib/redirect.php");
	redirect("runmodule.php?module=newbio&char=".httpget('char').(httpget("ret")?"&ret=".urlencode(cmd_sanitize(httpget("ret"))):""));
	return $args;
}

function newbio_run(){
	global $session;
	require_once("common.php");
	require_once("lib/sanitize.php");
	tlschema("bio");
	checkday();

	$ret = httpget('ret');
	if ($ret == "/motd.php") $ret = "/village.php";
	
	debug($ret);
	$ret=="" ? $return="/list.php" : $return=cmd_sanitize($ret);
	$char = httpget('char');
	is_numeric($char) ? $where = "acctid = $char" : $where = "login = '$char'";
	
	$sql = "SELECT login, name, level, sex, title, weapon, armor, specialty, hashorse, acctid, resurrections, dragonkills, race, clanname, clanshort, clanrank, charm, ".db_prefix("accounts").".clanid, laston, loggedin, donation FROM " . db_prefix("accounts") . " LEFT JOIN " . db_prefix("clans") . " ON " . db_prefix("accounts") . ".clanid = " . db_prefix("clans") . ".clanid WHERE $where";
	$result = db_query($sql);
	if ($target = db_fetch_assoc($result)) {
		$target['login'] = rawurlencode($target['login']);
		$id = $target['acctid'];
		$target['return_link']=$return;
// 		if ($target['login'] == "Legend" || $target['acctid'] == "779"){
// 			page_header("The Legend");
// 			if ($ret==""){
// 				$return = substr($return,strrpos($return,"/")+1);
// 				tlschema("nav");
// 				addnav("Return");
// 				addnav("Return to List",$return);
// 				tlschema();
// 			} else {
// 				$return = substr($return,strrpos($return,"/")+1);
// 				tlschema("nav");
// 				addnav("Return");
// 				$return=="list.php" ? addnav("Return to List",$return) : addnav("Return whence you came",$return);
// 				tlschema();
// 			}
// 			
// 			output("<table align='center' width='500px'><tr align='center'><td width='33%'><img src='images/skype.png'></td><td width='33%'><img src='images/steam.png'></td><td width='33%'><img src='images/chrome.png'></td></tr>".
// 			"<tr align='center'><td><a href='skype:stephen.kise?add'>`LSunday</a></td><td><a href='http://steamcommunity.com/id/dstrying' target='_blank'>`vSunday</a></td><td><a href='http://arcane.us' target='_blank'>`&`iUntitled Project`i</a></td></tr></table>",true);
// 			page_footer();
// 		}else{
		page_header("Character Biography");

		tlschema("nav");
		addnav("Return");
		tlschema();

		if ($session['user']['superuser'] & SU_EDIT_USERS){
			addnav("Admin Functions");
			addnav("1?Edit User","user.php?op=edit&userid=$id");
		}
		modulehook("biomusic", $target);
		$target = modulehook("biotop", $target);
		modulehook("bioadmin", $target);
		output_notl("<big>`cBiography for %s`^.",$target['name'],TRUE);
		$write = translate_inline("Write Mail");
 		if ($session['user']['loggedin'])
 			rawoutput("<a href=\"mail.php?op=write&to={$target['login']}\" target=\"_blank\"><img src='images/newscroll.GIF' width='16' height='16' alt='$write' border='0'></a>");
		output_notl("`c`0</big>`n`n",TRUE);
		if ($target['clanname']>"" && getsetting("allowclans",true)){
			$ranks = array(CLAN_APPLICANT=>"`!Applicant`0",CLAN_MEMBER=>"`#Member`0",CLAN_OFFICER=>"`^Officer`0",CLAN_LEADER=>"`&Leader`0", CLAN_FOUNDER=>"`\$Founder");
			$ranks = modulehook("clanranks", array("ranks"=>$ranks, "clanid"=>$target['clanid']));
			tlschema("clans"); //just to be in the right schema
			array_push($ranks['ranks'],"`\$Founder");
			$ranks = translate_inline($ranks['ranks']);
			tlschema();
			output("`c`@%s`2 is a %s`2 to `%%s`2`c`n", $target['name'], $ranks[$target['clanrank']], $target['clanname']);
		}
		modulehook("lastnames",$target);
		$target['tablebiostat'] = array("Basic Info" => array(), "Companions/Items" => array(), "Items Info" =>array(), "Bounty Info" => array(), "Skills/Achievement" => array() );
		$target['tablebiostat']['Basic Info']['Title'] = $target['title'];
		$target['tablebiostat']['Basic Info']['Level'] = $target['level'];
		$loggedin = false;
		if ($target['loggedin'] && (date("U") - strtotime($target['laston']) < getsetting("LOGINTIMEOUT", 900)))
			$loggedin = true;
		$status = translate_inline($loggedin?"`#Online`0":"`\$Offline`0");
		$target['tablebiostat']['Basic Info']['Status'] = $status;
		$target['tablebiostat']['Basic Info']['Resurrections'] = $target['resurrections'];
		//$race = $target['race'];
		//if (!$race) $race = RACE_UNKNOWN;
		//tlschema("race");
		//$race = translate_inline($race);
		tlschema();
		//$target['tablebiostat']['Basic Info']['Race'] = $race;
		$genders = translate_inline(array("Male","Female"));
		$target['tablebiostat']['Basic Info']['Gender'] = $genders[$target['sex']];
		$sql = "SELECT * FROM " . db_prefix("mounts") . " WHERE mountid='{$target['hashorse']}'";
		$result = db_query_cached($sql, "mountdata-{$target['hashorse']}", 3600);
		$mount = db_fetch_assoc($result);
		$mount['acctid']=$target['acctid'];
		$mount = modulehook("bio-mount",$mount);
		$none = translate_inline("`iNone`i");
		if (!isset($mount['mountname']) || $mount['mountname']=="") $mount['mountname'] = $none;
		if ($target['weapon']>0) $target['tablebiostat']['Items Info']['Weapon'] = $target['Weapon'];
		if ($target['armor']>0) $target['tablebiostat']['Items Info']['Armor'] = $target['Armor'];
		if ($target['dragonkills']>0) $target['tablebiostat']['Basic Info']['TentroKills'] = $target['dragonkills'];
		$arr = array(0 => "`@Common",500 => "`eBronze",1000 => "`7Silver",5000 => "`^Gold",10000 => "`jPlatinum",20000 => "`&Diamond");
		foreach($arr as $key => $val){
			if ($target['donation'] >= $key) $out = $val;
		}
		$target['tablebiostat']['Basic Info']['Donation Grade'] = $out;
		$target['tablebiostat']['Basic Info']['Charm'] = $target['charm'];
		$target['tablebiostat']['Companions/Items']['Mount Name'] = $mount['mountname'];
		if ($target['bio'] > "") $target['tablebiostat']['Basic Info']['Bio'] = soap($target['bio']);
		$target = modulehook("biostat", $target);
		$initkey = "";
		foreach ($target['tablebiostat'] as $category => $array){
			foreach ($array as $key => $val){
					if ($category != $initkey){
						output_notl("`n`7`b<big> $category </big>`b`0`n`n",TRUE);
						$initkey = $category;
					}
					output_notl("`^$key: `@%s`n",$val,TRUE);
			}
		}
		modulehook("bioinfo", $target);
		if ($ret==""){
			$return = substr($return,strrpos($return,"/")+1);
			tlschema("nav");
			addnav("Return");
			addnav("Return to List",$return);
			tlschema();
		} else {
			$return = substr($return,strrpos($return,"/")+1);
			tlschema("nav");
			addnav("Return");
			$return=="list.php" ? addnav("Return to List",$return) : addnav("Return whence you came",$return);
			tlschema();
		}
		addnav("Refresh","runmodule.php?module=newbio&char=".httpget('char').(httpget("ret")?"&ret=".urlencode(cmd_sanitize(httpget("ret"))):""));
		modulehook("bioend", $target);
		page_footer();
// 		}
	} else {
		page_header("Character has been deleted");
		output("This character is already deleted.");
		if ($ret==""){
			$return = substr($return,strrpos($return,"/")+1);
			tlschema("nav");
			addnav("Return");
			addnav("Return to List",$return);
			tlschema();
		} else {
			$return = substr($return,strrpos($return,"/")+1);
			tlschema("nav");
			addnav("Return");
			$return=="list.php" ? addnav("Return to List",$return) : addnav("Return whence you came",$return);
			tlschema();
		}
		page_footer();
	}
}
?>
