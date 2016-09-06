<?php
page_header("The Secret Cave");
global $session;
$op = httpget('op');
addnav("What do you do?");
output("`b`c`n`%C`3ave `%o`3f  `\$Q`^u`@e`#t`!z`%l`\$z`^a`@c`#a`!t`%e`\$n`^a`@n`#g`!o`^`b`c`n");
$ctreasure=array(get_module_setting("cfirstt"),get_module_setting("csecondt"),get_module_setting("cthirdt"),get_module_setting("cfourtht"),get_module_setting("cfiftht"),get_module_setting("csixtht"),get_module_setting("cseventht"),get_module_setting("ceigtht"),get_module_setting("cninetht"),get_module_setting("ctentht"));
if ($op=="artmuseum"){
	//taken from XChrisX's questbook.php starting here
	global $session;
	$return = httpget('return');
	$return = cmd_sanitize($return);
	$return = substr($return,strrpos($return,"/")+1);
	tlschema("nav");
	addnav("Return whence you came",$return);
	tlschema();
	$userid = httpget("user");
	$strike = false;
	//taken from xChrisX's questbook.php ending here
	page_header("Museum of Artifacts");
	rawoutput("<big>");
	output("`c");
	$artfinderid=get_module_pref("artfinder","jonescave",$userid);
	if ($artfinderid>=1) output("Current Title:`n");
	if ($artfinderid>9) output("`5`bWorld Renowned Archaeologist`b");
	elseif ($artfinderid==9) output("`6Dean of Archaeology");
	elseif ($artfinderid==8) output("`QDirector of Archaeology");
	elseif ($artfinderid==7) output("`&Senior Professor of Archaeology");
	elseif ($artfinderid==6) output("`\$Professor of Archaeology");
	elseif ($artfinderid==5) output("`%Associate Professor of Archaeology");
	elseif ($artfinderid==4) output("`!Assistant Professor of Archaeology");
	elseif ($artfinderid==3) output("`#Teaching Assistant of Archaeology");
	elseif ($artfinderid==2) output("`@Graduate Student of Archaeology");
	elseif ($artfinderid==1) output("`^Student of Archaeology");
	output("`c`n");
	rawoutput("</big>");
	$treasurenumid=get_module_pref("treasurenum","jonescave",$userid);
	if ($treasurenumid>=1) {	
		output("`c`b`0List of Aquisitions:`b`c");
		output("`b`c-%s`0-`b`c",get_module_setting("cfirstt"));	
	}
	if ($treasurenumid>=2) output("`b`c`0-%s`0-`b`c",get_module_setting("csecondt"));			
	if ($treasurenumid>=3) output("`b`c-%s`0-`b`c",get_module_setting("cthirdt"));		
	if ($treasurenumid>=4) output("`b`c-%s`0-`b`c",get_module_setting("cfourtht"));			
	if ($treasurenumid>=5) output("`b`c-%s`0-`b`c",get_module_setting("cfiftht"));			
	if ($treasurenumid>=6) output("`b`c-%s`0-`b`c",get_module_setting("csixtht"));			
	if ($treasurenumid>=7) output("`b`c-%s`0-`b`c",get_module_setting("cseventht"));		
	if ($treasurenumid>=8) output("`b`c-%s`0-`b`c",get_module_setting("ceigtht"));			
	if ($treasurenumid>=9) output("`b`c-%s`0-`b`c",get_module_setting("cninetht"));		
}
if ($op=="cjourney"){
	set_module_pref("cavetried",1);
	output("`^After gathering all your belongings, you head into the deepest parts of the forest looking for the area that the map points to.");
	output("It is definitely getting more and more dangerous.");
	output("You find a dart... and the `@poison`^ is still fresh.`n`n");
	output("You clear some brush and a huge `)Statue`^ looks like it's about to attack you!");
	output("You hear the sound of metal sliding behind you.");
	output("A huge `#B`\$i`^r`!d`^ sounds overhead.`n`n");
	output("What do you do?`n`n");
	addnav("`\$Fight the `)Statue","runmodule.php?module=jonescave&op=cstatuefight");
	addnav("Turn and `QWhip","runmodule.php?module=jonescave&op=cturnwhip");
	addnav("Hide from the `#B`\$i`^r`!d","runmodule.php?module=jonescave&op=chide");
	addnav("Leave the Forest","runmodule.php?module=jonescave&op=leavecave");
}
if ($op=="cstatuefight"){
	output("`^You grab your `QWhip`^ and get ready to fight the evil statue attacking you!`n`n");
	output("You turn to `#Zatipolio`^ and `\$Barca`^ for help!");
	set_module_pref("monsternum",2);
	addnav("`&Statue `\$Fight","runmodule.php?module=jonescave&op=attack");
}
if ($op=="chide"){
	output("`^You dive into the bushes as `#Zatipolio`^ and `\$Barca`^ watch.");
	output("You stand up and bravely brush yourself off.");
	output("`n`n`5'You know, that was just a `%p`4a`#r`!r`&o`@t`5'`^ says `#Zatipolio`^.");
	output("`5'Even I am not afraid of a `%p`4a`#r`!r`&o`@t`5.'`n`n");
	output("");
	if ($session['user']['turns']>=1) {
		output("`^You end up spending `@a turn`^ explaining how a killer `%p`4a`#r`!r`&o`@t`^ attacked your family.");
		output("`#Zatipolio`^ and `\$Barca`^ look at you with disbelief.");
		output("You feel a little `&less charming`^.");
		$session['user']['charm']--;
		$session['user']['turns']--;
	}else{
		output("Despite your most eloquent arguments to explain why `%p`4a`#r`&o`!r@t`!s`^ are so dangerous, neither of your companions believe you.");
		output("`n`nYou `&lose 2 charm`^.");
		$session['user']['charm']-=2;
	}
	output("`n`n`@'Well, shall we adventure on?'");
	addnav("Leave","runmodule.php?module=jonescave&op=leavecave");
	addnav("`%C`3ave `^Entrance","runmodule.php?module=jonescave&op=caveentrance");
}		
if ($op=="cturnwhip" || $op=="caveentrance"){
	set_module_pref("cavestage",1);
	output("`^Suddenly, you hear someone behind you drawing a weapon!");
	output("`n`nYou turn and see that `\$Barca`^ is about to betray you and stab you in the back!");
	output("With a quick flick of your `Qwhip`^ you disarm the fool and he runs away.`n`n");
	output("`#Zatipolio`^ looks nervous and intimidated by the entrance to the `%C`3ave`^.");
	output("After some negotiation, you both decide to enter the `%C`3ave`^ some other day.`n`n");
	output("You head back to the less dangerous parts of the `@Forest`^ and collect your old weapon and armor.`n`n");
	output("`@'Dont worry,'`^ you tell `#Zatipolio`^, `@'We'll be back.'`n`n");
	output("`c`\$`bTo Be Continued...`c`b`n`n`n");
	jonescave_leave();
	addnav("Back to the Forest","forest.php");
}
if ($op=="ccave2"){
	set_module_pref("cavetried",1);
	output("`^You enter the `%C`3ave`^ with `#Zatipolio`^ close behind.");
	output("The light fades quickly as you venture slowly in.");
	output("`n`nSuddenly, `#Zatipolio`^ stops and starts to stutter in fear.");
	output("You turn to find `)S`\$piders`^ everywhere!");
	output("`n`nIt's up to you to fight your way to safety and defeat the `)S`\$piders`^!`n`n");
	set_module_pref("monsternum",3);
	addnav("`)S`\$pider Fight","runmodule.php?module=jonescave&op=attack");
}
if ($op=="leavecave"){
	set_module_pref("cchicken",1);
	output("`\$Barca`^ and `#Zatipolio`^ look at you with disbelief.");
	output("All this preparation for nothing?`n`n");
	output("You shrug and grab your old `#%s`^ and `#%s`^ and head back to the village.`n`n",$session['user']['weapon'],$session['user']['armor']);
	switch(e_rand(1,12)){
		case 1: case 2: case 3:
			output("They decide that you aren't worth their time and grab the map from you.");
			output("The adventure will be theirs instead.`n`n");
			output("`c`\$`bTo Be Continued...`c`b`n`n`n");
			jonescave_leave();
			addnav("Back to the Forest","forest.php");
		break;
		case 4: case 5: case 6:
			output("Perhaps you can try again.");
			output("Maybe when you're feeling a little more adventurous.");
			output("`\$Barca`^ steals the map from you when you aren't looking.`n`n");
			output("`c`\$`bTo Be Continued...`c`b`n`n`n");
			jonescave_leave();
			addnav("Back to the Forest","forest.php");
		break;
		case 7: case 8: case 9:
			output("`\$Barca`^ taps you on the shoulder.");
			output("As you turn to see what he wants, he punches you in the nose and steals your map.`n`n");
			output("Although the punch didn't hurt too much, it makes you look kinda ugly.");
			output("`n`nYou `&Lose One Charm`^.`n`n");
			$session['user']['charm']--;
			output("`c`\$`bTo Be Continued...`c`b`n`n`n");
			jonescave_leave();
			addnav("Back to the Forest","forest.php");
		break;
		case 10:
			output("`\$Barca`^ taps you on the shoulder.");
			output("As you turn to see what he wants, he punches you in the nose and steals your map.`n`n");
			output("Oh man, that hurt!  I think he broke your nose.");
			output("You `\$Lose 5 hitpoints`^!`^`n`n");
			$session['user']['hitpoints'] -=5;
			if ($session['user']['hitpoints']<=0){
				jonescave_leave();
				output("`^The punch lodges a sliver of bone into your brain and you die!");
				output("`n`n`\$Barca`^ steals all your gold.`n`n");
				$exploss = round($session['user']['experience']*.05);
				output("`b`4You lose `#%s experience because of cowardice`4.`b`n`n",$exploss);
				addnews("%s`^ died from a punch because %s was too afraid to explore a `%C`3ave`^.",$session['user']['name'],translate_inline($session['user']['sex']?"she":"he"));
				addnav("Daily news","news.php");
				$session['user']['experience']-=$exploss;
				$session['user']['alive'] = false;
				$session['user']['hitpoints'] = 0;
				$session['user']['gold']=0;
			}else{
				output("Well, that's not acceptable behavior.");
				output("It's fighting time!");
				addnav("Fist Fight with `\$Barca`^","runmodule.php?module=jonescave&op=attack");
				set_module_pref("monsternum",1);
			}
		break;
		case 11: case 12:
			output("You turn to leave.");
			output("Nothing ventured, nothing gained.");
			output("Except `#Zatipolio`^ did gain something.");
			output("He stole your `bgold`b pouch.");
			output("`n`nYou lose all your gold!`n`n");
			output("Oh, and `\$Barca`^ stole the map.");
			output("Sheesh, it really sucks being a whimp.`n`n");
			$session['user']['gold']=0;
			output("`c`\$`bTo Be Continued...`c`b`n`n`n");
			jonescave_leave();
			addnav("Back to the Forest","forest.php");
		break;
	}
}
if ($op=="cpayoffgold"){
	output("`^You hand over the gold and look at `#Zatipolio`^; anxious to begin the journey.");
	output("He puts the money in a secret sac and gets the equipment ready.");
	set_module_pref("cchicken",0); 
	addnav("Ready to Go!","runmodule.php?module=jonescave&op=cavestart");
	$session['user']['gold']-=1000;
}
if ($op=="cpayoffgems"){
	output("`^You hand over the `%gems`^ and look at `#Zatipolio`^; anxious to begin the journey.");
	output("He puts the money in a secret sac and gets the equipment ready.");
	set_module_pref("cchicken",0); 
	addnav("Ready to Go!","runmodule.php?module=jonescave&op=cavestart");
	$session['user']['gems']-=2;
}
if ($op=="cpayoffcharm"){
	output("`^You drink the potion that he hands you and feel the effects wash over you.");
	output("Suddenly, `#Zatipolio`^ appears much more handsome as your Charisma suffers.");
	output("Finally, you look at `#Zatipolio`^; anxious to begin the journey.");
	output("He gets the equipment ready.");
	set_module_pref("cchicken",0); 
	addnav("Ready to Go!","runmodule.php?module=jonescave&op=cavestart");
	$session['user']['charm']-=3;
}
if ($op=="cavestart"){
	output("`^You recall that your trip to through the `%C`3ave`^ has been pretty rough so far.");
	output("You've been betrayed by `\$Barca`^, had to fight scary `)S`\$piders`^, and you set a `QWhip`^ up across the perilous pit.");
	output("Luckily, you know where to get another `QWhip`^!");
	output("`n`nOnce again, you go to the local bar and find `#Zatipolio`^.");
	output("He looks excited to	continue the journey!`n`n");
	addnav("Leave","runmodule.php?module=jonescave&op=leavecave3");
	addnav("Yes! Adventure Awaits","runmodule.php?module=jonescave&op=ccave3");
	output("`^In preparation, you decide to store your `#%s`^ and your `#%s`^ for later.",$session['user']['weapon'],$session['user']['armor']);
	output("Instead, you'll use your trusty `QWhip`^ and `qFedora`^ for protection.`n`n");
	output("Are you ready to seek the %s`^?",$ctreasure[get_module_pref("treasurenum")]);
	set_module_pref("tempweapon",$session['user']['weapon']);
	set_module_pref("temparmor",$session['user']['armor']);
	$session['user']['weapon']="`QWhip";
	$session['user']['armor']="`qFedora";
}
if ($op=="ccavepit"){
	output("`^You run deeper into the cave but stop suddenly.");
	output("After a quick glance over your shoulder at a wide eyed `#Zatipolio`^, you look down to notice a gaping pit before you.`n`n");
	output("With an expert flick of your wrist, the `QWhip`^ wraps around a beam and holds tight.");
	output("You'll have to swing across!`n`n");
	output("Are you ready to try?");
	addnav("Leave","runmodule.php?module=jonescave&op=leavecave2");
	addnav("Swing Across the Pit","runmodule.php?module=jonescave&op=cswing");	
}
if ($op=="cswing"){
	output("`^You give a test swing...`n`n");
	switch(e_rand(1,4)){
		case 1: case 2: case 3:
			output ("`^but don't make it across.");
			output("You turn around and notice `#Zatipolio`^ stifle a giggle`n`n");
			$number=(e_rand(1,3));
			if ($number==1) {
				if ($session['user']['turns']>=1) {
					output("You `@wasted a turn `^trying to cross.");
					output("`n`nReady to try again?");	
					$session['user']['turns']--;
				}else{
					output("You lose `&2 charm points`^ because of your poor attempt.");
					$session['user']['charm']-=2;
				}
			}else{
				output("You feel foolish, but perhaps you're ready to give it another try.");
			}	
			addnav("Leave","runmodule.php?module=jonescave&op=leavecave2");
			addnav("Swing Again","runmodule.php?module=jonescave&op=cswing");
		break;
		case 4:
			output("`@and make it across!!");
			output("`^`n`n You secure the `QWhip`^ and come back to help `#Zatipolio`^, but he hesitates.");
			output("You discuss your options about continuing further, but both of you realize that you've come far enough today.");
			output("`n`nDon't worry, you'll be back soon enough!`n`n");
			set_module_pref("cavestage",2);
			output("`c`\$`bTo Be Continued...`c`b`n`n`n");		
			jonescave_leave();
			addnav("Back to the Forest","forest.php");
		break;
	}					
}
if ($op=="leavecave2"){
	set_module_pref("cchicken",2);
	output("`#Zatipolio`^ looks at you with disbelief.");
	output("`#'All this preparation for nothing? I am getting tired of these games.'`n`n");
	output("`^You shrug and grab your old `#%s`^ and`# %s`^ and head back to the village.`n`n",$session['user']['weapon'],$session['user']['armor']);
	switch(e_rand(1,12)){
		case 1: case 2: case 3: case 4: case 5: case 6:
			output("He decides that you aren't worth his time and yells after you as you walk away...");
			output("`#'You may not like my price next time you need my help!'`^`n`n");
		break;
		case 7: case 8: case 9:
			output("`#Zatipolio`^  drops your gear violently and half of it lands on your foot...");
			output("A large gash appears in your leg and starts to bleed.");
			output("This will hurt next time you get into a battle.`n`n");
			apply_buff('legwound',array(
				"name"=>"Wounded Leg",
				"rounds"=>15,
				"wearoff"=>"`&your leg finally heals!",
				"atkmod"=>.92,
				"roundmsg"=>"`#Your throbbing leg causes you great pain.",
			));
		break;
		case 10:
			output("`#Zatipolio`^  drops your gear violently and half of it lands on your foot...");
			output("A large gash appears in your leg and starts to bleed.");
			output("The cut becomes infected and causes you to lose almost all your hitpoints!`n`n");
			$session['user']['hitpoints']=1;
		break;
		case 11: case 12:
			output("A violent storm occurs.");
			output("`#Zatipolio`^ looks at you a little gratefully for insisting on not going, but still a little bitter about the missed opportunity.");
			output("`n`nYou `@Gain 4 Extra Turns`^ because you feel so smart and smug!`n`n");
			$session['user']['turns']+=4;
		break;	
	}
	output("`c`\$`bTo Be Continued...`c`b`n`n`n");
	addnav("Back to the Forest","forest.php");
	jonescave_leave();	
}
if ($op=="ccave3"){
	set_module_pref("cavetried",1);
	output("`^You cautiously walk past the dead spiders to your `QWhip`^ swing.");
	output("Both of you easily swing across the pit.");
	output("`n`n You leave `#Zatipolio`^ and cautiously walk across a patterned floor.");
	output("Soon you find yourself staring at the amazing %s`^!!`n`n",$ctreasure[get_module_pref("treasurenum")]);
	output("You slowly approach the treasure and before you have a chance to think twice you grab it and run!");
	output("You'll have to dodge a barrage of `6shooting arrows`^ to make it to safety!");	
	addnav("Arrow Dodging","runmodule.php?module=jonescave&op=attack");
	set_module_pref("monsternum",4);
}
if ($op=="ccavepit2"){
	output("`^You arrive back at the `QWhip Swing`^ and send `#Zatipolio`^ across.");
	output("The walls start to shake and crumble.");
	output("You yell for the `QWhip`^, but `#Zatipolio`^ shakes his head.`n`n");
	output("`#'Throw me the %s`#!'",$ctreasure[get_module_pref("treasurenum")]);
	output("`n`n`^You realize you have no choice.");
	output("He catches the artifact and runs away without helping you across!`n`n");
	output("You have no choice but to jump across!`n`n");
	output("Using all your courage you make the great leap!");
	output("`n`nYou don't make it all the way, but you're able to grab the edge!");
	addnav("Keep Trying!","runmodule.php?module=jonescave&op=cjump");
}
if ($op=="cjump"){
	$jumptry=e_rand(1,6);
	output("`^You struggle and struggle to scamper up ");
	if ($jumptry==2) output("and up ");
	if ($jumptry==3) output("but slip a little ");
	if ($jumptry==4) output("and grab for a vine but you fail ");
	output("because you're not quite to the top yet...`n`n");
	if ($jumptry==1){
		output("With one last effort you pull yourself out of the pit and scramble to your feet. ");
		output("Now you have to just catch up with that traitor `#Zatipolio`^!!");
		addnav("Catch Him","runmodule.php?module=jonescave&op=ccatchup");
	}else{
		addnav("Keep trying!","runmodule.php?module=jonescave&op=cjump");
	}
}
if ($op=="ccatchup"){
	output("`^Before you know it, you catch up to `#Zatipolio`^... ");
	output("or at least what's left of him. ");
	output("He seems to have gotten himself killed in one of the many boobie traps throughout the `%C`3ave`^. ");
	output("Knowing that your time is running out, you grab the %s`^ and start to run to the exit!`n`n",$ctreasure[get_module_pref("treasurenum")]);
	output("This is getting bad... because you turn around to see the biggest roundest boulder you've ever seen coming `bRIGHT AT YOU`b!!!!");
	addnav("RUN!!!","runmodule.php?module=jonescave&op=attack");
	set_module_pref("monsternum",5);
}
if ($op=="leavecave3"){
	output("`#Zatipolio`^ can't believe you're passing up this opportunity.");
	output("He reminds you of all the excitement you've been having so far.");
	output("`n`n Won't you reconsider?!?!");
	addnav("No, Seriously I Want To Leave","runmodule.php?module=jonescave&op=leavecavenow3");
	addnav("Okay, Let's Adventure!","runmodule.php?module=jonescave&op=ccave3");		
}
if ($op=="leavecavenow3"){
	set_module_pref("cchicken",3);	
	output("`^You shrug and grab your old `#%s`^ and `#%s`^ and head back to the village.`n`n",$session['user']['weapon'],$session['user']['armor']);
	switch(e_rand(1,12)){
		case 1: case 2: case 3: case 4: case 5: case 6:
			output("He decides that you aren't worth your time and yells after you as you walk away.");
			output("Oh wait, I can't repeat what he said.");
			output("Let's just say that he is questioning your heritage and leave it at that.`^`n`n");
		break;
		case 7: case 8: case 9:
			output("`#Zatipolio`^ leaves and tells you that you aren't a nice person.");
			output("`n`nFor some reason, it makes you sad to think he would say such a thing.`n`n");
			apply_buff('sadness',array(
				"name"=>"Sadness",
				"rounds"=>5,
				"wearoff"=>"`%The Sun comes out and makes you happy.",
				"defmod"=>.95,
			));
		break;
		case 10:
			output("`#Zatipolio`^ feels relieved that he can go back to the bar.");
			output("He asks if you can give him some money for drinking.`n`n");
			if ($session['user']['gold']>=150) {
				output("You hand him 150 gold and head back to the `@forest`^.`n`n");	
				$session['user']['gold']-=150;
			}else{
				output("You shrug and give him all your gold so that you can head back to the `@forest`^.`n`n");
				$session['user']['gold']=0;
			}
		break;
		case 11: case 12:
			output("You head over to the bar so you know where to get `#Zatipolio`^.");
			output("You pick a flower on the way out and a little happy smile spreads across your face.`n`n");
			apply_buff('happiness',array(
				"name"=>"Happiness",
				"rounds"=>5,
				"wearoff"=>"`%The happiness fades and you go back to normal.",
				"defmod"=>1.05,
			));
		break;
	}
	output("`c`\$`bTo Be Continued...`c`b`n`n`n");
	addnav("Back to the Forest","forest.php");
	jonescave_leave();	
}
if ($op=="cfinish") {
	addnews("%s `^successfully recovered an artifact from a Secret `%C`3ave`^!",$session['user']['name']);
	set_module_pref("cchiken",0);
	set_module_pref("cavestage",0);
	output("`^You sit down to rest and pull out the %s`^ and stare at it.",$ctreasure[get_module_pref("treasurenum")]);
	output("It's beauty is startling.`n`n");
	if (get_module_pref("treasurenum")==0){
		$gems=get_module_setting("firstgem");
		$gold=get_module_setting("firstgold");
		output("You realize the %s`^ is more powerful than anyone imagined.",$ctreasure[get_module_pref("treasurenum")]);
		output("When you wander back to the `@Forest`^, you encounter an `#Ancient Scribe`^ who notices the artifact you're carrying.");
		output("`n`n`!'Oh my!  I've been looking for the %s`! for many years.",$ctreasure[get_module_pref("treasurenum")]);
		output("I will give you");
		if ($gems==0 && $gold==0) $gold=1;
		if ($gems>0) output("`%%s gem%s`!",$gems,translate_inline($gems>1?"s":""));
		if ($gems>0 && $gold>0) output("and");
		if ($gold>0) output("`^%s gold`!",$gold);
		output("for it!'`n`n");
		output("`^That's a pretty good bargain for the artifact, so you gladly trade.`n`n");
		output("You gain");
		if ($gems>0) output("`%%s gem%s`^",$gems,translate_inline($gems>1?"s":""));
		if ($gems>0 && $gold>0) output("and");
		if ($gold>0) output("`^%s gold",$gold);
		output("`^for the artifact.");
		$session['user']['gems']+=$gems;
		$session['user']['gold']+=$gold;
		debuglog("won $gems gems and $gold gold by finding the 1st artifact in the Secret Cave.");
	}elseif (get_module_pref("treasurenum")==1){
		$turns=get_module_setting("secondturns");
		output("You walk over to the`b sun`b and look at the time.");
		output("Suddenly, the %s`^ starts to fade away, passing the energy of the artifact into your body.`n`n",$ctreasure[get_module_pref("treasurenum")]);
		output("You `@Gain %s Turn%s`^!",$turns,translate_inline($turns>1?"s":""));
		debuglog("won $turns extra turns by finding the 2nd artifact in the Secret Cave.");
		$session['user']['turns']+=$turns;
	}elseif (get_module_pref("treasurenum")==2){
		$attack=get_module_setting("thirdattack");
		$gold=get_module_setting("thirdgold");
		output("You play with the %s`^ and feel a strange magic flow over you.",$ctreasure[get_module_pref("treasurenum")]);
		output("`n`nYou `&gain %s attack`^ point%s.",$attack,translate_inline($attack>1?"s":""));
		if ($gold>0) output("`n`nYou're also able to sell the artifact in the `@village `^for `b%s gold`b!",$gold);
		$session['user']['attack']+=$attack;
		$session['user']['gold']+=$gold;
		debuglog("won $gold gold and $attack attack points by finding the 3rd artifact in the Secret Cave.");
	}elseif (get_module_pref("treasurenum")==3){
		$defense=get_module_setting("fourthdefense");
		$charm=get_module_setting("fourthcharm");
		output("You look at the %s and smile.",$ctreasure[get_module_pref("treasurenum")]);
		output("Yes, this is quite a powerful artifact!");
		output("You put it on and it suddenly melts into your armor.`n`n");
		output("You`& Gain %s Defense `^ however your armor doesn't look as pretty anymore and you `&Lose %s Charm`^.",$defense,$charm);
		$session['user']['defense']+=$defense;
		$session['user']['charm']-=$charm;
		debuglog("Won 2 defense but lost 1 charm by finding the 4th artifact in the Secret Cave.");
	}elseif (get_module_pref("treasurenum")==4){
		$goldloss=round($session['user']['gold']*get_module_setting("fifthgold")/100);
		if ($goldloss==0) $goldloss=1;
		output("You sit down and do a quick calculation.");
		output("By playing with the %s`^, you see that your finances are improving!",$ctreasure[get_module_pref("treasurenum")]);
		output("`n`n  Oh no! You make a simple math error and suddenly");
		if ($session['user']['gold']==0){
			output("you find that one dinky little gold piece appears.");
			$session['user']['gold']=1;
			debuglog("gained 1 gold and a buff by finding the 5th artifact in the Secret Cave.");
		}elseif ($session['user']['gold']>$goldloss){
			output("you `blose %s gold`b!",$goldloss);
			$session['user']['gold']-=$goldloss;
			debuglog("lost $goldloss gold on hand but gained a buff by finding the 5th artifact in the Secret Cave.");
		}else{
			output("`bALL YOUR GOLD DISAPPEARS!!!`b");
			$session['user']['gold']=0;
			debuglog("lost all gold on hand but gained a buff by finding the 5th artifact in the Secret Cave.");
		}
		output("`n`nYou throw the %s far away in disgust.",$ctreasure[get_module_pref("treasurenum")]);
		output("Actually, it felt really good to do that.");
		output("The `\$Adrenaline `4Surges`^ through you!");
		apply_buff('adrenalinesurge',array(
			"name"=>"`\$Adrenaline `4Surge",
			"rounds"=>15,
			"wearoff"=>"You feel the adrenaline surge slow.",
			"atkmod"=>1.2,
			"defmod"=>1.2,
			"roundmsg"=>"`@Your anger makes you stronger!",
		));
	}elseif (get_module_pref("treasurenum")==5){
		$gold=get_module_setting("sixthgold");
		output("You sit down to stare at the artifact and start to read what is written on it.");
		output("`n`n`2'Whosoever reads this will become VERY VERY Rich!'`n`n");
		output("`^You look around excitedly for the riches to come!");
		output("`n`nNothing.");
		output("`n`n Oh wait!");
		output("You notice `bOne Gold Piece`b at your feet.");
		output("`n`nYou look at the %s `^one more time...",$ctreasure[get_module_pref("treasurenum")]);
		output("still says the same thing!");
		output("Darn it!");
		output("`n`nYou look down and see `bAnother Gold Piece!");
		output("`b`n`nHey wait, this rocks!`n`n");
		if ($session['user']['turns']>=2) {
			output("`^You end up spending `@2 turns`^ reading the artifact over and over!");
			output("After a LOT of reading, you realize you have `bGained %s Gold`b!",$gold);
			$session['user']['gold']+=$gold;
			$session['user']['turns']-=2;
			debuglog("gained $gold gold but lost 2 turns by finding the 6th artifact in the Secret Cave.");
		}else{
			$gold=round($gold/2);
			output("`^You end up spending `@a turn`^ reading the artifact over and over!");
			output("After a LOT of reading, you realize you have `bGained %s Gold`b!",$gold);
			$session['user']['gold']+=$gold;
			if ($session['user']['turns']>0) $session['user']['turns']--;
			debuglog("gained $gold gold but lost 1 turn by finding the 6th artifact in the Secret Cave.");
		}
		output("The %s disappears.",$ctreasure[get_module_pref("treasurenum")]);
	}elseif (get_module_pref("treasurenum")==6){
		output("You drop the %s`^ and enjoy the lovely sound that dropped artifacts make.`n`n",$ctreasure[get_module_pref("treasurenum")]);
		output("You try to find it, but you can't.");
		output("Boy, does this suck.");
		output("After spending quite a bit looking around, you look up to see a `)Z`&e`)b`&r`)a`^ smiling at you.");
		output("He decides to be your friend and help you in your battles today!");
		$dkb = round($session['user']['dragonkills']*.1);
		apply_buff('friendlyzebra',array(
			"name"=>"`)Z`&e`)b`&r`)a",
			"rounds"=>-1,
			"minioncount"=>1,
			"minbadguydamage"=>0,
			"maxbadguydamage"=>6+$dkb,
			"effectmsg"=>"`^The `)Z`&e`)b`&r`)a `^kicks for `\${damage}`) hitpoints`^.",
			"effectnodmgmsg"=>"`^The `)Z`&e`)b`&r`)a `^misses.",
			"effectfailmsg"=>"`^The `)Z`&e`)b`&r`)a `^misses.",
		));
		debuglog("gained a Zebra Buff by finding the 7th artifact in the Secret Cave.");
	}elseif (get_module_pref("treasurenum")==7){
		$perm=get_module_setting("giveperm");
		$temp=get_module_setting("eighthhps");
		$gold=get_module_setting("eighthgold");
		output("The %s`^ turns out to be a very impressive artifact.",$ctreasure[get_module_pref("treasurenum")]);
		output("In fact, the %s`^ gives you",$ctreasure[get_module_pref("treasurenum")]);
		if ($perm>0) {
			output("`\$%s permanent hitpoint%s`^!",$perm,translate_inline($perm>1?"s":""));
			$session['user']['maxhitpoints']+=$perm;
			debuglog("gained $perm maxhitpoints and $gold gold by finding the 8th artifact in the Secret Cave.");
		}else{
			output("`\$%s hitpoint%s`^!",$temp,translate_inline($temp>1?"s":""));
			$session['user']['hitpoints']+=$temp;
			debuglog("gained $temp hitpoints and $gold gold by finding the 8th artifact in the Secret Cave.");
		}
		output("`n`nIn addition, you are able to sell the %s`^ for `b%s gold`b!",$ctreasure[get_module_pref("treasurenum")],$gold);
		$session['user']['gold']+=$gold;
	}elseif (get_module_pref("treasurenum")==8){
		$charm=get_module_setting("ninethcharm");
		$turns=get_module_setting("ninethturns");
		$gold=get_module_setting("ninethgold");
		output("The %s`^ turns out to be a wonderful `@good-luck `&charm`^!",$ctreasure[get_module_pref("treasurenum")]);
		output("`n`n  You gain `&%s charm`^, `@%s turn%s`^, and `b%s gold`b!",$charm,$turns,translate_inline($turns>1?"s":""),$gold);
		$session['user']['charm']+=$charm;
		$session['user']['turns']+=$turns;
		$session['user']['gold']+=$gold;
		debuglog("gained $charm charm, $turns turns, and $gold gold by finding the 9th artifact in the Secret Cave.");
	}elseif (get_module_pref("treasurenum")==9){
		$attack=get_module_setting("tenthattack");
		$defense=get_module_setting("tenthdefense");
		$gold=get_module_setting("tenthgold");
		$name=$session['user']['name'];
		output("You look at the %s`^ and suddenly a strange impulse comes over you.",$ctreasure[get_module_pref("treasurenum")]);
		output("You take a bite out of the artifact and swallow!`n`n");
		output("The power of the %s`^ surges inside you!",$ctreasure[get_module_pref("treasurenum")]);
		output("`n`nYou gain `&%s defense`^ and`& %s attack`^!!",$defense,$attack);
		output("`n`nCongratulations! You have found all 10 artifacts.`n`n");
		increment_module_pref("artfinder",1);
		output("You are now recognized as a");
		if (get_module_pref("artfinder")>9){
			output("`5`bWorld Renowned Archaeologist`b`^!");
			addnews("%s `^is now recognized as a `5`bWorld Renowned Archaeologist`b`^.",$name);
		}
		if (get_module_pref("artfinder")==9){
			output("`6Dean of Archaeology`^!");
			addnews("%s `^is now recognized as a `6Dean of Archaeology`^.",$name);			
		}
		if (get_module_pref("artfinder")==8){
			output("`QDirector of Archaeology`^!");
			addnews("%s `^is now recognized as a `QDirector of Archaeology`^.",$name);			
		}
		if (get_module_pref("artfinder")==7){
			output("`&Senior Professor of Archaeology`^!");
			addnews("%s `^is now recognized as a `&Senior Professor of Archaeology`^.",$name);			
		}
		if (get_module_pref("artfinder")==6){
			output("`\$Professor of Archaeology`^!");
			addnews("%s `^is now recognized as a `\$Professor of Archaeology`^.",$name);			
		}
		if (get_module_pref("artfinder")==5){
			output("`%Associate Professor of Archaeology`^!");
			addnews("%s `^is now recognized as an `%Associate Professor of Archaeology`^.",$name);			
		}
		if (get_module_pref("artfinder")==4){
			output("`!Assistant Professor of Archaeology`^!");
			addnews("%s `^is now recognized as an `!Assistant Professor of Archaeology`^.",$name);			
		}
		if (get_module_pref("artfinder")==3){
			output("`#Teaching Assistant of Archaeology`^!");
			addnews("%s `^is now recognized as a `#Teaching Assistant of Archaeology`^.",$name);			
		}
		if (get_module_pref("artfinder")==2){
			output("`@Graduate Student of Archaeology`^!");
			addnews("%s `^is now recognized as a `@Graduate Student of Archaeology`^.",$name);			
		}
		if (get_module_pref("artfinder")==1){
			output("`^Student of Archaeology`^!");
			addnews("%s `^is now recognized as a `bStudent of Archaeology`b.",$name);			
		}
		if (get_module_pref("artfinder")<10) output("If you are able to repeat your accomplishments, you will receive even greater recognition.`n`n");
		output("In addition, a grant has been issued to you by the `%World Society of Archaeologists`^ in the form of `b%s gold`b.`n`n",$gold);
		output("`b`c`\$C`^o`@n`#g`%r`\$a`^t`@u`#l`%a`\$t`^i`@o`#n`%s`\$!`^!`b`c");
		$session['user']['defense']+=$defense;
		$session['user']['attack']+=$attack;
		$session['user']['gold']+=$gold;
		debuglog("gained $attack attack, $defense defense, and $gold gold by finding the 10th artifact in the Secret Cave.");
	}
	increment_module_pref("treasurenum",1);
	increment_module_pref("treasurehof",1);
	if (get_module_pref("treasurenum")==10) set_module_pref("treasurenum",0);
	output("`n`n`@If you want, before you leave, you can leave a note on the boulder just outside the cave so that other adventurers will be able to read your parting words.`n`n");
	addnav("Leave Your Mark","runmodule.php?module=jonescave&op=ttreasure1");	
}
if ($op=="ttreasure1") {
	addnav("Go back to the Forest","runmodule.php?module=jonescave&op=cend");
	require_once("lib/commentary.php");
	addcommentary();
	viewcommentary("lastwordcave","Please Mention Your Success",20,"brags");
}
if ($op=="cforest") {
	output("You don't have the energy for entering the `%C`3ave`^ today.");
	output("Perhaps you'll be able to come back another day.`n`n`n");
	addnav("Back to the Forest","forest.php");
	jonescave_leave();
}	
if ($op=="cend"){
	jonescave_leave();
	output("`^You pick up your `#%s`^ and put on your `#%s`^.",$session['user']['weapon'],$session['user']['armor']);
	output("It's time to put away the `QWhip`^ and `qFedora`^ for another day.`n`n");
	output("`n`b`c`@Congratulations on Completing the Adventure!!`b`c`n`n`n");
	addnav("Back to the Forest","forest.php");
}	
if ($op=="attack") {
	if (get_module_pref("monsternum")==1){
		$level = $session['user']['level']-1;
		if ($level<=0) $level=1;
		$name=translate_inline("Barca");
		$weapon=translate_inline("his furious fists");
		$badguy = array(
			"creaturename"=>$name,
			"creaturelevel"=>$level,
			"creatureweapon"=>$weapon,
			"creatureattack"=>round($session['user']['attack']),
			"creaturedefense"=>round($session['user']['defense']),
			"creaturehealth"=>round($session['user']['maxhitpoints']*1.1),
			"diddamage"=>0,
			"type"=>"barca"
		);
		$session['user']['badguy']=createstring($badguy);
	}
	if (get_module_pref("monsternum")==2){
		$level = $session['user']['level']+2;
		$name=translate_inline("`&Huge Statue");
		$weapon=translate_inline("his `&Boulder Shaped Fists");
		$badguy = array(
			"creaturename"=>$name,
			"creaturelevel"=>$level,
			"creatureweapon"=>$weapon,
			"creatureattack"=>round($session['user']['attack'])+2,
			"creaturedefense"=>round($session['user']['defense'])+3,
			"creaturehealth"=>round($session['user']['maxhitpoints']*1.2),
			"diddamage"=>0,
			"type"=>"statue"
		);
		apply_buff('backup', array(
			"startmsg"=>"`^Zatipolio and Barca fight by your side!`n",
			"name"=>"Zatipolio and Barca",
			"rounds"=>5,
			"wearoff"=>"Your friends can't keep up with the fight.",
			"minioncount"=>2,
			"minbadguydamage"=>0,
			"maxbadguydamage"=>10,
			"effectmsg"=>"`^Zatipolio and Barca hit for `#{damage} damage`^.",
			"effectnodmgmsg"=>"`^Zatipolio makes a feeble attempt at hurting the Statue.",
			"effectfailmsg"=>"`^Barca can't seem to hurt the Statue.",
		));
		$session['user']['badguy']=createstring($badguy);
	}
	if (get_module_pref("monsternum")==3){
		$level = $session['user']['level']+1;
		$dkb = round($session['user']['dragonkills']*.1);
		$name=translate_inline("the `)Huge Spider");
		$weapon=translate_inline("its `\$Creepy `)Fangs");
		$badguy = array(
			"creaturename"=>$name,
			"creaturelevel"=>$level,
			"creatureweapon"=>$weapon,
			"creatureattack"=>round($session['user']['attack']),
			"creaturedefense"=>round($session['user']['defense'])-1,
			"creaturehealth"=>round($session['user']['maxhitpoints']*1.1),
			"diddamage"=>0,
			"type"=>"hugespider"
		);
		apply_buff('spiderbites', array(
			"startmsg"=>"`4Little baby spiders start to bite you`n",
			"name"=>"`4Baby Spiders",
			"rounds"=>3,
			"wearoff"=>"You step on the last spider.",
			"minioncount"=>$session['user']['level']+$dkb,
			"mingoodguydamage"=>-2,
			"maxgoodguydamage"=>3+$dkb,
			"effectmsg"=>"A `\$spider`) bites you for `\${damage}`) hitpoints`^.",
			"effectnodmgmsg"=>"`^The spider gets brushed away by your `QWhip`^!",
			"effectfailmsg"=>"`^The spider bites uselessly at your `qFedora`^!",
		));
		apply_buff('zatipolio', array(
			"startmsg"=>"`n`#Zatipolio`^ grabs a torch to fight by your side`n",
			"name"=>"`#Zatipolio",
			"rounds"=>4,
			"wearoff"=>"`#Zatipolio`^ runs to hide in a corner",
			"minioncount"=>1,
			"minbadguydamage"=>5,
			"maxbadguydamage"=>15+$dkb,
			"effectmsg"=>"`#Zatipolio`^ burns a couple of spiders for `#{damage} damage`^.",
			"effectnodmgmsg"=>"`#Zatipolio`^ swings and misses the `)H`\$uge `)S`\$pider.",
			"effectfailmsg"=>"`#Zatipolio`^ tries to burn a little spider but misses.",
		));
		$session['user']['badguy']=createstring($badguy);
	}
	if (get_module_pref("monsternum")==4){
		$level = $session['user']['level'];
		$dkb = round($session['user']['dragonkills']*.1);
		$name=translate_inline("Barrage of arrows");
		$weapon=translate_inline("pointy hurty tips");
		$badguy = array(
			"creaturename"=>$name,
			"creaturelevel"=>$level,
			"creatureweapon"=>$weapon,
			"creatureattack"=>round($session['user']['attack']*.9),
			"creaturedefense"=>round($session['user']['defense']*.95),
			"creaturehealth"=>round($session['user']['maxhitpoints']*.75),
			"diddamage"=>0,
			"type"=>"arrowbarrage"
		);
		apply_buff('arrowpoints', array(
			"startmsg"=>"Little pointy arrows hit you`n",
			"name"=>"`4Pointy Arrows",
			"rounds"=>1,
			"minioncount"=>$session['user']['level']+2+$dkb,
			"mingoodguydamage"=>1,
			"maxgoodguydamage"=>1+$dkb,
			"effectmsg"=>"Owie owie owie owie!! You take `\${damage}`) hitpoints`^.",
		));
		$session['user']['badguy']=createstring($badguy);
	}
	if (get_module_pref("monsternum")==5){
		$level = $session['user']['level']-1;
		if ($level<=0) $level=1;
		$name=translate_inline("`&Huge Rolling Boulder");
		$weapon=translate_inline("impressive roundness");
		$badguy = array(
			"creaturename"=>$name,
			"creaturelevel"=>$level,
			"creatureweapon"=>$weapon,
			"creatureattack"=>round($session['user']['attack']*.9),
			"creaturedefense"=>round($session['user']['defense']*.9),
			"creaturehealth"=>round($session['user']['maxhitpoints']*.75),
			"diddamage"=>0,
			"type"=>"rollingbolder"
		);
		$session['user']['badguy']=createstring($badguy);
	}
	$op="fight";
}

