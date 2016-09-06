<?php
/**
	18/06/09 - v0.0.2
	+ I had changed double to single quotes resulting in a few $op breaking and causing badnavs. Thanks kaizerDRAGON. :)

	19/06/09 - v0.0.3
	+ Gamelog table gets a new entry each time a module is installed/uninstalled/reinstalled/activated/deactivated.
	  It does this by hooking into 'header-modules' and performing the tasks first then wiping the query.
	+ Can view gamelog ops by superuser.
	+ Can view gamelog ops for a particular superuser.

	26/06/09 - v0.0.4
	+ Blocked the next and previous links in the gamelog and replaced them with a link for each page.
	+ When viewing the 'View by Superuser' page, player's names are clickable.

	24/02/10 - v0.0.5
	+ Now lists modules in order of when they were last modified and uploaded without being reinstalled.

	02/09/10 - v0.0.6
	+ Added support for 'translationwizard' module.
	+ Added option to replace "manage modules" link in the grotto.
*/
function last_installed_getmoduleinfo()
{
	$info = array(
		"name"=>"Additional Module Operations",
		"description"=>"Displays last installed and deactivated module pages. Record and access module install/uninstall/reinstall/activate/deactivate gamelog data.",
		"version"=>"0.0.6",
		"author"=>"`@MarcTheSlayer",
		"category"=>"Administrative",
		"download"=>"http://dragonprime.net/index.php?topic=10228.0",
		"settings"=>array(
			"Settings,title",
				"maxlimit"=>"Max amount of modules to display?,int|10",
				"grottolink"=>"Replace 'manage modules' link in grotto?,bool",
				"`^Replace the uninstalled modules page. Quicker page load.,note",
				"grottonav"=>"Rename the nav text as well:,string,40|`^Manage Modules",
				"lastpage"=>"Link to which page?,enum,0,Last Installed,1,Last Updated|0",
		),
	);
	return $info;
}

function last_installed_install()
{
	module_addhook('changesetting');
	module_addhook('header-modules');
	module_addhook('header-configuration');
	return TRUE;
}

function last_installed_uninstall()
{
	output("`n`c`b`Q'last_installed' Module Uninstalled`0`b`c");
	return TRUE;
}

function last_installed_dohook($hookname,$args)
{
	switch( $hookname )
	{
		case 'changesetting':
			if( $args['module'] == 'last_installed' && $args['setting'] == 'grottolink' )
			{
				if( $args['new'] == 1 ) module_addhook('superuser');
				else module_drophook('superuser');
			}
		break;

		case 'superuser':
			global $session;
			if( $session['user']['superuser'] & SU_MANAGE_MODULES )
			{
				$default = translate_inline('Manage Modules');
				$nav_text = get_module_setting('grottonav');
				$last_page = ( get_module_setting('lastpage') == 1 ) ? 'update' : 'last';
				addnav('Editors');
				addnav(array('%s`0',($nav_text?$nav_text:$default)),'runmodule.php?module=last_installed&op2='.$last_page);
				blocknav('modules.php');
			}
		break;

		case 'header-modules':
			last_installed_ops();
		// FALL-THROUGH.
		case 'header-configuration':
			addnav('Navigation'); // This keeps the below 2 links appearing at the top.
			addnav('Module Extras');
			addnav('Last Installed Modules','runmodule.php?module=last_installed&op2=last');
			addnav('Last Updated Modules','runmodule.php?module=last_installed&op2=update');
			addnav('Deactivated Modules','runmodule.php?module=last_installed&op2=deac');
		break;
	}

	return $args;
}

