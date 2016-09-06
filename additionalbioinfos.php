<?php
//just did this mod to let users have more text
//yes,I like British English, but for users I will comply

//	Modified by MarcTheSlayer for Contessa
//	28/07/09 - v1.1
//	+ Made other alterations. 

function additionalbioinfos_getmoduleinfo()
{
	$additional = (is_module_installed('additionalbioinfos')?(get_module_setting('additional')?'`@enabled':'`$disabled'):'`$disabled');
	$charlimit = (is_module_installed('additionalbioinfos')?'`@'.get_module_setting('charlimit'):400);
	$physicalstats = (is_module_installed('additionalbioinfos')?(get_module_setting('physicalstats')?'`@enabled':'`$disabled'):'`$disabled');
	$allcolors = array("1","!","2","@","3","#","4","$","5","%","6","^","7","&",")","~","e","E","g","G","j","J","k","K","l","L","m","M","p","P","q","Q","R","T","t","V","v","x","X","y","Y");
	$num = 0;
	foreach($allcolors as $key){
		if ($string == "") $string .= "$num,$key,"; 
		else if ($num == 40) $string .= "$num,$key";
		else $string .= "$num,$key,";
		$num++;
	}
	

	$info = array(
	    "name"=>"Additional Bioinfos",
		"description"=>"This module offers more text to add to your bioinfos... in an extra box of course",
		"version"=>"1.1",
		"author"=>"`2Oliver Brendel, modified by `@MarcTheSlayer `2for `#C`&on`#t`&ess`#a, edited by `)J`lxy`Lt `&& `5Sara",
		"category"=>"Character",
		"override_forced_nav"=>true,
		"download"=>"http://dragonprime.net/dls/additionalbioinfos0.zip",
		"settings"=>array(
			"Additional Bioinformations - Settings,title",
				"additional"=>"Enable additional text,bool|1",
				"charlimit"=>"How many chars may the user enter?,int|65000",
				"`^Maximum chars allowed is 65000.,note",
				"physicalstats"=>"Let them enter physical stats?,bool|1",
		),
		"prefs"=>array(
		    "Biography,title",
			"user_showbioinfo"=>"Do you want to display this info in your bio?,bool|0",
			"`^Additional bio info is currently $additional`^.,note",
			"user_statcolor"=>"What color do you want to use for your stat names?,enum,".$string,
			"`^Additional bio info has a character limit of $charlimit`^.,note",
			"user_additionalbioinfo"=>"Additional Bio information `b`Vegg`b:,textarea,40",
			"user_family"=>"Family:,textarea,40",
			"user_friends"=>"Friends:,textarea,40",
			"`^Colour codes in the bio; and extra boxes are allowed; if you dont want one of these shown, simply keep it empty,note",
		),
	);
    return $info;
}

function additionalbioinfos_install()
{
    module_addhook('charstats');
    module_addhook_priority('bioinfo',90);
    module_addhook_priority('header-prefs',70);
	return TRUE;
}

function additionalbioinfos_uninstall()
{
	return TRUE;
}

function additionalbioinfos_dohook($hookname, $args)
{
	switch ($hookname){
		case "bioinfo":
			if (get_module_pref("user_showbioinfo",FALSE,$args['acctid'])) {
				additionalbioinfos_displaytext($args['acctid']);
			}
		break;
		case 'header-prefs':
			$bio = stripslashes(get_module_pref('user_additionalbioinfo'));
			$maxlength = get_module_setting('charlimit');
			if( strlen($bio) > $maxlength )
				$bio = substr($bio,0,$maxlength);
			$newbio = stripslashes($bio);
			while ($bio != $newbio) {
				$bio=$newbio;
				$newbio = stripslashes($bio);
			}
			set_module_pref('user_additionalbioinfo',stripslashes($bio));
		break;
        case 'charstats':
		    $open = translate_inline("Open");
		    addnav("runmodule.php?module=additionalbioinfos&op=read");
		    addcharstat("Other");
		    addcharstat("Your Bio", "<a href='runmodule.php?module=additionalbioinfos&op=read' target='additionalbioinfos' onClick=\"".popup("runmodule.php?module=additionalbioinfos&op=read").";return false;\">$open</a>");
        break;
	}
	return $args;
}

