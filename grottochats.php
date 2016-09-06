<?php
function grottochats_getmoduleinfo(){
	$info = array(
		"name" => "Grotto Chats",
		"author" => "Based off of `i`7Aeolus`i`0's sectionedchats.php, modified by `i`b`&Xpert`b`i`0",
		"version" => "1.0",
		"category" => "Administrative",
		"prefs" => array(
			"Grotto Chats Prefs,title",
			"chat" => "Which chat has user saved,enum,regular,Regular,notifs,Notifications,important,Important|regular",
			"readregular"=>"Last viewing of Grotto Chat?,string,hidden,20|2005-01-01 01:00:00",
			"readnotifs"=>"Last viewing of Notifications Chat?,string,hidden,20|2005-01-01 01:00:00",
			"readimportant"=>"Last viewing of Important Chat?,string,hidden,20|2005-01-01 01:00:00",
		),
	);
	return $info;
}

function grottochats_install(){
	module_addhook("moderate");
	module_addhook("superusertop");
	return true;
}

function grottochats_uninstall(){
	return true;
}

function grottochats_dohook($hookname, $args){
	switch ($hookname){
		case "moderate":
			$args["superuser-notifs"] = "Grotto Notes";
			$args["superuser-important"] = "Grotto Votes";
		break;
		case "superusertop":
			$chat = httpget("chat");
			if ($chat) set_module_pref("chat",$chat);
			
			$chat0 = get_module_pref("chat","grottochats");
			
			$read0 = get_module_pref("readregular","grottochats");
			$read1 = get_module_pref("readnotifs","grottochats");
			$read2 = get_module_pref("readimportant","grottochats");
			
			$link1 = "superuser.php?chat=regular";
			$link2 = "superuser.php?chat=notifs";
			$link3 = "superuser.php?chat=important";
			addnav("", $link1);
			addnav("", $link2);
			addnav("", $link3);
			
			if ($chat0 == "regular"){
			set_module_pref("readregular",date("Y-m-d H:i:s"));
			$sql = db_num_rows(db_query("SELECT commentid FROM ".db_prefix('commentary')." WHERE section = 'superuser-notifs' AND postdate > '$read1'"));
			$sqlz = db_num_rows(db_query("SELECT commentid FROM ".db_prefix('commentary')." WHERE section = 'superuser-important' AND postdate > '$read2'"));
				$style1 = "style='color:lightblue;'";
			if ($sql) $style2 = "style='color:red;'"; else $style2 = "";
			if ($sqlz) $style3 = "style='color:red;'"; else $style3 = "";
			} else if ($chat0 == "notifs"){
			set_module_pref("readnotifs",date("Y-m-d H:i:s"));
			$sql = db_num_rows(db_query("SELECT commentid FROM ".db_prefix('commentary')." WHERE section = 'superuser' AND postdate > '$read0'"));
			$sqlz = db_num_rows(db_query("SELECT commentid FROM ".db_prefix('commentary')." WHERE section = 'superuser-important' AND postdate > '$read2'"));
			if ($sql) $style1 = "style='color:red;'"; else $style1 = "";
				$style2 = "style='color:lightblue;'";
			if ($sqlz) $style3 = "style='color:red;'"; else $style3 = "";
			}else{
			set_module_pref("readimportant",date("Y-m-d H:i:s"));
			$sql = db_num_rows(db_query("SELECT commentid FROM ".db_prefix('commentary')." WHERE section = 'superuser' AND postdate > '$read0'"));
			$sqlz = db_num_rows(db_query("SELECT commentid FROM ".db_prefix('commentary')." WHERE section = 'superuser-notifs' AND postdate > '$read1'"));
			if ($sql) $style1 = "style='color:red;'"; else $style1 = "";
			if ($sqlz) $style2 = "style='color:red;'"; else $style2 = "";
				$style3 = "style='color:lightblue;'";
			}
			output_notl("`0<center><big><b>`t[<a href='$link1' $style1>Important</a>] [<a href='$link2' $style2>Updates</a>] [<a href='$link3' $style3>General</a>]</b></big></center><br />",TRUE);
			
			if ($chat0 == "notifs"){ 
			$chatao = "-notifs";
			}else if ($chat0 == "important"){
			$chatao = "-important";
			}else{
			$chatao = "";
			}
			
			$args['section'] .= $chatao;
		break;
	}
	return $args;
}
?>