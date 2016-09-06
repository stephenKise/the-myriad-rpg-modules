<?php
function mngmods_getmoduleinfo()
{
	$info = array
	(
		"name"	=> "Mundane Module Manager Links",
		"version"	=> "1.0",
		"author"	=> "<a href='http://www.rpgee.com' target=_blank>`&RPGee.com</a>",
		"category"	=> "Administrative",
		"download"	=> "http://www.rpgee.com/lotgd/mngmods.zip",
		"vertxtloc"	=> "http://www.rpgee.com/lotgd/",
	);
	return $info;
}
function mngmods_install()
{
	module_addhook("village");
	module_addhook("forest");
	module_addhook("shades");
	return true;
}
function mngmods_uninstall(){	return true;}
function mngmods_dohook($hookname,$args)
{
	global $session;
	switch($hookname)
	{
		case "village":
			addnav("Superuser");
			if ($session['user']['superuser'] & SU_MANAGE_MODULES) addnav("1?Manage Modules","modules.php");
		break;
		case "forest":
			addnav("Superuser");
			if ($session['user']['superuser'] & SU_MANAGE_MODULES) addnav("1?Manage Modules","modules.php");
		break;
		case "shades":
			addnav("Superuser");
			if ($session['user']['superuser'] & SU_MANAGE_MODULES) addnav("1?Manage Modules","modules.php");
		break;
	}
	return $args;
}
?>