function additionalbioinfos_displaytext($id){
	if (get_module_setting("physicalstats")) {
		$sett = get_all_module_prefs(FALSE,$id);
		$abi = array();
		$checker = array(
			"basicinfo" => FALSE,
			"physique" => FALSE,
			"personalprefs" => FALSE,
			"rshipsfamily" => FALSE,
			"anyfriends" => FALSE
		);
		$abi['header_rshipsfamily'] = "Family";
		$family = stripslashes($sett["user_family"]);
		if ($family != ''){ $abi['Family'] = $family; $checker['rshipsfamily'] = TRUE; }
		$abi['header_anyfriends'] = "Friends";
		$friends = stripslashes($sett["user_friends"]);
		if ($friends != ''){ $abi['Friends'] = $friends; $checker['anyfriends'] = TRUE; }

		
		/* $newbio = false;
		if (!get_module_pref("user_display","newbio") && get_module_pref("user_bio","newbio")) $newbio = true;
		if ($newbio) rawoutput("<center><table>"); */
		foreach ($abi as $key => $val){
			/* if ($newbio){
				if (substr($key, 0, 6) == "header"){
					if ($checker[str_replace("header_","",$key)]){
						rawoutput("<tr><td colspan='2'>");
							output("`c`7`b~ $val ~`b`0`c");
						rawoutput("</td></tr>");
					}
				} else {
					rawoutput("<tr><td>");
						output("`^$key");
					rawoutput("</td><td>");	
						output("`@%s`n",$val);
					rawoutput("</td></tr>");
				}
			} else { */
				$val = stripslashes($val);
				$val = str_replace(chr(13),"`n",$val);
				if (substr($key, 0, 6) == "header"){
					if ($checker[str_replace("header_","",$key)]){
						output("`n`7`b$val:`b`0`n");
					}
				} else {
					output("%s`n",$val);
				}
			// }
		}
		// if ($newbio) rawoutput("</table></center><br />");
	}
	
	if( get_module_setting("additional") )
	{
		$bio = stripslashes(get_module_pref("user_additionalbioinfo",FALSE,$id));
		if( !empty($bio) )
		{
			$bio = stripslashes($bio);
			$bio = str_replace(chr(13),"`n",$bio);
			if (is_numeric($id)){
				$where = "acctid = $id";
			} else {
				$where = "login = '$id'";
			}
			$nn = db_fetch_assoc(db_query("SELECT name FROM ".db_prefix("accounts")." WHERE $where"));
			output("`n`b`7Biography`b:`n%s`0`n",$bio,TRUE);
		}
	}
}

function additionalbioinfos_run()
{

	$op = httpget("op");
	$id = httpget("id");

	popup_header("Your Bio");

	$text = get_module_pref("user_additionalbioinfo");
	$text = stripslashes($text);

	switch ($op) {
		case "read":
			output("<a href='runmodule.php?module=additionalbioinfos&op=edit'>Edit Bio</a>",TRUE);
			output("`n`n");

			$text = str_replace("\n", "`n", $text);
			if ($text == "")
				output("You should make a bio ;)");

			output("%s", $text);
			output("`n`n");
		break;

		case "save":
			$newtext = httppost("bio");
			$newtext = stripslashes($newtext);
			$newtext = str_replace("`n", "\n", $newtext);
			if ($newtext != $text) {
				// if (strlen($newtext) > 
				$text = $newtext;
				set_module_pref("user_additionalbioinfo", stripslashes($text));
				output("`^Modified bio saved`n`n");
			}
			/* fall through */
		case "edit":
			output("`0");
			rawoutput("<form action='runmodule.php?module=additionalbioinfos&op=save' method='POST'>");
			output("<a href='runmodule.php?module=additionalbioinfos&op=read'>View Bio</a>`0",TRUE);
			output("`\$(does `inot`i save)`0");

			rawoutput("<input type='submit' class='button' value='Save' style='float: right'>");

			output_notl("`n`n`c`0");

			$text = htmlentities($text, ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
			rawoutput("<textarea class='input' name='bio' cols='50' rows='15'>$text</textarea>");

			output_notl("`c`n`0");
			rawoutput("<input type='submit' class='button' value='Save' style='float: right'>");
			rawoutput("</form>");

		break;
	}
	popup_footer();
}
?>