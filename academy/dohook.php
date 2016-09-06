<?php
	$classarray = array(
		0=>"Squire",
		1=>"Knight",
		2=>"Warlord",
	);
	$classarray = translate_inline($classarray);
	$name = get_module_pref("name");
	if (get_module_pref("active")){
		$maxarray = array(
			0=>get_module_setting("knight"),
			1=>get_module_setting("warlord"),
			2=>get_module_setting("max")
		);
		$class = get_module_pref("class");
		if (get_module_setting("exp") != 0){
			$exp = 1-(get_module_setting("exp")/100);
		}else{
			$exp = 1;
		}
		$min = get_module_setting("mindmg");
		$max = get_module_setting("maxdmg");
		// Apply flux based on Squire's level and class
		$div = (get_module_setting("div")/100);
		$bk = (get_module_setting("boost-knight")/100);
		$bw = (get_module_setting("boost-warlord")/100);
		$minion = 1;
		$mindmg = $min+get_module_pref("level");
		switch (get_module_pref("class")){
			case 0:
				$maxdmg = $max+get_module_pref("level");
				break;
			case 1:
				$maxdmg = round($max+get_module_pref("level")*$bk);
				break;
			case 2:
				$maxdmg = round($max+get_module_pref("level")*$bw);
				$minion = 2; // Two Blades
				break;
		}
		$mindmg = round($mindmg*$div);
		if (get_module_setting("is-dmg")) $mindmg = 0;
		$maxdmg = round($maxdmg*$div);
		$minstat = $mindmg;
		$maxstat = $maxdmg;
		// Applying miss calculation
		if (e_rand(1,100) > get_module_pref("acc")){
			$mindmg = 0;
			$maxdmg = $max;
		}
	}
	switch ($hookname){
		case "footer-hof":
			if (get_module_setting("hof")){
				addnav("Civilian Rankings");
				addnav("Strongest Squires","runmodule.php?module=academy&op=hof");	
			}
			break;
		case "creatureencounter":
			$buffarray = array(0=>1.05,1=>$bk,2=>$bw);
			if (get_module_pref("active") && !get_module_pref("dead")){
				$args['creatureexp'] = round($args['creatureexp']*$exp,0);
				$args['creatureattack'] = round($args['creatureattack']*($buffarray[$class]));
				$args['creaturedefense'] = round($args['creaturedefense']*($buffarray[$class]));
				$args['creaturehealth'] = round($args['creaturehealth']*($buffarray[$class]));
			}
			break;
		case "newday":
			if (get_module_pref("active") && !get_module_pref("dead")){
				$name = get_module_pref("name");
				if (e_rand(1,100) <= get_module_pref("level")*5){
					apply_buff("academy", array(
						"name"=>sprintf_translate("%s's Melody",$name),
						"roundmsg"=>sprintf_translate("The tune of %s's `5melody empowers you.",$name),
						"rounds"=>e_rand(5,30),
						"atkmod"=>1.1,
						"defmod"=>1.1,
						"wearoff"=>sprintf_translate("The tune of %s's `5melody slowly fades amongst the wind...",$name),
						"schema"=>"module-academy",
						)
					);
				}
			}
			set_module_pref("tacc",0);
			break;
		case "battle":
			global $options;
			debug($options['type']);
			if ((!get_module_setting("training") && $options['type'] == 'train')
				|| (!get_module_setting("pvp") && $options['type'] == 'pvp')) break;
			if (has_buff("battle-academy")) break; 
			if (get_module_pref("active") && !get_module_pref("dead")){
				debuglog("Enemy approached and buff battle-academy is a go.", FALSE, FALSE, $options['type']);
				$name = get_module_pref("name");
				apply_buff("battle-academy", array(
					"name"=>$name,
					"startmsg"=>sprintf_translate("%s `6readies his weapon.",$name),
					"rounds"=>-1,
					"minioncount"=>$minion,
					"minbadguydamage"=>$mindmg,
					"maxbadguydamage"=>$maxdmg,
					"allowinpvp"=>get_module_setting("pvp"),
					"allowintrain"=>get_module_setting("training"),
					"effectmsg"=>sprintf_translate("`&%s `6strikes `\${badguy} `6for `^{damage}`6 damage!",$name),
				    "effectnodmgmsg"=>sprintf_translate("`\${badguy} `6narrowly dodges the weapon of `&%s",$name),
					"effectfailmsg"=>sprintf_translate("`\${badguy} `6narrowly dodges the weapon of `&%s",$name),
					"schema"=>"module-academy",
					)
				);
			}
			break;
		case "battle-victory":
			global $options;
			static $runonce = false;
			if ($runonce !== false) break;
			$runonce = true;
			strip_buff("battle-academy");
			break;
		// Sanity checks.
		case "shades":
		case "graveyard":
		case "footer-news":
			if (get_module_pref("active") && httpget('module') != "battlearena"){
				if (!get_module_pref("dead") && !$session['user']['alive']){
					output("`n`)Sadly, %s `)has lost his life, trying to defend your body.`n",$name);
					debuglog("'s Squire is dead.");
					set_module_pref("dead",1);
					debug("Squire died.");
				}
			}
			strip_buff("battle-academy");
			break;
		case "biostat":
			if (get_module_pref("active","academy",$args['acctid'])){
				$name = get_module_pref("name","academy",$args['acctid']);
				$class = get_module_pref("class","academy",$args['acctid']);
				debug($class);
				$level = get_module_pref("level","academy",$args['acctid']);
				$acc = get_module_pref("acc","academy",$args['acctid']) . "%";
				$ac = db_prefix("accounts"); $mu = db_prefix("module_userprefs");
				$sql = "SELECT a.value AS name, b.value AS class, (c.value+0) AS level, (d.value+0) AS acc, $ac.name AS fullname
						FROM $ac
						INNER JOIN $mu AS a ON $ac.acctid=a.userid
						INNER JOIN $mu AS b ON $ac.acctid=b.userid
						INNER JOIN $mu AS c ON $ac.acctid=c.userid
						INNER JOIN $mu AS d ON $ac.acctid=d.userid
						INNER JOIN $mu AS e ON $ac.acctid=e.userid
						WHERE (a.setting = 'name' AND a.modulename = 'academy')
						AND (b.setting = 'class' AND b.modulename = 'academy')
						AND (c.setting = 'level' AND c.modulename = 'academy')
						AND (d.setting = 'acc' AND d.modulename = 'academy')
						AND (e.setting = 'active' AND e.modulename = 'academy' AND e.value = '1')
						ORDER BY class DESC, level DESC, acc DESC";
				$result = db_query($sql);
				$num1 = db_num_rows($result);
				$yournumber = 0; $i = 1;
				while ($row = db_fetch_assoc($result)){
					$nn = db_fetch_assoc(db_query("SELECT acctid FROM $ac WHERE name = '{$row['fullname']}'"));
					if ($nn['acctid'] == $args['acctid']) $yournumber = $i;
					$i++;
				}
				$args['tablebiostat']['Companions/Items'][$classarray[$class].' Name'] = $name;
				$args['tablebiostat']['Companions/Items'][$classarray[$class].' Level'] = $level;
				$args['tablebiostat']['Companions/Items'][$classarray[$class].' Placement in HOF'] = $yournumber." of ".$num1;
			}
			break;
		case "dragonkill":
		case "training-victory":
			if (get_module_pref("active") && !get_module_pref("dead")){
				$name = get_module_pref("name");
				$levelsetting = get_module_setting("level");
				$levelpref = get_module_pref("level");
				$levelsincelevel = get_module_pref("lsl");
				if ($levelpref < $maxarray[$class]){
					if ($levelsincelevel != $levelsetting){
						output("`n`%%s `^is slowly becoming stronger!`n",$name);
						$levelsincelevel++;
						set_module_pref("lsl",$levelsincelevel);
					}else{
						output("`n`%%s `^should be able to level up now!`n",$name);
					}
				}else{
					output("`n%s has become the strongest he can be...`n",$name);
				}
			}
			break;			
		case "village":
			//if ($session['user']['location'] == get_module_setting("academyloc") || $session['user']['location'] == "Siochanta"){
				tlschema($args['schemas']['fightnav']);
				addnav($args['fightnav']);
				tlschema();
				addnav("`b`ES`b`i`eq`i`Tu`b`4i`b`\$r`i`pe`i `i`b`PA`i`b`pc`i`\$a`i`4d`Te`em`i`Ey`i ","runmodule.php?module=academy&op=enter");
			//}
			break;
		case "changesetting":
			if ($args['setting'] == "villagename"){
				if ($args['old'] == get_module_setting("academyloc")){
					set_module_setting("academyloc",$args['new']);
				}
			}
			break;
		case "charstats":
			if (get_module_pref("user_show") && get_module_pref("active","academy")){
				addcharstat("Squire Info");
				addcharstat("Squire Name",get_module_pref("name"));
				addcharstat("Squire Level",get_module_pref("level"));
				addcharstat("Squire Class",$classarray[(get_module_pref("class"))]);
				addcharstat("Squire Alive",get_module_pref("dead") ? "`4Dead" : "`^Yes");
				addcharstat("Ready to Level",get_module_pref("lsl") == get_module_setting("level") ? "`^Yes" : "`4No");
			}
			break;
		}
?>