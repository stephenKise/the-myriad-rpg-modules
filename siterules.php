<?php

function siterules_getmoduleinfo(){
	$info = array(
		"name"=>"Site Rules",
	    "author"=>"Christopher King, edits by Maverick",
	    "version"=>"1.5",
	    "category"=>"Rules/Incentives",
		"override_forced_nav"=>true,
	   	"settings"=>array(
		    "Rules,title",
		    "Rule1"=>"Add Rule 1,textarea|",
		    "Rule2"=>"Add Rule 2,textarea|",
		    "Rule3"=>"Add Rule 3,textarea|",
		    "Rule4"=>"Add Rule 4,textarea|",
		    "Rule5"=>"Add Rule 5,textarea|",
			"Rule6"=>"Add Rule 6,textarea|",
			"Rule7"=>"Add Rule 7,textarea|",
			"Rule8"=>"Add Rule 8,textarea|",
			"Other,title",
		    "topblurb"=>"Blurb at Top,textarea|",
	    ),
		"prefs"=>array(
			"hasread"=>"Has this user accepted the rules?,int|0",
		),
	);
	return $info;
}

function siterules_install(){
	module_addhook("village");
    return true;
}

function siterules_uninstall(){
 	return true;
}

function siterules_dohook($hookname,$args){
	switch($hookname){
	case "village":
		if (get_module_pref("hasread")==0){
			require_once("lib/redirect.php");
			redirect("runmodule.php?module=siterules");
		}else{
    		addnav($args['infonav']);
			addnav("R?`b`\$Site Rules`b","runmodule.php?module=siterules");
		}
	break;
	}
return $args;
}


function siterules_run(){
	global $session;
	$sett = get_all_module_settings();
	$Rule1=$sett['Rule1'];
	$Rule2=$sett['Rule2'];
	$Rule3=$sett['Rule3'];
	$Rule4=$sett['Rule4'];
	$Rule5=$sett['Rule5'];
	$Rule6=$sett['Rule6'];
	$Rule7=$sett['Rule7'];
	$Rule8=$sett['Rule8'];

//	$add=$sett['add1'];
//	$add2=$sett['add2'];
//	$add3=$sett['add3'];
//	$add4=$sett['add4'];
//	$add5=$sett['add5'];
//	$add5=$sett['add6'];
//	$add5=$sett['add7'];
//	$add5=$sett['add8'];
	$op = httpget('op');
	$topblurb=$sett['topblurb'];
	page_header("The Law");

	modulehook("siterules");

	if ($op=="" || $op=="popup"){
		if ($op=="popup") popup_header("Site Rules");
		output("`n`n`n");
		if ($topblurb <> ""){
			output("%s",$topblurb);
			output("`n`n");
		}
		
		for ($i = 1; $i <= 8; $i++){
			$rule = "Rule$i";
			$var = $$rule;
			
			if ($var <> ""){
				output("`\$`iRule ".$i."`i. `7%s",nl2br($var),true);
				output("`n`n");
			}
		}
		
		output("`\$ *** If asked with a legitament reason, we will give the never expire flag to a character.");

		if (get_module_pref("hasread")==0){
			output("<center><form action='runmodule.php?module=siterules&op=accept' method='post'><input type='submit' value='I Accept and Understand the Rules Above'></form></center>",true);
			addnav("","runmodule.php?module=siterules&op=accept");
		}else{
			addnav("Leave");
			addnav("Return to the Village","village.php");
		}
		if ($op=="popup") popup_footer();
	}
	
	if ($op=="accept"){
		require_once("lib/addnews.php");
		addnews("`i`@Everyone welcome `&{$session['user']['name']}`@, the newest member of the Myriad!`i");
		increment_module_pref("hasread");
		require_once("lib/redirect.php");
		redirect("runmodule.php?module=siterules");
	}
	
	page_footer();
}

?>
