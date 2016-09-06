<?php

function clanname_getmoduleinfo(){
	$info = array(
		"name"=>"Clan Name Change",
		"author"=>"`&Stephen Kise",
		"version"=>"1.0",
		"category"=>"Lodge",
		"download"=>"nope",
	);
	return $info;
}
function clanname_install(){
	module_addhook_priority("lodge","3");
	module_addhook_priority("pointsdesc","3");
	return true;
}
function clanname_uninstall(){
	return true;
}
function clanname_dohook($hookname,$args){
	global $session;
	switch ($hookname){
		case "lodge":
$pointsavailable =
	$session['user']['donation']-$session['user']['donationspent'];
			if ($pointsavailable > 500 && $session['user']['clanrank'] > 30){
				addnav("Use Points");
				addnav("Guild Name `@(500 DP)","runmodule.php?module=clanname&op=enter");
			}
		break;
		case "pointsdesc":
		$args['count']++;
		output("`\$- `^Ability to change your Guild's name and tag.`n");
		break;
	}
	return $args;
}
function clanname_run(){
	global $session;
	$op = httpget('op');
	page_header("Donation Center");
 	output("`Q`c`bGuild Name Change`b`c");
	switch ($op){
		case "enter":
		require_once("lib/sanitize.php");
			addnav("Back to the Donation Center","lodge.php");
			$row = db_fetch_assoc(db_query("SELECT * FROM clans WHERE clanid = {$session['user']['clanid']}"));
			output("`2Here you can change the coloring of your Guild tag which appears beside your name in the village/city commentary. You can also color the name of your Guild as it appears in the Guild list to give it a bit of 'spice' to potential applicants. Be sure to close your bold and italics tags, please!`n`n");
			output("`^Your color codes for your Guild look like: `\$");
			rawoutput("<{$row['clanshort']}> {$row['clanname']}<br>");
			output("`^Your colors look like: `\$<`2{$row['clanshort']}`\$> `&{$row['clanname']}`n`n");
			output("<form action='runmodule.php?module=clanname&op=set' method='POST'>",true);
				output("`2Guild. Name: ");
				rawoutput("<input id='input' name='clanname' width='75' maxlength='250' value='".htmlentities($row['clanname'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."'><br>");
				output("`2Guild. Tag: ");
				rawoutput("<input id='input' name='clantag' width='35' maxlength='25' value='".htmlentities($row['clanshort'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."'><br>");
				output("<input type='submit' value='Submit'>",true);
			output("</form>",true);
			addnav("","runmodule.php?module=clanname&op=set");
		break;
		case "set":
			$session['user']['donationspent'] += 500;
 			db_query("UPDATE clans SET clanname = '".rawurldecode(httppost('clanname'))."', clanshort = '".rawurldecode(httppost('clantag'))."' WHERE clanid = {$session['user']['clanid']}");
 			output("`^Congratulations! Your Guild's name and tag are now configured to look like `\$<`2".rawurldecode(httppost('clantag'))."`\$> `&".rawurldecode(httppost('clanname'))."`^!`n");
			addnav("Back to the Donation Center","lodge.php");
		break;
	}
	page_footer();
}
?>