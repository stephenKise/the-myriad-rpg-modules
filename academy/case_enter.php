<?php
	output("`6You wander into a grand hall.");
	output("Gazing at the ceiling, you suddenly lose track of your footing.");
	output("All of a sudden, a tall knight stands before you.");
	output("He glares gently at you, as your eyes scan over him.");
	output("You note a large Lion on his cape, his cut physique and his well-groomed beard.");
	output("`n`n\"`&Hello, my name is Dycedarg Beoulve... may I ask what you think you are doing in my Academy?`6\"");
	output("`n`nHe takes a step back and turns his back to you, ushering you forward with a single finger.");
	output("`n`nHis voice booms throughout the halls, \"`&Here, we take pride in the development of a warrior.");
	output("Our Squires are taught the most powerful techniques, the strongest moves and they are trained to their fullest potential.");
	output("If you are interested in purchasing one of my fine protege's, you shall have to pay up `%%s `&Gems.",get_module_setting("cost"));
	output("Once you pay for your Squire, it shall be indoctrinated to you.");
	output("It shall serve you until death.`6\"");
	addnav("Venture");
	addnav("Training Hall","runmodule.php?module=academy&op=hall");
	if ($active && !$dead){
		output("`n`n`6Dycedarg scans his eyes over you and tilts his head to the side.");
		output("\"`&My... I didn't see you there...");
		output("Please step forward, `^%s`&.`6\"",$name);
		output("You take a quick look at your warrior and Dycedarg smiles.");
		addnav(array("Dismiss %s",$classarray[$class]),"runmodule.php?module=academy&op=dismiss");
	}elseif($dead){
		output("`n`n`6Dycedarg notes the limp body in your arms.");
		output("\"`&I see... Well, if you will follow me into the Training Hall... I shall be able to sort this all out.`6\"");
	}
?>