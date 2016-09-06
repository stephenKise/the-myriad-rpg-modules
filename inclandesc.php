<?php

function inclandesc_getmoduleinfo(){
	$info = array(
		"name"=>"Internal Clan Description",
		"author"=>"Chris Vorndran",
		"category"=>"Clan",
		"version"=>"1.0",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=77",
		"vertxtloc"=>"http://dragonprime.net/users/Sichae/",
		"description"=>"Allows a clan to set an internal description for their clan, only viewable by their clan.",
		"prefs-clans"=>array(
			"Internal Description,title",
				"offset"=>"Can officers alter/set the Internal Clan Description?,bool|0",
				"intclandesc"=>"Internal clan description,textarea|",
				"id"=>"Acctid of Setter,int|",
			),
		);
	return $info;
}
function inclandesc_install(){
	module_addhook("header-clan");
	return true;
}
function inclandesc_uninstall(){
	return true;
}
function inclandesc_dohook($hookname,$args){
	global $session;
	$clandesc = get_module_objpref("clans",$session['user']['clanid'],"intclandesc","inclandesc");
	$id = get_module_objpref("clans",$session['user']['clanid'],"id");
	switch ($hookname){
		case "header-clan":
			if (httpget('op') == ""){
				if ($session['user']['clanrank'] > CLAN_APPLICANT 
					&& ($session['user']['clanrank'] == CLAN_LEADER || 
						(get_module_objpref("clans",$session['user']['clanid'],"offset","inclandesc") 
							&& $session['user']['clanrank'] == CLAN_OFFICER))){
					addnav("Management");
					addnav("Internal Description","runmodule.php?module=inclandesc&op=enter");
				}
				if ($clandesc <> ""){
					$sql = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid=$id";
					$res = db_query($sql);
					$row = db_fetch_assoc($res);
					output("`b`&Internal Guild Description`b: `#by %s`0`n",$row['name']);
					require_once("lib/nltoappon.php");
					output_notl(nltoappon($clandesc)."`n`n");
				}
			}
			break;
		}
	return $args;
}
function inclandesc_run(){
	global $session;
	
	$op = httpget('op');
	$clandesc = get_module_objpref("clans",$session['user']['clanid'],"intclandesc","inclandesc");
	$offset = get_module_objpref("clans",$session['user']['clanid'],"offset","inclandesc");
	if (httpget('offset') == 1){
		set_module_objpref("clans",$session['user']['clanid'],"offset",1);
		$of = 0;
	}else{
		set_module_objpref("clans",$session['user']['clanid'],"offset",0);
		$of = 1;
	}
	if ($session['user']['clanrank'] == CLAN_LEADER){
		addnav("Options");
		addnav(array("Turn %s Officer Setting",translate_inline($of==1?"On":"Off")),
			"runmodule.php?module=inclandesc&op=enter&offset=$of");
	}
	$newdesc = httppost('newdesc');
	
	page_header("Change Internal Description");
	
	switch ($op){
		case "enter":
			if ($newdesc == ""){
				output("`#An internal Guild description is like that of a Guild MotD, but it is meant to be more static, such as the Guild Description.");
				output("The different between internal and external, is that the internal shall only be viewed by those in the Guild.`n`n");
				$change = translate_inline("Change");
				rawoutput("<form action='runmodule.php?module=inclandesc&op=enter' method='post'>
					<textarea name='newdesc' rows='10' cols='60'>".htmlentities($clandesc)."</textarea><br>
					<input type='submit' class='button' value='$change'></form>");
				addnav("","runmodule.php?module=inclandesc&op=enter");
			}else{
				output("`#Here is your new Internal Guild Description:`0`n`n");
				set_module_objpref("clans",$session['user']['clanid'],"intclandesc",$newdesc);
				set_module_objpref("clans",$session['user']['clanid'],"id",$session['user']['acctid']);
				require_once("lib/nltoappon.php");
				output_notl(nltoappon($newdesc));
			}
			break;
		}
	addnav("Leave");
	addnav("Return to Guild","clan.php");
	page_footer();
}
?>