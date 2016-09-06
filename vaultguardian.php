<?php
/*
Details:
 * This is a module that allows users to break into the vaults of others
 * Created by Damien
 * Version 1.2 for LotGD 1.1.0
 * Requires the latest version of the Clan Vault module
 * Modified by:
 	o CortalUX
 +-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-+
 History Log:
 	* v1.0:
 		o Module released
 	* v1.1 (CortalUX):
 		o Allowed users to search with partial names in the dark horse, instead of having to enter the name
 		o Admin option for gold to actually disappear from the vault of another
 		o Fixed a bug with gold and gems
 		o Fixed a bug with commentary
 		o Uses Lonnyl's system
 		o Fixed a bug with urlencode()
 		o Made lists visually nicer
 		o As of 1.95 of the clan vault it refers to other clans, so you now break into anothers vault from your own
	* v1.2 (Fieser-Kardinal):
 		o Fixed some Settings so that this great Module works under 1.1.0!	

*/
require_once("lib/villagenav.php");
require_once("lib/fightnav.php");
require_once("lib/http.php");
require_once("lib/e_rand.php");

function vaultguardian_getmoduleinfo(){
	$info = array(
		"name"=>"Vault Guardian",
		"author"=>"`^Damien`3, modifications by `@CortalUX, `#fixed by Fieser-Kardinal",
		"version"=>"1.2",
		"category"=>"Clan",
		"download"=>"http://dragonprime.net/users/Fieser-Kardinal/vaultguardian.zip",
		"vertxtloc"=>"http://dragonprime.net/users/Fieser-Kardinal/",
		"settings"=>array(
			"Vault Guardian - General Settings,title",
			"sorcerername"=>"What is the name of the sorcerer?,text|Venom",
			"summonpricegold"=>"How much gold does the summoning of the guardian cost?,int|20000",
			"summonpricegems"=>"How many gems does the summoning of the guardian cost?,int|20",
			"upgradeweapongold"=>"How much gold does the guardian's weapon upgrade cost?,int|5000",
			"upgradeweapongems"=>"How many gems does the guardian's weapon upgrade cost?,int|5",
			"upgradearmorgold"=>"How much gold does the guardian's armor upgrade cost?,int|5000",
			"upgradearmorgems"=>"How many gems does the guardian's armor upgrade cost?,int|5",
			"pricetokeepguardianalive"=>"How much does the clan have to pay per day to the sorcerer to keep the guardian alive?,int|1000",
			"infocost"=>"How much gold does information cost about other clans in the Darkhorse Tavern?,int|100",
			"brSteal"=>"Does breaking into a vault actually steal the gold or gems?,bool|1",
			"Vault Guardian - Weapon Settings,title",
			"weapon1"=>"What is the name of Weapon 1?,text|Venomous Spear",
			"weapon2"=>"What is the name of Weapon 2?,text|Sword of Underworld",
			"weapon3"=>"What is the name of Weapon 3?,text|Demonic spells",
			"weapon4"=>"What is the name of Weapon 4?,text|Spell of Twisted Mind",
			"weapon5"=>"What is the name of Weapon 5?,text|Tears of Poison",
			"weapon6"=>"What is the name of Weapon 6?,text|Blood Napalm",
			"weapon7"=>"What is the name of Weapon 7?,text|Soul of Betrayed Ruler",
			"weapon8"=>"What is the name of Weapon 8?,text|Claws of Demonic Dragon",
			"weapon9"=>"What is the name of Weapon 9?,text|Cruelty of the Beast",
			"weapon10"=>"What is the name of Weapon 10?,text|Spiritual Black Dimension",
			"Vault Guardian - Armor Settings,title",
			"armor1"=>"What is the name of Armor 1?,text|Blood Armor",
			"armor2"=>"What is the name of Armor 2?,text|Power of unvisibility",
			"armor3"=>"What is the name of Armor 3?,text|Human Shield",
			"armor4"=>"What is the name of Armor 4?,text|Spell of Diversion",
			"armor5"=>"What is the name of Armor 5?,text|Gravity moving",
			"armor6"=>"What is the name of Armor 6?,text|Ring of Fire",
			"armor7"=>"What is the name of Armor 7?,text|Shield of Lightning",
			"armor8"=>"What is the name of Armor 8?,text|Army of Darkness",
			"armor9"=>"What is the name of Armor 9?,text|Masochist Mind",
			"armor10"=>"What is the name of Armor 10?,text|Powers of Immortals",
		),
		"prefs-clans"=>array(
			"Vault Guardian - Clan Preferences,title",
			"hasguardian"=>"Has this clan bought a guardian?,bool|0",
			"guardianname"=>"What is the name of their guardian?,text|Finarfin Carnesîr",
			"guardianarmorlevel"=>"What is the Guardian's armor level?,int|1",
			"guardianweaponlevel"=>"What is the Guardian's weapon level?,int|1",
		),
		"prefs"=>array(
			"Vault Guardian - User preferences,title",
			"hasattacked"=>"Has this player broken into a vault?,bool|0",
		),
		"requires"=>array(
			"clanvault"=>"1.99| http://dragonprime.net/users/CortalUX/clanvault.zip",
		),
	);
	return $info;
}

