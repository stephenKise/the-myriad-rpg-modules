<?php

function journeytoshades_getmoduleinfo(){
	$info = array(
		"name"=>"Journey to the Shades",
		"author"=>"`i`)Ae`7ol`&us`i`0, based on idea by Stephen Kise",
		"version"=>"1.0",
		"category"=>"Administrative",
		"prefs"=>array(
			"Journey to the Shades Pref,title",
			"The following pref is for those that don't have the SU_EDIT_USERS flag,note",
			"candie"=>"Can user use 'Go to Shades' nav?,bool|0",
			"jdie"=>"Is user dead via this module?,bool|0",
		),
	);
	return $info;
}

function journeytoshades_install(){
	module_addhook("superuser");
	module_addhook("shades");
	module_addhook("newday");
	return true;
}
function journeytoshades_uninstall(){
	return true;
}
function journeytoshades_dohook($hookname, $args){
	global $session;
	switch($hookname){
		case "superuser":
			if($session['user']['superuser'] & SU_EDIT_USERS || get_module_pref("candie") == 1){
				addnav("Die!");
				addnav("Go to Shades","runmodule.php?module=journeytoshades&op=die");
			}
		break;
		case "shades":
			if(get_module_pref("jdie") == 1){
				addnav("Live!");
				addnav("Go to Village","runmodule.php?module=journeytoshades&op=live");
			}
		break;
		case "newday":
			set_module_pref("jdie",0);
		break;
	}
	return $args;
}

function journeytoshades_run(){
	global $session;
	
	$op = httpget('op');
	
	page_header("Dramatic Event!");
	
	if ($op == "die"){
		$session['user']['alive']=0;
		$session['user']['hitpoints']=0;
		set_module_pref("jdie",1);
		output("Proceed to the Shades.");
		addnav("Return");
		addnav("Carry on","shades.php");
	} elseif ($op == "live") {
		$session['user']['alive']=1;
		$session['user']['hitpoints']=$session['user']['maxhitpoints'];
		set_module_pref("jdie",0);
		output("Proceed to the Village.");
		addnav("Return");
		addnav("Carry on","village.php");
	}
	
	page_footer();

}
?>