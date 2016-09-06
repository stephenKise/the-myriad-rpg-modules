<?php
	if ($session['user']['gems'] >= get_module_setting("cost")){
		$names = array();
		$names = explode(",",get_module_setting("names"));
		$i = e_rand(1,count($names));
		$name = $names[$i];
		if ($name == "") $name = translate_inline("Useless Soldier");
		set_module_pref("name",$name);
		set_module_pref("active",1);
		set_module_pref("dead",0);
		set_module_pref("acc",65);
		output("`6Dycedarg fetches a Squire from the wall and returns.");
		output("\"`&This one's name is %s. `&Say hello to your new owner...`6\"",$name);
		output("The Squire bows and stands at your side.");
		$session['user']['gems']-=get_module_setting("cost");
	}else{
		output("`6Dycedarg snatches you from the scruff of the neck and tosses you to the ground.");
		output("\"`&One of the things that we teach here is honesty.... now get out!`6\"");
	}
?>