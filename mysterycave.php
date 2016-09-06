<?php
//A thanks to  Thricebornphoenix, for finding a few errors I overlooked, and helping me with Case 7.
//A thanks to Kalazaar for showing me how to make version 1.0 of this module.
function mysterycave_getmoduleinfo() {
	$info = array(
		"name"=>"Cave of Mystery",
		"author"=>"Akutozo, Kalazaar. tweaks by `%K`1alisiin",
		"version"=>"1.5",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1147",
		"category"=>"Forest Specials",
		"settings"=>array(
			"Cave of Mystery Settings,title",
			"name"=>"Name of person mooning,text|some guy",
			"addgems"=>"MAX Gems Given per DK of user,int|3",
			"goldinchest"=>"Gold Given,int|500",
			"lightningbuff1"=>"Buff 1 - Attack Buff,int|1.73",
			"lightningbuff2"=>"Buff 1 - Defence Buff,int|0.79",
			"hpgain"=>"Buff 2 - Regeneration Buff,int|3",
			"minmin"=>"Buff 3 - Minimum Minion Damage,int|1",
			"maxmin"=>"Buff 3 - Maximum Minion Damage,int|5",
			"nav1"=>"First Module,text|findgem",
			"nav2"=>"Second Module,text|stonehenge",
			"nav3"=>"Third Module,text|glowingstream",
			),
		"prefs"=>array(
			"today" => "Has the player seen this special today?, bool|0",
			),
		);
	return $info;
}
function mysterycave_chance() {
	global $session;
	if (get_module_pref('today','mysterycave',$session['user']['acctid'])==1) return 0;
	else return 100;
}
function mysterycave_install(){
	module_addeventhook("forest","require_once(\"modules/mysterycave.php\"); return mysterycave_chance();");
	module_addhook("newday");
	return true;
}
function mysterycave_uninstall() {
	return true;
}
function mysterycave_dohook($hookname, $args) {
	global $session;
	switch($hookname) {
		case "newday":
			set_module_pref("today",0);
		break;
	}
	return $args;
}
function mysterycave_runevent() {
	global $session;
	page_header("Cave of Mystery");
	$op=httpget('op');
	$session['user']['specialinc'] = "module:mysterycave";
	if ($op=="" || $op=="search") {
		set_module_pref("today",1);
		output("You come across a mysterious Cave. Curious, you wonder if you should look inside or go back to the forest.");
		addnav("Look","runmodule.php?module=mysterycave&op=look");
		addnav("Forest","forest.php");
		$session['user']['specialinc'] = "";
 	}
}
function mysterycave_run() {
	global $session;
	$op=httpget('op');
	page_header("Cave of Mystery");
	if($op=="look") {
		switch (e_rand(1,10)) {
			case 1:
			output("You are blinded by the bright light of a new day as you enter the cave. You stumble and fall like an idiot. Luckly, you still have the new day.");
			$session['user']['lasthit'] = "0000-00-00 00:00";
			$session['user']['restorepage'] = "forest.php";
			addnews(" %s found the fabled New Day Cave!",$session['user']['name']);
			break;

			case 2:
			$addgems = get_module_setting("addgems");
			output ("You are blinded by the bright light of a new day. You stumble and fall like an idiot, and notice some gems. You grab them greedily and lose the new day.");
			$max = $session['user']['dragonkills'] * $addgems;
			$gems = e_rand(1,$max);
			$session['user']['gems'] += $gems;
			break;

			case 3:
			output ("You see a dim light in the distance. As you walk, you realize you are in the fabled old day cave! You have lost all of your forest fights... Maybe you should go take a nap.");
			$session['user']['turns'] = 0;
			addnews(" %s was last seen sleeping in the forest like a baby.",$session['user']['name']);
			break;

			case 4:
			output ("You see a dark tunnel and stumble through it like an idiot. You get cut and bruises all over yourr body, leaving you with 1 HP and half your forest fights. You notice a hole in your purse. Your gold was lost as well!");
			$session['user']['hitpoints'] = 1;
			$session['user']['turns'] *= 0.5;
			$session['user']['gold'] = 0;
			break;

			case 5:
			$name = get_module_setting("name");
			output ("You look walk through the cave and see a bright new light. Thinking it's a new day, you run toward it, only to see %s mooning you. Disgusted, you walk away",$name);
			addnews(" %s was last seen being mooned in the forest.",$session['user']['name']);
			break;
			
			case 6:
			switch (e_rand(1,3)) {
			case 1:
			$lightningbuff1 = get_module_setting("lightningbuff1");
			$lightningbuff2 = get_module_setting("lightningbuff2");
			output ("`^You stumble out of the cave and look around you dizzily. Thunder clashes as dark clouds form in the sky above you, and moment later your body is struck by lightning. You feel a power within you.",$name);
			apply_buff('MB1',array(
			"name"=>"`@Mystery Buff",
			"rounds"=>8,
			"wearoff"=>"The mysterious Powers have worn off",
			"atkmod"=>$lightningbuff1,
			"defmod"=>$lightningbuff2,
			"schema"=>"module-mysterycave",
			)
		);
			break;
			case 2:
			$hpgain = get_module_setting("hpgain");
			output ("`^You stumble out of the cave and look around you dizzily. Thunder clashes as dark clouds form in the sky above you, and moment later your body is struck by lightning. You feel a power within you.",$name);
			apply_buff('MB2',array(
			"name"=>"`@Mystery Buff",
			"rounds"=>8,
			"wearoff"=>"The mysterious Powers have worn off",
			"regen"=>$hpgain,
			"schema"=>"module-mysterycave",
			)
		);
			break;
			case 3:
			$minmin = get_module_setting("minmin");
			$maxmin = get_module_setting("maxmin");
			output ("`^You stumble out of the cave and look around you dizzily. Thunder clashes as dark clouds form in the sky above you, and moment later your body is struck by lightning. You feel a power within you.",$name);
			apply_buff('MB3',array(
			"name"=>"`@Mystery Buff",
			"rounds"=>8,
			"wearoff"=>"The mysterious Powers have worn off",
			"minioncount"=>1,
			"minbadguydamage"=>$minmin,
			"minbadguydamage"=>$maxmin,
			"schema"=>"module-mysterycave",
			)
		);
			break;
			}
			break;
			case 7:
			switch (e_rand(1,3)) {
			case 1:
			$nav1 = get_module_setting("nav1");
			addnav("Bright Light", "forest.php?eventhandler=module-$nav1");
			output ("`^You stumble across the Cave, and see a Bright Light! Whatever will be behind it?");
			break;
			case 2:
			$nav2 = get_module_setting("nav2");
			addnav("Bright Light", "forest.php?eventhandler=module-$nav2");
			output ("`^You stumble across the Cave, and see a Bright Light! Whatever will be behind it?");
			break;
			case 3:
			$nav3 = get_module_setting("nav3");
			addnav("Bright Light", "forest.php?eventhandler=module-$nav3");
			output ("`^You stumble across the Cave, and see a Bright Light! Whatever will be behind it?");
			break;
		}
			break;
			case 8:
			$goldinchest = get_module_setting("goldinchest");
			$session['user']['gold'] +=$goldinchest;
			output ("As you wander through the cave, you discover a treasure chest!  There's `^gold `&in that that chest!!  Not taking the time to count it, you simply dump all the gold into your bags, and head out.");
			break;
			
			case 9:
			output ("You walk through the Cave, starting to wonder if you have wasted your time. After several more minutes, you notice a chest. Walking over, you open the chest and notice an assortment of scrolls! You read them and become more experienced!");
			$expgain = round($session['user']['experience']*0.05);
			$session['user']['experience'] +=$expgain;
			output("You have gained %s experience.",$expgain);
			break;
			
			case 10:
			output ("As you walk though th cave, your kicks something across the floor. Leaning down, you pick up what seems to be be a flask. Deciding you're thirsty and willing to take a risk, you down the drink! Your Hit Points are boosted!");
			$session['user']['hitpoints'] *=1.10;
			break;
			}
		addnav("Return to Forest", "forest.php");
		$session['user']['specialinc'] = "";
	}
	page_footer();
}

?>