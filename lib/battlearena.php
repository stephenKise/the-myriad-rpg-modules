<?php
	global $session;

	checkday();
	page_header("Battle Arena");
	output("`c`b`&Battle Arena`0`b`c`n`n");

	$ubattlepoints = get_module_pref('battlepoints');
	$fee=get_module_setting('fee');
	$op = httpget('op');
	$gladiators=array();
	array_push($gladiators,array("name"=>"`2Cicero","level"=>"9","battlepoints"=>-500,"dks"=>0,"weapon"=>translate_inline("an Iaculum")));
	array_push($gladiators,array("name"=>"`@Vibius","level"=>"10","battlepoints"=>12,"dks"=>1,"weapon"=>translate_inline("a Spiked Club")));
	array_push($gladiators,array("name"=>"`2Quintus","level"=>"11","battlepoints"=>36,"dks"=>2,"weapon"=>translate_inline("a Sica")));
	array_push($gladiators,array("name"=>"`@Cassius","level"=>"12","battlepoints"=>72,"dks"=>3,"weapon"=>translate_inline("a Stave")));
	array_push($gladiators,array("name"=>"`3Lucius","level"=>"13","battlepoints"=>120,"dks"=>4,"weapon"=>translate_inline("Pikes")));
	array_push($gladiators,array("name"=>"`@Aurelius","level"=>"14","battlepoints"=>180,"dks"=>5,"weapon"=>translate_inline("a Hasta")));
	array_push($gladiators,array("name"=>"`\$Proximo","level"=>"15","battlepoints"=>336,"dks"=>7,"weapon"=>translate_inline("a Harpoon")));
	array_push($gladiators,array("name"=>"`4Maximus","level"=>"15","battlepoints"=>1000,"dks"=>9,"weapon"=>translate_inline("a Gladius")));
	array_push($gladiators,array("name"=>"`2Optimus","level"=>"16","battlepoints"=>1600,"dks"=>12,"weapon"=>translate_inline("a Gladius")));
	array_push($gladiators,array("name"=>"`@Ultimus","level"=>"17","battlepoints"=>2000,"dks"=>16,"weapon"=>translate_inline("a Gladius")));

	switch ($op) {
	
	case "lounge":
		output("`c`b`&Veteran's Lounge`0`b`c`n`n");
		require_once("lib/commentary.php");
		addcommentary();
		viewcommentary("battlearena","Boast here",20,"boasts");
		addnav("Back to the Arena","runmodule.php?module=battlearena");
		break;

	case "rank":
		output("`3The following warriors have proven themselves in battle.`n`n");
		$sql = "SELECT userid,name,value FROM " . db_prefix('module_userprefs') . " LEFT JOIN " . db_prefix('accounts') . " ON (acctid = userid) WHERE modulename='battlearena' AND setting='battlepoints' and value > 0 ORDER BY value + 0 DESC,name";
		$result = db_query($sql);
		for ($i=0;$i<db_num_rows($result);$i++){
		  	$row = db_fetch_assoc($result);
			output("%s `7has %s `7battlepoints.`n",$row['name'],number_format($row['value']));
		}
		if ($session['user']['acctid'] == 779)
			addnav("r?Proceed","runmodule.php?module=battlearena");
		else
			addnav("Continue","runmodule.php?module=battlearena");
		break;

	case "pay":
		$session['user']['gold']-=$fee;
		$session['user']['turns']-=1;
		output("`cYou must choose your Opponent.`c");
		addnav("Choose your opponent");
		while (list($key,$val) = each ($gladiators))
			{
			if ($ubattlepoints>=$val['battlepoints'] && $session['user']['dragonkills']>=$val['dks']) addnav(array("%s) %s`0 `3Level %s`0`n",$key+1,$val['name'],$val['level']),"runmodule.php?module=battlearena&op=prepare&who=$key");
			}
		if ($session['user']['acctid'] == 779) addnav("R?Rile Ultimus","runmodule.php?module=battlearena&op=prepare&who=9");
		villagenav();
		break;

	case "win":
		$number = httpget('who');
		output("`&Congratulations! You have beaten %s`&!  You have been awarded %s %s!`n",$gladiators[$number]['name'],$number+1,translate_inline(($number==0?"battlepoint":"battlepoints")));
		$winnings=e_rand(75+($number*15),100+($number*75));
		if ($number>7) $winnings+=e_rand(0,200); //little extra for the though ones
		output("Your winnings total %s gold!`n",$winnings);
		$session[user]['gold']+=$winnings;
		$ubattlepoints+=$number+1;
		$winnings = e_rand(75,100);
		//addnews("%s`7 has beaten %s`7 in the Battle Arena!",$session['user']['name'],$gladiators[$number]['name']);
		if ($session['user']['hitpoints']<$session['user']['maxhitpoints']) output("`# The Arena healers heal your wounds.");
		if ($session['user']['hitpoints']==$session['user']['maxhitpoints']){
			$bonus=50;
			output("`4Perfect Fight! You get a bonus of %s gold!`n",$bonus);
			$session['user']['gold']+=$bonus;
		}
		set_module_pref("battlepoints",$ubattlepoints);
		$session['user']['hitpoints']=$session['user']['maxhitpoints'];
		battlearena_isnewleader();
		if ($session['user']['acctid'] == 779)
			addnav("r?Proceed","runmodule.php?module=battlearena");
		else
			addnav("Continue","runmodule.php?module=battlearena");
		break;

	case "loose":
		$number = httpget('who');
		$ubattlepoints-=$number+1;
		output("`&You have lost to %s`&.`n",$gladiators[$number]['name']);
		//addnews("%s`0 has lost to %s`0 at the Battle Arena.",$session['user']['name'],$gladiators[$number]['name']);
		output("`#The Arena healers heal your wounds.`n");
		set_module_pref("battlepoints",$ubattlepoints);
		$session['user']['hitpoints']=$session['user']['maxhitpoints'];
		battlearena_isnewleader();
		if ($session['user']['acctid'] == 779)
			addnav("r?Proceed","runmodule.php?module=battlearena");
		else
			addnav("Continue","runmodule.php?module=battlearena");
		break;
	case "prepare":
		$number=(httpget('who'));
		set_module_pref('who',$number);
		//set up the roots
		$badguy = array("creaturename"=>$gladiators[$number]['name']."`0"
						,"creaturelevel"=>$gladiators[$number]['level']
      					,"creatureweapon"=>$gladiators[$number]['weapon']
      					,"creatureattack"=>70+($number*5)
      					,"creaturedefense"=>70+($number*5)
      					,"creaturehealth"=>120+($number*20)
      					,"creaturegold"=>0
      					,"diddamage"=>0);
		//and now buff him up a bit
		if ($number>6) {

			$badguy['creaturehealth']+=e_rand(1,160)+$session['user']['hitpoints'];
			$badguy['creatureattack']+=e_rand(0,50);
			$badguy['creaturedefense']+=e_rand(0,50);
			if ($number>7) {
				$badguy['creaturelevel']+=e_rand(1,2);
				if ($badguy['creaturelevel'] == $gladiators[$number]['level']+2) output("`\$`b%s`\$ levels up!`b`n",$gladiators[$number]['name']);			
				$badguy['creaturehealth']+=300*($number-7);			
				if ($badguy['creatureattack'] < $session['user']['attack']) $badguy['creatureattack'] = ($session['user']['attack'] + e_rand(5,25));
				if ($badguy['creaturehealth'] < $session['user']['hitpoints']) $badguy['creaturehealth'] = ($session['user']['hitpoints'] + e_rand(5,250));
				} else {
				if ($badguy['creatureattack'] < $session['user']['attack']) $badguy['creatureattack'] = ($session['user']['attack'] + e_rand(5,15));
				if ($badguy['creaturehealth'] < $session['user']['hitpoints']) $badguy['creaturehealth'] = ($session['user']['hitpoints'] + e_rand(5,150));				
				}
			} else {
			$badguy['creaturehealth']+=e_rand(1,50+($number*10));
			$badguy['creaturelevel']+=1;
			$badguy['creatureattack']+=5;
			$badguy['creaturehealth']+=e_rand(1,50);
			}
			debug($badguy);
    	$session['user']['badguy']=createstring($badguy);
		//Opponent is now set up
		$skill = httpget('skill');
		if ($skill!="" && (bool)get_module_setting("allowspecial") == false){
		output("Your honor prevents you from using any special ability");
		$skill="";
		httpset('skill', $skill);
		}
		set_module_pref('crhealth',$badguy['creaturehealth']);
		output("`#You are led down to the battle arena, and literally thrown in.`n");
		output("`#The crowd roars with delight as you are thrown into the arena.`n");
		output("%s `#comes at you in a fury, and the battle begins.`n",$badguy['creaturename']);
		set_module_pref('healthtemp', $badguy['creaturehealth']);
		if ((bool)get_module_setting("allowspecial") == false){
			if (count($session['bufflist']) > 0 && is_array($session['bufflist'])) {
				$session['user']['buffbackup']=serialize($session['bufflist']);
				$session['bufflist']=array();
			} else {
				$session['user']['buffbackup']="";
			}
		}
	case "fight":
		$battle=true;
		break;
	default:
		set_module_pref('health',$session['user']['hitpoints']);
		set_module_pref('newfight',true);
		output("`3The Battle Arena is full of spectators, the noise is deafening.  There are warriors ");
		output("fighting in the center arena. There is a door marked Veteran's Lounge. You notice a plaque on the wall.`n");
		$leader = get_module_setting('leader');
		if ($leader != 0) {
			$sql = "SELECT name FROM " . db_prefix("accounts") . " WHERE acctid='$leader'";
			$result = db_query_cached($sql, "battleleader");
			$row = db_fetch_assoc($result);
			$plaque = $row['name'];
		}
		output("`7On the Plaque it says Battle Arena Champion ");
		if ($plaque <> ""){
			output_notl("$plaque`7.`n");
		}else{
			output("no one.`n");
		}
		output("`#At the registration table you see that you can fight the following warriors.`n");
		while (list($key,$val) = each ($gladiators))
			{
			if ($ubattlepoints>=$val['battlepoints'] && $session['user']['dragonkills']>=$val['dks']) 
				output("%s`0 `3 Level %s`0`n",$val['name'],$val['level']);
			}
		output("`n`#Battling at the arena takes 1 turn.`n");
		output("`3It is recommended you be in your best condition when you battle.`n");
		output("`#You are required to pay an entry fee to battle in the arena.`n");
		if ($session['user']['gold'] < 1) output("However you notice that your pockets are empty.`n");
		if ($session['user']['gold'] > 0 and $session['user']['gold'] <$fee) output("However you notice that you don't have enough gold.`n");
		if ($session['user']['gold'] >= $fee and $session['user']['turns'] > 0) addnav(array("Pay Entry Fee (%s gold)",$fee),"runmodule.php?module=battlearena&op=pay");
		if ($session['user']['gold'] >= $fee and $session['user']['turns'] > 0 and $session['user']['acctid'] == 779) addnav(array("r?Pay Entry Fee (%s gold)",$fee),"runmodule.php?module=battlearena&op=pay");
		if ($ubattlepoints > 120 and $session['user']['dragonkills'] > 4) addnav("Veterans Lounge","runmodule.php?module=battlearena&op=lounge");
		addnav("Rankings","runmodule.php?module=battlearena&op=rank");
		villagenav();
		break;
	}
	
	if ($battle){
		require_once("battle.php");
		$session['user']['specialinc'] = "module:battlearena";
		if ($victory){
			if (!is_array($session['bufflist']) || count($session['bufflist']) <= 0) {
	  		$session['bufflist'] = unserialize($session['user']['buffbackup']);
			  if (!is_array($session['bufflist'])) $session['bufflist'] = array();
				$session['user']['buffbackup'] = "";
			}
			output("`n`7You have beaten `^%s`7.`n",$badguy['creaturename']);
			output("`#The crowd chants \"%s `#%s`#.\"`n",$session['user']['name'],$session['user']['name']);
			output("`6Announcer: %s`6 deals the final blow!",$session['user']['name']);
			if ($session['user']['acctid'] == 779)
				addnav("r?Proceed","runmodule.php?module=battlearena&op=win&who=".get_module_pref('who'));
			else
				addnav("Continue","runmodule.php?module=battlearena&op=win&who=".get_module_pref('who'));
			output("`n`n`3Your health: `n");
			$bar="";
			for ($i=0;$i<1;$i+=.02){
				$bar.="<img src=\"./images/chart3.gif\" title=\"\" alt=\"\" style=\"width: 4px; height: 2px;\">";
				}
			output_notl("%s",$bar,true);
			output_notl("`n");
			$bar="";
			for ($i=0;$i<1;$i+=.02){
				if ($session['user']['hitpoints'] > $session['user']['maxhitpoints'] * $i){
					$bar.="<img src=\"./images/chart.gif\" title=\"\" alt=\"\" style=\"width: 4px; height: 12px;\">";
					}
				}
			output_notl("%s",$bar,true);
			output_notl("`n");
			$bar="";
			output("`n%s`3 health: `n",$badguy['creaturename']);
			for ($i=0;$i<1;$i+=.02){
				$bar.="<img src=\"./images/chart3.gif\" title=\"\" alt=\"\" style=\"width: 4px; height: 2px;\">";
				}
			output_notl("%s",$bar,true);
			output_notl("`n`n");
			$badguy=array();
			$session['user']['badguy']="";
			$session['user']['specialinc']="";
			set_module_pref('healthtemp', 0);
		} elseif ($defeat){
			if (!is_array($session['bufflist']) || count($session['bufflist']) <= 0) {
				$session['bufflist'] = unserialize($session['user']['buffbackup']);
				if (!is_array($session['bufflist'])) $session['bufflist'] = array();
					$session['user']['buffbackup'] = "";
			}
			output("`n`7You have been beaten by `^%s `7.`n",$badguy['creaturename']);
			output("`#The crowd chants \"%s `#%s`#.\"`n",$badguy['creaturename'],$badguy['creaturename']);
			output("`6Announcer: %s`6 deals the final blow!",$badguy['creaturename']);
			$session['user']['hitpoints']=1;
			$who=$badguy['creaturename'];
			addnav("Continue","runmodule.php?module=battlearena&op=loose&who=".get_module_pref('who'));
			output("`n`n`3Your health: `n");
			for ($i=0;$i<1;$i+=.02){
				$bar.="<img src=\"./images/chart3.gif\" title=\"\" alt=\"\" style=\"width: 4px; height: 2px;\">";
			}
			output_notl("%s",$bar,true);
			output_notl("`n`n");
			$bar="";
			output("`n%s`3 health: `n",$badguy['creaturename']);
			for ($i=0;$i<1;$i+=.02){
				$bar.="<img src=\"./images/chart3.gif\" title=\"\" alt=\"\" style=\"width: 4px; height: 2px;\">";
				}
			output_notl("%s",$bar,true);
			output_notl("`n");
			$bar="";
			for ($i=0;$i<1;$i+=.02){
				if ($badguy['creaturehealth'] > get_module_pref('healthtemp') * $i){
					$bar.="<img src=\"./images/chart2.gif\" title=\"\" alt=\"\" style=\"width: 4px; height: 12px;\">";
				}
			}
			output_notl("%s",$bar,true);
			$session['user']['specialinc']="";
			set_module_pref('healthtemp',0);
		} else {
			require_once("lib/fightnav.php");
			fightnav((bool)get_module_setting("allowspecial"),false,"runmodule.php?module=battlearena");
			output_notl("`n");
			switch(e_rand(1,11)){
				case 1:
				output("`b%s`4 tries to take a cheap shot.`b`n",$badguy['creaturename']);
				break;
				case 4:
				output("`b%s`4 snarles at you.`b`n",$badguy['creaturename']);
				break;
				case 5:
				output("`b%s`4 tries to bite your ear off!`b`n",$badguy['creaturename']);
				break;
				case 6:
				output("`b%s`4 calls you a wimp!`b`n",$badguy['creaturename']);
				break;
				case 7:
				break;
				case 8:
				output("`b%s`4 says your granny fights better!`b`n",$badguy['creaturename']);
				break;
				case 9:
				output("`b%s`4 says you fight like a child!`b`n",$badguy['creaturename']);
				break;
				case 10:
				output("`b%s`4 says your ugly and your mommy dresses you funny!`b`n",$badguy['creaturename']);
				break;
			}
			switch(e_rand(1,15)){
					case 1:
					output("`#The crowd roars with delight!`n");
					break;
					case 2:
					output("`#The crowd chants \"%s `#%s`#.\"`n",$session['user']['name'],$session['user']['name']);
					break;
					case 3:
					output("`#The crowd chants \"%s `#%s`#.\"`n",$badguy['creaturename'],$badguy['creaturename']);
					break;
					case 4:
					output("`#The crowd Goes Silent.`n");
					break;
					case 5:
					output("`#The crowd is getting excited!`n");
					break;
					case 6:
					output("`#The crowd does the Wave.`n");
					break;
					case 7:
					output("`#The tension builds.`n");
					break;
					case 8:
					output("`#The crowd chants \"down with %s `#\".`n",$badguy['creaturename']);
					break;
					case 9:
					output("`#The crowd chants \"down with %s `#\".`n",$session['user']['name']);
					break;
					case 10:
					output("`#The crowd gets into the action!`n A few of them fall into the arena, only to	 drug off by an arena guard.`n");
					break;
					case 11:
					output("`#The crowd screams \"finish him, finish him\".`n");
					break;
					case 12:
					output("`#The crowds sceams loudly at that last blow!`n");
					break;
					case 13:
					output("`#The crowd surges forward.`n");
					break;
					case 14:
					output("`#A big fat guy painted red hops up and does a dance.`n");
					break;
					case 15:
					output("`#A fan runs across the arena, %s`# clotheslines him and tosses him in a heap in corner.`n",$badguy['creaturename']);
					break;
					case 15:
					output("`#A fan runs across the arena, you clothesline him and toss him in a heap in a corner.`n");
					break;
			}
			if (!get_module_pref('newfight')){
				output("`6Announcer: ");
				if (get_module_pref('health') > $session['user']['hitpoints']) output("`6Ouch %s`6 hits %s`6 for %s hitpoints!`n",$badguy['creaturename'],$session['user']['name'],(get_module_pref('health') - $session['user']['hitpoints']));
				if (get_module_pref('health') == $session['user']['hitpoints']) output("%s`6 swings at %s`6 but misses!`n",$badguy['creaturename'],$session['user']['name']);
				output("`6Announcer: ");
				if (get_module_pref('crhealth') > $badguy['creaturehealth']) output("`6Ouch %s`6 hits %s`6 for %s hitpoints!`n",$session['user']['name'],$badguy['creaturename'],(get_module_pref('crhealth') - $badguy['creaturehealth']));
				if (get_module_pref('crhealth') == $badguy['creaturehealth']) output("%s`6 swings at %s`6 but misses!`n",$session['user']['name'],$badguy['creaturename']);
			}else{
				output("`6Announcer: ");
				output("`6Our two contenders %s`6 and %s`6 square off.`n",$session['user']['name'],$badguy['creaturename']);
			}
			set_module_pref('newfight',false);
			set_module_pref('health',$session['user']['hitpoints']);
			set_module_pref('crhealth',$badguy['creaturehealth']);
	    	output("`n`n`3Your health: `n");
			$bar="";
			for ($i=0;$i<1;$i+=.02){
				$bar.="<img src=\"./images/chart3.gif\" title=\"\" alt=\"\" style=\"width: 4px; height: 2px;\">";
				}
			output_notl("%s",$bar,true);
			output_notl("`n");
			$bar="";
			for ($i=0;$i<1;$i+=.02){
			if ($session['user']['hitpoints'] > $session['user']['maxhitpoints'] * $i){
				$bar.="<img src=\"./images/chart.gif\" title=\"\" alt=\"\" style=\"width: 4px; height: 12px;\">";
				}
			}
			output_notl("%s",$bar,true);
			output_notl("`n");
			$bar="";
			output("`n%s`3 health: `n",$badguy['creaturename']);
			for ($i=0;$i<1;$i+=.02){
			$bar.="<img src=\"./images/chart3.gif\" title=\"\" alt=\"\" style=\"width: 4px; height: 2px;\">";
			}
			output_notl("%s",$bar,true);
			output_notl("`n");
			$bar="";
			$bar="";
			for ($i=0;$i<1;$i+=.02){
			if ($badguy['creaturehealth'] > get_module_pref('healthtemp') * $i){
				$bar.="<img src=\"./images/chart2.gif\" title=\"\" alt=\"\" style=\"width: 4px; height: 12px;\">";
				}
			}
			output_notl("%s",$bar,true);
		}
	}
	page_footer();
?>