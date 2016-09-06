<?php
/*
Details:
 * This is a module for Guilds to set up a few options
*/
require_once("lib/commentary.php");
require_once("lib/http.php");
require_once("lib/systemmail.php");
require_once("lib/villagenav.php");

function clanoptions_getmoduleinfo(){
	$info = array(
		"name"=>"Clan Options",
		"vertxtloc"=>"http://dragonprime.net/users/CortalUX/",
		"download"=>"http://dragonprime.net/users/CortalUX/clanoptions.zip",
		"author"=>"`@CortalUX",
		"version"=>"1.5",
		"category"=>"Clan",
		"download"=>"http://dragonprime.net/users/CortalUX/clanoptions.zip",
		"prefs-clans"=>array(
			"Guild Options - General,title",
			"officersUse"=>"Can officers set stuff up?,bool|0",
			"autoAc"=>"Will users be automatically added?,bool|0",
			"minDKs"=>"Min DKs?,int|0",
			"fountPoster"=>"Fountain poster?,text|..It is too smudged to make out...",
			"authID"=>"Author ID?,int|0",
		),
	);
	return $info;
}

function clanoptions_install(){
	if (!is_module_active('clanoptions')){
		output("`n`c`b`QGuild Options Module - Installed`0`b`c");
	}else{
		output("`n`c`b`QGuild Options Module - Updated`0`b`c");
	}
	module_addhook("moderate");
	module_addhook("newday");
	module_addhook("footer-clan");
	return true;
}
function clanoptions_uninstall(){
	output("`n`c`b`QGuild Options Module - Uninstalled`0`b`c");
	return true;
}
function clanoptions_dohook($hookname, $args){
	global $session;
	$op = httpget('op');
	$clan = $session['user']['clanid'];
	switch($hookname){
		case "moderate":
			$sql = "SELECT *FROM " . db_prefix("clans");
			$result = db_query($sql);
			if (db_num_rows($result)>0){
				for ($i=0;$i<db_num_rows($result);$i++){
					$row = db_fetch_assoc($result);
					$x = "clanoptions-fount-".$row['clanid'];
					$y = "<".$row['clanshort']."> ".htmlentities(full_sanitize($row['clanname']))." - "."Guild Fountain";
					$args[$x] = $y;
				}
			}
		break;
		case "footer-clan":
			$title = translate_inline("`\$Auto Acceptance");
			$message = translate_inline("`@The Guild you have just joined automatically accepts Applicants. You are now a member.");
			if ($session['user']['clanid']!=0&&get_module_objpref("clans", $session['user']['clanid'], "minDKs")>0) {
				if ($session['user']['clanrank']!=CLAN_LEADER||$session['user']['clanrank']!=CLAN_OFFICER&&get_module_objpref("clans", $clan, "officersUse")==1) {
					$dks = get_module_objpref("clans", $session['user']['clanid'], "minDKs");
					if ($session['user']['dragonkills']<$dks&&$dks!=0) {
						$sql = "SELECT * FROM " . db_prefix("clans") . " WHERE clanid=".$session['user']['clanid'];
						$result = db_query($sql);
						$row = db_fetch_assoc($result);
						$n = full_sanitize($row['clanname']);
						$title = translate_inline("`\$Kicked Out");
						$message = array(translate_inline("`@The Guild you were a member of (%s), has a minimum `&Dragonkill`@ limit of `^%s`@, however you don't pass it."),$n,$dks);
						systemmail($session['user']['acctid'],$title,$message);
						$session['user']['clanrank'] = CLAN_APPLICANT;
						$session['user']['clanid'] = 0;
						$session['user']['clanjoindate'] = '0000-00-00 00:00:00';
						require_once("lib/safeescape.php");
						$apply_short = "`@Guild App: `&%s`0";
						$subj = safeescape(serialize(array($apply_short, $session['user']['name'])));
						$sql = "DELETE FROM " . db_prefix("mail") . " WHERE msgfrom=0 AND seen=0 AND subject='$subj'";
						db_query($sql);
					}
				}
			} elseif ($session['user']['clanrank'] == CLAN_APPLICANT&&get_module_objpref("clans", $session['user']['clanid'], "autoAc")==1&&$session['user']['clanid']!=0){
				$session['user']['clanrank'] = CLAN_MEMBER;
				systemmail($session['user']['acctid'],$title,$message);
			}
			if (httpget('op')=='list'||httpget('op')=='apply') {
				rawoutput("<div style='clear:left;'>");
				$to = httpget('to');
				if ($to!=''&&$to>0) {
					$dks = get_module_objpref("clans", $to, "minDKs");
					$sql = "SELECT * FROM " . db_prefix("clans") . " WHERE clanid=$to";
					$result = db_query($sql);
					$row = db_fetch_assoc($result);
					$n = full_sanitize($row['clanname']);
					if ($dks>=$session['user']['dragonkills']&&$dks!=0) {
						output("`^%s`@ has a minimum `&Dragonkill`@ limit of `^%s`@, but you pass it.",$n,$dks);
					} elseif ($dks>0) {
						output("`^%s`@ has a minimum `&Dragonkill`@ limit of `^%s`@, and you don't pass it.",$n,$dks);
					}
				} else {
					$sql = "SELECT * FROM " . db_prefix("clans");
					$result = db_query($sql);
					if (db_num_rows($result)>0){
						output_notl('`n');
						for ($i=0;$i<db_num_rows($result);$i++){
							$row = db_fetch_assoc($result);
							$n = full_sanitize($row['clanname']);
							$dks = get_module_objpref("clans", $row['clanid'], "minDKs");
							if ($dks<=$session['user']['dragonkills']&&$dks!=0) {
								output("`^%s`@ has a minimum `&Tentrokill`@ limit of `^%s`@, but you pass it.`n",$n,$dks);
							} elseif ($dks>0) {
								output("`^%s`@ has a minimum `&Tentrokill`@ limit of `^%s`@, and you don't pass it.`n",$n,$dks);
							}
						}
					}
				}
				rawoutput("</div>");
			}
			if ($op=='enter'&&$clan!=0||$op==''&&$clan!=0) {
				addnav("Management");
				if ($session['user']['clanrank'] >= CLAN_LEADER){
					addnav("Guild Fountain","runmodule.php?module=clanoptions&op=fountain");
					addnav("Guild Options","runmodule.php?module=clanoptions&op=admin");
				} elseif ($session['user']['clanrank'] == CLAN_OFFICER){
					addnav("Guild Fountain","runmodule.php?module=clanoptions&op=fountain");
					if (get_module_objpref("clans", $clan, "officersUse")==1){
						addnav("Guild Options","runmodule.php?module=clanoptions&op=admin");
					}
				}
			}
		break;
	}
 	return $args;
}

