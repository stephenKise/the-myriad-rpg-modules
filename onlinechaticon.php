<?php
/**
	16/06/2011 - v0.0.2
	+ Added module name and player's acctid to get/set function calls.
	16/06/2011 - v0.0.3
	+ Fixed the problem for earlier versions. Added support for 'login' in the bio url link.
*/
function onlinechaticon_getmoduleinfo()
{
	$info = array(
		"name"=>"Online Chat Icon",
		"description"=>"Display online/offline icons for commentary posters.",
		"version"=>"0.0.3",
		"author"=>"`@MarcTheSlayer`2, idea (and images) from CavemanJoe",
		"category"=>"Commentary",
		"download"=>"",
		"settings"=>array(
			"README,title",
			"`^Make sure the 4 images in the zip are placed in `b'root/images/icons/onlinestatus/'`b. If it doesn't exist then create it.,note",
		),
		"prefs"=>array(
			"Preferences,title",
				"section"=>"Commentary section last in:,text",
		)
	);
	return $info;
}

function onlinechaticon_install()
{
	output("`c`b`Q%s 'onlinechaticon' Module.`b`n`c", translate_inline(is_module_active('onlinechaticon')?'Updating':'Installing'));
	module_addhook('blockcommentarea');
	module_addhook_priority('viewcommentary', 90);
	return TRUE;
}

function onlinechaticon_uninstall()
{
	output("`n`c`b`Q'onlinechaticon' Module Uninstalled`0`b`c");
	return TRUE;
}

$onlineacctids_array = array();
function onlinechaticon_dohook($hookname,$args)
{
	global $session, $chatsection, $onlineacctids_array;

	switch( $hookname )
	{
		case 'blockcommentarea':
			// This hook might get called twice so I globalled the variable so as not to have another sql query if it is.
			// It has the section name which the 'viewcommentary' hook doesn't.
			if( empty($chatsection) )
			{
				$chatsection = $args['section'];
				set_module_pref('section',$args['section'],'onlinechaticon',$session['user']['acctid']);
			}
		break;

		case 'viewcommentary':
			$section = get_module_pref('section','onlinechaticon',$session['user']['acctid']);
			$online = translate_inline('Online');
			$offline = translate_inline('Offline');
			$nearby = translate_inline('Nearby');

			$commentline = $args['commentline'];
			// Player's names are linked to their bio so look for a key found only in the link.
			if( strpos($commentline, 'char=') !== FALSE )
			{
				// Now get the acctid from the string.
				if( preg_match("/char=(.*)&ret/", $commentline, $matches) == 1 )
				{
					$acctid = $matches[1];
					if( $acctid == $session['user']['acctid'] || $acctid == $session['user']['login'] )
					{
						$args['commentline'] = '<img src="images/icons/onlinestatus/nearby.png" width="3" height="16" title="'.$nearby.'" alt="'.$nearby.'" align="top" />&nbsp;' . $commentline;
					}
					else
					{
						if( !isset($onlineacctids_array[$acctid]) )
						{
							$accounts = db_prefix('accounts');
							$moduleprefs = db_prefix('module_userprefs');
							$where = ( is_numeric($acctid) ) ? "$accounts.acctid = '$acctid'" : "$accounts.login = '$acctid'";
							$sql = "SELECT $accounts.login, $accounts.acctid, $accounts.loggedin, $moduleprefs.value
									FROM $accounts
									LEFT JOIN $moduleprefs
										ON $accounts.acctid = $moduleprefs.userid
									WHERE $moduleprefs.modulename = 'onlinechaticon'
										AND $moduleprefs.setting = 'section'
										AND $where";
							$result = db_query($sql);
							$row = db_fetch_assoc($result);
							$onlineacctids_array[$row['acctid']]['online'] = $row['loggedin'];
							$onlineacctids_array[$row['acctid']]['section'] = $row['value'];
							$onlineacctids_array[$row['login']]['online'] = $row['loggedin'];
							$onlineacctids_array[$row['login']]['section'] = $row['value'];
						}

						if( $onlineacctids_array[$acctid]['online'] == 1 && $onlineacctids_array[$acctid]['section'] == $section )
						{
							$args['commentline'] = '<img src="images/icons/onlinestatus/nearby.png" width="3" height="16" title="'.$nearby.'" alt="'.$nearby.'" align="top" />&nbsp;' . $commentline;
						}
						elseif( $onlineacctids_array[$acctid]['online'] == 1 && $onlineacctids_array[$acctid]['section'] != $section )
						{
							$args['commentline'] = '<img src="images/icons/onlinestatus/online.png" width="3" height="16" title="'.$online.'" alt="'.$online.'" align="top" />&nbsp;' . $commentline;
						}
						else
						{
							$args['commentline'] = '<img src="images/icons/onlinestatus/offline.png" width="3" height="16" title="'.$offline.'" alt="'.$offline.'" align="top" />&nbsp;' . $commentline;
						}
					}
				}
			}
			else
			{
				// For non player like /game. Keeps things aligned.
				$args['commentline'] = '<img src="images/icons/onlinestatus/nonplayer.png" width="3" height="16" align="top" />&nbsp;' . $commentline;
			}
		break;
	}

	return $args;
}

function onlinechaticon_run()
{
}
?>