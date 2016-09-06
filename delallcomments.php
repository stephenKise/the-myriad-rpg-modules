<?php
/**
	Modified by MarcTheSlayer

	23/02/09 - v0.0.2
	+ Rewrote module.
	+ Added a page with delete options.
	+ Can choose not to delete grotto and petition commentary.
	+ Can choose to delete only from one commentary section.

	14/05/09 - v0.0.3
	+ Option to delete audited or petition commentary only.
*/
function delallcomments_getmoduleinfo()
{
	$info = array(
		"name"=>"Delete All Comments",
		"description"=>"Delete all the commentary with a couple of simple mouse clicks.",
		"version"=>"0.0.3",
		"author"=>"Derek0, modified by `@MarcTheSlayer",
		"category"=>"Administrative",
		"download"=>"http://dragonprime.net/index.php?topic=9880.0",
		"settings"=>array(
			"Delete Commentary - Settings,title",
			"delgrotto"=>"Allow comments editor to delete grotto comments?,bool|0",
			"delpetition"=>"Allow comments editor to delete petition comments?,bool|0",
			"delaudit"=>"Allow comments editor to delete audited comments?,bool|0"
		)
	);
	return $info;
}

function delallcomments_install()
{
	output("`c`b`Q%s 'delallcomments' Module.`0`b`c`n", translate_inline(is_module_active('delallcomments')?'Updating':'Installing'));
	module_addhook('header-moderate');
	return TRUE;
}

function delallcomments_uninstall()
{
	output("`n`c`b`Q'delallcomments' Module Uninstalled`0`b`c`n");
	return TRUE;
}

function delallcomments_dohook($hookname,$args)
{
	addnav('Other');
	addnav('Delete All Commentary','runmodule.php?module=delallcomments');

	return $args;
}

function delallcomments_run()
{
	global $session;

	page_header('Delete All Commentary');

	$area_names = array();
	$area_names['no'] = translate_inline('No - Ignore This');

	// Always allow MEGAUSER to delete grotto and petition commentary.
	if( $session['user']['superuser'] & SU_MEGAUSER )
	{
		$area_names['superuser'] = translate_inline('Grotto Commentary');
		$area_names['audit'] = translate_inline('Audited Commentary');
		$area_names['pet'] = translate_inline('Petition Commentary');
		$delgro = httppost('delgro');
		$delpet = httppost('delpet');
	}
	else
	{
		// Comment editors can only delete if the settings say they can.
		if( get_module_setting('delgrotto') == 1 )
		{
			$area_names['superuser'] = translate_inline('Grotto');
			$delgro = httppost('delgro');
		}
		else
		{
			$delgro = FALSE;
			output('`$Permission to delete Grotto commentary is denied. This option is disabled.');
		}
		if( get_module_setting('delpetition') == 1 )
		{
			$area_names['pet'] = translate_inline('Petition Commentary');
			$delpet = httppost('delpet');
		}
		else
		{
			$delpet = FALSE;
			output('`$Permission to delete Petition commentary is denied. This option is disabled.');
		}
		if( get_module_setting('delaudit') == 1 )
		{
			$area_names['audit'] = translate_inline('Audited Commentary');
		}
		else
		{
			$delmod = FALSE;
			output('`$Permission to delete Audited commentary is denied. This option is disabled.');
		}
	}

	$area_select = 'no'; // DO NOT TRANSLATE!!!

	if( httpget('op') == 'submit' )
	{
		$area_select = httppost('area');
		if( httppost('sure') == 1 )
		{
			if( $area_select == 'no' )
			{
				$like = '';
				$grotto = '';
				$petition = '';
				if( empty($delgro) )
				{
					$like .= "section <> 'superuser'";
					$grotto = translate_inline('No grotto comments were deleted.');
				}
				if( empty($delgro) && empty($delpet) )
				{
					$like .= " AND ";
				}
				if( empty($delpet) )
				{
					$like .= "section NOT LIKE 'pet%'";
					$petition = translate_inline('No petition comments were deleted.');
				}

				$where = ( !empty($like) ) ? " WHERE $like" : '';
				db_query("DELETE FROM " . db_prefix('commentary') . "$where");

				output('`^A total of `@%s `^comments have been deleted.`n', db_affected_rows());
				output_notl('`^%s`n%s`n`0', $grotto, $petition);
			}
			elseif( $area_select == 'audit' )
			{
				db_query("DELETE FROM " . db_prefix('moderatedcomments'));
				output('`^A total of `@%s `^audited comments have been deleted.`n', db_affected_rows());
			}
			elseif( $area_select == 'pet' )
			{
				db_query("DELETE FROM " . db_prefix('commentary') . " WHERE section LIKE 'pet%'");
				output('`^A total of `@%s `^petition comments have been deleted.`n', db_affected_rows());
			}
			else
			{
				db_query("DELETE FROM " . db_prefix('commentary') . " WHERE section = '$area_select'");
				output('`^A total of `@%s `^comments from section "%s" have been deleted.`n', db_affected_rows(), $area_select);
			}
		}
		else
		{
			output('`^No comments were deleted because you didn\'t select `bYes`b to confirm.`0`n');
		}
	}

	// Grab as many section names as possible.
	$vname = getsetting('villagename', LOCATION_FIELDS);
	$area_names['village'] = sprintf_translate('%s Square', $vname);
	$area_names['shade'] = translate_inline('Land of the Shades');
	$area_names['grassyfield'] = translate_inline('Grassy Field');
	$area_names['inn'] = getsetting('innname', LOCATION_INN);
	$area_names['veterans'] = translate_inline('Veterans Club');
	$area_names['hunterlodge'] = translate_inline('Hunter\'s Lodge');
	$area_names['gardens'] = translate_inline('Gardens');
	$area_names['waiting'] = translate_inline('Clan Hall Waiting Area');
	// If a module has commentary, but does not hook onto 'moderate', it will not be listed.
	$area_names = modulehook('moderate',$area_names);
	$area = '';
	foreach( $area_names as $key => $value )
	{
		$area .= ','.$key.','.$value;
	}

	$row = array(
			"delgro"=>$delgro,
			"delpet"=>$delpet,
			"area"=>$area_select,
			"sure"=>0,
	);

	$form = array(
			"Delete Commentary,title",
			"delgro"=>"Also delete grotto commentary?,bool",
			"delpet"=>"Also delete petition commentary?,bool",
			"`^Note: The 2 above options will be ignored if the below option is used.,note",
			"area"=>"Delete all from this section only?,enum".$area,
			"sure"=>"Select `bYes`b to confirm you want to delete.,bool",
	);

	rawoutput('<form action="runmodule.php?module=delallcomments&op=submit" method="POST">');
	addnav('','runmodule.php?module=delallcomments&op=submit');	
	require_once('lib/showform.php');
	showform($form,$row,TRUE);
	$submit = translate_inline('Delete Comments');
	rawoutput('<input type="submit" value="'.$submit.'" /></form>');

	addnav('Main Overview');
	addnav('Commentary Overview','moderate.php');

	require_once('lib/superusernav.php');
	superusernav();

	page_footer();
}
?>