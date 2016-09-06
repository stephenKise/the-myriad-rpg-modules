<?php
function lodgeneverexpire_getmoduleinfo()
{
	$info = array(
		"name"=>"Lodge - Account Never Expires",
		"description"=>"Allow players to buy 'never expire' pref at the Lodge, or be awarded it.",
		"version"=>"1.0.0",
		"author"=>"`@MarcTheSlayer",
		"category"=>"Lodge",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1454",
		"settings"=>array(
			"Buy Pref,title",
				"cost"=>"How much does 'never expire' pref cost?,int|2000",
				"`^Set to zero (0) to disable.,note",
			"Award Pref,title",
				"award"=>"Award pref if player *spends* this amount:,int|20000",
				"`^Set to zero (0) to disable.,note",
// 			"DK Reward Pref,title",
// 				"reachdk"=>"Award pref if player has this many Dragon kills:,int|300",
// 				"`^Set to zero (0) to disable.,note",
		),
		"prefs"=>array(
			"bought"=>"Has player bought the flag?,string,20",
			"awarded"=>"Has player spent enough points for flag?,string,20",
			"dkreward"=>"Has player killed enough Dragons for flag?,string,20",
		)
	);
	return $info;
}

function lodgeneverexpire_install()
{
	output("`c`b`Q%s 'lodgeneverexpire' Module.`b`n`c", translate_inline(is_module_active('lodgeneverexpire')?'Updating':'Installing'));
	module_addhook_priority('delete_character',10);
	module_addhook_priority('lodge','31');
	module_addhook_priority('pointsdesc','31');
// 	module_addhook('lodge_incentives');
// 	module_addhook('dragonkill');
	module_addhook('superuser');
	return TRUE;
}

function lodgeneverexpire_uninstall()
{
	output("`n`c`b`Q'lodgeneverexpire' Module Uninstalled`0`b`c");
	return TRUE;
}

function lodgeneverexpire_dohook($hookname,$args)
{
	global $session;

	$cost = get_module_setting('cost');

	switch( $hookname )
	{
		case 'delete_character':
			if( get_module_pref('bought') > '' || get_module_pref('awarded') > '' || get_module_pref('dkreward') > '' ) $args['dodel'] = FALSE;
		break;

		case 'lodge':
			if( get_module_pref('bought') == '' && get_module_pref('awarded') == '' && get_module_pref('dkreward') == '' && !($session['user']['superuser'] & SU_NEVER_EXPIRE) )
			{
// 				if( $session['user']['donationspent'] >= get_module_setting('award') && get_module_setting('award') > 0 )
// 				{
// 					set_module_pref('awarded',date("Y-m-d H:i:s"));
// 					require_once('lib/systemmail.php');
// 					$subject = translate_mail('`@Thank You For Your Support!`0');
// 					$message = translate_mail(array('`2Dear `#%s`2, Because you have spent over %s Lodge points, you have been awarded the "Never Expire" pref.`n`n
// 					This pref is assigned to your account and will protect it from getting automatically deleted should you find yourself unable to log into the game for more than %s days.`n`n
// 					Thankyou for helping to keep this website going.`n`n-The Staff.', $session['user']['name'], get_module_setting('award'), getsetting('expireoldacct',45)));
// 					systemmail($session['user']['acctid'],$subject,$message);
// 					debuglog('`^has been awarded the NEVER EXPIRE pref for spending a total of ' . get_module_setting('award') . ' points at the lodge.');
// 				}
				if( get_module_pref('awarded') == '' && $session['user']['donation'] > 5000 )
				{
					$points = translate_inline(array('point','points'));
					addnav('Use Points');
					addnav('Never Expire `@(2000 DP)','runmodule.php?module=lodgeneverexpire&op=lodge&sop=buy');
				}
			}
		break;

		case 'pointsdesc':
			$args['count']++;
			output("`^`bGold Grade:`b`n`\$- `^Never have your account expire.`n");
		break;

// 		case 'lodge_incentives':
// 			$points = $args['points'];
// 			$points[$cost][] = translate("`^Character account never expires (never auto deleted).");
// 			$args['points'] = $points;
// 		break;

// 		case 'dragonkill':
// 			if( get_module_pref('dkreward') == '' )
// 			{
// 				if( $session['user']['dragonkills'] >= get_module_setting('reachdk') && get_module_setting('reachdk') > 0 )
// 				{
// 					set_module_pref('dkreward',date("Y-m-d H:i:s"));
// 					require_once('lib/systemmail.php');
// 					$subject = translate_mail('`@Congratulations!`0');
// 					$message = translate_mail(array('`2Dear `#%s`2, Because you have slain the Dragon %s times, you have been awarded the "Never Expire" pref.`n`n
// 					This pref is assigned to your account and will protect it from getting automatically deleted should you find yourself unable to log into the game for more than %s days.`n`n
// 					Thankyou for helping to keep this Realm safe.`n`n--The Staff.', $session['user']['name'], get_module_setting('reachdk'), getsetting('expireoldacct',45)));
// 					systemmail($session['user']['acctid'],$subject,$message);
// 					debuglog('`^has been rewarded with the NEVER EXPIRE pref for slaying ' . get_module_setting('reachdk') . ' Dragons.');
// 				}
// 			}
// 		break;

		case 'superuser':
			if( $session['user']['superuser'] & SU_EDIT_USERS )
			{
				addnav('Mechanics');
				addnav('Never Expire Players','runmodule.php?module=lodgeneverexpire&op=seeplayers');
			}
		break;
	}

	return $args;
}

