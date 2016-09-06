<?php

function delcpets_getmoduleinfo(){
	$info = array(
		"name"=>"Delete Closed Petitions",
		"author"=>"`&`bStephen Kise`b",
		"category"=>"Administrative",
		"version"=>"2.0",
		"download"=>"nope",
		"settings"=>array(
			"Delete Closed Petitions Settings,title",
			"whichway"=>"Should only one person be able to do this? Or those who can edit the configurations of the game?,enum,0,One Person,1,Config Managers",
			"acc"=>"The acctid of the person(s) that can delete the closed petitions,int|1",
			"ONLY applies if one person can delete closed petitions,note",
		),
	);
	return $info;
}

function delcpets_install(){
	output("`Q`b`iInstalling or Updating Delete Closed Petitions by Stephen Kise`n`b`i");
	module_addhook("superuser");
	return true;
}

function delcpets_uninstall(){
	return true;
}

function delcpets_dohook($hookname,$args){
	global $session;
	switch ($hookname){
		case "superuser":
			$acc = explode(",",get_module_setting("acc"));
			$wway = get_module_setting("whichway");
			if (in_array($session['user']['acctid'],$acc) && $wway == 0 || $wway == 1 && $session['user']['superuser'] & SU_EDIT_CONFIG){
				addnav("Mechanics");
				addnav("Delete Closed Petitions","runmodule.php?module=delcpets&op=del");
			}
		break;
	}
	return $args;
}

function delcpets_run(){
	global $session;
	$op = httpget('op');
	page_header("Delete Closed Petitions");
	switch ($op){
		case "del":
			$sql = "DELETE FROM " . db_prefix("petitions") ." WHERE status = 7";
			db_query($sql);
			rawoutput("<big><center>");
			output("`Q`b`iDeleted ALL closed petitions!`b`i");
			rawoutput("</center></big>");
				debuglog("has deleted all closed petitions");
			addnews("%s `Q`b`ihas deleted all closed petitions!`b`i",$session['user']['name']);
			invalidatedatacache("petition_counts");
			addnav("Go Back");
			addnav("Grotto","superuser.php");
		break;
	}
	page_footer();
}
?>