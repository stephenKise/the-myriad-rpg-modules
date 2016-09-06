<?php
			addnav("Back to the Vault","runmodule.php?module=clanvault&op=enter");
			switch ($action) {
				case "members":
					if (get_module_pref("stipends")>0) {
						output("Stipend a Guild member`n`n");
						
					//DAHZL - SQL joindate and display stipends and level
						output("You have %s out of %s possible stipends for today.`n`n", get_module_pref('stipends'), get_module_setting('maxstipends'));
						$sql="SELECT name,acctid,clanrank,clanjoindate,level FROM ".db_prefix("accounts")." WHERE clanid=".$session['user']['clanid']." ORDER BY clanrank DESC";
						$result = db_query($sql);
					//END
						
						rawoutput("<table border=0 cellpadding=2 cellspacing=0>");
						$name = translate_inline("Name");
						$rank = translate_inline("Rank");
						$option = translate_inline("Option");
						
					//DAHZL - added in level and stipend limit display
						$level = translate_inline("Level");
						$stipend = translate_inline("Stipend");
						rawoutput("<tr class='trhead'><td>$name</td><td>$rank</td><td>$level</td><td>$stipend</td><td>$option</td></tr>");
						while ($row = db_fetch_assoc($result)) {
							rawoutput("<tr><td>");
							output_notl($row['name']);
							rawoutput("</td><td>");
							output_notl($ranks[$row['clanrank']]);
							rawoutput("</td><td>");
							
							output_notl($row['level']);
							rawoutput("</td><td>");
							output("`^");
							output_notl($row['level'] * get_module_setting('stipendgoldlevel'));
							output("`0/");
							output("`%");
							output_notl(floor($row['level'] * get_module_setting('stipendgemlevel')));
							output("`0");
							rawoutput("</td>");
					//END

							$text = translate_inline("Stipend");
							
						//DAHZL stipend receive time check. Also checks for max stipends received
							$stipendtimemember = get_module_setting('stipendtimemember') * 3600;
							$id = $row['acctid'];
							$stipendreceive = get_module_pref('stipendreceive', 'clanvault', $id);
							if ($row['acctid'] != $session['user']['acctid'] && $row['clanjoindate'] < date("Y-m-d H:i",time()-$stipendtimemember) && get_module_setting('maxreceive') > $stipendreceive) {
						//END
						
								rawoutput("<td><a href='runmodule.php?module=clanvault&op=stipend&action=form&id=".$row['acctid']."'>$text</a></td>");
								addnav("","runmodule.php?module=clanvault&op=stipend&action=form&id=".$row['acctid']."");
							}

						//DAHZL - added in note for no stipend for set time
							elseif ($row['clanjoindate'] > date("Y-m-d H:i",time()-$stipendtimemember))
							{
								rawoutput("<td>Too Soon</td>");
							}
							elseif (get_module_setting('maxreceive') <= $stipendreceive)
							{
								rawoutput("<td>0 Left</td>");
							}
						//END

							else {
								rawoutput("<td>-</td>");
							}
							rawoutput("</tr>");
						}
						rawoutput("</table>");
						output("`n*`iYou can't stipend yourself`i");
	 				}
	 				else
	 					output("You have already sent all your stipends.`nTry again another day.`n");
				break;
				case "form":
					$id = httpget("id");
					$sql = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid=".$id."";
					$result = db_query($sql);
					$row = db_fetch_assoc($result);
					output("A Stipend verification`n`n");
					output("To: %s`n",$row['name']);

					rawoutput("<form action='runmodule.php?module=clanvault&op=stipend&action=stipend' method='POST'>");
					addnav("","runmodule.php?module=clanvault&op=stipend&action=stipend");
										
					if (get_module_setting("allowgoldinvault")==1) {
						rawoutput("<input type='radio' name='type' value='gold' checked>");
						output("Gold`n");
					}
					if (get_module_setting("allowgemsinvault")==1) {
						rawoutput("<input type='radio' name='type' value='gems'>");
						output("Gems`n");
					}
					output("Amount: ");
					rawoutput("<input type='text' name='amount' size='5'>");
					rawoutput("<input type='hidden' name='id' value='".$id."'>");

					rawoutput("<input type='hidden' name='member' value='".$name."'>");
					$text = translate_inline("Stipend");

					rawoutput("<input type='submit' class='button' value=$text></form>");
				break;
				case "stipend":
					$type = httppost("type");
					$amount = httppost("amount");
					$id = httppost("id");
					$member = httppost("member");
					$stipendflag = false;
					switch ($type) {
						case "gold":
							if ($gold < $amount) {
								output("You can't stipend more gold than there's available.`n");
							} else {
								if ($amount>0) {
								
				//DAHZL - gold per level allowed
									$sql = "SELECT level,name FROM ".db_prefix("accounts")." WHERE acctid=".$id."";
									$result = db_query($sql);
									$row = db_fetch_assoc($result);
									$stipendgoldlevel = get_module_setting('stipendgoldlevel') * $row['level'];	
									$namex = $row['name'];
									if ($amount<=$stipendgoldlevel) {
										if (get_module_setting("goldtransfer")==1)
											$sql= "UPDATE ".db_prefix("accounts")." SET goldinbank=goldinbank+".$amount." WHERE acctid='".$id."'";
										else
											$sql = "UPDATE ".db_prefix("accounts")." SET gold=gold+".$amount." WHERE acctid='".$id."'";
										db_query($sql);
										set_module_objpref("clans", $session['user']['clanid'], "vaultgold",($gold-$amount),"clanvault");
										debuglog("stipened ".$namex." ".$amount." gold from clan vault");
										output("Stipend completed`n`n");
										$stipendflag = true;
										set_module_pref("stipends",get_module_pref("stipends")-1);
										
									//DAHZL - max stipend
										$stipendreceive = get_module_pref('stipendreceive', 'clanvault', $id) + 1;
										set_module_pref('stipendreceive', $stipendreceive, 'clanvault', $id);
									//END
																		
									} else {
										output("Stipend size is limited to %s gold per receiving member's level. You may stipend %s gold to this member.", get_module_setting('stipendgoldlevel'), get_module_setting('stipendgoldlevel') * $row['level'] );
										//output("Stipend size is limited to %s gold!", get_module_setting("maxgoldstipend"));
				//END
					
									}
								} else {
									output("Zero or minus stipends are not allowed!");
								}
							}
						break;
						case "gems":
							if ($gems < $amount) {
								output("You can't stipend more gems than there are available.`n");
							}
							else {
								if ($amount>0) {

				//DAHZL: - Gem limit per level
									$sql = "SELECT level,name FROM ".db_prefix("accounts")." WHERE acctid=".$id."";
									$result = db_query($sql);
									$row = db_fetch_assoc($result);
									$stipendgemlevel = floor(get_module_setting('stipendgemlevel') * $row['level']);	
									$namex = $row['name'];
									if ($_POST['amount']<=$stipendgemlevel) {
									//if ($_POST['amount']<=get_module_setting("maxgemsstipend")) {
										$sql = "UPDATE ".db_prefix("accounts")." SET gems=gems+".$amount." WHERE acctid='".$id."'";
										db_query($sql);
										set_module_objpref("clans", $session['user']['clanid'], "vaultgems",($gems-$amount),"clanvault");
										output("Stipend completed`n`n");
										$stipendflag = true;
										set_module_pref("stipends",get_module_pref("stipends")-1);

									//DAHZL - max stipend
										$stipendreceive = get_module_pref('stipendreceive', 'clanvault', $id) + 1;
										set_module_pref('stipendreceive', $stipendreceive, 'clanvault', $id);
										debuglog("stipened ".$namex." ".$amount." gems from clan vault");
									//END
														
									}
									else {
										output("Stipend size is limited to 1 gem per %s receiving member's level. You may stipend %s gems to this member.", floor($row['level'] / get_module_setting('stipendgemlevel')), floor(get_module_setting('stipendgemlevel') * $row['level']));
										//output("Stipend size is limited to %s gems!", get_module_setting("maxgemsstipend"));
				//END
				
									}
								}
								else {
									output("Zero or minus stipends are not allowed!");
								}
							}
						break;
					}
					if ($stipendflag) {
						$subject = translate_inline("You've been rewarded");
						$msg = array("I have decided to reward you for being an exemplary member of our clan. The reward is %s %s.`n`n%s of the clan`n%s", $amount, $type, $ranks[$session['user']['clanrank']], $session['user']['name']);
						systemmail($id, $subject, $msg);
						$sql="SELECT acctid FROM ".db_prefix("accounts")." WHERE acctid<>".$session['user']['acctid']." AND acctid<>".$id." AND clanid=".$session['user']['clanid']." and clanrank>=".CLAN_OFFICER."";
						$result = db_query($sql);
						/*while ($row = db_fetch_assoc($result)) {
							if (get_module_pref('check_showNot','clanvault',$row['acctid'])==1) {
								$subject = translate_inline("A Reward for a member");
								$msg = array("I have rewarded a member %s for being an exemplary member of our Guild. The reward was %s %s.`n`n%s of the Guild`n%s", $member, $amount, $type, $ranks[$session['user']['clanrank']], $session['user']['name']);
								systemmail($row['acctid'], $subject, $msg);
							}
						}*/
					}
				break;
			}

?>