function lodgeneverexpire_run()
{
	global $session;

	$cost = get_module_setting('cost');
	$points = translate_inline(array('point','points'));

	page_header("Hunter's Lodge");

	$op = httpget('op');
	if( $op == 'lodge' )
	{
		page_header("Hunter's Lodge");
		$sop = httpget('sop');
		if( $sop == 'buy' )
		{
			output("`n`7J. C. Petersen turns to you. \"`&Expired or non active accounts currently get deleted after %s days and so to make your account one that will never expire and therefore never get automatically deleted, will cost %s %s,`7\" he says.  \"`&Will this suit you?`7\"`n`n", getsetting('expireoldacct',45), $cost, ($cost==1?$point[0]:$points[1]));

			addnav('Confirm Purchase');
			addnav('Yes','runmodule.php?module=lodgeneverexpire&op=lodge&sop=confirm');
			addnav('No','lodge.php');
		}
		elseif( $sop == 'confirm' )
		{
			global $session;

			if( $session['user']['donation'] >= 5000 )
			{
				output("`n`7J. C. Petersen writes out a YoM to the person in charge and sends it off. \"`&There you go `7%s`&. Your account will be protected from automatic deletion.`7\"", $session['user']['name']);
				set_module_pref('bought',date("Y-m-d H:i:s"));
				debuglog('`^has activated their NEVER EXPIRE.');
			}
			else
			{
				output("`n`7J. C. Petersen looks down his nose at you. \"`&I'm sorry, but you do not have the donation grade required. Please return when you do and I'll be happy to do business with you.`7\"");
			} 
			addnav('L?Return to the Lodge','lodge.php');
		}
	}
	elseif( $op == 'seeplayers' )
	{
		page_header('Players with Never Expire Pref');

		$writemail = translate_inline('Write Mail');
		$when = translate_inline('When');
		$empty = translate_inline('- empty -');
		$flag_array = array();

		//
		// Bought.
		//
		$sql = "SELECT a.acctid, a.name, a.login, b.value
				FROM " . db_prefix('accounts') . " a, " . db_prefix('module_userprefs') . " b
				WHERE b.modulename = 'lodgeneverexpire'
					AND b.setting = 'bought'
					AND b.value > ''
					AND a.acctid = b.userid
				ORDER BY a.dragonkills";
		$result = db_query($sql);

		$bought = translate_inline('Bought');
		output('Pref can be bought for %s lodge points.`n', get_module_setting('cost'));
		rawoutput('<table border="0" cellpadding="2" cellspacing="1">');
		rawoutput('<tr class="trhead"><td align="center">' . $bought . '</td><td align="center">' . $when . '</td></tr>');

		if( db_num_rows($result) > 0 )
		{
			$i = 0;
			while( $row = db_fetch_assoc($result) )
			{
				$flag_array[$row['acctid']] = '';
				rawoutput("<tr class=\"" . ($i%2?'trlight':'trdark') . "\"><td nowrap=\"nowrap\"><a href=\"mail.php?op=write&to=" . rawurlencode($row['login']) . "\" target=\"_blank\" onClick=\"" . popup("mail.php?op=write&to=" . rawurlencode($row['login']) . "") . ";return false;\">");
				rawoutput('<img src="images/newscroll.GIF" width="16" height="16" alt="' . $writemail . '" border="0"></a>');
				rawoutput("<a href='bio.php?char=" . $row['acctid'] . "&ret=" . urlencode($_SERVER['REQUEST_URI']) . "'>" . appoencode($row['name']) . "</a></td><td>" . $row['value'] . "</td></tr>");
				addnav('','bio.php?char=' . $row['acctid'] . '&ret=' . urlencode($_SERVER['REQUEST_URI']));
			}
		}
		else
		{
			rawoutput("<tr class=\"trlight\"><td colspan=\"2\" align=\"center\">" . $empty . "</td></tr>");
		}

		rawoutput('</table><br /><br />');

		//
		// Awarded.
		//
		$sql = "SELECT a.acctid, a.name, a.login, a.donation, a.donationspent, b.value
				FROM " . db_prefix('accounts') . " a, " . db_prefix('module_userprefs') . " b
				WHERE b.modulename = 'lodgeneverexpire'
					AND b.setting = 'awarded'
					AND b.value > ''
					AND a.acctid = b.userid
				ORDER BY a.dragonkills";
		$result = db_query($sql);

		$awarded = translate_inline('Awarded');
		$spent = translate_inline('Spent');
		output('Pref is awarded for spending %s lodge points.`n', get_module_setting('award'));
		rawoutput('<table border="0" cellpadding="2" cellspacing="1">');
		rawoutput('<tr class="trhead"><td align="center">' . $awarded . '</td><td align="center">' . $spent . '</td><td align="center">' . $when . '</td></tr>');

		if( db_num_rows($result) > 0 )
		{
			$i = 0;
			while( $row = db_fetch_assoc($result) )
			{
				$flag_array[$row['acctid']] = '';
				rawoutput("<tr class=\"" . ($i%2?'trlight':'trdark') . "\"><td nowrap=\"nowrap\"><a href=\"mail.php?op=write&to=" . rawurlencode($row['login']) . "\" target=\"_blank\" onClick=\"" . popup("mail.php?op=write&to=" . rawurlencode($row['login']) . "") . ";return false;\">");
				rawoutput('<img src="images/newscroll.GIF" width="16" height="16" alt="' . $writemail . '" border="0"></a>');
				rawoutput("<a href='bio.php?char=" . $row['acctid'] . "&ret=" . urlencode($_SERVER['REQUEST_URI']) . "'>" . appoencode($row['name']) . "</a></td><td align=\"center\">" . $row['donationspent'] . "</td><td>" . $row['value'] . "</td></tr>");
				addnav('','bio.php?char=' . $row['acctid'] . '&ret=' . urlencode($_SERVER['REQUEST_URI']));
			}
		}
		else
		{
			rawoutput("<tr class=\"trlight\"><td colspan=\"3\" align=\"center\">" . $empty . "</td></tr>");
		}

		rawoutput('</table><br /><br />');

		//
		// DK Reward.
		//
		$sql = "SELECT a.acctid, a.name, a.login, a.dragonkills, b.value
				FROM " . db_prefix('accounts') . " a, " . db_prefix('module_userprefs') . " b
				WHERE b.modulename = 'lodgeneverexpire'
					AND b.setting = 'dkreward'
					AND b.value > ''
					AND a.acctid = b.userid
				ORDER BY a.dragonkills";
		$result = db_query($sql);

		$dkreward = translate_inline('DK Reward');
		$kills = translate_inline('Kills');
		output('Pref is awarded for defeating the Dragon %s times.`n', get_module_setting('reachdk'));
		rawoutput('<table border="0" cellpadding="2" cellspacing="1">');
		rawoutput('<tr class="trhead"><td align="center">' . $dkreward . '</td><td align="center">' . $kills . '</td><td align="center">' . $when . '</td></tr>');

		if( db_num_rows($result) > 0 )
		{
			$i = 0;
			while( $row = db_fetch_assoc($result) )
			{
				$flag_array[$row['acctid']] = '';
				rawoutput("<tr class=\"" . ($i%2?'trlight':'trdark') . "\"><td nowrap=\"nowrap\"><a href=\"mail.php?op=write&to=" . rawurlencode($row['login']) . "\" target=\"_blank\" onClick=\"" . popup("mail.php?op=write&to=" . rawurlencode($row['login']) . "") . ";return false;\">");
				rawoutput('<img src="images/newscroll.GIF" width="16" height="16" alt="' . $writemail . '" border="0"></a>');
				rawoutput("<a href='bio.php?char=" . $row['acctid'] . "&ret=" . urlencode($_SERVER['REQUEST_URI']) . "'>" . appoencode($row['name']) . "</a></td><td align=\"center\">" . $row['dragonkills'] . "</td><td>" . $row['value'] . "</td></tr>");
				addnav('','bio.php?char=' . $row['acctid'] . '&ret=' . urlencode($_SERVER['REQUEST_URI']));
			}
		}
		else
		{
			rawoutput("<tr class=\"trlight\"><td colspan=\"3\" align=\"center\">" . $empty . "</td></tr>");
		}

		rawoutput('</table><br /><br />');

		addnav('Navigation');
		addnav('The Grotto','superuser.php');
	}

	page_footer();
}
?>