function last_installed_run()
{
	global $session;

	addnav('Navigation');
	require_once('lib/superusernav.php');
	superusernav();

	$op2 = httpget('op2');

	if( $op2 == 'last' || $op2 == 'deac' || $op2 == 'update' )
	{
		last_installed_ops();

		require_once('lib/sanitize.php');

		$ops = translate_inline('Ops');
		$status = translate_inline('Status');
		$mname = translate_inline('Module Name');
		$cat = translate_inline('Category');
		$mauth = translate_inline('Module Author');
		$install = translate_inline('Installed');
		$ago = translate_inline('ago');
		$active = translate_inline('`@Active`0');
		$inactive = translate_inline('`$Inactive`0');
		$activate = translate_inline('Activate');
		$deactivate = translate_inline('Deactivate');
		$reinstall = translate_inline('Reinstall');
		$uninstall = translate_inline('Uninstall');
		$strsettings = translate_inline('Settings');
		$strnosettings = translate_inline('`$No Settings`0');
		$noops = translate_inline('This module created this page.');
		$uninstallconfirm = translate_inline('Are you sure you wish to uninstall this module?  All user preferences and module settings will be lost. If you wish to temporarily remove access to the module, you may simply deactivate it.');

		$limit = get_module_setting('maxlimit');
		$limit = ( $limit > 0 ) ? $limit : 10;
		addnav('Module Categories');
		if( $op2 == 'deac' )
		{
			$op = '&op2=deac';
			page_header('Deactivated Modules');
			addnav('Last Installed Modules','runmodule.php?module=last_installed&op2=last');
			addnav('Last Updated Modules','runmodule.php?module=last_installed&op2=update');
			$no_modules = translate_inline('No Deactivated Modules');
			$sql = "SELECT *
					FROM " . db_prefix('modules') . "
					WHERE active = 0
					ORDER BY installdate DESC
					LIMIT $limit";
		}
		elseif( $op2 == 'last' )
		{
			$op = '&op2=last';
			page_header('Last Installed Modules');
			addnav('Last Updated Modules','runmodule.php?module=last_installed&op2=update');
			addnav('Deactivated Modules','runmodule.php?module=last_installed&op2=deac');
			$no_modules = translate_inline('No Modules Installed');
			$sql = "SELECT *
					FROM " . db_prefix('modules') . "
					ORDER BY installdate DESC
					LIMIT $limit";
		}
		elseif( $op2 == 'update' )
		{
			$op = '&op2=update';
			page_header('Last Updated Modules');
			addnav('Last Installed Modules','runmodule.php?module=last_installed&op2=last');
			addnav('Deactivated Modules','runmodule.php?module=last_installed&op2=deac');
			$no_modules = translate_inline('No Modules Updated Since Installation');
			$sql = "SELECT *
					FROM " . db_prefix('modules') . "
					WHERE installdate != filemoddate
					ORDER BY filemoddate DESC
					LIMIT $limit";
			$install = translate_inline('Updated');
		}

		rawoutput('<form action="runmodule.php?module=last_installed'.$op.'&op=mass" method="POST">');
		addnav('','runmodule.php?module=last_installed'.$op.'&op=mass');
		rawoutput('<br /><table border="0" cellpadding="2" cellspacing="1" bgcolor="#999999">');
		rawoutput("<tr class=\"trhead\"><td>&nbsp;</td><td>$ops</td><td>$status</td><td>$cat</td><td>$mname</td><td>$mauth</td><td>$install</td></tr>");

		$time = time();
		$result = db_query($sql);
		$count = db_num_rows($result);
		if( $count == 0 )
		{
			rawoutput('<tr class="trlight"><td colspan="7" align="center">');
			output_notl("`i-- %s --`i", $no_modules);
			rawoutput('</td></tr>');
		}
		else
		{
			$i = 1;
			while( $row = db_fetch_assoc($result) )
			{
				rawoutput('<tr class="'.($i%2?'trlight':'trdark').'"><td valign="top">'.($row['modulename']!='last_installed'?'<input type="checkbox" name="module[]" value="'.$row['modulename'].'">':'').'</td><td valign="top" nowrap="nowrap">[ ');

				if( $row['modulename'] != 'last_installed' )
				{
					if( $row['active'] == 1 && $op == '&op2=last' )
					{
						rawoutput("<a href='runmodule.php?module=last_installed$op&op=deactivate&modulename={$row['modulename']}'>$deactivate</a>");
						addnav('','runmodule.php?module=last_installed'.$op.'&op=deactivate&modulename='.$row['modulename']);
					}
					else
					{
						rawoutput("<a href='runmodule.php?module=last_installed$op&op=activate&modulename={$row['modulename']}'>$activate</a>");
						addnav('','runmodule.php?module=last_installed'.$op.'&op=activate&modulename='.$row['modulename']);
					}

					rawoutput(" | <a href='runmodule.php?module=last_installed$op&op=uninstall&modulename={$row['modulename']}' onClick='return confirm(\"$uninstallconfirm\");'>$uninstall</a>");
					addnav('','runmodule.php?module=last_installed'.$op.'&op=uninstall&modulename='.$row['modulename']);
					rawoutput(" | <a href='runmodule.php?module=last_installed$op&op=reinstall&modulename={$row['modulename']}'>$reinstall</a>");
					addnav('','runmodule.php?module=last_installed'.$op.'&op=reinstall&modulename='.$row['modulename']);
				}
				else
				{
					rawoutput($noops);
				}

				if( $session['user']['superuser'] & SU_EDIT_CONFIG )
				{
					if( strstr($row['infokeys'], "|settings|") )
					{
						rawoutput(" | <a href='configuration.php?op=modulesettings&module={$row['modulename']}'>$strsettings</a>");
						addnav('',"configuration.php?op=modulesettings&module={$row['modulename']}");
					}
					else
					{
						output_notl(' | %s', $strnosettings);
					}
				}

				rawoutput(' ]</td><td valign="top">');
				output_notl('%s', ($row['active']?$active:$inactive));
				rawoutput('</td><td valign="top"><a href="modules.php?cat='.$row['category'].'">');
				addnav('','modules.php?cat='.$row['category']);
				output_notl('`&%s`0', $row['category']);
				rawoutput('</a></td><td nowrap="nowrap" valign="top"><span title="' . (isset($row['description'])&&!empty($row['description'])?$row['description']:sanitize($row['formalname'])) . '">');
				output_notl('%s %s', $row['formalname'], $row['version']);
				rawoutput('<br />');
				output_notl('(%s) ', $row['modulename']);
				rawoutput('</span></td><td valign="top">');
				output_notl('`#%s`0', $row['moduleauthor'], true);
				rawoutput('</td><td nowrap="nowrap" valign="top">');
				output_notl('%s %s`n%s', last_installed_time(strtotime(($op2=='last'?$row['installdate']:$row['filemoddate']))), $ago, $row['installedby']);
				rawoutput('</td></tr>');
				$i++;
			}
		}

		rawoutput('</table><br />');

		if( $count > 0 )
		{
			rawoutput('<input type="submit" name="activate" class="button" value="'.$activate.'">');
			if( $op == '&op2=last' )
			{
				rawoutput('<input type="submit" name="deactivate" class="button" value="'.$deactivate.'">');
			}
			rawoutput('<input type="submit" name="uninstall" class="button" value="'.$uninstall.'">');
			rawoutput('<input type="submit" name="reinstall" class="button" value="'.$reinstall.'">');
		}
		rawoutput('</form>');

		addnav('Manage Modules','modules.php');
	}

	page_footer();
}

