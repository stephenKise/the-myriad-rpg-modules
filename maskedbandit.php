<?php
/**
* Version: 1.0 - Converted from 097
* Date:   April 17, 2005
* Author: Robert of MaddRio dot com
* corrected debug error v1.1 Jul05
*/
function maskedbandit_getmoduleinfo(){
        $info = array(
        "name"=>"Masked Bandit",
        "version"=>"1.1",
        "author"=>"`2Robert",
        "download"=>"http://dragonprime.net/users/Robert/maskedbandit098.zip",
        "category"=>"Forest Specials",
        "settings"=>array(
		"Masked Bandit Settings,title",
		"mingold"=>"Minimum gold that bandit steals,range,10,90,10|50",
		"maxgold"=>"Maximum gold that bandit steals,range,100,500,25|200"
		),
        );
        return $info;
}

function maskedbandit_install(){
        module_addeventhook("forest", "return 100;");
        return true;
}

function maskedbandit_uninstall(){
        return true;
}

function maskedbandit_dohook($hookname,$args){
        return $args;
}

function maskedbandit_runevent($type){
global $session;
$min = get_module_setting("mingold");
$max = get_module_setting("maxgold");
$gold = e_rand($min, $max);
if ($session['user']['gold']>$gold){
output("`n`n`2 A dark shadow appears from nowhere, you are confronted by a masked bandit. `n`n");
output(" He grabs for your pouch and dashes off into the forest with %s of your gold! ",$gold);
$session['user']['gold']-=$gold;
debuglog(" lost $gold gold to the masked bandit ");
}else{
	output("`n`n`2 A dark shadow pass's off to your right, you look but see nothing.");
	}
}
function maskedbandit_run(){
}
?>