function clanoptions_run() {
	global $session;
	$op = httpget('op');
	switch ($op) {
		case "fountain":
			page_header("Guild Fountain");
			clanoptions_fountain();
		break;
		case "admin":
			page_header("Guild Setup");
			clanoptions_setup();
		break;
	}
	page_footer();
}

function clanoptions_fountain() {
	global $session;
	addnav("Navigation");
	addnav("Return to your Guild","clan.php");
	$clan = $session['user']['clanid'];
	$type = httpget('type');
	$link = "runmodule.php?module=clanoptions&op=fountain&";
	addnav("Actions");
	switch ($type) {
		default:
		case "talk":
			output("`@You step under a curtain to hide from the rest of the Guild, and sit in the High Fountain, bathing in the cool water. You wave to your fellow Leaders and Officers. Members are not allowed here.`n");
			$time = gametime();
			$tomorrow = strtotime(date("Y-m-d H:i:s",$time)." + 1 day");
			$tomorrow = strtotime(date("Y-m-d 00:00:00",$tomorrow));
			$secstotomorrow = $tomorrow-$time;
			$realsecstotomorrow = $secstotomorrow / getsetting("daysperday",4);
			$authid = get_module_objpref("clans", $clan, "authID");
			if ($authID!=0) {
				$sql = "SELECT name FROM " . db_prefix("accounts") . " WHERE acctid='".$authid."'";
				$result = db_query_cached($sql, "clanoptions-fount-".$clan);
				$row = db_fetch_assoc($result);
				$author = $row['name'];
				unset($row,$result,$sql,$time,$secstotomorrow,$tomorrow,$time);
			} else {
				$author = translate_inline('Anonymous');
			}
			output("`@You read the poster that is attached to wall in front of the fountain.`nIt says `^%s`0`@... and you see the name `&%s`@ etched in a corner.`nYou then stare at the waterclock- `^%s`@.`nYou figure a new day in `^%s hours %s minutes and %s seconds`@.`0",str_replace("\'","'",str_replace("\\\"","\"",get_module_objpref("clans", $clan, "fountPoster"))),$author,getgametime(),date("G",strtotime("1970-01-01 00:00:00 + $realsecstotomorrow seconds")),date("i",strtotime("1970-01-01 00:00:00 + $realsecstotomorrow seconds")),date("s",strtotime("1970-01-01 00:00:00 + $realsecstotomorrow seconds")));
			addcommentary();
			output("`n`n`#You listen in..`n");
			viewcommentary("clanoptions-fount-".$session['user']['clanid'],"`#Talk?`@",25,"talks");
		break;
		case "poster":
			if (httpget('s')=='yes') {
				output("`@You decide the old poster is boring, and change it.");
				$poster = substr(str_replace("'","\'",httppost('poster')),0,255);
				set_module_objpref("clans", $clan, "fountPoster",$poster);
				set_module_objpref("clans", $clan, "authID",$session['user']['acctid']);
				$time = gametime();
				$tomorrow = strtotime(date("Y-m-d H:i:s",$time)." + 1 day");
				$tomorrow = strtotime(date("Y-m-d 00:00:00",$tomorrow));
				$secstotomorrow = $tomorrow-$time;
				$realsecstotomorrow = $secstotomorrow / getsetting("daysperday",4);
				$authID = $session['user']['name'];
				output("`@You read the poster that is attached to the wall opposite the fountain.`nIt says `^%s`0`@... with your name etched in a corner.`nYou then stare at the waterclock- `^%s`@.`nYou figure a new day in `^%s hours %s minutes and %s seconds`@.`0",str_replace("\'","'",str_replace("\\\"","\"",get_module_objpref("clans", $clan, "fountPoster"))),$author,getgametime(),date("G",strtotime("1970-01-01 00:00:00 + $realsecstotomorrow seconds")),date("i",strtotime("1970-01-01 00:00:00 + $realsecstotomorrow seconds")),date("s",strtotime("1970-01-01 00:00:00 + $realsecstotomorrow seconds")));
			} else {
				rawoutput("<form action='".$link."type=poster&s=yes' method='POST'>");
				addnav("",$link."type=poster&s=yes");
				output("`&`bNew Poster:`b `7(255 chars max)`n");
				rawoutput("<input name='poster' value='' maxlength='255' size='50'>");
				$submit = translate_inline('Replace');
				rawoutput("<input type='submit' name='submit' value=\"$submit\" class='button'>");
				rawoutput("</form>");
			}
		break;
	}
	addnav("Change the Poster",$link."type=poster");
	addnav("Talk to the Others",$link."type=talk");
	addnav("Navigation");
	villagenav();
	if ($session['user']['clanrank'] >= CLAN_LEADER){
		addnav("Guild Options","runmodule.php?module=clanoptions&op=admin");
	} elseif ($session['user']['clanrank'] == CLAN_OFFICER&&get_module_objpref("clans", $clan, "officersUse")==1){
		addnav("Guild Options","runmodule.php?module=clanoptions&op=admin");
	}
	addnav("Actions");
	modulehook("clanoptions-fountain");
}