function vaultguardian_install(){
	if (!is_module_installed('vaultguardian')){
		output("`n`c`b`QVault Guardian Module - Installed`0`b`c");
	}else{
		output("`n`c`b`QVault Guardian Module - Updated`0`b`c");
	}
	module_addhook("footer-runmodule");
	module_addhook("footer-forest");
	module_addhook("header-forest");
	module_addhook("newday-runonce");
	module_addhook("newday");
	return true;
}

function vaultguardian_uninstall(){
	output("`n`c`b`QVault Guardian Module - Uninstalled`0`b`c");
	return true;
}

function vaultguardian_dohook($hookname, $args){
	global $session;
	switch($hookname){
	
	case "footer-runmodule":
		if (httpget('module')=='clanvault') {
			vaultguardian_navs();
		}
	break;	
	case "newday-runonce":
		if(get_module_objpref("clans", $session['user']['clanid'], "hasguardian", "vaultguardian")==1){
			$clangold = get_module_objpref("clans", $session['user']['clanid'], "vaultgold", "clanvault");
			$price = get_module_objpref("clans", $session['user']['clanid'], "pricetokeepguardianalive", "vaultguardian");
			$armorlevel = get_module_objpref("clans", $session['user']['clanid'], "guardianarmorlevel", "vaultguardian");
			$weaponlevel = get_module_objpref("clans", $session['user']['clanid'], "guardianweaponrlevel", "vaultguardian");
			$sorcerer = get_module_setting("sorcerername");
			if ($clangold >= $price){ //payment for the sorcerer
				set_module_objpref("clans", $session['user']['clanid'], "vaultgold",$clangold-$price,"clanvault");
				output("The Dark Sorcerer %s seems to be satisfied with his and your clan's guardian deal...`n", $sorcerer);
			} else{ //if we can't pay
				if($armorlevel > 1){ //if guardian has better than level 1 armor we'll take random number of levels down
					set_module_objpref("clans", $session['user']['clanid'], "guardianarmorlevel", $armorlevel-e_rand(1, $armorlevel)+1, "vaultguardian");
					output("Your clan's guardian is somehow weaker than before...`n");
				} else{ //if armor is level 1 we'll remove whole guardian
					set_module_objpref("clans", $session['user']['clanid'], "hasguardian", 0, "vaultguardian");
					output("Your clan is no longer protected by the summoned demon...`n");
				}
				if($weaponlevel > 1){ //if guardian has better than level 1 weapon we'll take random number of levels down
					set_module_objpref("clans", $session['user']['clanid'], "guardianweaponlevel", $weaponlevel-e_rand(1, $weaponlevel)+1, "vaultguardian");
					output("Your clan's guardian is somehow weaker than before...`n");
				} else { //if weapon is level 1 we'll remove whole guardian 
					set_module_objpref("clans", $session['user']['clanid'], "hasguardian", 0, "vaultguardian");
					output("Your clan is no longer protected by the summoned demon...`n");
				}					
				$sql = "SELECT clanname FROM ".db_prefix("clans")." WHERE clanid=".$session['user']['clanid']."";
				$result = db_query($sql);
				$row = db_fetch_assoc($result);
				addnews("`@The Dark Sorcerer `^%s`@ is angry with `^%s`@.", $sorcerer, $row['clanname']);
				$msg = "::".translate_inline("found out that there wasn't enough gold in the vault to pay to the sorcerer for the guardian...");
				injectcommentary("clan-".$session['user']['clanid'], "", $msg, false);
			}
		}
	break;
	case "newday":
		set_module_pref("hasattacked", 0); //player can attack a clan once per day
	break;
	case "header-forest":
		if(is_module_installed("darkhorse")){
			if(httpget("op")=="bartender" and httpget("what")==""){
				addnav("Ask about other Clans","forest.php?op=bartender&what=clans&action=form");
			}
		}
	break;
	case "footer-forest": //this adds option into bartender part
		if(is_module_installed("darkhorse")){
			if(httpget("op")=="bartender" and httpget("what")=="clans"){
				$action = httpget("action");
				switch($action){
						case "form":
							output("\"Clansh, who caresh aboot clansh?! Well I might hav shom informathion, jusht gimme %s gold after yeh telll me the name of the clann...\"`n`n", get_module_setting("infocost"));
							$text = translate_inline("Search");
							rawoutput("<form action='forest.php?op=bartender&what=clans&action=result' method='POST'>");
							addnav("","forest.php?op=bartender&what=clans&action=result");
							rawoutput("<input type=text name='name'>");
							rawoutput("<input type='submit' class='button' value='$text'></form>");
						break;
						case "result":
							$name = httppost("name");
							$string="%";
							for ($x=0;$x<strlen($name);$x++){
								$string .= substr($name,$x,1)."%";
							}
							$sql = "SELECT clanid,clanname FROM ".db_prefix("clans")." WHERE clanname LIKE '%".str_replace("'","\'",$string)."%'";
							$result = db_query($sql);
							if (httpget('stage')=='') {
								if (db_num_rows($result)>0) {
									rawoutput("<table style='width:80%;' align='center' border='0' padding='1'><tr style='text-align:center;' class='trhead'><td>");
									output("`&Name (click on one!)");
									rawoutput("</td></tr>");
									$i=0;
									while ($row = db_fetch_assoc($result)) {
										$i++;
										rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td style='text-align:left;'>");
										rawoutput("<a href='forest.php?op=bartender&what=clans&action=result&stage=1&id=".$row['clanid']."' class='colLtGreen'>".$row['clanname']."</a>");
										addnav("","forest.php?op=bartender&what=clans&action=result&stage=1&id=".$row['clanid']);
										rawoutput("</td></tr>");
									}
									rawoutput("</table>");
									output("`n`0\"Shorry, which one didja want?\"");
								} else {
									output("\"Shorry, I can't find a clan whith thatsh nash!\"");
								}
							} else {
								$id=httpget('id');
								if($session['user']['gold'] >= get_module_setting("infocost")){
									output("\"Okay, herre we go, here'sh all I know about that clan.\"`n`n");
									output("Gold in their vault: %s`n", get_module_objpref("clans", $id, "vaultgold", "clanvault"));
									if(get_module_objpref("clans", $id, "vaultgems", "clanvault")!="") output("Gems in their vault: %s`n", get_module_objpref("clans", $id, "vaultgems", "clanvault"));
									output("Vault guarded: ");
									if(get_module_objpref("clans", $id, "hasguardian", "vaultguardian")==1) output("Yes`n");
									else output("No`n");
									$session['user']['gold']-=get_module_setting("infocost");
								} else{
									output("\"Oye! I told you I need %s gold for theshe informations!\"",get_module_setting("infocost"));
								}
							}
						break;			
					}
				}
			}
		break;
	}
	return $args;
}

