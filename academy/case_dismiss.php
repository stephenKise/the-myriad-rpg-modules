<?php

$return = round(get_module_setting("cost")/2);

if (httpget('confirm')){
	output("Dycedarg nods and takes %s back into the Training Hall.",$name);
	output("On the table, Dycedarg had left %s gems for you.",$return);
	output("Without haste, you pick up the gems and leave.");
	$session['user']['gems']+=$return;
	set_module_pref("name","");
	set_module_pref("active",0);
	set_module_pref("lsl",0);
	set_module_pref("level",0);
	set_module_pref("acc",65);
	set_module_pref("tacc",0);
	set_module_pref("class",0);
}else{
	output("`6You approach Dycedarg and ask, \"`3May I dismiss my %s?`6\"",$classarray[$class]);
	output("Dycedarg arches a brow and nods, \"`&Yes, you may.");
	output("To compensate, I shall offer you %s gems.",$return);
	output("Are you sure you wish to dismiss your %s?`6\"",$classarray[$class]);
	addnav("Decide");
	addnav("Dismiss","runmodule.php?module=academy&op=dismiss&confirm=1");
}
?>