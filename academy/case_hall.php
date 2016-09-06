<?php
	addnav("Training Hall");
	if (!$active){
		output("`6Dycedarg marches you into the Training Hall.");
		output("\"`&Here, we strive for perfection.");
		output("Not one of our student's is out of line...`6\"");
		output("`n`nJust then, fire erupts from his fingertips and flies towards one of the misbehaving students.");
		output("\"`&He was looking at my cock-eyed...");
		output("Now then, would you like to Indoctrinate a Squire?`6\"");
		addnav("Indoctrinate Squire","runmodule.php?module=academy&op=buy");
	}else{
		output("`6Dycedarg looks at you, \"`&How many I be of service outside of renaming your Squire for `^%s `&gold?`6\"",get_module_setting("re"));
		addnav(array("Rename %s",$name),"runmodule.php?module=academy&op=train&type=rename");
		if ($dead){
			output("`n`n`6Dycedarg frowns and looks solemn.");
			output("\"`&It is quite sad that we have to meet once more on this occasion... ");
			output("As I am your %s's master, I would like to do the honors in reviving him.",$classarray[$class]);
			if (get_module_setting("favor") > 0 && get_module_setting("gold-revive") <= 0){
				output("It will cost `\$%s `&Favor.`6\"",get_module_setting("favor"));
				addnav(array("Revive %s (%s Favor)",$classarray[$class],	get_module_setting("favor")),
					"runmodule.php?module=academy&op=train&type=revive");
			}elseif (get_module_setting("favor") <= 0 && get_module_setting("gold-revive") > 0){
				output("It will cost `\$%s `&Gold.`6\"",get_module_setting("gold-revive"));
				addnav(array("Revive %s (%s Gold)",$classarray[$class],	get_module_setting("gold-revive")),
					"runmodule.php?module=academy&op=train&type=revive");
			}else{
				output("It will cost `\$%s `&Favor and `\$%s `&Gold.`6\"",
					get_module_setting("favor"), get_module_Setting("gold-revive"));
				addnav(array("Revive %s (%s Favor and %s Gold)",$classarray[$class],
					get_module_setting("favor"),get_module_setting("gold-revive")),
					"runmodule.php?module=academy&op=train&type=revive");
			}
		}
		if (get_module_pref("lsl") >= get_module_setting("level") && !$dead && get_module_pref("level") < get_module_setting("max")){
			addnav(array("Train %s", $classarray[$class]),"runmodule.php?module=academy&op=train&type=level");
			output("`n`n`6Dycedarg smiles, \"`&So, I see that %s `&has grown quite strong.",$name);
			output("`&I shall allow him to level, IF he can best me in battle.");
			output("`&Would you care to submit him into battle?`6\"");					
		}
		if (get_module_pref("level") == $max[$class] && !$dead){
			output("`n`n`6\"`&Splendid, simply splendid!");
			output("If you come with me, we shall make your servant much better!`6\"");
			addnav("Advance Class","runmodule.php?module=academy&op=train&type=upgrade");
		}
		if (!get_module_pref("tacc") && !$dead && get_module_pref("acc") < get_module_setting("miss")){
			output("`n`n`6\"`&I see... so, you would like to train your servant's accuracy today.");
			output("Very well then...`6\"");
			addnav("Train Accuracy","runmodule.php?module=academy&op=train&type=acc");
		}
	}
?>