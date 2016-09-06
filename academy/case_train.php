<?php
	switch (httpget('type')){
		case "revive":
			if ($session['user']['deathpower'] >= get_module_setting("favor") && 
				$session['user']['gold'] >= get_module_setting("gold-revive")){
				output("`6Dycedarg looks to the sky and spreads his arms.");
				output("His eyes roll to the back of his skull and low murmers escape his throat.");
				output("Suddenly, your %s's body rises into thin air and a light pierces him.",$classarray[$class]);
				set_module_pref("dead",0);
				$session['user']['deathpower']-=get_module_setting("favor");
				$session['user']['gold']-=get_module_setting("gold-revive");
				output("`n`nDycedarg looks at you, \"`&It is done...`6\"");
			}else{
				output("`6Dycedarg frowns, \"`&With not enough supplies, I am unable to make a bargain with Ramius.");
				output("Please come back when you have more supplies.`6\"");
			}
			break;
		case "level":
			// Sanity check
			if (get_module_pref("lsl") < get_module_setting("level")){
				output("`6Dycedarg looks stunned, \"`&I don't know who you are trying to fool... but your %s is not strong enough to face me.`6\"",$classarray[$class]);
			}else{
				$excesslevel = get_module_pref("lsl")-get_module_setting("level");
				set_module_pref("lsl",$excesslevel);
				// This makes sure to carry over any levels.
				// Even though extra lvl's can not be gained
				// It is best to keep this in, just in case
				// If it equates to 0, that is what we want. :)
				increment_module_pref("level",1);
				output("`6Dycedarg shows you into a small room, that is highly decorated with little furnishing.");
				output("He pulls out his sword and looks at %s, \"`&Come at me!`6\"",$name);
				output("Your %s runs at Dycedarg and slashes him across the front.",$classarray[$class]);
				output("Dycedarg stands and pokes %s in the forehead, making him topple over.",$name);
				output("Your %s wipes the blood from his chin and strikes against Dycedarg once more.",$classarray[$class]);
				output("Amazingly, he is able to strike Dycedarg down!");
				output("\"`&Very good, my faithful servant.`6\"");
				output("`n`n%s gained a level!",$name);
				output("His attack has increased!");
			}
			break;
		case "acc":
			if (get_module_pref("acc") < get_module_setting("miss")){
				output("`6\"`&So, you would like to train your %s's accuracy?`6\"",$classarray[$class]);
				output("`6Dycedarg draws up a target on the wall.");
				output("\"`&Now, %s, `&focus your energy on the center, and let's see if you can hit the target.`6\"`n`n",$name);
				output("`6You can see that %s is straining on the target.",$name);
				output("Your eyes begin to strain and you begin to see a red dot circling around the target.");
				switch(e_rand(1,4)){
					case 1: case 2: case 3:
						output("For a brief instant, the dot stays in the center and Dycedarg smiles.");
						output("\"`&Very good job...`6\"");
						increment_module_pref("acc",1);
						break;
					case 4:
						output("`6You can see that %s is unable to focus on the target.",$name);
						output("After five minutes, Dycedarg takes down the target and retreats into the back room.");
						break;
					}
				set_module_pref("tacc",1);
			}else{
				output("`6Dycedarg looks at %s.",$name);
				output("\"`&He is about as accurate as he is going to get.`6\"");
			}
			break;
		case "rename":
			$newname = httppost('name');
			$set = translate_inline("Set Name");
			if ($session['user']['gold'] >= get_module_setting("re")){
				if ($newname ==	""){
					output("`6Dycedarg walks forward, looking at you.");
					output("\"`&Let's get this over with.");
					output("Write the name you want here, and I shall transfer it over.`6\"`n`n");
					rawoutput("<form action='runmodule.php?module=academy&op=train&type=rename' method='post'>");
					rawoutput("<input name='name' size='20'>");
					rawoutput("<input type='submit' class='button' value='$set'></form>");
				}else{
					output("`6\"`&There we go, your %s has been renamed to %s`&.`6\"",$classarray[$class],$newname);
					set_module_pref("name",$newname."`0");
					$session['user']['gold']-=get_module_setting("re");
				}
			}else{
				output("`6\"`&What do you think this is, a free clinic?");
				output("Get out of here before I rend your limbs from your body.`6\"");
			}
			addnav("","runmodule.php?module=academy&op=train&type=rename");
			break;
		case "upgrade":
			$advance = httpget('ad');
			$current = $classarray[$class];
			$next = $classarray[$class+1];
			// Sanity Check
			if (get_module_pref("level") != $max[$class]){
				output("`6\"`&I have no idea how you got in here, but I want you out!`6\"");
			}else{
				if ($advance == ""){
					output("`6You approach Dycedarg, your %s at your side.",$current);
					if($class == 2){
						output("`6Dycedarg shakes his head, \"`&I am sorry, but %s `&is as strong as he will ever be.`6\"",$name);
					}else{
						output("`6Dycedarg looks at your %s, \"`&Ah... so I see that %s would like to train for a new class.",$current,$name);
						output("You do know that class would happen to be %s and is far more powerful than your %s class?`6\"",$next,$current);
						output("`n`nYou nod in agreement.");
						output("\"`&So then, let's get started...`6\"");
						addnav(array("Upgrade to %s",$next),"runmodule.php?module=academy&op=train&type=upgrade&ad=yes");
					}
				}elseif($advance == "yes"){
					$class++;
					set_module_pref("class",$class);
					set_module_pref("lsl",0);
					set_module_pref("level",0);
					output("`6%s heads into another room with Dycedarg.",$name);
					output("You hear clanging of weapons and the breaking of skin.");
					output("A cloth rips and you hear liquid hit the floor.");
					output("Hours later, he returns with a heavily inked tattoo on his arm.");
					output("\"`&Meet your new %s.`6\"",$next);
				}
			}
			break;
		}
?>