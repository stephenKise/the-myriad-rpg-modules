<?php

function dragonpoints_getmoduleinfo(){
                $info = array(
                        "name"=>"Tentromech points reset",
                        "author"=>"`)ShadowRaven",
                        "version"=>"1.0",
                        "category"=>"Lodge",
                        "download"=>"http://dragonprime.net/users/ShadowRaven/dragonpoints.zip",
                        "description"=>"Players can spend Tentromech points to reset their Dragon points and redistribute them..",

//                 "settings"=>array(
//                         "Lodge Settings,title",
//                         "cost"=>"Donation points needed to reset Dragon points,int|1000",
//         ),
         "prefs" => array(
             "times"=>"How many times has this user reset points?,int|0",
         ),
        );
        return $info;
}
function dragonpoints_install(){
     module_addhook_priority("lodge","2");
     module_addhook_priority("pointsdesc","2");
return true;
}
function dragonpoints_uninstall(){
return true;
}
function dragonpoints_dohook($hookname,$args){
        global $session;
    $cost = get_module_setting("cost");
            switch ($hookname){
                   case "lodge":

						$pointsavailable =
							$session['user']['donation']-$session['user']['donationspent'];
                          if ($pointsavailable >= 250){
	                          addnav("Use Points");
	                          addnav("Reset TK Points `@(250 DP)","runmodule.php?module=dragonpoints&op=reset");
                      	  }
                          break;
                    case "pointsdesc":
                        $args['count']++;
                        $format = $args['format'];
                        $str = "`\$- `^Reset your Tentromech Points at any time.`n";
                        output($str, true);
                        break;
                }
        return $args;
}
function dragonpoints_run(){
    global $session;
	page_header("Donation Center");
	output("`Q`c`bReset TK Points`b`c");
    $op = httpget('op');
    $cost=get_module_setting("cost");
	if ($op == "reset") {
		addnav("Return to the Lodge","lodge.php");
	    output("`2When you kill a Tentromech you are allowed to allocate a point to one of your stats. If you are not pleased with your current stats, at level 15 you may request to have your points reset.`n");
	    if ($session['user']['level']>=15) addnav("`@Reset Points","runmodule.php?module=dragonpoints&op=reset2");
	}
	if ($op == "reset2") {
		$id = $session['user']['acctid'];
		db_query("UPDATE " . db_prefix("accounts") . " SET dragonpoints='' WHERE acctid='$id'");
		output("`2Your points have been reset! Please click `@'Continue' `2to redistribute them!`n");
		addnav("`@Continue","village.php");
		set_module_pref("times",(get_module_pref("times")+1));
		$session['user']['lasthit']="0000-00-00 00:00";
		$session['user']['donationspent'] += 250;
	
	}
        page_footer();
}
?>
