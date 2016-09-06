<?php
/**
	Modified by MarcTheSlayer
	16/07/1009 - v0.12
	+ Changed hook to the loggedin version.
	+ Created an array filled with script names you want the SU grotto link to appear in.
	+ Added a module settings link. Idea from Twisted. :)
	08/03/2010 - v0.13
	+ Added forest hook to catch events and show an addnav to the module's settings.
	12/09/2010 - v0.14
	+ Removed forest hook and added specialinc/eventhandler code to the everyfooter-loggedin hook.
*/
function sulink_getmoduleinfo()
{
	$info = array(
		"name"=>"Superuser Link",
		"description"=>"Adds a Grotto link to a number of pages where you might need it. Plus a module's settings link where appropriate.",
		"version"=>"0.14",
		"author"=>"Chris Vorndran`2, modified by `@MarcTheSlayer",
		"category"=>"Administrative",
		"download"=>"http://dragonprime.net/index.php?topic=10456.0",
	);
	return $info;
}

function sulink_install()
{
	output("`c`b`Q%s 'sulink' Module.`b`n`c", translate_inline(is_module_active('sulink')?'Updating':'Installing'));
	module_addhook('everyfooter-loggedin');
	module_addhook('superuser');
	return true;
}

function sulink_uninstall()
{
	output("`n`c`b`Q'sulink' Module Uninstalled`0`b`c");
	return true;
}

function sulink_dohook($hookname,$args)
{
	global $session;

	switch( $hookname )
	{
		case 'everyfooter-loggedin':
			addnav('Superuser');
			// List of script names that you want the SU grotto link to appear in.
			$scriptname_array = array('bank','armor','bio','clan','forest','gardens','graveyard','gypsy','healer','hof','inn','list','lodge','mercenarycamp','prefs','pvp','rock','runmodule','stables','train','weapons');
			if( in_array($args['__scriptfile__'], $scriptname_array) && $session['user']['superuser'] &~ SU_DOESNT_GIVE_GROTTO )
			{
				addnav('X?`bSuperuser Grotto`b','superuser.php');
			}
			if( $args['__scriptfile__'] == 'runmodule' && $session['user']['superuser'] & SU_MANAGE_MODULES )
			{
				$module = httpget('module');
				addnav('Z?`bModule Settings`b','configuration.php?op=modulesettings&module='.$module);
			}
			if( (!empty($session['user']['specialinc']) || ($module = httpget('eventhandler')) !== FALSE) && $session['user']['superuser'] & SU_MANAGE_MODULES )
			{
				$module = ( !empty($module) ) ? str_replace('module-', '', $module) : str_replace('module:', '', $session['user']['specialinc']);
				addnav('Z?`bModule Settings`b','configuration.php?op=modulesettings&module='.$module);
			}
		break;

		case 'superuser':
			$session['user']['specialinc'] = '';
		break;
	}

	return $args;
}

function sulink_run()
{
}
?>