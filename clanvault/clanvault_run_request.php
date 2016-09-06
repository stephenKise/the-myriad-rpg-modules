<?php
			$amount = httppost("amount");
			$type = httppost("type");

		//RPGee.com - max request settings
			$stipendgoldlevel = get_module_setting('stipendgoldlevel') * $session['user']['level'];
			$stipendgemlevel = floor(get_module_setting('stipendgemlevel') * $session['user']['level']);
		//END
		
			switch ($action) {
				case "form":
					if (get_module_pref("requests") > 0)
					{
						if (get_module_pref("hasrequested")==0)
						{
							if (get_module_setting("allowgemsinvault")==1 and get_module_setting("allowgoldinvault")==1)
							{
								output("Request Gold/Gems`n`n");
							}
							else
							{
								if (get_module_setting("allowgoldinvault")==1) output("Request Gold`n`n");
								if (get_module_setting("allowgemsinvault")==1) output("Request Gems`n`n");
							}
							rawoutput("<form action='runmodule.php?module=clanvault&op=request&action=request' method='POST'>");
							addnav("","runmodule.php?module=clanvault&op=request&action=request");
							if (get_module_setting("allowgoldinvault")==1)
							{
								rawoutput("<input type='radio' name='type' value='gold' checked>");
								output("Gold`n");
							}
							if (get_module_setting("allowgemsinvault")==1)
							{
								rawoutput("<input type='radio' name='type' value='gems'>");
								output("Gems`n");
							}
							output("Amount: ");
							rawoutput("<input type='text' name='amount' size='5'>");
							$text = translate_inline("Send a request");
							rawoutput("<input type='submit' class='button' value='$text'></form>");
						}
						else
						{
							output("There's already one request from you in a line!`n");
							output("Try again after the Guild leaders have made their decision about your current request in a line.");
						}
					}
					else
					{
						output("Looks like you have requested enough for today...");
					}
					addnav("Back to the Vault","runmodule.php?module=clanvault&op=enter");
				break;
				case "request":
					$amount = httppost("amount");
					if ($amount>0 and is_numeric($amount))
					{
						$amount = round($amount);
						
					//RPGee.com - check for max request
						if (($type=="gold" and $amount <= $stipendgoldlevel) || ($type=="gems" and $amount <= $stipendgemlevel))
						{
						//	clanvault_MakeRequest($type,$amount);
							$id = $session['user']['acctid'];
							$clanid = $session['user']['clanid'];
							$hit = 0;
							for($i=1;$i<11;$i++) {
							if (get_module_objpref("clans", $clanid, "request".$i, "clanvault")=="empty") {
							$request = $session['user']['name']."|".$amount."|".$type."|".$id;
							set_module_objpref("clans", $clanid, "request".$i,$request,"clanvault");
							$hit++;
							set_module_pref("hasrequested",1,"clanvault",$id);
							set_module_pref("requests",get_module_pref("requests")-1, "clanvault", $id);
							output("Request sent");
							addnav("Back to the Vault","runmodule.php?module=clanvault&op=enter");
							break;
						}
					}
					if ($hit==0) {
						output("There are too many request already in a line!`n");
						output("Try again later. Maybe the Guild leaders will have shortened the line by then.");

//RPGee.com - added nav back to prevent becoming stuck
						addnav("Back to the Vault","runmodule.php?module=clanvault&op=enter");
//END

					}
						}
						else
						{
							if ($type=="gold") output("Your request size is limited to `^%s %s`0", $stipendgoldlevel, $type);
							if ($type=="gems") output("Your request size is limited to `%%s %s`0", $stipendgemlevel, $type);

//RPGee.com - added nav back to prevent becoming stuck
							addnav("Back to the Vault","runmodule.php?module=clanvault&op=enter");
//END

						}
					//END

					}
					else
					{
						output("Zero or minus requests are not allowed!");
						addnav("Back to the Vault","runmodule.php?module=clanvault&op=enter");

					}

				break;
				case "accept":
					$type = httpget("type");
					$name = str_replace("*"," ",httpget('name'));
					$amount = httpget("amount");
					$id = httpget("id");
					if ((($gold >= $amount) and $type == "gold") or (($gems >= $amount) and $type == "gems"))
					{
						if ($type == "gold")
						{
							if (get_module_setting("goldtransfer")==1)
							{
								$type = "goldinbank";
							}
						}
						$sql="UPDATE ".db_prefix("accounts")." SET ".$type."=".$type."+".$amount." WHERE acctid=".$id."";
						db_query($sql);
						


						if ($type == "goldinbank") $type = "gold";
						$subject = translate_inline("About your request");
						$msg = array("Your request for %s %s has been accepted by the Guild management.`n`n%s of the Guild `n%s", $amount, $type, $ranks[$session['user']['clanrank']], $session['user']['name']);
						systemmail($id, $subject, $msg);
						set_module_pref("hasrequested",0,"clanvault",$id);


						//clanvault_RemoveRequest($id); - RPGee.com - moved function to this file
						$clanid = $session['user']['clanid'];
						for($i=1;$i<11;$i++) {
							$line = get_module_objpref("clans", $clanid, "request".$i, "clanvault");
							if ($line!="empty") {
								$temp = explode("|",$line);
								if ($temp[3]==$id) {
									set_module_objpref("clans", $clanid, "request".$i,"empty","clanvault");
						break;
							}
						}
					}



						output("Request accepted");
						if ($type=="gold" || $type == "goldinbank")
							set_module_objpref("clans", $session['user']['clanid'], "vaultgold",($gold-$amount),"clanvault");
						if ($type=="gems")
							set_module_objpref("clans", $session['user']['clanid'], "vaultgems",($gems-$amount),"clanvault");										   
						


						$sql="SELECT acctid FROM ".db_prefix("accounts")." WHERE acctid<>".$session['user']['acctid']." AND acctid<>".$id." AND clanid=".$session['user']['clanid']." and clanrank>=".CLAN_OFFICER."";
						$result = db_query($sql);
						/*while ($row = db_fetch_assoc($result))
						{
							if (get_module_pref('check_showNot','clanvault',$row['acctid'])==1)
							{
								$subject = translate_inline("Accepted member's request");
								$msg = array("%s requested for %s %s and I have accepted it.`n`n%s of the Guild`n%s", $name, $amount, $type, $ranks[$session['user']['clanrank']], $session['user']['name']);
								systemmail($row['acctid'], $subject, $msg);
							}
						}*/
					}
					else output("There's not enough %s in the vault!", $type);
					addnav("Back to the list","runmodule.php?module=clanvault&op=request&action=displayrequests");
				break;

				case "deny":
					$type = httpget("type");
					$amount = httpget("amount");
					$name = str_replace("*"," ",httpget('name'));
					$id = httpget("id");
					$subject = translate_inline("About your request");
					$msg = array("Your request for %s %s has been denied by the Guild management.`nReason for this is probably too high amount or too many request from same person and too often.`n`n%s of the clan`n%s", $amount, $type, $ranks[$session['user']['clanrank']], $session['user']['name']);
					systemmail($id, $subject, $msg);
					set_module_pref("hasrequested",0,"clanvault",$id);

//RPGee.com				
	$clanid = $session['user']['clanid'];
	for($i=1;$i<11;$i++) {
		$line = get_module_objpref("clans", $clanid, "request".$i, "clanvault");
		if ($line!="empty") {
			$temp = explode("|",$line);
			if ($temp[3]==$id) {
				set_module_objpref("clans", $clanid, "request".$i,"empty","clanvault");
				break;
			}
		}
	}
//					clanvault_RemoveRequest($id);
//END RPGee.com

					$sql="SELECT acctid FROM ".db_prefix("accounts")." WHERE acctid<>".$session['user']['acctid']." AND acctid<>".$id." AND clanid=".$session['user']['clanid']." and clanrank>=".CLAN_OFFICER."";
					$result = db_query($sql);
					/*while ($row = db_fetch_assoc($result)) {
						if (get_module_pref('check_showNot','clanvault',$row['acctid'])==1) {
							$subject = "Denied member's request";
							$msg = array("%s requested for %s %s and I have denied it.`n`n%s of the Guild`n%s", $name, $amount, $type, $ranks[$session['user']['clanrank']], $session['user']['name']);
							systemmail($row['acctid'], $subject, $msg);
						}
					}*/
					output("Request denied");
					addnav("Back to the list","runmodule.php?module=clanvault&op=request&action=displayrequests");
				break;
			case "displayrequests":
				addnav("Back to the Vault","runmodule.php?module=clanvault&op=enter");
				rawoutput("<table cellspacing='1' cellpadding='2' border='0'>");
				$name = translate_inline("Name");
				$amount = translate_inline("Requested");
				$options = translate_inline("Options");
				rawoutput("<tr class='trhead'><td>$name</td><td>$amount</td><td>$options</td></tr>");
				$requests=0;
				for($i=1;$i<11;$i++) {
					if (($request = get_module_objpref("clans", $session['user']['clanid'], "request".$i, "clanvault"))!="empty") {
						$requests++;
						$temp = explode("|",$request);
						rawoutput("<tr><td>");
						output_notl($temp[0]); //name
						$temp[0] = str_replace(" ","*",$temp[0]);
						$temp[0] = rawurlencode(color_sanitize($temp[0]));
						rawoutput("</td><td>");
						output_notl($temp[1]); //amount
						output(" ");
						output_notl($temp[2]); //type
						rawoutput("</td>");
						$accept = translate_inline("Accept");
						$deny = translate_inline("Deny");
						if ($temp[3] != $session['user']['acctid'])
						{
							rawoutput("<td><a href='runmodule.php?module=clanvault&op=request&action=accept&id=".$temp[3]."&amount=".$temp[1]."&type=".$temp[2]."&name=".rawurlencode(color_sanitize($temp[0]))."'>$accept</a>");
							addnav("","runmodule.php?module=clanvault&op=request&action=accept&id=".$temp[3]."&amount=".$temp[1]."&type=".$temp[2]."&name=".rawurlencode(color_sanitize($temp[0]))."");
							output("|");
							rawoutput("<a href='runmodule.php?module=clanvault&op=request&action=deny&id=".$temp[3]."&name=".rawurlencode(color_sanitize($temp[0]))."&amount=".$temp[1]."&type=".$temp[2]."'>$deny</a></td>");
							addnav("","runmodule.php?module=clanvault&op=request&action=deny&id=".$temp[3]."&name=".rawurlencode(color_sanitize($temp[0]))."&amount=".$temp[1]."&type=".$temp[2]."");
						}
						else
						{
							rawoutput("<td>Accept | Deny</td>");
						}
					rawoutput("</tr>");
					}
				}	
			if ($requests==0)rawoutput("<tr><td>No requests</td></tr>");
			rawoutput("</table>");
			break;
		}
		//addnav("Back to the Vault","runmodule.php?module=clanvault&op=enter");
?>