function vaultguardian_run(){
	global $session;
	$op = httpget('op');
	if($op=="") $op = "clans";
	switch ($op) {
		case "enter":
			page_header(array("The Dark Sorcerer %s", get_module_setting("sorcerername")));
			if(get_module_objpref("clans", $session['user']['clanid'], "hasguardian", "vaultguardian")==0){
				output("`7Behind the clan hall a path leads to a cottage of powerful sorcerer `#%s`7.`n", get_module_setting("sorcerername"));
				output("`7You walk the path and start feeling restless. Your mind feels fuzzy and you pass out.`n`n");
				output("`7Sudddenly you wake up floating in the air. vicious looking figure stares at you with his `$ red `7eyes.`n");
				output("`Q\"Why an immortal like you want to disturb me, Superior %s `Q?\", `7the figure asks with mighty voice`n", get_module_setting("sorcerername"));
				addnav("Ask about the guardian","runmodule.php?module=vaultguardian&op=asksummoning");
				vaultguardian_navs();
			} else{
				output("The Dark Sorcerer %s `7looks at you, `Q\"I know you, We have a little arrangement with your clan\", `7he laughs.`n`n", get_module_setting("sorcerername"));
				output("`Q\"Is there something complaining about the Demon?\", `7he asks and you understand he's talking about the vault guardian of your clan`n`n");
				addnav("Guardian's weapon","runmodule.php?module=vaultguardian&op=weapon&action=verify");
				addnav("Guardian's armor","runmodule.php?module=vaultguardian&op=armor&action=verify");
				addnav("Cancel the deal","runmodule.php?module=vaultguardian&op=cancel&action=verify");
				vaultguardian_navs();
			}
		break;
		case "asksummoning":
			page_header(array("The Dark Sorcerer %s", get_module_setting("sorcerername")));
			output("`Q\"You want me to summon a guardian to your clan?\"`7, the sorcerer keeps talking with his godlike voice.`n`n");
			output("`Q\"Very well, if you understand what you're asking, it can be arranged, but...\"`7, he stops suddenly.`n`n");
			output("`7After a few seconds silence he continues, `Q\"Your clan has to pay for my services, `^%s gold `Qper day\"`7.`n`n", get_module_setting("pricetokeepguardianalive"));			
			output("`Q\"Otherwise I can't promise what the Demon does...\"`7, he grins.`n`n");
			output("`Q\"I also need `^%s gold `Qand `@%s gems `Qto summon the guardian. Nothing can be done with plain air\"`7, the sorcerer sits down and waits your reaction.", get_module_setting("summonpricegold"), get_module_setting("summonpricegems"));
			addnav("Summon the guardian","runmodule.php?module=vaultguardian&op=guardianname");
			vaultguardian_navs();
		break;
		case "guardianname":
			page_header(array("The Dark Sorcerer %s", get_module_setting("sorcerername")));
			if ($session['user']['gold']>=get_module_setting("summonpricegold") and $session['user']['gems']>=get_module_setting("summonpricegems")) {
				output("`Q\"What is the name that the guardian has to obey?\"`7, the sorcerer asks before starting the summoning.`n");
				addnav("Run away","clan.php");
				rawoutput("<form action='runmodule.php?module=vaultguardian&op=summon' method='POST'>");
				addnav("","runmodule.php?module=vaultguardian&op=summon");
				rawoutput("<input type='text' name='name' size='25'>");
				$text = translate_inline("Continue");
				rawoutput("<input type='submit' class='button' value='$text'></form>");
			} else{
				output("`Q\"So you want everything for free? Feel free to take journey to the shades!\"`7, the sorcerer puts deadly spell on you and you fall down.`n`n");
				$session['user']['hitpoints']=0;
				addnav("Land of shades", "shades.php");
			}
		break;		
		case "summon":
			page_header(array("The Dark Sorcerer %s", get_module_setting("sorcerername")));
			$name = httppost("name");
			if($name == "")  $name = "`#Finarfin Carnesîr";
			else $name.=" `7The Vault Guardian";
			output("`7%s starts the summoning of the demon.`n`n", get_module_setting("sorcerername"));
			output("`7Weird voices are whispering something that you don't understand and thick fog appears around the sorcerer.");
			output("`7After a few minutes earth shaking the fog moves away and reveals a huge dragonlike demon.`n`n");
			output("`7%s introduces your clan's new guardian, %s.", get_module_setting("sorcerername"), $name);			
			$session['user']['gold']-=get_module_setting("summonpricegold");
			$session['user']['gems']-=get_module_setting("summonpricegems");
			set_module_objpref("clans", $session['user']['clanid'], "guardianweaponlevel", 1, "vaultguardian");
			set_module_objpref("clans", $session['user']['clanid'], "guardianarmorlevel", 1, "vaultguardian");
			$sql = "SELECT clanname FROM ".db_prefix("clans")." WHERE clanid=".$session['user']['clanid']."";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			addnews("Something weird has been seen near the clan %s's clan hall", $row['clanname']);
			$msg = sprintf("::hired a guardian to keep the clan savings safe.");
			//injectcommentary("clan-".$session['user']['clanid'], "", $msg, false);			
			set_module_objpref("clans", $session['user']['clanid'], "hasguardian", 1, "vaultguardian");
			set_module_objpref("clans", $session['user']['clanid'], "guardianname", $name, "vaultguardian");			
			vaultguardian_navs();
		break;
		case "weapon":
			page_header(array("The Dark Sorcerer %s", get_module_setting("sorcerername")));
			$action = httpget("action");
			$weaponlevel = get_module_objpref("clans", $session['user']['clanid'], "guardianweaponlevel", "vaultguardian");
			$goldcost = get_module_setting("upgradeweapongold")*($weaponlevel+1);
			$gemscost = get_module_setting("upgradeweapongems")*($weaponlevel+1);
			switch($action){
				case "verify":
					output("`Q\"Oh you think your demon isn't strong enough?!\"`7, the almighty wizard asks with a bit angry voice.`n`n");
					output("`Q\"Just hand me `^%s gold `Qand `@%s gems `Qand I'll boost the demon's strenght!\"`7, he shouts.`n`n", $goldcost, $gemscost);
					rawoutput("<form action='runmodule.php?module=vaultguardian&op=weapon&action=upgrade' method='POST'>");
					addnav("","runmodule.php?module=vaultguardian&op=weapon&action=upgrade");
					$text = translate_inline("Upgrade powers");
					rawoutput("<input type='submit' class='button' value='$text'></form>");
					addnav("More upgrades","runmodule.php?module=vaultguardian&op=enter");
					vaultguardian_navs();
				break;
				case "upgrade":
					if($session['user']['gold']>=$goldcost and $session['user']['gems']>=$gemscost){
						if($weaponlevel < 10){
							$session['user']['gold']-=$goldcost;
							$session['user']['gems']-=$gemscost;
							set_module_objpref("clans", $session['user']['clanid'], "guardianweaponlevel", $weaponlevel+1, "vaultguardian");	
							output("`7You watch while the sorcerer does his tricks and after couple of minutes he says, `Q\"This creature is now a living nightmare to every opponent. Make sure you don't need to face it!\"`n`n");
							output("`7Weapon that the demon is carrying now is `#%s.", get_module_setting("weapon".($weaponlevel+1)));
							addnav("More upgrades","runmodule.php?module=vaultguardian&op=enter");
							vaultguardian_navs();
						} else{
							output("`Q\"Your demon is strong enough!\"`7, the sorcerer shouts and uses his magic to teleport you out of his cottage.`n");
							vaultguardian_navs();
						}
					} else{
						output("`Q\"So you want everything for free? Feel free to take journey to the shades!\"`7, the sorcerer puts deadly spell on you and you fall down.`n`n");
						$session['user']['hitpoints']=0;
						addnav("Land of shades", "shades.php");
					}
				break;
			}			
		break;
		case "armor":
			page_header(array("The Dark Sorcerer %s", get_module_setting("sorcerername")));
			$action = httpget("action");
			$armorlevel = get_module_objpref("clans", $session['user']['clanid'], "guardianarmorlevel", "vaultguardian");
			$goldcost = get_module_setting("upgradearmorgold")*($armorlevel+1);
			$gemscost = get_module_setting("upgradearmorgems")*($armorlevel+1);
			switch($action){
				case "verify":
					output("`Q\"What?! You're saying your demon is getting killed all the time?!\"`7, the sorcerer wonders.`n`n");
					output("`Q\"Give me `^%s gold `Qand `@%s gems `Qand I make sure your demon is unbeatable!\"`7, he promises.`n`n", $goldcost, $gemscost);
					rawoutput("<form action='runmodule.php?module=vaultguardian&op=armor&action=upgrade' method='POST'>");
					addnav("","runmodule.php?module=vaultguardian&op=armor&action=upgrade");
					$text = translate_inline("Upgrade powers");
					rawoutput("<input type='submit' class='button' value='$text'></form>");
					addnav("More Upgrades","runmodule.php?module=vaultguardian&op=enter");
					vaultguardian_navs();
				break;
				case "upgrade":
					if($session['user']['gold']>=$goldcost and $session['user']['gems']>=$gemscost){
						if($armorlevel < 10){
							$session['user']['gold']-=$goldcost;
							$session['user']['gems']-=$gemscost;
							set_module_objpref("clans", $session['user']['clanid'], "guardianarmorlevel", $armorlevel+1, "vaultguardian");	
							output("`7You watch while the sorcerer does his tricks and after couple of minutes he says, `Q\"Okay, I bet no one can beat this monster in a battle anymore.\"`n`n");
							output("`7Armor that the demon is wearing now is `#%s.", get_module_setting("armor".($armorlevel+1)));
							addnav("More Upgrades","runmodule.php?module=vaultguardian&op=enter");
							vaultguardian_navs();
						} else{
							output("`Q\"You're lying! Your demon is nearly unbeatable!\"`7, the sorcerer shouts and uses his magic to teleport you out of his cottage.`n");
							vaultguardian_navs();
						}
					} else{
						output("`Q\"So you want everything for free? Feel free to take journey to the shades!\"`7, the sorcerer puts deadly spell on you and you fall down.`n`n");
						$session['user']['hitpoints']=0;
						addnav("Land of the Shades", "shades.php");
					}
				break;
			}
		break;
		case "clans":
			page_header("Clans in the Realm");
			$sql="SELECT clanid,clanname FROM ".db_prefix("clans")." WHERE clanid<>".$session['user']['clanid']." ORDER BY clanname ASC";
			$result = db_query($sql);
			if (db_num_rows($result) > 0){
				output("`QList of Clans in the Realm`n`n");
				rawoutput("<table border=0 cellpadding=2 cellspacing=0>");
				$name = translate_inline("Clan Name");
				$option = translate_inline("Option");
				rawoutput("<tr class='trhead'><td class='colLtWhite'>$name</td><td class='colLtWhite'>$option</td></tr>");
				$n=0;
				while ($row = db_fetch_assoc($result)) {
					$n++;
					rawoutput("<tr class='".($n%2?"trlight":"trdark")."'><td>");
					output_notl("`&".$row['clanname']);
					rawoutput("</td>");
					$text = translate_inline("Attack");
					output_notl("<td>`^[<a class='colLtGreen' href='runmodule.php?module=vaultguardian&op=attack&clanid=".$row['clanid']."&clanname=".urlencode($row['clanname'])."'>$text</a>`^]</td>",true);
					addnav("","runmodule.php?module=vaultguardian&op=attack&clanid=".$row['clanid']."&clanname=".urlencode($row['clanname']));
					rawoutput("</tr>");
				}
				rawoutput("</table>");
			} else{
				 output("Your clan is the only clan in the realm.`n");
			}
			vaultguardian_navs();
		break;
		case "attack":
			page_header("Break into a Vault!");
			$clanid = httpget("clanid");
			if(get_module_objpref("clans", $clanid, "hasguardian", "vaultguardian")==1){
				if(get_module_pref("hasattacked")==0){
					if ($session['user']['level'] >= 13 and $session['user']['playerfights'] > 0){
						$session['user']['playerfights']--;
						$badguy = vaultguardian_createGuard($clanid);
						$session['user']['badguy'] = createstring($badguy);
						$op = "fight";
						httpset('op', $op);
						httpset('clanname', httpget("clanname"));
						httpset('clanid', $clanid);
						set_module_pref("hasattacked", 1);
					} else {
						output("You decide NOT to break into their vault. There's a huge guardian patrolling in front of the vault and the beast seems to be much more deadly warrior than you are.");
						vaultguardian_navs();
					}
				} else{
					output("You have tried it already today. Try again tomorrow.");
					vaultguardian_navs();	
				}
			 } else{
				if(e_rand(1,3)==3){
					output("`@Looks like this clan has forgot to protect their vault.`n");
					addnav("Continue","runmodule.php?module=vaultguardian&op=vault&clanid=".$clanid."&clanname=".httpget("clanname")."");
					set_module_pref("hasattacked", 1);
				} else{
					output("`@You are walking towards the vault without watching your steps.");
					output("There's a empty bucket lying on the ground you kick it.");
					output("The noise from the bucket brings whole clan in the front of their vault.");
					output("Trying into the vault is a suicide. This isn't your day and you leave the place quickly.");
					vaultguardian_navs();
				}
			}		
		break;
		case "vault":
			$clanname = urldecode(httpget("clanname"));
			page_header(array("Clan %s's vault", $clanname));
			$goldinvault = get_module_objpref("clans", httpget("clanid"), "vaultgold", "clanvault");
			$gemsinvault = get_module_objpref("clans", httpget("clanid"), "vaultgems", "clanvault");
			$foundgold = e_rand(0, round($goldinvault*0.3));
			$foundgems = e_rand(0, round($gemsinvault*0.2));
			output("`6Your income has been noticed in the %s's clan hall and you have to hurry.`n`n", $clanname);
			output("`6You take with you as much as you carry: `^%s gold `6and `@%s gems`6.`n`n",$foundgold,$foundgems);
			output("`\$You should run now...");
			$session['user']['gold'] += $foundgold;
			$session['user']['gems'] += $foundgems;
			if (get_module_setting('brSteal')==1) {
				if ($foundgold>0) {
					set_module_objpref('clans',httpget('clanid'),'vaultgold',($goldinvault-$foundgold),'clanvault');
					injectcommentary("clan-".httpget('clanid'),"","::".str_replace("%s",$foundgold,translate_inline("stole %s gold from your vault.")),false);
				}
				if ($foundgems>0) {
					set_module_objpref('clans',httpget('clanid'),'vaultgems',($gemsinvault-$foundgems),'clanvault');
					if ($foundgems>1) {
						injectcommentary("clan-".httpget('clanid'),"","::".str_replace("%s",$foundgems,translate_inline("stole %s gems from your vault.")),false);
					} else {
						injectcommentary("clan-".httpget('clanid'),"","::".str_replace("%s",$foundgems,translate_inline("stole a gem from your vault.")),false);
					}
				}
			}
			vaultguardian_navs();
		break;
		case "cancel":
			page_header(array("The Dark Sorcerer %s", get_module_setting("sorcerername")));
			addnav("Actions");
			$action = httpget("action");
			switch($action){
				case "verify":
					output("`Q\"Are you sure you want your clan to be unproteced\"`7, %s asks and looks at you.", get_module_setting("sorcerername"));
					addnav("Yes", "runmodule.php?module=vaultguardian&op=cancel&action=yes");
					addnav("No", "runmodule.php?module=vaultguardian&op=cancel&action=no");
				break;				
				case "yes":
					output("`Q\"Well the deal is off now! I hope your clan is robbed immediately!\"`7, the sorcerer shouts.`n`n");
					output("Atleast you don't have to pay anything for him...");
					set_module_objpref("clans", $session['user']['clanid'], "guardianarmorlevel", 0, "vaultguardian");
					set_module_objpref("clans", $session['user']['clanid'], "guardianweaponlevel", 0, "vaultguardian");
					set_module_objpref("clans", $session['user']['clanid'], "guardianname", "", "vaultguardian");
					set_module_objpref("clans", $session['user']['clanid'], "hasguardian", 0, "vaultguardian");
				break;
				case "no":
					output("`7%s smiles, `Q\"You know this is best for all of us!\"`n`n", get_module_setting("sorcerername"));
					output("`Q\"Remember to keep paying!\"`7, the sorcerer laughs.`n`n");
				break;
			}
			vaultguardian_navs();
		break;
	}
	
	if ($op=="fight"){
		page_header("Fight the Vault Guardian!");
		$battle = true;
	}
	if ($battle){
		include_once("battle.php");
		$clanname = httpget("clanname");
		$clanid = httpget("clanid");
		if ($victory){
			$badguy=array();
			output("`n `@The Guardian makes his final strike missing it totally.`n`n");
			output("`&You took your %s and cut the beast's head off.`n`n", $session['user']['weapon']);
			output("`@Wind starts blowing and a small hurricane takes all that is left of the beast and carries them away.`n`n");
			output("`&You have now access to the clan vault!.");
			addnews("`#%s `@managed `7to break into `#%s's `7clan vault. This might mean war...",$session['user']['name'], $clanname);
			addnav("Continue","runmodule.php?module=vaultguardian&op=vault&clanid=".$clanid."&clanname=".$clanname."");
		} elseif($defeat){
			output("`n `$ You feel weak and tired. This battle can't be won.`n`n");
			output("`&Your head isn't working clearly and you turn your look away from the guardian.`n`n");
			output("`$ As lightning from the clear sky the guardian hits you and you fall down feeling nothing to live for.`n`n");
			output("`&You `$ lose %s `&experience and all your gold you were carrying.", $session['user']['experience']*0.1);
			$session['user']['experience']*=0.9;
			$session['user']['gold']=0;
			addnews("`#%s `7was `$ killed `7trying to break into `#%s's `7clan vault.",$session['user']['name'], $clanname);
			addnav("Land of Shades","shades.php");
		} else{
			fightnav(false,false,"runmodule.php?module=vaultguardian&clanid=".$clanid."&clanname=".$clanname."");
		}
	}
	page_footer();
}

