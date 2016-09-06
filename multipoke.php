<?php
function multipoke_getmoduleinfo(){
	$info = array(
		"name" => "Multi-Poke",
		"author" => "`i`)Ae`7ol`&us`i`0",
		"version" => "1.4",
		"category" => "General",
		"settings" => array(
			"Multi-Poke Settings,title",
				"alodge" => "Allow users to buy pokes with DPs?,bool|1",
				"lodge" => "Lodge points to buy a poke?,int|250",
				"lodgecon" => "Require staff confirmation for custom pokes via the lodge?,bool|1",
		),
		"prefs" => array(
			"Poking,title",
				"user_allow" => "Allow users to use various pokes on you?,bool|1",
				"showpokes" => "Is user showing pokes in bios?,bool|1",
				"pokemuted"=>"User banned from poking others?,bool|0",// Added by Senare
		),
	);
	return $info;
}

function multipoke_install(){
	if (!db_table_exists(db_prefix("multipoke"))){
		$sql = "CREATE TABLE ".db_prefix("multipoke")." (
				`id` int(11) NOT NULL auto_increment,
				`name` varchar(255) NOT NULL,
				`desc` varchar(255) NOT NULL,
				`yom` varchar(500) NOT NULL,
				`news` varchar(255) NOT NULL,
				`accounts` varchar(255) NOT NULL,
				`pmessage` tinyint(4) DEFAULT 0,
				`confirmed` tinyint(4) DEFAULT 1,
				PRIMARY KEY (`id`)
			)";
		db_query($sql);
	}
	module_addhook("lodge");
	module_addhook("biostat");
	module_addhook("superuser");
	module_addhook("pointsdesc");
	return true;
}

function multipoke_uninstall(){
	if (db_table_exists(db_prefix("multipoke")))
		db_query("DROP TABLE ".db_prefix("multipoke"));
	return true;
}

function multipoke_dohook($hookname,$args){
	global $session;
	$db_multipoke = db_prefix('multipoke');
	$db_accounts = db_prefix('accounts');
	
	$lodge = get_module_setting('lodge');
	$alodge = get_module_setting('alodge');
	
	switch ($hookname){
		/*case "lodge":
			$pointsavail = $session['user']['donation'] - $session['user']['donationspent'];
			if (($pointsavail >= $lodge) && $alodge) addnav(array("Custom Poke (%s points)",$lodge),"runmodule.php?module=multipoke&op=lodge");
		break;*/
		case "pointsdesc":
			if ($alodge){
				$args['count']++;
				$format = $args['format'];
				$str = translate("The ability to buy a custom poke costs %s donator points.");
				$str = sprintf($str, $lodge);
				output($format, $str, true);
			}
		break;
		case "biostat":
			//Adding the 'Poke Mute' feature with the break below.
			if ($session['user']['acctid'] == $args['acctid'] || get_module_pref("pokemuted") == 1 || !get_module_pref('user_allow','multipoke',$args['acctid'])) break;
			require_once('lib/sanitize.php');
			$ret = $args['return_link'];
			$char = $args['login'];
			$id = $args['acctid'];
			$showpokes = get_module_pref("showpokes");
			
			tlschema("nav");
			addnav("Pokes");
			addnav(array("`#`b~ %s Pokes ~`b`0",$showpokes?"Hide":"Show"),"runmodule.php?module=multipoke&op=showhide&char=".$id."&ret=".urlencode($ret)."");
			if ($showpokes){
				$sql = "SELECT * FROM $db_multipoke WHERE confirmed = 1 ORDER BY name";
				$res = db_query($sql);
				while ($row = db_fetch_assoc($res)){
					$limited = false; $disabled = false;
					if ($row['accounts'] != ""){
						if (stristr($row['accounts'],"disabled")) $disabled = true;
						$accounts = explode(",",$row['accounts']);
						$limited = true;
					}
					if (!$disabled && (!$limited || ($limited && in_array($session['user']['acctid'],$accounts)))){
						$nav = str_replace("^P^","",$row['name']);
						$nav = str_replace("^U^","",$nav);
						
						$op = "poke";
						if ($row['pmessage']) $op = "pm";
						addnav("$nav","runmodule.php?module=multipoke&op=$op&id={$row['id']}&char=$char&ret=".urlencode($ret)."");
					}
				}
			}
			tlschema();
		break;
		case "superuser":	
			$new = "";
			$sql = db_query("SELECT * FROM $db_multipoke WHERE confirmed = 0");
			if (db_num_rows($sql) > 0) $new = "(`\$`bNEW`b`0)";
			
			if ($session['user']['superuser'] & SU_EDIT_USERS){
				addnav("Editors");
				addnav("Multi-Poke Editor $new","runmodule.php?module=multipoke&op=su");
			}
		break;
	}
	return $args;
}