function last_installed_ops()
{
	// Haleth asked for this so they'd still get the option to uninstall translations from the database.
	if( is_module_active('translationwizard') ) modulehook('header-modules', FALSE, FALSE, 'translationwizard');

	$op = httpget('op');
	$module = httpget('modulename');

	if( empty($module) )
	{
		$module = httpget('module');
		httpset('modulename','');
	}

	if( $op == 'mass' )
	{
		if (httppost('install'))	$op = 'install';
		if (httppost('uninstall'))	$op = 'uninstall';
		if (httppost('reinstall'))	$op = 'reinstall';
		if (httppost('activate'))	$op = 'activate';
		if (httppost('deactivate')) $op = 'deactivate';
		$module = httppost('module');
	}

	if( !empty($module) && !empty($op) )
	{
		global $session;

		if( is_array($module) )
		{
			$modules = $module;
		}
		else
		{
			if( !empty($module) )
			{
				$modules = array($module);
			}
			else
			{
				$modules = array();
			}
		}

		$message = translate_inline('`$Error: No $op was available.');
		$category = 'general';
		foreach( $modules as $key => $module )
		{
			output("`2Performing `^%s`2 on `%%s`0`n", translate_inline($op), $module);
			if( $op == 'install' )
			{
				if( install_module($module) )
				{
					$message = translate_inline('installed');
				}
				else
				{
					$message = translate_inline('not installed. `$FAILED!');
					httpset('cat','');
				}
				$category = 'modules installed';
			}
			elseif( $op == 'uninstall' )
			{
				if( uninstall_module($module) )
				{
					$message = translate_inline('uninstalled');
				}
				else
				{
					$message = translate_inline('not uninstalled. `$FAILED!');
					output("Unable to inject module. Module not uninstalled.`n");
				}
				$category = 'modules uninstalled';
			}
			elseif( $op == 'reinstall' )
			{
				db_query("UPDATE " . db_prefix('modules') . " SET filemoddate = '0000-00-00 00:00:00' WHERE modulename = '$module'");
				injectmodule($module, true);
				invalidatedatacache("inject-$module");
				$message = translate_inline('reinstalled');
				$category = 'modules reinstalled';
			}
			elseif( $op == 'activate' )
			{
				activate_module($module);
				invalidatedatacache("inject-$module");
				$message = translate_inline('activated');
				$category = 'modules activated';
			}
			elseif( $op == 'deactivate' )
			{
				deactivate_module($module);
				invalidatedatacache("inject-$module");
				$message = translate_inline('deactivated');
				$category = 'modules deactivated';
			}

		}
		$op = '';
		httpset('op','');
		httpset('module','');
	}
}

function last_installed_time($original)
{
// Function written by skyhawk133 - March 2, 2005
// http://www.dreamincode.net/code/snippet86.htm

	// array of time period chunks
	$chunks = array(
		array(60 * 60 * 24 * 365, 'year','years'),
		array(60 * 60 * 24 * 30, 'month','months'),
		array(60 * 60 * 24 * 7, 'week','weeks'),
		array(60 * 60 * 24, 'day','days'),
		array(60 * 60, 'hour','hours'),
		array(60, 'minute','minutes'),
	);

	$today = time();
	$since = $today - $original;

	// $j saves performing the count function each time around the loop
	for( $i=0, $j=count($chunks); $i<$j; $i++ )
	{
		$seconds = $chunks[$i][0];
		$name = translate_inline($chunks[$i][1]);
		$names = translate_inline($chunks[$i][2]);

		// finding the biggest chunk (if the chunk fits, break)
		if( ($count = floor($since / $seconds)) != 0 )
		{
			break;
		}
	}

	$print = ($count == 1) ? '1 '.$name : "$count $names";

	if( $i + 1 < $j )
	{
		// now getting the second item
		$seconds2 = $chunks[$i + 1][0];
		$name2 = translate_inline($chunks[$i + 1][1]);
		$names2 = translate_inline($chunks[$i + 1][2]);

		// add second item if it's greater than 0
		if( ($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0 )
		{
			$print .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 $names2";
		}
	}
	return $print;
}
?>