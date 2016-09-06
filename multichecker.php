<?php
/*
	Modified by MarcTheSlayer

	== 26/09/08 - v1.0a ==
	+ Rewrote the code to put the names into groups with links to bio page and YoM along with character name.
	+ Added 'last on' column.
	+ Added referrer column.

	== 14/10/08 - v1.0b ==
	+ Added regdate column.
*/
function multichecker_getmoduleinfo()
{
	$info = array(
		"name"=>"Multichecker",
		"version"=>"1.0",
		"author"=>"`2Oliver Brendel, modified by `@MarcTheSlayer",
		"category"=>"Administrative",
		"download"=>"http://dragonprime.net/index.php?topic=9930.0"
	);
	return $info;
}

function multichecker_install()
{
	output("`c`b`Q%s 'multichecker' Module.`b`n`c", translate_inline(is_module_active('multichecker')?'Updating':'Installing'));
	module_addhook('superuser');
	return TRUE;
}

function multichecker_uninstall()
{
	output("`n`c`b`Q'multichecker' Module Uninstalled`0`b`c");
	return TRUE;
}

function multichecker_dohook($hookname, $args)
{
	global $session;

	if( $session['user']['superuser'] & SU_GIVES_YOM_WARNING )
	{
		addnav('Mechanics');
		addnav('Multichecker','runmodule.php?module=multichecker&op=ID');
	}

	return $args;
}