function multipoke_run(){
	global $session;
	
	$id = httpget('id');
	$op = httpget('op');
	$act = httpget('act');
	$ret = httpget('ret');
	$sub = httpget('sub');
	$char = httpget('char');
	$incomplete = httpget('incomplete');
	
	$db_multipoke = db_prefix('multipoke');
	$db_accounts = db_prefix('accounts');
	
	$lodgecon = get_module_setting('lodgecon');
	$alodge = get_module_setting('alodge');
	$lodge = get_module_setting('lodge');
	
	switch ($op){
		case "showhide":
			require_once("lib/redirect.php");
			$showpokes = get_module_pref("showpokes");
			if ($showpokes) $setpref = 0; else $setpref = 1;
			set_module_pref("showpokes",$setpref);
			redirect("bio.php?char=".httpget('char')."&ret=".urlencode(httpget('ret')));
		break;
		case "lodge":
			page_header("Custom Pokes");
			switch ($sub){
				case "":
					$valuename = httpget('name');
					$valuedesc = httpget('desc');
					$valueyom  = httpget('yom');
					$valuenews = httpget('news');
					
					if ($incomplete){
						output("J.C. Peterson looks over at you, and shakes his head.`n");
						output("\"No, you must complete ALL sections of the form! Try again!`n`n");
					} else {
						output("J.C. Peterson looks over at you, and raises his eyebrows.. \"So, you decided to buy a Custom Poke, hm?\"`n");
						output("He points over to several empty forms on a nearby desk, titled 'Custom Pokes', simply awaiting to be filled out.`n`n");
						output("Fill those forms out. Naturally, it will cost you %s donation points.`n`n",$lodge);
					}
					
					$send = translate_inline("Submit");
					rawoutput("<form action='runmodule.php?module=multipoke&op=lodge&sub=check' method='POST'>");
					rawoutput("<table border=0><tr><td>");
					output("`^Name:");
					rawoutput("</td><td><input name='name' maxlength='255' size='50' value='$valuename'></td></tr><tr><td>");
					output("`n`^Description: `&*");
					rawoutput("</td><td><input name='desc' maxlength='255' size='50' value='$valuedesc'></td></tr><tr><td>");
					output("`n`^Message: `&*");
					rawoutput("</td><td><input name='yom'  maxlength='500' size='50' value='$valueyom'></td></tr><tr><td>");
					output("`n`^Add News:`n(optional) `&*");
					rawoutput("</td><td><input name='news' maxlength='255' size='50' value='$valuenews'></td></tr><tr><td>");
					output("`n`n");
					rawoutput("</td><td><input type='submit' class='button' value='$send'></td></tr></table>");
					addnav("","runmodule.php?module=multipoke&op=lodge&sub=check");
					rawoutput("</form>");
					
					output("`n`n*: You can also used ^P^ where your name needs to go, and ^U^ where the name of the person being poked needs to go.");
					
					addnav("Options");
					addnav("Return to Lodge","lodge.php");
				break;
				case "check":
					$hppost = httpallpost();
					$uname = urlencode($hppost['name']);
					$udesc = urlencode($hppost['desc']);
					$uyom  = urlencode($hppost['yom']);
					$unews  = urlencode($hppost['news']);
					$combolink = "&name=$uname&desc=$udesc&yom=$uyom&news=$unews";
					if ($hppost['name'] != "" && $hppost['desc'] != "" && $hppost['yom'] != ""){
						output("J.C. Peterson looks over your shoulder, and nods as he sees the complete forms.`n");
						output("He says, \"Are you sure this is exactly what you want your custom poke to look like`n");
						output("\"Remember, only YOU will be allowed to use this poke on others!\"`n`n");
						
						output("`^Name: `&%s`n",$hppost['name']);
						output("`^Description: `&%s`n",$hppost['desc']);
						output("`^Message: `&%s`n",$hppost['yom']);
						output("`^Add News: `&%s`n`n",$hppost['news']?$hppost['news']:"`i`\$None`i`0");
						
						addnav("Are you sure?");
						addnav("Yes!", "runmodule.php?module=multipoke&op=lodge&sub=confirm$combolink");
						addnav("No!", "runmodule.php?module=multipoke&op=lodge$combolink");
					} else {
						redirect("runmodule.php?module=multipoke&op=lodge&incomplete=1$combolink");
					}
				break;
				case "confirm":
					$name = httpget('name');
					$desc = httpget('desc');
					$yom  = httpget('yom');
					$news = httpget('news');
					$accounts = $session['user']['acctid'];
					
					if ($lodgecon){
						$dbconfirm = 0;
						output("J.C. Peterson dusts his hands and nods! \"Your custom poke has been submitted, and you shall receive a YOM concerning its acceptance or declination soon!\"");
					} else {
						$dbconfirm = 1;
						output("J.C. Peterson dusts his hands and nods! \"Your custom poke has been submitted, and you use it straight away! Enjoy!\"");
					}
					db_query("INSERT INTO $db_multipoke (`id`, `name`, `desc`, `yom`, `accounts`, `news`, `pmessage`, `confirmed`) VALUES ('', '$name', '$desc', '$yom', '$accounts', '$news', 1, $dbconfirm)");
					$session['user']['donationspent'] += $lodge;
					
					addnav("Options");
					$pointsavail = $session['user']['donation'] - $session['user']['donationspent'];
					if ($pointsavail >= $lodge)
					addnav("Add Another?","runmodule.php?module=multipoke&op=lodge");
					addnav("Return to Lodge","lodge.php");
				break;
			}
		break;
		case "su":
			page_header("Multi-Pokes Editor");
			
			switch ($sub){
				case "":
					$value_id   = "";
					$value_name = "";
					$value_desc = "";
					$value_yom  = "";
					$value_pm   = 0;
					$value_acct = "";
					$value_news = "";
					$showformurl = "add";
					
					if ($act == "add"){
						$post = httpallpost();
						if ($post['poke_name'] != "" && $post['poke_desc'] != "" && $post['poke_yom'] != ""){
							db_query("INSERT INTO $db_multipoke (`id`, `name`, `desc`, `yom`, `accounts`, `news`, `pmessage`, `confirmed`) VALUES ('', '{$post['poke_name']}', '{$post['poke_desc']}', '{$post['poke_yom']}', '{$post['poke_acct']}', '{$post['poke_news']}', {$post['poke_pm']}, 1)");
							output("`b`@Poke inserted!`b`n`0");
						} else {
							output("`b`\$Poke not inserted. Missing field.`b`n`0");
						}
					}
					if ($act == "del"){
						db_query("DELETE FROM $db_multipoke WHERE id = $id");
						output("`b`@Poke deleted!`b`n`0");
					}
					if ($act == "edit"){
						$sql = db_fetch_assoc(db_query("SELECT * FROM $db_multipoke WHERE id = $id"));
						$value_id   = $id;
						$value_name = $sql['name'];
						$value_desc = $sql['desc'];
						$value_yom  = $sql['yom'];
						$value_acct = $sql['accounts'];
						$value_news = $sql['news'];
						$value_pm   = $sql['pmessage'];
						$showformurl = "editsave&id=$id";
					}
					if ($act == "editsave"){
						$hppost = httpallpost();
						if ($hppost['poke_name'] != "" && $hppost['poke_desc'] != "" && $hppost['poke_yom'] != ""){
							db_query("UPDATE $db_multipoke SET `name` = '{$hppost['poke_name']}', `desc` = '{$hppost['poke_desc']}', `yom` = '{$hppost['poke_yom']}', `accounts` = '{$hppost['poke_acct']}', `news` = '{$hppost['poke_news']}',  `pmessage` = {$hppost['poke_pm']} WHERE id = $id");
							output("`b`@Poke updated!`b`n`0");
						} else {
							output("`b`\$Poke not updated. Missing field.`b`n`0");
						}
					}
					
					$h_ops  = translate_inline("Ops");
					$h_name = translate_inline("Name");
					$h_desc = translate_inline("Description");
					$h_yom  = translate_inline("YOM Message");
					$h_news = translate_inline("Add News");
					$h_pm   = translate_inline("PM?");
					$h_play = translate_inline("Players");
					$t_del  = translate_inline("Del");
					$t_edit = translate_inline("Edit");
					$dconfm = translate_inline("Are you sure you wish to delete this poke?");
					$table = 0;
					
					while ($table < 2){
						if ($table == 0) output("`n`b`@Table of regular pokes:`b`n`n`0");
						if ($table == 1) output("`n`b`@Table of player pokes:`b`n`n`0");
						
						if ($table == 0) $sql = "SELECT * FROM $db_multipoke WHERE confirmed = 1 AND accounts = ''";
						if ($table == 1) $sql = "SELECT * FROM $db_multipoke WHERE confirmed = 1 AND accounts > ''";	
						$res = db_query($sql);
						
						rawoutput("<table border=0>");
							rawoutput("<tr class='trhead'><td>$h_ops</td><td>$h_name</td><td>$h_desc</td><td>$h_yom</td><td>$h_news</td><td>$h_pm</td><td>$h_play</td></tr>");
							if (db_num_rows($res) > 0){
								$i=0;
								while($row = db_fetch_assoc($res)){
									rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>");
										rawoutput("[<a href='runmodule.php?module=multipoke&op=su&act=del&id={$row['id']}' onClick='return confirm(\"$dconfm\");'>$t_del</a>] | [<a href='runmodule.php?module=multipoke&op=su&act=edit&id={$row['id']}'>$t_edit</a>]");
										addnav("","runmodule.php?module=multipoke&op=su&act=del&id={$row['id']}");
										addnav("","runmodule.php?module=multipoke&op=su&act=edit&id={$row['id']}");
									rawoutput("</td><td>");
										output_notl("%s",$row['name']);
									rawoutput("</td><td>");
										output_notl("%s",$row['desc']);
									rawoutput("</td><td>");
										output_notl("%s",$row['yom']);
									rawoutput("</td><td>");
										output_notl("%s",$row['news']?$row['news']:"`i`\$None`i`0");
									rawoutput("</td><td>");
										output_notl("%s",$row['pmessage']?"`@Yes":"`\$No");
									rawoutput("</td><td>");
										if ($table == 0) output("`#All!");
										if ($table == 1){
											if (stristr($row['accounts'],"disabled")){
												output("`\$`iDisabled!`i");
											} else {
												$clickhere = translate_inline("Display");
												$ex_accounts = explode(",",$row['accounts']);
												$display  = "<script language=\"JavaScript\">\nfunction displayCustom(theId)\n{\n   var el = document.getElementById(theId)\n\n   if (el.style.display==\"none\")\n   {\n      el.style.display=\"block\"; //show element\n   }\n   else\n   {\n      el.style.display=\"none\"; //hide element\n   }\n}\n</script>";
												$display .= "<span style='cursor:pointer;' onClick=\"javascript:displayCustom('cpoke_$i');\">$clickhere</span>";
												$display .= "<div id='cpoke_$i' style=\"display:none;\"><br>";
												for ($j = 0; $j < count($ex_accounts); $j++){
													$n = db_fetch_assoc(db_query("SELECT name FROM $db_accounts WHERE acctid = {$ex_accounts[$j]}"));
													$display .= ("`&".($n['name']?$n['name']:"`7`iEmpty User`i`0")." `^(ID: ".$ex_accounts[$j].")`n");
												}
												$display .= "</div>";
												rawoutput(appoencode($display,TRUE));
											}
										}
									rawoutput("</td></tr>");
									$i++;
								}
							} else {
								$none = translate_inline("None");
								rawoutput("<tr class='trlight'><td colspan=7><center><i>$none</i></center></td></tr>");
							}
						rawoutput("</table>");
						output_notl("`n");
						$table++;
					}
					
					$multipoke = array(
						"poke_id"  =>$value_id,
						"poke_name"=>$value_name,
						"poke_desc"=>$value_desc,
						"poke_yom" =>$value_yom,
						"poke_acct"=>$value_acct,
						"poke_news"=>$value_news,
						"poke_pm"  =>$value_pm,
					);
					$form = array(
						"Add Poke,title",
						"poke_id"  =>"ID,viewonly",
						"poke_name"=>"Name,",
						"poke_desc"=>"Description `^*`0,",
						"poke_yom" =>"YOM to send to player `^*`0,",
						"poke_news"=>"Add News for poke (optional) `^*`0,",
						"poke_pm"  =>"Does this poke allow a private message?,bool",
						"poke_acct"=>"AcctIDs of those who can use this poke `^**`0,",
						"`^*: `6Use ^P^ for the poker's name and ^U^'s for the player being poked's name.,note",
						"`^**: Leave empty to allow all players to use the poke. Seperare each AcctID with a comma if limiting the use of this poke.,note",
					);
					
					require_once("lib/showform.php");
					rawoutput("<form action='runmodule.php?module=multipoke&op=su&act=$showformurl' method='POST'>");
						addnav("","runmodule.php?module=multipoke&op=su&act=$showformurl");
						showform($form, $multipoke);
					rawoutput("</form>");
				break;
				case "confirm":
					$pick = httpget('pick');
					switch ($pick){
						case "":
							output("`@Below is a list of all custom pokes that have been submitted via the lodge:`n`n`0");
							
							$h_ops  = translate_inline("Ops");
							$h_name = translate_inline("Name");
							$h_desc = translate_inline("Description");
							$h_yom  = translate_inline("YOM Message");
							$h_news  = translate_inline("Add News");
							$h_play = translate_inline("Player");
							$t_rejt = translate_inline("Reject");
							$t_acct = translate_inline("Accept");
							
							$sql = "SELECT * FROM $db_multipoke WHERE confirmed = 0";;
							$res = db_query($sql);
							
							rawoutput("<table border=0>");
								rawoutput("<tr class='trhead'><td>$h_ops</td><td>$h_name</td><td>$h_desc</td><td>$h_yom</td><td>$h_news</td><td>$h_play</td></tr>");
								if (db_num_rows($res)){	
									while($row = db_fetch_assoc($res)){
										rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>");
											rawoutput("[<a href='runmodule.php?module=multipoke&op=su&sub=confirm&pick=acct&id={$row['id']}'>$t_acct</a>] | [<a href='runmodule.php?module=multipoke&op=su&sub=confirm&pick=rejt&id={$row['id']}&mes=1'>$t_rejt</a>]");
											addnav("","runmodule.php?module=multipoke&op=su&sub=confirm&pick=acct&id={$row['id']}");
											addnav("","runmodule.php?module=multipoke&op=su&sub=confirm&pick=rejt&id={$row['id']}&mes=1");
										rawoutput("</td><td>");
											output_notl("%s",$row['name']);
										rawoutput("</td><td>");
											output_notl("%s",$row['desc']);
										rawoutput("</td><td>");
											output_notl("%s",$row['yom']);
										rawoutput("</td><td>");
											output_notl("%s",$row['news']?$row['news']:"`i`\$None`i`0");
										rawoutput("</td><td>");
											$n = db_fetch_assoc(db_query("SELECT name FROM $db_accounts WHERE acctid = {$row['accounts']}"));
											output_notl("`&%s `^(%s)`n",$n['name'],$row['accounts']);
										rawoutput("</td></tr>");
										$i++;
									}
								} else {
									rawoutput("<tr class='trlight'><td colspan=6>");
										output("`&`c`iNo custom poke requests`c`i`0");
									rawoutput("</td></tr>");
								}
							rawoutput("</table>");
						break;
						case "acct":
							$sql = db_fetch_assoc(db_query("SELECT * FROM $db_multipoke WHERE id = $id"));
							$n = db_fetch_assoc(db_query("SELECT name FROM $db_accounts WHERE acctid = {$sql['accounts']}"));
							output("`&%s`@ has been notified of your acceptance of their poke \"`&%s`@\"",$n['name'],$sql['name']);
							
							require_once('lib/systemmail.php');
							$subject = translate_inline("`^Custom Poke: `&".$sql['name']."`0");
							$body = "`^Your poke with the following details has been accepted! You will now be able to see it in all bios!`n`n";
							$body.= "`^Name: `&".$sql['name'];
							$body.= "`n`^Description: `&".$sql['desc'];
							$body.= "`n`^Message: `&".$sql['yom'];
							$body.= "`n`^Add News: `&".($sql['news']?$sql['news']:"`i`\$None`i`0");
							$body.= "`n`n`^You are also allowed to add a personal note when you use this poke. Enjoy!";
							systemmail($sql['accounts'], $subject, $body);
							
							db_query("UPDATE $db_multipoke SET `confirmed` = 1 WHERE id = $id");
						break;
						case "rejt":
							if (httpget("mes")){
								$form = "runmodule.php?module=multipoke&op=su&sub=confirm&pick=rejt&id=$id";
								rawoutput("<form action='$form' method='post'>");
								addnav("", $form);
								output("`n`n`^Reason for rejection of custom poke:");
								rawoutput("<input size='70' name='rmessage'>");
								rawoutput("<input type='submit' class='button' value='".translate_inline("Send")."'>");
								rawoutput("</form>");
								output("`n`n`^Leave this section blank if you do not want to give a reason.");
								output("`n`n");
							} else {
								$rmessage = httppost("rmessage");
								$sql = db_fetch_assoc(db_query("SELECT * FROM $db_multipoke WHERE id = $id"));
								$n = db_fetch_assoc(db_query("SELECT name, donationspent FROM $db_accounts WHERE acctid = {$sql['accounts']}"));
								output("`&%s`@ has been notified of your declination of their poke \"`&%s`@\"",$n['name'],$sql['name']);
								
								require_once('lib/systemmail.php');
								$subject = translate_inline("`^Custom Poke: `&".$sql['name']."`0");
								$body = "`^Your poke with the following details has been rejected! You have been refunded your donator points.`n`n";
								$body.= "`^Name: `&".$sql['name'];
								$body.= "`n`^Description: `&".$sql['desc'];
								$body.= "`n`^Message: `&".$sql['yom'];
								$body.= "`n`^Add News: `&".($sql['news']?$sql['news']:"`i`\$None`i`0");
								$body.= "`n`n`^If you have any question regarding the declining of this custom poke, please petition in.";
								if ($rmessage > "") $body = $body . "`n`n`iReason for Rejection:`i`&`n" . $rmessage;
								systemmail($sql['accounts'], $subject, $body);
								
								$n['donationspent'] -= $lodge;
								
								db_query("UPDATE $db_accounts SET donationspent = {$n['donationspent']} WHERE acctid = {$sql['accounts']}");
								db_query("DELETE FROM $db_multipoke WHERE id = $id");
							}
						break;
					}
				break;
			}
			
			$sql = db_num_rows(db_query("SELECT * FROM $db_multipoke WHERE confirmed = 0"));
			$pointsavail = $session['user']['donation'] - $session['user']['donationspent'];
			
			addnav("Options");
			addnav("Return to Grotto","superuser.php");
			if ($sub == "confirm") addnav("Return","runmodule.php?module=multipoke&op=su");
			if ($sub == "") addnav("Refresh","runmodule.php?module=multipoke&op=su");
			if ($sql) addnav(array("Check Pending `#`b(%s)`b`0",$sql),"runmodule.php?module=multipoke&op=su&sub=confirm");
			else addnav("Check Pending (0)","runmodule.php?module=multipoke&op=su&sub=confirm");
			if (($pointsavail >= $lodge) && $alodge){
				addnav("Lodge");
				addnav("Lodge Pokes","runmodule.php?module=multipoke&op=lodge");
			}
		break;
		case "poke":
			require_once("lib/systemmail.php");
			$poke = db_fetch_assoc(db_query("SELECT * FROM $db_multipoke WHERE id = $id"));
			$player = db_fetch_assoc(db_query("SELECT acctid, name, login FROM $db_accounts WHERE login = '$char'"));
			$pmessage = httppost("pmessage");
			$nav = str_replace("^P^","",$poke['name']);
			$nav = str_replace("^U^","",$nav);
			
			page_header("$nav!");
			
			if ($player['acctid'] != $session['user']['acctid']) {
				$output = str_replace("^P^",$session['user']['name'],$poke['desc']);
				$output = str_replace("^U^",$player['name'],$output);
				output("`@%s`n`n`0",$output);
				
				$subject = sprintf_translate("`^$nav!`0");
				
				$message = str_replace("^P^",$session['user']['name'],$poke['yom']);
				$message = str_replace("^U^",$player['name'],$message);
				$message = sprintf_translate("`^$message");
				if ($pmessage > "") $message = $message . "`n`n`iPersonal Message:`i`&`n" . $pmessage;
				
				 systemmail($player['acctid'],$subject,$message,$session['user']['acctid']);
				//alert($player['acctid'],$message." `& - `2[<a href='mail.php?op=write&to={$session['user']['login']}&body=".rawurlencode("\n\n`^---Original Message from {$session['user']['name']}`^---\n$message")."'>Click here to reply</a>`2]");
				
				if ($poke['news']){
					$addnews = str_replace("^P^",$session['user']['name'],$poke['news']);
					$addnews = str_replace("^U^",$player['name'],$addnews);
					addnews("`^%s`0", [$addnews]);
				}
			} else {
				output("`^You can't do this action to yourself!`n`n");
			}
			addnav("Return","bio.php?char=$char&ret=".urlencode($ret)."");
		break;
		case "pm":
			$poke = db_fetch_assoc(db_query("SELECT name FROM $db_multipoke WHERE id = $id"));
			$nav = str_replace("^P^","",$poke['name']);
			$nav = str_replace("^U^","",$nav);
			page_header("$nav!");
			
			$form = "runmodule.php?module=multipoke&op=poke&id=$id&char=$char&ret=$ret";
			rawoutput("<form action='$form' method='post'>");
			addnav("", $form);
			output("`n`n`^Personal Message:");
			rawoutput("<input size='70' name='pmessage'>");
			rawoutput("<input type='submit' class='button' value='".translate_inline("Send")."'>");
			rawoutput("</form>");
			output("`n`n`^Leave this section blank if you do not want to add a personal message");
			output("`n`n");
			addnav("Return","bio.php?char=$char&ret=$ret");
		break;
	}
	
	page_footer();
}
?>