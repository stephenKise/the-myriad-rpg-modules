<?php
// Version history
// 1.0 - initial version by JT Traub
// 1.1 - Modifications by Sixf00t4 to actually charge for titles rather than
//       having them be 'freebies'
function titlechange_getmoduleinfo(){
	$info = array(
		"name"=>"Title Change",
		"author"=>"JT Traub",
		"version"=>"1.1",
		"download"=>"core_module",
		"category"=>"Lodge",
		"settings"=>array(
			"Title Change Module Settings,title",
//  			"initialpoints"=>"How many donator points needed to get first title change?,int|500",
//  			"extrapoints"=>"How many additional donator points needed for subsequent title changes?,int|0",
// 			"take"=>"Actually remove points when purchasing a title?,bool|0", // change by sixf00t4.
			"bold"=>"Allow bold?,bool|1",
			"italics"=>"Allow italics?,bool|1",
			"blank"=>"Allow blank titles?,bool|1",
			"spaceinname"=>"Allow spaces in custom titles?,bool|1",
		),
		"prefs"=>array(
			"Title Change User Preferences,title",
			"timespurchased"=>"How many title changes have been bought?,int|0",
		),
	);
	return $info;
}

function titlechange_install(){
	module_addhook_priority("lodge","1");
	module_addhook_priority("pointsdesc","1");
	return true;
}
function titlechange_uninstall(){
	return true;
}

function titlechange_dohook($hookname,$args){
	global $session;
	switch($hookname){
	case "pointsdesc":
		$args['count']++;
		$format = $args['format'];
		$str = "`7`bSilver Grade:`b`n`\$- `^Custom in-game titles.`n";
		output($str, true);
		break;
	case "lodge":
$pointsavailable =
	$session['user']['donation']-$session['user']['donationspent'];
		if ($pointsavailable >= 100){
			addnav("Use Points");
			addnav("Edit Title `@(100 DP)","runmodule.php?module=titlechange&op=titlechange");
		}
		break;
	}
	return $args;
}

function titlechange_run(){
	require_once("lib/sanitize.php");
	require_once("lib/names.php");
	global $session;
	$op = httpget("op");
	page_header("Donation Center");
	output("`Q`c`bChange Title`b`c");
	if ($op=="titlechange"){
		output("`2You can change the surname of your character, here. For example, when you start out you are 'Faulty' but after you have this unlocked you may change your 'Faulty' surname to whatever you please. Be sure to close your bold and italics tags, please!`n`n");
		$otitle = get_player_title();
		if ($otitle=="`0") $otitle="";
		output("`^Your title colors are currently: `\$");
		rawoutput($otitle."<br>");
		output("`^Your title shows up as: `\$%s`n`n", $otitle);
		if (httpget("err")==1) output("`\$Please enter a title.`n");
		rawoutput("<form action='runmodule.php?module=titlechange&op=titlepreview' method='POST'>");
		output("`2Title: ");
		rawoutput("<input id='input' name='newname' width='25' maxlength='300' value='".htmlentities($otitle, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."'>");
		rawoutput("<input type='submit' class='button' value='Preview'>");
		rawoutput("</form>");
		addnav("", "runmodule.php?module=titlechange&op=titlepreview");
	}elseif ($op=="titlepreview"){
		$ntitle = rawurldecode(httppost('newname'));
		$ntitle=newline_sanitize($ntitle);

		if ($ntitle=="") {
			if (get_module_setting("blank")) {
				$ntitle = "`0";
			}else{
				redirect("runmodule.php?module=titlechange&op=titlechange&err=1");
			}
		}
		if (!get_module_setting("bold")) $ntitle = str_replace("`b", "", $ntitle);
		if (!get_module_setting("italics")) $ntitle = str_replace("`i", "", $ntitle);
		//$ntitle = sanitize_colorname(get_module_setting("spaceinname"), $ntitle);
		$ntitle = preg_replace("/[`][cHw]/", "", $ntitle);
		$ntitle = sanitize_html($ntitle);

		$nname = get_player_basename();
		if ((substr_count($ntitle,'`i') % 2) != 0 || (substr_count($ntitle,'`b') % 2) != 0){
			output("`^There is an issue with your title! You need to make sure that you have starting and closing");
			rawoutput(" `i");
			output(" or");
			rawoutput(" `b");
			output("! You can copy what you entered below to revise your issue:`n");
			rawoutput($ntitle);
			addnav("Go back", "runmodule.php?module=titlechange&op=titlechange");
		}else{
			output("`^Your new title will look like: `0%s`0`n", $ntitle);
			output("`^Your entire name will look like: `0%s `0%s`0`n`n",
					$ntitle, $nname);
			addnav("`bConfirm Customization`b");
			addnav("Yes", "runmodule.php?module=titlechange&op=changetitle&newname=".rawurlencode($ntitle));
			addnav("No", "runmodule.php?module=titlechange&op=titlechange");
		}
	}elseif ($op=="changetitle"){
		$ntitle=rawurldecode(httpget('newname'));
		$session['user']['donationspent'] += 100;
		$fromname = $session['user']['name'];
		$newname = change_player_ctitle($ntitle);
		$session['user']['ctitle'] = $ntitle;
		$session['user']['name'] = $newname;
		addnews("%s`^ has become known as %s.",$fromname,$session['user']['name']);


		output("Your custom title has been set.");
		modulehook("namechange", array());
	}
	addnav("L?Return to the Lodge","lodge.php");
	page_footer();
}
?>
