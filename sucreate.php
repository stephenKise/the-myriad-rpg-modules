<?php
// Use nocreate.php from the .zip archive if you want ONLY admins to
// be able to create new accounts. If you change the game setting instead,
// this module will not work.

function sucreate_getmoduleinfo(){
	$info = array(
		"name"=>"Superuser: Create a Character from the Grotto",
		"version"=>"1.0",
		"author"=>"Catscradler",
		"category"=>"Administrative",
		"download"=>"http://dragonprime.net",
	);
	return $info;
}

function sucreate_install(){
	module_addhook("superuser");
	module_addhook("create-form");
	module_addhook("footer-create");
	module_addhook("process-create");
	return true;
}

function sucreate_uninstall(){
	return true;
}

function sucreate_dohook($hookname,$args){
	global $session;
	if (! $session['loggedin']){
		blockmodule("sucreate");
		return $args;
	}

	switch ($hookname){

		case "superuser":
			if ($session['user']['superuser'] & SU_EDIT_USERS)
				addnav("Actions");
				addnav("Create New Character","create.php");
			break;

		case "create-form":
			addnav("","create.php?op=create");
			break;

		case "footer-create":
			blocknav("index.php");
			require_once("lib/superusernav.php");
			superusernav();
			break;

		case "process-create":
			output("Character has been created.");
			if (getsetting("requirevalidemail",0))
				output("An email was sent to %s to validate their address.", httppost("email"));
			addnav("Create Another Character","create.php");
			set_block_new_output(true);
			break;
	}
	return $args;
}
?>
