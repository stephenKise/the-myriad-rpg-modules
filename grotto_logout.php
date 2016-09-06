<?php
//http://thequidditchpitch.org/viewstory.php?sid=6774&ageconsent=ok&warning=5
/**
	07/06/09 - v0.0.2
	+ Fixed log out problem that was caused by logging out from the Grotto more
	  than once and never haven gone to mundane in between.

	26/06/09 - v0.0.3
	+ Rewrote the code. Now hooks into login and logout.
*/
function grotto_logout_getmoduleinfo()
{
	$info = array(
		"name"=>"Grotto Logout",
		"description"=>"A logout link in the grotto.",
		"version"=>"0.0.3",
		"author"=>"`@MarcTheSlayer",
		"category"=>"Administrative",
		"download"=>"http://dragonprime.net/index.php?topic=10170.0",
		"settings"=>array(
			"Settings,title",
			"logoutloc"=>"Location players log out to:,|The Grotto",
			"`^Each player must be granted access to this logout option to have it available to them. Except Megausers.,note",
		),
		"prefs"=>array(
			"lastloc"=>"Last location of player before logging out:,",
		)
	);
	return $info;
}

function grotto_logout_install()
{
	output("`c`b`Q%s 'grotto_logout' Module.`b`n`c", translate_inline(is_module_active('grotto_logout')?'Updating':'Installing'));
	module_addhook('superuser');
	module_addhook('player-login');
	module_addhook('player-logout');
	return TRUE;
}

function grotto_logout_uninstall()
{
	output("`n`c`b`Q'grotto_logout' Module Uninstalled`0`b`c");
	return TRUE;
}

function grotto_logout_dohook($hookname,$args)
{
	global $session;

	if( $session['user']['superuser'] & SU_EDIT_CREATURES )
	{
		switch( $hookname )
		{
			case 'superuser':
				addnav('Navigation');
				addnav('Log Out','login.php?op=logout');
			break;

			case 'player-login':
				// Had to include module name and userid because without them it wasn't working. :(
				$lastloc = get_module_pref('lastloc','grotto_logout',$session['user']['acctid']);
				if( !empty($lastloc) )
				{
					$session['user']['location'] = $lastloc;
					clear_module_pref('lastloc','grotto_logout',$session['user']['acctid']);
				}
			break;

			case 'player-logout':
				set_module_pref('lastloc',$session['user']['location']);
				$session['user']['location'] = get_module_setting('logoutloc');
				$session['user']['restorepage'] = 'superuser.php';
			break;
		}
	}

	return $args;
}

function grotto_logout_run()
{
}
?>