if ($op=="fight"){ $battle=true; }
if ($battle){       
	include("battle.php");  
	if ($victory){
		if (get_module_pref("monsternum")==1){
			output("`n`^You look at `\$Barca`^ and tell him ");
			if (get_module_pref("cchicken")==0) output("`b`@'I am NOT interested today!'`^`b`n`n");
			else output("`b`@'I think I'm ready to explore that `%C`3ave`@.'`b`^`n`n");
			output("You leave him unconscious for the scavengers to pick through his belongings... but only after you've grabbed");
			if (get_module_pref("cchicken")==1) output("your map back and taken ");
			output("his `bgold`b.`n`n");
			output("You'll have to come back some other time to look for treasure, and maybe by then `\$Barca`^ will have a better attitude.`n`n");
			set_module_pref("cchicken",0);
			$expbonus=$session['user']['dragonkills']*2;
			$expgain =($session['user']['level']*e_rand(15,20)+$expbonus);
			$session['user']['experience']+=$expgain;
			output("`@You gain `#%s experience`@.`n",$expgain);
			$goldgain= e_rand(200,400);
			$session['user']['gold']+=$goldgain;
			output("`@You gain `^%s gold`@.`n`n",$goldgain);
			output("`c`\$`bTo Be Continued...`c`b`n`n`n");
			addnews("%s taught a thug posing as a forest guide a lesson.",$session['user']['name']);
			jonescave_leave();
			addnav("Back to the Forest","forest.php");
		}
		if (get_module_pref("monsternum")==2){
			output("`n`^The statue ends up at your feet in pieces.  You smile at your companions and thank them for their help.`n`n");
			$expbonus=$session['user']['dragonkills']*3;
			$expgain =($session['user']['level']*e_rand(17,22)+$expbonus);
			$session['user']['experience']+=$expgain;
			output("`@You gain `#%s experience`@.`n`n",$expgain);
			output("`^The eyes of the `&Statue`^ were made of `%gems`^.  You give one to your companions and keep one for yourself.`n`n");
			$session['user']['gems']++;
			addnews("%s defeated a `&statue`^ in the `2deep forest`^.",$session['user']['name']);
			output("You all rest and share a healing potion.  You feel like you may be ready to journey on!`n`n");
			output("Soon enough, you're at the entrance to the `%C`3ave`^ on the map. `n`n What is your next step?");
			$session['user']['hitpoints']=$session['user']['maxhitpoints'];
			addnav("Leave","runmodule.php?module=jonescave&op=leavecave");
			addnav("`%C`3ave `^Entrance","runmodule.php?module=jonescave&op=caveentrance");
			apply_buff('backup', array());
		}
		if (get_module_pref("monsternum")==3){
			output("`n`^A pile of dead spiders lie at your feet.");
			output("You notice `#Zatipolio`^ shivering in the corner in fear.");
			output("`n`nYou search through the debris of spider bodies and collect a bottle of the `@poisonous `\$venom`^.");
			output("It will sell for well over 1000 gold!`n`n");
			$expbonus=$session['user']['dragonkills']*5;
			$expgain =($session['user']['level']*e_rand(19,25)+$expbonus);
			$session['user']['experience']+=$expgain;
			output("`@You gain `#%s experience`@.`n`n",$expgain);
			output("`^You save the `\$venom`^ and eventually sell it for 1250 gold!`n`n");
			$session['user']['gold']+=1250;
			addnews("%s `^defeated a hoard of spiders in the `%C`3ave`^.",$session['user']['name']);
			output("You both share a healing potion, but it isn't able to heal you fully.");
			output("You feel like you may be ready to journey on!`n`n");
			output("Are you ready to resume your journey deeper into the `%C`3ave`^?");
			$healing=($session['user']['maxhitpoints']-$session['user']['hitpoints'])/2;
			$session['user']['hitpoints']+=$healing;
			addnav("Leave","runmodule.php?module=jonescave&op=leavecave2");
			addnav("Deeper into the `%C`3ave","runmodule.php?module=jonescave&op=ccavepit");
			apply_buff('spiderbites', array());
			apply_buff('zatipolio', array());
		}
		if (get_module_pref("monsternum")==4){
			output("`nThe little arrows stop hitting you and you count yourself lucky for surviving the onslaught.`n`n");
			$expbonus=$session['user']['dragonkills']*4;
			$expgain =($session['user']['level']*e_rand(20,26)+$expbonus);
			$session['user']['experience']+=$expgain;
			output("`@You gain `#%s experience`@.`n`n",$expgain);
			output("`^You meet up with `#Zatipolio`^ and plan to run out of the `%C`3ave`^!`n`n");
			output("A quick sip of your temporary healing potion gets your strength up a bit.");
			$healing=($session['user']['maxhitpoints']-$session['user']['hitpoints'])*.75;
			$session['user']['hitpoints']+=$healing;
			addnav("Try to get out of the `%C`3ave","runmodule.php?module=jonescave&op=ccavepit2");
		}
		if (get_module_pref("monsternum")==5){
			output("`n`^You dodge the boulder and burst through the end of the `%C`3ave`^.");
			output("The %s`^ is yours!!`n`n",$ctreasure[get_module_pref("treasurenum")]);
			$expbonus=$session['user']['dragonkills']*6;
			$expgain =($session['user']['level']*e_rand(22,27)+$expbonus);
			$session['user']['experience']+=$expgain;
			output("`@You gain `#%s experience`@.`n`n",$expgain);
			addnav("Finish the Adventure","runmodule.php?module=jonescave&op=cfinish");
		}
	}elseif($defeat){
		jonescave_leave();
		require_once("lib/taunt.php");
		$taunt = select_taunt_array();
		$session['user']['alive'] = false;
		$session['user']['hitpoints'] = 0;
		if (get_module_pref("monsternum")==1){	
			$exploss = round($session['user']['experience']*.03);
			output("`n`\$Barca`^ stands over your body laughing.");
			output("`n`n`5'Maybe next time you won't be afraid of a little adventure.'`n");
			output("`n`\$He steals your map and all your `^gold`\$!`n");
			output(" You lose `#%s experience`\$.`b`0`n",$exploss);
			output("`@`n`c`bYou may begin your adventures again tomorrow.`c`b");
			addnav("Daily news","news.php");
			$session['user']['experience']-=$exploss;
			$session['user']['gold']=0;
			set_module_pref("cchicken",1);
			$session['user']['specialinc']="";
			addnews("%s `@was defeated by a thug instead of going on an adventure.",$session['user']['name'],$taunt);
		}
		if (get_module_pref("monsternum")==2){
			$exploss = round($session['user']['experience']*.04);
			output("`^The statue crushes you!`n");
			output("`^All your gold is lost under the statue.`n");
			output(" `\$You lose `#%s experience`\$.`b`0`n",$exploss);
			output("`n`\$Barca`^ is able to survive and steals your map from you!`n");
			output("`@`n`c`bYou may begin your adventures again tomorrow.`c`b");
			addnav("Daily news","news.php");
			$session['user']['experience']-=$exploss;
			$session['user']['gold']=0;
			set_module_pref("cchicken",1);
			$session['user']['specialinc']="";
			apply_buff('backup', array());
			addnews("%s `^was defeated by a huge `&Statue`^ in the `@deep jungle`^.",$session['user']['name'],$taunt);
		}
		if (get_module_pref("monsternum")==3){
			$exploss = round($session['user']['experience']*.05);
			output("`^The `)H`\$uge `)S`\$pider`^ takes one finally bite and it's `\$fatal`^!`n");
			output("`n`^All your gold is lost in the cave, and `#Zatipolio`^ crawls away to hide in a local bar.`n");
			output(" `\$You lose `#%s experience`\$.`b`0`n",$exploss);
			output("`@`n`c`bYou may begin your adventures again tomorrow.`c`b");
			addnav("Daily news","news.php");
			$session['user']['experience']-=$exploss;
			$session['user']['gold']=0;
			set_module_pref("cchicken",2);
			$session['user']['specialinc']="";
			apply_buff('spiderbites', array());
			apply_buff('zatipolio', array());
			addnews("%s `^was defeated by a `)H`\$uge `)S`\$pider`^ in a secret `%C`3ave`^.",$session['user']['name'],$taunt);
		}
		if (get_module_pref("monsternum")==4){
			$exploss = round($session['user']['experience']*.07);
			output("`^One of the arrows hits you hard and it's `\$fatal`^!`n");
			output("`n`#Zatipolio`^ sees you fall and runs away!`n");
			output(" `\$You lose `#%s experience`\$.`b`0`n",$exploss);
			output("`@`n`c`bYou may begin your adventures again tomorrow.`c`b");
			addnav("Daily news","news.php");
			$session['user']['specialinc']="";
			$session['user']['experience']-=$exploss;
			$session['user']['gold']=0;
			set_module_pref("cchicken",3);
			addnews("%s `@was hit by an arrow and died in a secret `%C`3ave`^.",$session['user']['name']);
		}
		if (get_module_pref("monsternum")==5){
			set_module_pref("cavestage",0);
			$session['user']['specialinc']="";
			$exploss = round($session['user']['experience']*.1);
			output("`^The boulder rolls right over your body.");
			output("Oh well, that means you won't be able to find out what was so special about the %s`^.`n",$ctreasure[get_module_pref("treasurenum")]);
			output(" `\$You lose `#%s experience`\$.`b`0`n",$exploss);
			output("`@`n`c`bYou may begin your adventures again tomorrow.`c`b");
			addnav("Daily news","news.php");
			increment_module_pref("treasurenum",1);
			$session['user']['experience']-=$exploss;
			$session['user']['gold']=0;
			set_module_pref("cavestage",0);
			addnews("%s `@was run over by a huge boulder leaving the `%C`3ave`^ with an amazing artifact.  It is lost forever.",$session['user']['name']);
		}
	}else{
		require_once("lib/fightnav.php");
		fightnav(true,false,"runmodule.php?module=jonescave");
	}
}
if ($op=="hof"){
	require_once("modules/jonescave/jonescave_hof.php");
	jonescave_hof();
}
function jonescave_leave(){
	global $session;
	$session['user']['specialinc'] = "";
	$session['user']['weapon']=get_module_pref("tempweapon");
	$session['user']['armor']=get_module_pref("temparmor");
}
page_footer();
?>