function vaultguardian_createGuard($clanid){
	global $session;	
	$name = get_module_objpref("clans", $clanid, "guardianname", "vaultguardian");
	$armorlevel = get_module_objpref("clans", $clanid, "guardianarmorlevel", "vaultguardian");
	$weaponlevel = get_module_objpref("clans", $clanid, "guardianweaponlevel", "vaultguardian");
	$weaponname = get_module_setting("weapon".$weaponlevel);
	$armorname = get_module_setting("armor".$armorlevel);
	$hp=e_rand(round($session['user']['maxhitpoints']/2), 1.25*$session['user']['maxhitpoints']);
	$attack = 30 + round(1.5*e_rand(1, $weaponlevel)) + e_rand(($session['user']['dragonkills']+3)/3, $session['user']['dragonkills']+1);
	$defense = 30 + round(1.5*e_rand(1, $armorlevel)) + e_rand(($session['user']['dragonkills']+3)/3, $session['user']['dragonkills']+1);
	$level = 14;
	$badguy=array("creaturename"=>$name,"creaturelevel"=>$level,"creatureweapon"=>$weaponname, "creaturearmor"=>$armorname, "creaturegold"=>0,"creatureexp"=>0,"creaturehealth"=>$hp, "creatureattack"=>$attack,"creaturedefense"=>$defense,"diddamage"=>0,"type"=>"pvp");
	return $badguy;
}

function vaultguardian_navs() {
	global $session;
	if (httpget('module')=='vaultguardian') {
		addnav("Navigation");
		addnav("C?Return to your Clan","clan.php");
		addnav("M?Back to your Vault","runmodule.php?module=clanvault&op=enter");
		villagenav();
	} elseif (httpget('module')=='clanvault'&&httpget('op')=='enter') {
		addnav("Clan Options");
		addnav("Break into a Vault","runmodule.php?module=vaultguardian&op=clans");	
		if ($session['user']['clanrank']>=CLAN_LEADER) {
			addnav("Other");
			addnav(array("The Dark Sorcerer %s", get_module_setting("sorcerername")),"runmodule.php?module=vaultguardian&op=enter");
		}
	}
}
?>