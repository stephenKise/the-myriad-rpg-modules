<?php
// Inspired by pkhonor.net, a RuneScape Private Server.
function adminchatcommands_getmoduleinfo(){
	$info = array(
		"name" => "Admin Chat Commands",
		"version" => "0.1",
		"author" => "`&`bStephen Kise`b, `5edited by Sara",
		"category" => "General",
		"download" => "nope",
		"settings" => array(
			"Admin Commands,title",
				"usingtags"=>"Do you have the core modded for bold and centering in the chat?,bool|0",
				"btag"=>"Tag for bold:,text",
				"centertag"=>"Tag for centering:,text",
				"color"=>"Color to use when warning:,text",
				"maft"=>"Message to declare AFTER player name:,text",
			"Player Commands,title",
				"afknews"=>"Make a news addition when a player goes afk?,bool|0",
				"backnews"=>"Make a news addition when a player comes back?,bool|0",
			"General Commands,title",
				"wepallow"=>"Should we allow players to enter their own weapon names?,bool|0",
				"rwep"=>"Text to replace with weapon name:,text|^W^",
				"armallow"=>"Should we allow players to enter their own armor names?,bool|0",
				"rarm"=>"Text to replace with armor name:,text|^A^"
		),
        "prefs" => array(
            "OOC Chat,title",
				"user_ooccolor" => "Choose a color code to use for ooc emotes",
                "user_oocsaycolor" => "Choose a color code to use for ooc talking"
        ),
	);
	return $info;
}

function adminchatcommands_install(){	
	module_addhook("commentary");
	return true;
}

function adminchatcommands_uninstall(){
	return true;
}

function adminchatcommands_dohook($hookname, $args){
	global $session;
	switch ($hookname){
		case "commentary":
			//ADMIN COMMANDS
			$line = $args["commentline"];
			$b = get_module_setting("btag");
			$c = get_module_setting("centertag");
			$clr = get_module_setting("color");
			$u = get_module_setting("usingtags");
			$ma = get_module_setting("maft");
				if((strpos($line, "/warn") !== false) && ($session['user']['superuser']&SU_IS_GAMEMASTER)){
					$msgyname = preg_replace('/\/warn/','',$line);
					if ($u){
						$endline = "/game" . $b.$c.$msgyname.$clr.$ma.$b.$c;
					}else{
						$endline = "/game" . $msgyname.$clr.$ma;
					}
					$args["commentline"] = $endline;
				}
				if(strpos($line, "/clear") !== false && ($session['user']['superuser'] & SU_EDIT_COMMENTS)){
					$args["commentline"] = ":`^has deleted all commentary for this section";
					db_query("DELETE FROM commentary WHERE section = '".httppost('section')."'");
				}
			//PLAYER COMMANDS
			$an = get_module_setting("afknews");
			$bn = get_module_setting("backnews");
				if(strpos($line, "/afk") !== false){
					$args["commentline"] = ":`^is now AFK!";
					if ($an)
						addnews($session['user']['name']." `^is AFK and will be back shortly.");
				}
				if(strpos($line, "/back") !== false){
					$args["commentline"] = ":`^is no longer AFK.";
					if ($bn)
						addnews($session['user']['name']." `^is now back.");
				}
				if((strpos($line, "/ooc") !== false)){
                    $emcolor = get_module_pref("user_ooccolor");
					$msg = preg_replace('/\/ooc/','',$line);
					$myname = str_replace("`b", "", $name);
					$oocline = "/me" . $emcolor . "ooc" . $msg . "";
					$args["commentline"] = $oocline;
				}
				if((strpos($line, "/oocsay") !== false)){
                    $saycolor = get_module_pref("user_oocsaycolor");
					$msg = preg_replace('/\/oocsay/','',$line);
					$myname = str_replace("`b", "", $name);
					$oocline = "/me `3ooc says, \"" . $saycolor . "" . trim($msg) . "`3\"";
					$args["commentline"] = $oocline;
                }
			//GENERAL
			$wa = get_module_setting("wepallow");
			$rw = get_module_setting("rwep");
			$aa = get_module_setting("armallow");
			$ra = get_module_setting("rarm");
				if ($wa){
					$args['commentline'] = str_replace($rw, $session['user']['weapon'], $args['commentline']);
				}
				if ($aa){
					$args['commentline'] = str_replace($ra, $session['user']['armor'], $args['commentline']);
				}
		break;
	}
	return $args;
}
?>