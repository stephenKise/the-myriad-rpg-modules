
<?php
global $session;
page_header("Forbidden Vault");
$op=httpget('op');
$u=&$session['user'];
$which=httpget('p');
$clanid=$u['clanid'];
$userid=$u['acctid'];
$square=get_module_pref("square");
//output($square);
$owned1=get_module_setting("owned1");
$owned2=get_module_setting("owned2");
$owned3=get_module_setting("owned3");
//time settings, if they're killed.
$time=date("Y-m-d H:i:s",strtotime("-600 seconds"));
$timeold = get_module_pref("time","clanpyramid",$userid);
if ($timeold>$time && $op<>"enter"){
	output("You have been killed.  This has cost you 1 Guild point for your Guild`n");
	$cw=get_module_objpref("clans",$clanid,"clanwins","clanpyramid")-1;
	if ($cw<=0){
		$cw=0;
	}
	set_module_objpref("clans",$clanid,"clanwins",$cw,"clanpyramid");
	villagenav();
}else{
	if ($square==900){
		clanpyramid_take();
	}/*elseif($square==0){
		output("You have been killed and must now leave the pyramid");
		villagenav;
		page_footer();
	}*/
	clear_module_pref("time");
	if ($square<0){
		hidden_passage();
	}
	if ($op=="enter"){
	page_header("The Entrance");
	clear_module_pref("time");
	clear_module_pref("defender");
	if ($clanid==0){
		output("Sorry, you must be a member of a Guild to explore the Vault");
		output_notl("`n");
	}else{
		output("You duck through the entrance to the Forbidden Vault, only to find there also appears to be two smaller vaults, alongside it.  Some strange inscriptions appear next to the entrance. `b`%egg`b");//, which of the three will you end up in?");
		switch (e_rand(1,3)){
			case 1:
			addnav("Vault of Rapacity","runmodule.php?module=clanpyramid&op=one");
			addnav("Vault of Cupidity","runmodule.php?module=clanpyramid&op=two");
			break;
			case 2:
			addnav("Vault of Rapacity","runmodule.php?module=clanpyramid&op=one");
			addnav("Vault of Avarice","runmodule.php?module=clanpyramid&op=three");
			break;
			case 3:
			addnav("Vault of Cupidity","runmodule.php?module=clanpyramid&op=two");
			addnav("Vault of Avarice","runmodule.php?module=clanpyramid&op=three");
			break;
		}
	}
	villagenav();
	page_footer();
}
if ($op=="one"){
	page_header("Vault of Avrice");
	output("Pulling back the vine enshrouded cover stone to the vaults entrance, you step inside, it takes your eye's a moment to adjust to the dim interior.  You realise in this gloom you can only see a few paces in front and behind you, you wish you had some form of illumination, but it appears this is not to be.");
	output_notl("`n`n");
	if ($owned1==$clanid){
		output("You may enter and defend");
		addnav("Enter Vault","runmodule.php?module=clanpyramid&op=defend&p=1");
	}elseif ($owned1<>$clanid){
		output("You have two choices, explore or leave.");
		addnav("What Will You Do?");
		addnav("Explore","runmodule.php?module=clanpyramid&op=move&move=entry1&p=1");
	}
	set_module_pref("square",901);
	if (get_module_pref("user_see")==1){
		output_notl("`n`n");
		rawoutput("<IMG SRC=\"modules/clanpyramid/images/Map_pyramid1.gif\"><BR>\n");
	}
	addnav("Warriors in Vault","runmodule.php?module=clanpyramid&op=listwarriors&p=1");
	villagenav();
	page_footer();
}
if ($op=="listwarriors"){
	clanpyramid_warriorslist();
	if ($square==901){
		addnav("Back to Vault","runmodule.php?module=clanpyramid&op=one");
	}elseif ($square==222){
		addnav("Back to Vault","runmodule.php?module=clanpyramid&op=move&move=return&wall=222&p=1");
	}elseif($square==1300){
		addnav("Back to Vault","runmodule.php?module=clanpyramid&op=two");
	}elseif($square==1095){
		addnav("Back to Vault","runmodule.php?module=clanpyramid&op=move&move=return&wall=1095&p=2");
	}elseif($square==2157){
		addnav("Back to Vault","runmodule.php?module=clanpyramid&op=move&move=return&wall=2157&p=3");
	}elseif($square=="P3"){
		addnav("Back to Vault","runmodule.php?module=clanpyramid&op=move&move=entry&p=3");
	}elseif($square=="3001"){
		addnav("Back to Vault","runmodule.php?module=clanpyramid&op=three&p=3");
	}
}
if ($op=="two"){
	set_module_pref("square",1300);
	if ($owned2==$clanid){
		output("You may enter and defend");
		addnav("Enter Vault","runmodule.php?module=clanpyramid&op=defend&p=2");
	}elseif ($owned2<>$clanid){
		output("Stepping through the gateway, you are confronted with a set of 4 passageways, 2 of which are blocked.	Which entry will you choose?");
		addnav("What Will You Do?");
		$rand=(e_rand(1,6));
		switch ($rand){
			case 1:
			addnav("Passage 1","runmodule.php?module=clanpyramid&op=move&move=passage1&p=2");
			addnav("Passage 2","runmodule.php?module=clanpyramid&op=move&move=passage2&p=2");
			break;
			case 2:
			addnav("Passage 3","runmodule.php?module=clanpyramid&op=move&move=passage3&p=2");
			addnav("Passage 4","runmodule.php?module=clanpyramid&op=move&move=passage4&p=2");
			break;
			case 3:
			addnav("Passage 1","runmodule.php?module=clanpyramid&op=move&move=passage1&p=2");
			addnav("Passage 3","runmodule.php?module=clanpyramid&op=move&move=passage3&p=2");
			break;
			case 4:
			addnav("Passage 2","runmodule.php?module=clanpyramid&op=move&move=passage2&p=2");
			addnav("Passage 4","runmodule.php?module=clanpyramid&op=move&move=passage4&p=2");
			break;
			case 5:
			addnav("Passage 2","runmodule.php?module=clanpyramid&op=move&move=passage2&p=2");
			addnav("Passage 3","runmodule.php?module=clanpyramid&op=move&move=passage3&p=2");
			break;
			case 6:
			addnav("Passage 1","runmodule.php?module=clanpyramid&op=move&move=passage1&p=2");
			addnav("Passage 4","runmodule.php?module=clanpyramid&op=move&move=passage4&p=2");
			break;
		}
	}
	addnav("Warriors List","runmodule.php?module=clanpyramid&op=listwarriors&p=2");
	villagenav();
}
if ($op=="three"){
	if ($owned3==$clanid){
		output("You may enter and defend");
		addnav("Enter Vault","runmodule.php?module=clanpyramid&op=defend&p=3");
		set_module_pref("square","3001");
		addnav("Warriors List","runmodule.php?module=clanpyramid&op=listwarriors&p=3");
	}elseif ($owned3<>$clanid){
		output("You enter a shadowy realm, a dappled light, causes the shadows to dance, giving them a ghostly lifelike appearance, you shiver slightly and unsheathing your weapon continue on towards the entrance you can see in the distance.");
		output_notl("`n`n");
		output("Following a heavily overgrown pathway, you are suddenly confronted with a group of temple guards......");
		output_notl("`n`n");
		output("Before you have time to wonder what they are doing this far from the Vault itself, they are upon you");
		addnav("What Will You Do?");
		set_module_pref("square","3000");
		addnav("Fight","runmodule.php?module=clanpyramid&op=attack&f=1&p=3");
	}
	villagenav();
}
if ($op=="defend"){
	require_once("modules/clanpyramid/defend_func.php");
}
if ($op=="move"){
	if ($which==1){
		require_once("modules/clanpyramid/move_func.php");
	}elseif($which==2){
		require_once("modules/clanpyramid/move2_func.php");
	}elseif($which==3){
		require_once("modules/clanpyramid/move3_func.php");
	}
}
if ($op=="wall"){
	if ($which==1){
		require_once("modules/clanpyramid/walls_func.php");
		walls_hit();
	}elseif($which==2){
		require_once("modules/clanpyramid/walls2_func.php");
		walls2_hit();
	}elseif($which==3){
		require_once("modules/clanpyramid/walls3_func.php");
		walls3_hit();
	}
}
if ($op=="transport"){
	require_once("modules/clanpyramid/pyramid3.php");
	pyramid3_transport();
}
if ($op=="warriors"){
	$p=httpget('p');
	warriors_list($p);
}
if ($op=="warriorattack"){
	$p=httpget('p');
	warrior_attack($p);
}
if ($op=="giveup"){
	page_header("Leave the Vault");
	output("You have chosen to leave, you will not be able to return to any vault for 10 minutes");
	addnav("Leave","village.php?");
	$timenow = date("Y-m-d H:i:s");
	set_module_pref("time",$timenow);
	page_footer();
}
if ($op=="attack"){
	$fightnum=httpget('f');
	$wall=httpget('wall');
	$p=httpget('p');
	if (is_module_active("clanwarvault")){
		$clanatk = get_module_objpref("clans", $clanid, "att","clanwarvault");
		$clandef = get_module_objpref("clans", $clanid, "def","clanwarvault");
	}else{
		$clanatk = 500;
		$clandef = 500;
	}
	if ($fightnum==1){
		$level = $u['level']+1;
		$dk = round($u['dragonkills']*.1);
		$minion=$clanatk*0.1;
		$badguy = array(
			"creaturename"=>"`tAngry `eTemple `tGuards",
			"creaturelevel"=>$level,
			"creatureweapon"=>"`TStaffs and `ERods",
			"creatureattack"=>round($u['attack']),
			"creaturedefense"=>round($u['defense'])-1,
			"creaturehealth"=>round($u['maxhitpoints']*1.65),
			"diddamage"=>0,
			"type"=>"Guards");
			apply_buff('clanstrength', array(
			"startmsg"=>"`^Your Guild strength comes to your aid!`n",
			"name"=>"`4Guild Attack",
			"rounds"=>20,
			"wearoff"=>"The Strength Fades.",
			"minioncount"=>$minion,
			"minbadguydamage"=>0,
			"maxbadguydamage"=>$clanatk,
			"effectmsg"=>"`RThe Strength of Your Guild empowers you. You inflict {damage} damage",
			"effectnodmgmsg"=>"`VYour attack is blocked.",
			"effectfailmsg"=>"`RYou grow weary.",
			));
		$u['badguy']=createstring($badguy);
	}elseif ($fightnum==2){
		$level = $u['level']+1;
		$dk = round($u['dragonkills']*.1);
		if ($dk==0){
			$dk=1;
		}
		$minion=$clanatk*0.1;
		$rand = e_rand(1,4);
		switch ($rand){
			case 1:
			$creaturename = "`!Anubis`0";
			$creatureweapon = "`1Deaths Glare`0";
			break;
			case 2:
			$creaturename = "`RAm-Heh`0";
			$creatureweapon="`4Lake of Fire`0";
			break;
			case 3:
			$creaturename = "`vDenwen`0";
			$creatureweapon = "`4Fiery Destruction`0";
			break;
			case 4:
			$creaturename = "`)Kek and Kauket`0";
			$creatureweapon="`)Cloak of Darkness`0";
			 break;
		}
		$badguy = array(
		"creaturename"=>$creaturename,
		"creaturelevel"=>$level,
		"creatureweapon"=>$creatureweapon,
		"creatureattack"=>round($u['attack']),
		"creaturedefense"=>round($u['defense'])-1,
		"creaturehealth"=>round($u['maxhitpoints']*2),
		"diddamage"=>0,
		"type"=>"Level Guardian");
		apply_buff('clanstrength', array(
		"startmsg"=>"`^Your Guild strength comes to your aid!`n",
		"name"=>"`4Guild Attack",
		"rounds"=>20,
		"wearoff"=>"The Strength Fades.",
		"minioncount"=>$minion,
		"minbadguydamage"=>0,
		"maxbadguydamage"=>$clanatk,
		"effectmsg"=>"`RThe Strength of Your Guild empowers you. You inflict {damage} damage",
		"effectnodmgmsg"=>"`jYour attack is blocked.",
		"effectfailmsg"=>"`RYou grow weary.",
		));
		$u['badguy']=createstring($badguy);
	}elseif ($fightnum==3){
		$level = $u['level']+3;
		$dk = round($u['dragonkills']*.1);
		$minion=$clanatk*0.1;
		$badguy = array(
			"creaturename"=>"`$ Nehebkau",
			"creaturelevel"=>$level,
			"creatureweapon"=>"`TStaffs and `ERods",
			"creatureattack"=>round($u['attack']),
			"creaturedefense"=>round($u['defense'])-1,
			"creaturehealth"=>round($u['maxhitpoints']*2.5),
			"diddamage"=>0,
			"type"=>"Throne Guardian");
			apply_buff('clanstrength', array(
			"startmsg"=>"`^Your Guild strength comes to your aid!`n",
			"name"=>"`4Guild Attack",
			"rounds"=>20,
			"wearoff"=>"The Strength Fades.",
			"minioncount"=>$minion,
			"minbadguydamage"=>0,
			"maxbadguydamage"=>$clanatk,
			"effectmsg"=>"`RThe Strength of Your Guild empowers you. You inflict {damage} damage",
			"effectnodmgmsg"=>"`jYour attack is blocked.",
			"effectfailmsg"=>"`RYou grow weary.",
			));
		$u['badguy']=createstring($badguy);
	}
	$op="fight";
	httpset("op", "fight");
	if ($wall==""){
		$wall=get_module_pref("square");
	}
	$store['p']=$p;
	$store['w']=$wall;
	$store['f']=$fightnum;
	$u['specialmisc']=serialize($store);
}
if ($op=="fight"){ $battle=true; }
	if ($battle){
		$store = unserialize($u['specialmisc']);
		$p=$store['p'];
		$wall=$store['w'];
		$fightnum=$store['f'];
		include("battle.php");
		if ($victory){
			//output("`bFightnum: $fightnum wall: $wall pyramid: $p`b");
			if ($fightnum==1){
				output_notl("`n");
				output("`^You're surrounded by the dead bodies of many Guards.");
				$expbonus=$u['dragonkills']*4;
				if ($expbonus>600){
					$expbonus=600;
				}
				$expgain =($u['level']*e_rand(5,15)+$expbonus);
				$u['experience']+=$expgain;
				output("`@You gain `#%s experience`@.",$expgain);
				output_notl("`n`n");
				output("You continue on further down the path, knowing you shall reach the Vault soon.  Also knowing, you have reached the point of no return, after this there is no turning back.");
				addnav("Continue On");
				set_module_pref("square","P3");
				addnav("Vault Entrance","runmodule.php?module=clanpyramid&op=move&move=entry&p=3");
				addnav("Warriors List","runmodule.php?module=clanpyramid&op=listwarriors&p=3");
				villagenav();
			}elseif ($fightnum==2){
				output_notl("`n");
				output("The Guardian Falls at your feet, stepping swiftly over the body you move on");
				addnav("Continue","runmodule.php?module=clanpyramid&op=move&move=return&wall=$wall&p=$p");
				$expbonus=$u['dragonkills']*4;
				if ($expbonus>400){
					$expbonus=400;
				}
				$expgain =($u['level']*e_rand(5,15)+$expbonus);
				$u['experience']+=$expgain;
			}elseif ($fightnum==3){
				output_notl("`n");
				output("The Great God Nehebkau falls at your feet.");
				addnav("Continue","runmodule.php?module=clanpyramid&op=move&move=thronec&p=$p");
				$expbonus=$u['dragonkills']*4;
				if ($expbonus>400){
					$expbonus=400;
				}
				$expgain =($u['level']*e_rand(5,15)+$expbonus);
				$u['experience']+=$expgain;
			}
			output("`@You gain `#%s experience`@.",$expgain);
			output_notl("`n`n");
			strip_buff('clanstrength');
			blocknav("runmodule.php?module=clanpyramid&op=wall&hit=south");
			blocknav("runmodule.php?module=clanpyramid&op=move&move=south");
			blocknav("runmodule.php?module=clanpyramid&op=move&move=east");
			blocknav("runmodule.php?module=clanpyramid&op=wall&hit=east");
			blocknav("runmodule.php?module=clanpyramid&op=move&move=north");
			blocknav("runmodule.php?module=clanpyramid&op=wall&hit=north");
			blocknav("runmodule.php?module=clanpyramid&op=wall&hit=west");
			blocknav("runmodule.php?module=clanpyramid&op=move&move=west");
			$u['specialmisc']="";
		}elseif($defeat){
			if ($fightnum==1){
				output("You can feel the guards pick your lifeless body up and toss you bodily back through the entranceway");
				$exploss = round($u['experience']*.05);
		 		$u['experience']-=$exploss;
				output("You have lost %s experience",$exploss);
				output("You start to come too a bit more, until your body hits the hard surface of the village square");
				addnews("`GThe body of %s `Gwas seen being thrown from a Vaults Entrance.",$u['name']);
				$time = date("Y-m-d H:i:s");
			}elseif ($fightnum==2){
				$u['hitpoints'] = 1;
				output("You can feel the guardian throw you bodily back from the Vault");
				$exploss = round($u['experience']*.05);
				$u['experience']-=$exploss;
				output("You have lost %s experience",$exploss);
				output("You start to come too a bit more, until your body hits the hard surface of the village square");
				addnews("%s `Gwas thrown bodily from a Vault.",$u['name']);
			}elseif ($fightnum==3){
				$u['hitpoints'] = 1;
				output("You can feel the God Nehebkau throw you bodily out of the Vault");
				$exploss = round($u['experience']*.05);
				$u['experience']-=$exploss;
				output("You have lost %s experience",$exploss);
				output("You start to come too a bit more, until your body hits the hard surface of the village square");
				addnews("%s `Gwas hurled from a Vault by a God.",$u['name']);
			}
			$u['hitpoints']=1;
			$time = date("Y-m-d H:i:s");
			set_module_pref("time",$time);
			$u['specialmisc']="";
			villagenav();
			strip_buff('clanstrength');
		}else{
			$store = unserialize($u['specialmisc']);
			$p=$store['p'];
			$wall=$store['w'];
			$fightnum=$store['f'];
			require_once("lib/fightnav.php");
			
			//output("`bFightnum: $fightnum wall: $wall pyramid: $p`b");
			fightnav(true,false,"runmodule.php?module=clanpyramid");
			blocknav("runmodule.php?module=clanpyramid&op=wall&hit=south");
			blocknav("runmodule.php?module=clanpyramid&op=move&move=south");
			blocknav("runmodule.php?module=clanpyramid&op=move&move=east");
			blocknav("runmodule.php?module=clanpyramid&op=wall&hit=east");
			blocknav("runmodule.php?module=clanpyramid&op=move&move=north");
			blocknav("runmodule.php?module=clanpyramid&op=wall&hit=north");
			blocknav("runmodule.php?module=clanpyramid&op=wall&hit=west");
			blocknav("runmodule.php?module=clanpyramid&op=move&move=west");
		}
	}
}
if (get_module_pref("user_see")==0){
	if ($which==1){
		output_notl("`n`n");
		rawoutput("<IMG SRC=\"modules/clanpyramid/images/Map_pyramid1.gif\"><BR>\n");
	}elseif ($which==2){
		map_pyramid2($squarenew);
	}elseif ($which==3){
		output_notl("`n`n");
		rawoutput("<IMG SRC=\"modules/clanpyramid/images/Map_pyramid3.gif\"><BR>\n");
	}
}
page_footer();
?>
