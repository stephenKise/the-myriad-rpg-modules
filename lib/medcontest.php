<?php
global $session;
$mode = httpget('mode');
$op = httpget('op');
if ($mode == "super"){
page_header("Medallion Contest");
if ($op == ""){
addnav("Reset Contest","runmodule.php?module=medcontest&mode=super&op=reset");
}
if ($op == "reset"){
	medcontest_reset();
}
addnav("Return to the Grotto","superuser.php");
addnav("Return to the Mundane","village.php");
page_footer();
}else{
	page_header("Contest Corner");
	output("`c`b`&Turn in Stars`0`b`c`n`n");
	output("`2Star Catching Contest!  The person who finds the most stars in the allotted time period wins!`n");
	output("While playing you can carry up to 5 Stars on your person.  Turn Stars in here for points.`n`n");
	if ($op == ""){
		$sql = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid = '".get_module_setting('medconthighid')."'";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		output("Alltime high score: %s by %s`n`n",get_module_setting('medconthigh'),$row['name']);
		$sql = "SELECT value FROM ".db_prefix("module_userprefs")." WHERE modulename = 'medcontest' and setting = 'medhunt' and value > 0 ORDER BY value+0 DESC";
		$result = db_query($sql);
		$totalpot=1000;
		$firstplace=1000;
		$secondplace=500;
		$thirdplace=250;
		output("`3Current Pot: %s gems.`n",$totalpot);
		output("`#First Place: %s gems.`n",$firstplace);
		output("`3Second Place: %s gems.`n",$secondplace);
		output("`#Third Place: %s gems.`n`n",$thirdplace);
		$sql = "SELECT userid,value FROM ".db_prefix("module_userprefs")." WHERE modulename = 'medcontest' and setting = 'medpoints' ORDER BY value+0 DESC";
		$result = db_query($sql);
	if (get_module_pref('medhunt')){
		output("You currently have %s points!`n`n",get_module_pref('medpoints'));
		output("Current Scores: `n");
		for ($i=0;$i<db_num_rows($result);$i++){
	    $row = db_fetch_assoc($result);
	    	$sql2="SELECT name FROM ".db_prefix("accounts")." WHERE acctid ='".$row['userid']."'";
    		$result2 = db_query($sql2);
    		$row2 = db_fetch_assoc($result2);
			output("%s `7- %s `n",$row2['name'],$row['value']);
			}
		output("`n");
		if (get_module_pref('medallion')>0) addnav("Turn in Stars","runmodule.php?module=medcontest&op=turnin");
	}else{
		output("It costs 2 gems to enter the contest.  The winner gets all of the gems collected from");
		output("contestants.  So if there are 50 contestants the prize will be 100 gems.  `n");
		if ($session['user']['gems'] > 1 and $op == ""){
			addnav("Enter the Contest","runmodule.php?module=medcontest&op=enter");	
		}else{
			output("Too bad you don't have enough gems to enter.`n");	
		}
	}
	}
	if ($op == "turnin"){
		$from = httpget("from");
		if ($from == 1) popup_header("Turn In");
		set_module_pref('medpoints', (get_module_pref('medpoints') + get_module_pref('medallion')));
		set_module_pref('medallion', 0);
		if (get_module_setting('medconthigh') < get_module_pref('medpoints')){
		    set_module_setting('medconthigh', get_module_pref('medpoints'));
		    set_module_setting('medconthighid', $session['user']['acctid']);
		}
		output("Your Stars have been turned in!");
		if ($from == 1) popup_footer();
		addnav("Continue","runmodule.php?module=medcontest");
	}
	if ($op == "enter"){
		$session['user']['gems']-=2;
		set_module_pref('medhunt',true);
		set_module_pref('medfind',e_rand(round(get_module_setting('medallionmax') * .75),get_module_setting('medallionmax')));
		output("`n`4Lonny hands you a potion... you down it without hesitation as you trust Lonny Implicitly.");
		output("`n`4Lonny tells you that this magic potion will give you the perception you need to see the stars that are hidden everywhere.");
		output("`n`4You have been entered into the contest!  Get out there and start collecting stars!");
		addnav("Continue","runmodule.php?module=medcontest");
	}
	addnav("Back to the Village","village.php");
	//I cannot make you keep this line here but would appreciate it left in.
	// rawoutput("<div style=\"text-align: left;\"><a href=\"http://www.pqcomp.com\" target=\"_blank\">Medallion Contest by Lonny @ http://www.pqcomp.com</a><br>");
	page_footer();
	}
?>