function clanoptions_setup() {
	global $session;
	$clan = $session['user']['clanid'];
	$type = httpget('type');
	$link = "runmodule.php?module=clanoptions&op=admin";
	addnav("Navigation");
	addnav("Actions");
	switch ($type) {
		case "save":
			$minDKs=httppost('minDKs');
			if (is_numeric($minDKs)) {
				set_module_objpref("clans", $clan, "minDKs",$minDKs);
				output("`@`bThat minimum TK limit has been saved.`b`n`n");
			} else {
				output("`@`bThat minimum TK limit is not valid.`b`n`n");
			}
		break;
		case "toggle":
			$status = translate_inline((get_module_objpref("clans", $session['user']['clanid'], "officersUse")?"0":"1"));
			set_module_objpref("clans", $session['user']['clanid'], "officersUse",$status);
			$status = translate_inline(($status?"On":"Off"));
			output_notl("`n`n`b`Q%s `^%s `Q%s`b",translate_inline("You have just toggled Officers System Editing to"),$status,translate_inline("."));
		break;
		case "atoggle":
			$status = translate_inline((get_module_objpref("clans", $session['user']['clanid'], "autoAc")?"0":"1"));
			set_module_objpref("clans", $session['user']['clanid'], "autoAc",$status);
			$status = translate_inline(($status?"On":"Off"));
			output_notl("`n`n`b`Q%s `^%s `Q%s`b",translate_inline("You have just toggled Auto Accept to"),$status,translate_inline("."));
		break;
	}
	modulehook("clanoptions-admin-savetext");
	rawoutput("<form action='".$link."&type=save' method='POST'>");
	$b = translate_inline("Minimum Dragon Kills limit:");
	$s = translate_inline('Submit');
	output("`n`&");
	rawoutput("<b>$b"."</b> <input type='text' name='minDKs'> <input type='submit' value='$s'>");
	rawoutput("</form>");
	addnav("",$link."&type=save");
	modulehook("clanoptions-admin-form");
	output_notl("`n");
	addnav("Navigation");
	addnav("Return to your Guild","clan.php");
	villagenav();
	addnav("Guild Fountain","runmodule.php?module=clanoptions&op=fountain");
	addnav("Actions");
	if ($session['user']['clanrank'] >= CLAN_LEADER){
		$status = translate_inline((get_module_objpref("clans", $clan, "officersUse")?"On":"Off"));
		output_notl("`n`@%s `^%s `@%s",translate_inline("You currently have Officers Setup Editing"),$status,translate_inline(". If this is on, Officers and all higher ranks can change the settings, otherwise, only Leaders can."));
		addnav(array("`^%s`@%s`^%s",translate_inline("Toggle Guild Officers (Currently: "),$status,translate_inline(")")),$link."&type=toggle");
	}
	output("`n`@The minimum DK limit for your Guild is `&%s`@.",get_module_objpref("clans", $session['user']['clanid'], "minDKs"));
	output("`n`@Anyone who cannot change settings here will be kicked out, if they do not meet the requirement.");
	$status = translate_inline((get_module_objpref("clans", $clan, "autoAc")?"On":"Off"));
	output_notl("`n`@%s `^%s `@%s",translate_inline("You currently have Auto Accept"),$status,translate_inline(". If this is on, users will automatically be added to the Guild upon application."));
	addnav(array("`^%s`@%s`^%s",translate_inline("Toggle Auto Accept (Currently: "),$status,translate_inline(")")),$link."&type=atoggle");
	modulehook("clanoptions-admin-text");
}
?>