function multichecker_run()
{
	global $session;

	require_once('./lib/superusernav.php');
	superusernav();

	page_header('Multichecker');
	addnav('Multichecker');

	$op = httpget('op');

	switch( $op )
	{
		case 'IP':
			$sql = "SELECT a.acctid as acctid_a, a.name as name_a, a.login as login_a, a.loggedin as loggedin_a, a.laston as laston_a, a.emailaddress as email_a, a.clanid as clanid_a, a.clanrank as clanrank_a, a.referer as referer_a, a.regdate as regdate_a,
				b.acctid as acctid_b, b.name as name_b, b.login as login_b, b.loggedin as loggedin_b, b.laston as laston_b, b.emailaddress as email_b, b.clanid as clanid_b, b.clanrank as clanrank_b, b.referer as referer_b, b.regdate as regdate_b, a.lastip as nums
				FROM " . db_prefix('accounts') . " AS b 
				LEFT JOIN " . db_prefix('accounts') . " AS a 
				ON b.lastip = a.lastip
				WHERE b.login <> a.login
				ORDER BY a.acctid";
	
			output("Note: AOL proxys always have the same IP, AOL users are therefore treated as multi's.`n`n");
			output("University and other nets who have one proxy IP will also appear here, please check them carefully.`n`n");

			addnav('Check by Cookie ID','runmodule.php?module=multichecker&op=ID');
		break;

		case '':
		case 'ID':
			$sql = "SELECT a.acctid as acctid_a, a.name as name_a, a.login as login_a, a.loggedin as loggedin_a, a.laston as laston_a, a.emailaddress as email_a, a.clanid as clanid_a, a.clanrank as clanrank_a, a.referer as referer_a, a.regdate as regdate_a,
				b.acctid as acctid_b, b.name as name_b, b.login as login_b, b.loggedin as loggedin_b, b.laston as laston_b, b.emailaddress as email_b, b.clanid as clanid_b, b.clanrank as clanrank_b, b.referer as referer_b, b.regdate as regdate_b, a.uniqueid as nums
				FROM " . db_prefix('accounts') . " AS b 
				LEFT JOIN " . db_prefix('accounts') . " AS a 
				ON b.uniqueid = a.uniqueid
				WHERE b.login <> a.login
				ORDER BY a.acctid";

			output("Note: The cookie ID is stored on the users machine. If they use another browser, you won't be able to track them down. ");
			output("This check should be used WITH the IP check, because here you won't have to care for proxies like AOL, University nets and the like.`n`n");

			addnav('Check by IP','runmodule.php?module=multichecker&op=IP');
		break;
	}

	if( $result = db_query($sql) )
	{
		$array = array();
		$i = 0;
		while( $row = db_fetch_assoc($result) )
		{
			if( $row['nums'] )
			{
				// yes, you'll end up getting duplicates this way, but searching the array
				// to see if the account is already there slows the page down.
				$array[$row['nums']][$i]['acctid'] = $row['acctid_a'];
				$array[$row['nums']][$i]['name'] = $row['name_a'];
				$array[$row['nums']][$i]['login'] = $row['login_a'];
				$array[$row['nums']][$i]['loggedin'] = $row['loggedin_a'];
				$array[$row['nums']][$i]['laston'] = $row['laston_a'];
				$array[$row['nums']][$i]['email'] = $row['email_a'];
				$array[$row['nums']][$i]['clanid'] = $row['clanid_a'];
				$array[$row['nums']][$i]['clanrank'] = $row['clanrank_a'];
				$array[$row['nums']][$i]['referrer'] = $row['referer_a'];
				$array[$row['nums']][$i]['regdate'] = $row['regdate_a'];
				$i++;
				$array[$row['nums']][$i]['acctid'] = $row['acctid_b'];
				$array[$row['nums']][$i]['name'] = $row['name_b'];
				$array[$row['nums']][$i]['login'] = $row['login_b'];
				$array[$row['nums']][$i]['loggedin'] = $row['loggedin_b'];
				$array[$row['nums']][$i]['laston'] = $row['laston_b'];
				$array[$row['nums']][$i]['email'] = $row['email_b'];
				$array[$row['nums']][$i]['clanid'] = $row['clanid_b'];
				$array[$row['nums']][$i]['clanrank'] = $row['clanrank_b'];
				$array[$row['nums']][$i]['referrer'] = $row['referer_b'];
				$array[$row['nums']][$i]['regdate'] = $row['regdate_b'];
				$i++;
			}
		}

		$sql1 = "SELECT clanid, clanshort
			FROM " . db_prefix('clans');
		$result1 = db_query($sql1);
		$clan_array = array();
		while( $row1 = db_fetch_assoc($result1) )
		{
			$clan_array[$row1['clanid']] = $row1['clanshort'];	
		}
		$clanrankcolors = array("`!","`#","`^","`&","`\$");

		$writemail = translate_inline('Write Mail');
		$name = translate_inline('Login Name');
		$clan = translate_inline('Clan');
		$laston = translate_inline('Last on');
		$online = appoencode(translate_inline('`#(Online)'));
		$acctid = translate_inline('Acctid');
		$email = translate_inline('Email');
		$referred = translate_inline('Referred By');
		$regdate = translate_inline('Reg Date');
		$ip_id = ( $op == 'IP' ) ? translate_inline('IP Address') : translate_inline('Cookie ID:');

		rawoutput('<table width="100%" border="0" cellpadding="2" cellspacing="1">');

		$array2 = array();
		foreach( $array as $key => $value )
		{
				rawoutput("<tr class=\"trhead\"><td colspan=\"2\">$name</td><td align=\"center\">$clan</td><td nowrap=\"nowrap\">$laston</td><td>$acctid</td><td>$email</td><td nowrap=\"nowrap\">$referred</td><td align=\"center\" nowrap=\"nowrap\">$regdate</td></tr>");
				foreach( $value as $key2 )
				{
					// Stop the duplicates here, add name to a new smaller array and check it.
					if( !in_array($key2['login'],$array2) )
					{
						$time = ( $key2['loggedin'] ) ? $online : relativedate($key2['laston']);
						$regdate1 = relativedate($key2['regdate']);
						$regdate1 = ( $regdate1 == translate_inline('Never') ) ? '-' : $regdate1 . translate_inline(' ago');
						$clanid = ( $key2['clanid'] ) ? appoencode($clanrankcolors[ceil($key2['clanrank']/10)] . '<`2' . $clan_array[$key2['clanid']] . $clanrankcolors[ceil($key2['clanrank']/10)] . '>') : '-';
						$referrer = ( $key2['referrer'] ) ? $key2['referrer'] : '-';
						rawoutput("<tr class=\"trdark\"><td><a href=\"mail.php?op=write&to=" . rawurlencode($key2['login']) . "\" target=\"_blank\" onClick=\"" . popup("mail.php?op=write&to=" . rawurlencode($key2['login']) . "") . ";return false;\">");
						rawoutput('<img src="images/newscroll.GIF" width="16" height="16" alt="' . $writemail . '" border="0"></a></td>');
						rawoutput("<td><a href='bio.php?char=" . $key2['acctid'] . "&ret=" . urlencode($_SERVER['REQUEST_URI']) . "'>" . $key2['login'] . "</a><br />" . appoencode($key2['name']) . "</td><td align=\"center\">" . $clanid . "</td><td align=\"center\" nowrap=\"nowrap\">" . $time . "</td><td>" . $key2['acctid'] . "</td><td>" . $key2['email'] . "</td><td align=\"center\">" . $referrer . "</td><td align=\"center\">" . $regdate1 . "</td></tr>");
						addnav('','bio.php?char=' . $key2['acctid'] . '&ret=' . urlencode($_SERVER['REQUEST_URI']));
						$array2[] = $key2['login'];
					}
				}
				rawoutput('<tr class="trlight"><td colspan="2">' . $ip_id . '</td><td colspan="6">' . $key . '</td></tr>');
				rawoutput('<tr class="trdark"><td colspan="8">&nbsp;</td></tr>');
		}
		rawoutput('</table>');
		unset($array, $array2);
	}
	else
	{
		output('`n`&The check has come back empty. There are no multi accounts.');
	}

	page_footer();
}
?>