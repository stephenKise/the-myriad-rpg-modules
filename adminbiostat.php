<?php
//This mod is for checking the stats that previously were only viewable by using the User Editor


function adminbiostat_getmoduleinfo(){
	$info = array(
	    "name"=>"Admin Bio Stats",
		"description"=>"This module allows Admins with SU_EDIT_USERS to view stats in a persons bio that they would normally have to go through the editor to get",
		"version"=>"1.7",
		"author"=>"`b`GJolly`@GG`b`0, with help from `\$Programmer16`0, modified by `i`)Ae`7ol`&us`i`0",
		"category"=>"Administrative",
		"download"=>"http://www.hogwartsnow.com/customfiles/modules/adminbiostat.php",
		);
    return $info;
}

function adminbiostat_install(){
	module_addhook_priority("biotop",1);
	module_addhook_priority("bioadmin",1);
	return true;
}

function adminbiostat_uninstall()
{
	return true;
}


function adminbiostat_dohook($hookname, $args){
	global $session;
	
	$t = false;
	switch ($hookname){
		case "biotop":
			if (!is_module_active('newbio')) $t = true;
			if (is_module_active('newbio') && !get_module_pref('user_bio','newbio')) $t = true;
		break;
		case "bioadmin":
			if (is_module_active('newbio') && get_module_pref('user_bio','newbio')) $t = true;
		break;
	}
	
	if ($t && $session['user']['superuser'] & SU_EDIT_USERS) {
		require_once("lib/datetime.php");
		$id = $args['acctid'];
		$sql = "SELECT login, acctid, emailaddress, location, lastip, uniqueid, laston FROM " . db_prefix("accounts") . " WHERE acctid='" . $id . "'";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$abslogin = $row['login'];
		$absacctid = $row['acctid'];
		$absemail = $row['emailaddress'];
		$abslastip = $row['lastip'];
		$absuniqueid = $row['uniqueid'];
		$absloc = $row['location'];
		$abslasthit = reltime(strtotime($row['laston']));
		output_notl("<table>",TRUE);
		output_notl("<tr><td>`\$Login:</td><td>`%%s`n</td></tr>",$abslogin,TRUE);
		output_notl("<tr><td>`\$Account ID:</td><td>`%%s`n</td></tr>",$absacctid,TRUE);
		output_notl("<tr><td>`\$Email Address:</td><td>`%%s`n</td></tr>",$absemail,TRUE);
		output_notl("<tr><td>`\$Last IP:</td><td>`%%s`n</td></tr>",$abslastip,TRUE);
		output_notl("<tr><td>`\$Last Hit:</td><td>`%%s`n",$abslasthit,TRUE);
		output_notl("</table>`0",TRUE);
	}
	
	return $args;
}
?>