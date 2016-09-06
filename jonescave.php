<?php
/*
Module Name:  Secret Cave
Category:  Forest Specials
Worktitle:  jonescave
Author:  DaveS
Date: October 10, 2005

Description:
Multi-visit module based on the beginning adventure by a famous archaeologist using a whip and wearing
a fedora going into a cave and grabbing an artifact.

This module will take at least 3 separate encounters to complete, and has 10 different artifacts to retrieve.
Stage 1:  Hire 2 helpers and get to the cave entrance
Stage 2:  Fight Spiders and swing across a pit
Stage 3:  Grab the artifact, dodge arrows, jump the pit, and dodge a huge rolling boulder.
It finishes with an award and a chance to leave a commentary.

v1.01 Parrots or parots? Yeah, I fixed the typo.  Thanks Dager!
v1.1 fixed a specialinc error and a typo, prefs clarified
v3.0 added vertxtloc
v3.11 Changed module to a jonescave folder and did a lot of debugging
v3.15 Translation improved, added Bio Title and Museum of Artifacts
v3.16 Minimum level and dk settings added
v3.17 Settings for all rewards and debugged even more.
v3.19 HoF fixes by Arieswind
v3.2  Some tweaks and fixes
*/
function jonescave_getmoduleinfo(){
	$info = array(
		"name"=>"Secret Cave",
		"version"=>"3.22",
		"author"=>"DaveS",
		"category"=>"Forest Specials",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=183",
		"vertxtloc"=>"",
		"description"=>"Multi-visit trip to retrieve an artifact from a cave.",
		"settings"=>array(
			"Secret Cave Settings, title",
			"minlevel"=>"Minimum level before encountering the Cave?,int|0",
			"mindk"=>"Minimum number of dks before encountering the Cave?,int|0",
			"jonesbookbio"=>"Show list of artifacts obtained in Museum of Artifacts under Bio?,bool|1",
			"jonesbio"=>"Show title for finding all the artifacts in Bio?,bool|1",
			"hof"=>"Use HoF?,bool|0",
			"pp"=>"Number of players to show per page on the HoF?,int|25",
			"First Item,title",
			"You may customize the names but try to keep the item similar: Rewards are tied to the item,note",
			"cfirstt"=>"`!First Treasure to be found:,text|`@A`4ncient `@Q`4uill `@o`4f `@E`4vad",
			"`!Try to make the 1st item a writing instrument,note",
			"firstgold"=>"`!Gold gained for the 1st item?,int|800",
			"firstgem"=>"`!Gem gained for the 1st item?,int|3",
			"Second Item,title",
			"csecondt"=>"`1Second Treasure to be found:,text|`&N`)armyan's `&S`)undial",
			"`1Try to make the 2nd item a time piece of some type,note",
			"secondturns"=>"`1Turns gained for the 2nd item?,int|5",
			"Third Item,title",
			"cthirdt"=>"`@Third Treasure to be found:,text|`%Amleine's Lost Flute of the Ancients",
			"`@Try to make the 3rd item an instrument,note",
			"thirdattack"=>"`@Attack points gained for 3rd item?,int|2",
			"thirdgold"=>"`@Gold gained for 3rd item?,int|900",
			"Fourth Item,title",
			"cfourtht"=>"`2Fourth Treasure to be found:,text|`\$Uraal's `^Helm",
			"`2Try to make the 4th item a piece of armor,note",
			"fourthdefense"=>"`2Defense gained for 4th item?,int|2",
			"fourthcharm"=>"`2Charm lost for 4th item?,int|1",
			"Fifth Item,title",
			"cfiftht"=>"`3Fifth Treasure to be found:,text|`\$A`7bacus `\$o`7f `\$S`7h`&y`\$l`7l`&e`^",
			"`3Try to make the 5th item something that counts gold,note",
			"fifthgold"=>"`3How much gold is lost because of this item?,enum,10,10%,25,25%,75,75%,95,95%,100,100%|75",
			"Sixth Item,title",
			"csixtht"=>"`#Sixth Treasure to be found:,text|`&Sacred Parchment of Dr. Mit",
			"`#Try to make the 6th item something that has writing on it,note",
			"sixthgold"=>"`#Gold gained for 6th item?,int|2000",
			"Seventh Item,title",
			"cseventht"=>"`4Seventh Treasure to be found:,text|`b`^Gold Coins`b of `@H`7annos`^",
			"`4Try to make the 7th item something that can be dropped and lost,note",
			"Eighth Item,title",
			"ceigtht"=>"`\$Eighth Treasure to be found:,text|`@Salve of Hara",
			"giveperm"=>"`\$If Allowed: Number of Permanent hitpoints gained for 8th item?,int|5",
			"eighthhps"=>"`\$If not: How many temp hitpoints are gained?,int|50",
			"eighthgold"=>"`\$Gold gained for 8th item?,int|1000",
			"Nineth Item,title",
			"cninetht"=>"`5Nineth Treasure to be found:,text|`0C`0rystal `7of `&Lakinne",
			"`5Try to make the 9th item something that could be a good-luck charm,note",
			"ninethcharm"=>"`5Charm gained for 9th item?,int|2",
			"ninethturns"=>"`5Turns gained for 9th item?,int|2",
			"ninethgold"=>"`5Gold gained for 9th item?,int|2000",
			"Tenth Item,title",
			"ctentht"=>"`%Tenth Treasure to be found:,text|`2Magic Beans of Giantdom",
			"`%Try to make the 10th item something that could be eaten,note",
			"tenthdefense"=>"`%Defense gained for 10th item?,int|1",
			"tenthattack"=>"`%Attack gained for 10th item?,int|1",
			"tenthgold"=>"`%Gold gained for 10th item?,int|2500",
		),
		"prefs"=>array(
			"Secret Jones Cave Preferences,title",
			"cavetried"=>"Been to the cave in the forest this newday?,bool|0",
			"cavestage"=>"What Stage of the event are they on?,enum,0,First,1,Second,2,Third|0",
			"tempweapon"=>"What weapon do they normally have?,text|0",
			"temparmor"=>"what armor do they normally have?,text|0",
			"monsternum"=>"What number monster are they fighting?,range,1,5,1|1",
			"treasurenum"=>"What number treasure are they seeking?,range,0,10,1|0",
			"treasurehof"=>"Total number of treasures found,int|0",
			"cchicken"=>"When did the player turn chicken?,enum,0,Never,1,First Stage,2,Second Stage,3,Third Stage|0",
			"artfinder"=>"How many times have they found ALL artifacts?,int|0",
		),
	);
	return $info;
}
function jonescave_chance() {
	global $session;
	if (get_module_pref('cavetried','jonescave',$session['user']['acctid'])==1 || $session['user']['level']<get_module_setting("minlevel",'jonescave') || $session['user']['dragonkills']<get_module_setting("mindk",'jonescave')) return 0;
	else return 100;
}
function jonescave_install(){
	module_addeventhook("forest","require_once(\"modules/jonescave.php\"); return jonescave_chance();");
	module_addhook("newday");
	module_addhook("footer-hof");
	module_addhook("footer-prefs");
	module_addhook("bioinfo");
	return true;
}
function jonescave_uninstall(){
	return true;
}
function jonescave_dohook($hookname,$args){
	global $session;
	global $session, $REQUEST_URI;
	$userid = $session['user']['acctid'];
	$argsid = $args['acctid'];
	$argsname = $args['login'];
	switch ($hookname) {
		case "footer-hof":
		if (get_module_setting("hof")==1){
			addnav("Warrior Rankings");
			addnav("Famous Archaeologists","runmodule.php?module=jonescave&op=hof");
		}
		break;
		case "newday":
			set_module_pref("cavetried",0);
		break;
		case "bioinfo":
		if (get_module_setting("jonesbookbio")==1){	
		if ((get_module_pref("treasurenum","jonescave",$argsid)>=1)||(get_module_pref("artfinder","jonescave",$argsid)>=1)) 
			addnav("Museum of Artifacts", "runmodule.php?module=jonescave&op=artmuseum&user=$argsid&username=$argsname&return=".URLencode($_SERVER['REQUEST_URI']));
		}
		if (get_module_setting("jonesbio")==1){
			$artfinder=get_module_pref("artfinder","jonescave",$argsid);
			if ($artfinder>0) {
				output("`^Archaeological Title: ");
				if ($artfinder>9) output("`5`bWorld Renowned Archaeologist`b");
				elseif ($artfinder==9) output("`6Dean of Archaeology");
				elseif ($artfinder==8) output("`QDirector of Archaeology");
				elseif ($artfinder==7) output("`&Senior Professor of Archaeology");
				elseif ($artfinder==6) output("`\$Professor of Archaeology");
				elseif ($artfinder==5) output("`%Associate Professor of Archaeology");
				elseif ($artfinder==4) output("`!Assistant Professor of Archaeology");
				elseif ($artfinder==3) output("`#Teaching Assistant of Archaeology");
				elseif ($artfinder==2) output("`@Graduate Student of Archaeology");
				elseif ($artfinder==1) output("`^Student of Archaeology");
				output_notl("`n");
			}
		}
		break;
	}
	return $args;
}
function jonescave_runevent($type) {
	global $session;
	$session['user']['specialinc']="module:jonescave";
	$op = httpget('op');
	$ctreasure=array(get_module_setting("cfirstt"),get_module_setting("csecondt"),get_module_setting("cthirdt"),get_module_setting("cfourtht"),get_module_setting("cfiftht"),get_module_setting("csixtht"),get_module_setting("cseventht"),get_module_setting("ceigtht"),get_module_setting("cninetht"),get_module_setting("ctentht"));
	addnav("What do you do?");
	output_notl("`n");
	set_module_pref("tempweapon",$session['user']['weapon']);
	set_module_pref("temparmor",$session['user']['armor']);
	if (get_module_pref("cchicken")==1){
		set_module_pref("monsternum",1);
		output("`^You think about going on an adventure to that `%S`3ecret `%C`3ave`^, but you forget that `\$Barca`^ stole it last time you met.");
		output("`n`nYou track down the old scoundrel and demand that he give you your map back.`n`n");
		addnav("Fight with `\$Barca`^","runmodule.php?module=jonescave&op=attack");
	}elseif (get_module_pref("cchicken")==2){
		addnav("Return to the Forest","runmodule.php?module=jonescave&op=cforest");
		output("`#Zatipolio`^ has gotten weary of your silly games.");
		output("If you don't want to give him");
		if ($session['user']['gold']>=1000){
			output("1000 gold, he will not help you and you won't be able to make the journey.");
			output("`n`nWill you pay him?");
			addnav("Give him the `^Gold","runmodule.php?module=jonescave&op=cpayoffgold");
		}elseif ($session['user']['gems']>=2){
			output("`%2 gems`^, he will not help you and you won't be able to make the journey.");
			output("`n`nWill you pay him?");
			addnav("Give him the `%Gems","runmodule.php?module=jonescave&op=cpayoffgems");
		}else{
			output("`&Three of your charm points`^.");
			output("He explains that he has a special potion that will take three of your charm and give it to his... errr... somewhat lacking appearance.");
			addnav("Give him Charm","runmodule.php?module=jonescave&op=cpayoffcharm");
		}	
	}elseif (get_module_pref("cchicken")==3){
		output("`#Zatipolio`^ taps his foot and looks at you anxiously.");
		output("`n`n`#'I'm not going to make a big deal about you turning tail and running last time.");
		output("Let's just get going back to the `%C`3ave`#, okay?'`n`n");
		set_module_pref("cchicken",0);
		$session['user']['weapon']="`QWhip";
		$session['user']['armor']="`qFedora";
		addnav("Adventure Awaits","runmodule.php?module=jonescave&op=ccave3");		
	}else{
		if (get_module_pref("cavestage")==0) {
			output("`^On your journeys you find a map to a `%S`3ecret `%C`3ave`^ where treasures untold lie!`n`n");
			output("Since you know this will be a treacherous journey, you get `2two helpful guides`^ to travel with you.");
			output("`n`n The first is a bold fellow named `\$Barca`^.");
			output("He doesn't seem very trustworthy at all, but his knowledge of the area is unparalleled.");
			output("`n`n The second is named `#Zatipolio`^.");
			output("He isn't one of the smartest companions you've ever traveled with, but he is a good worker and is helping carry a lot of your supplies.");
			output("His anxiety about this adventure is readily apparent.`n`n");
			addnav("Leave","runmodule.php?module=jonescave&op=leavecave");
			addnav("Yes! Adventure Awaits","runmodule.php?module=jonescave&op=cjourney");
		}elseif (get_module_pref("cavestage")==1){
			output("`^You decide it's time to return to the `%C`3ave`^ and see if you can figure out where the %s`^ really is.",$ctreasure[get_module_pref("treasurenum")]);
			output("After getting `#Zatipolio`^ from the local bar, you set out for some more adventure.`n`n");
			addnav("Leave","runmodule.php?module=jonescave&op=leavecave2");
			addnav("Yes! Adventure Awaits","runmodule.php?module=jonescave&op=ccave2");
		}elseif (get_module_pref("cavestage")==2){
			output("`^You recall that your trip to through the `%C`3ave`^ has been pretty rough so far.");
			output("You've been betrayed by `\$Barca`^, had to fight scary `)S`\$piders`^, and you set a `QWhip`^ up across the perilous pit.");
			output("Luckily, you know where to get another `QWhip`^!`n`nOnce again, you go to the local bar and find `#Zatipolio`^.");
			output("He looks excited to continue the journey!`n`n");
			addnav("Leave","runmodule.php?module=jonescave&op=leavecave3");
			addnav("Yes! Adventure Awaits","runmodule.php?module=jonescave&op=ccave3");
		}
		output("`^In preparation, you decide to store your `#%s`^ and your `#%s`^ for later.",$session['user']['weapon'],$session['user']['armor']);
		output("Instead, you'll use your trusty `QWhip`^ and `qFedora`^ for protection.`n`n");
		output("Are you ready to seek the %s`^?",$ctreasure[get_module_pref("treasurenum")]);
		$session['user']['weapon']="`QWhip";
		$session['user']['armor']="`qFedora";
	}
}
function jonescave_run(){
	include("modules/jonescave/jonescave.php");
}
?>