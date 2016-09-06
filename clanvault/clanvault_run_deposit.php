<?php
addnav("Back to the Vault","runmodule.php?module=clanvault&op=enter");
switch ($action) {
	case "form":
		if (get_module_setting("allowgemsinvault")==1 and get_module_setting("allowgoldinvault")==1) {
			output("Deposit Gold/Gems`n`n");
		}else {
			if (get_module_setting("allowgoldinvault")==1){
				output("Deposit Gold`n`n");
			}
			if (get_module_setting("allowgemsinvault")==1){
				output("Deposit Gems`n`n");
			}
		}
		if ($gold == $MAXAMOUNTOFGOLD && $gems == $MAXAMOUNTOFGEMS) {
			output("The vault is full, no more gold or gem deposits are allowed.`n");
		} else {
			rawoutput("<form action='runmodule.php?module=clanvault&op=deposit&action=deposit' method='POST'>");
			addnav("","runmodule.php?module=clanvault&op=deposit&action=deposit");
			if (get_module_setting("allowgoldinvault")==1) {
				if ($gold == $MAXAMOUNTOFGOLD) {
					output("`iThere's no room for more gold!`i`n");
				} else {
					rawoutput("<input type='radio' name='type' value='gold' checked>");
					output("Gold`n");
				}
			}
			if (get_module_setting("allowgemsinvault")==1) {
				if ($gems == $MAXAMOUNTOFGEMS) {
					output("`iThere's no room for more gems!`i`n");
				} else {
					rawoutput("<input type='radio' name='type' value='gems'>");
					output("Gems`n");
				}
			}
			output("Amount: ");
			rawoutput("<input type='text' name='amount' size='5'>");
			output("(0 to deposit all)`n");
			$text = translate_inline("Deposit");
			rawoutput("<input type='submit' class='button' value='$text'></form>");
		}
	break;
	case "deposit":
		$type = httppost("type");
		$checkdeposit = false;
		$amount = httppost("amount");
		switch ($type) {
			case "gold":
				if ($session['user']['gold'] < $amount || ($gold + $amount) > $MAXAMOUNTOFGOLD) {
					if (isset($_POST['donate']) && $_POST['donate'] == 1) {
						output("You don't have enough gold to donate or there's no room for more gold in your Corporation's vault.`n");
					} else {
						output("You don't have enough gold to deposit or there's no room for more gold in your Corporation's vault.`n");
					}
				}elseif(!is_numeric($amount)) {
					output("That is not a number.`n");
				} else {
					if ($amount>=0) {
						$checkdeposit = true;
						if ($amount==0) {
							if (($gold + $session['user']['gold']) <= $MAXAMOUNTOFGOLD) {
								set_module_objpref("clans", $session['user']['clanid'], "vaultgold",($gold+$session['user']['gold']),"clanvault");
								$deposit = $session['user']['gold'];
								$session['user']['gold']=0;
								debuglog("put ".$deposit." in clan vault");
							} else {
								output("There's no room for more gold in your Corporation's vault.`n");
								$checkdeposit = false;
							}
						} else {
							$session['user']['gold']-=$amount;
							set_module_objpref("clans", $session['user']['clanid'], "vaultgold",($gold+$amount),"clanvault");
							$deposit = $amount;
							debuglog("put ".$deposit." in clan vault");
						}
						if (isset($_POST['donate']) && $_POST['donate'] == 1 && $checkdeposit) {
							output("Donation completed`n`n");
							//Added by Aaron per dreports.php
							$session['user']['golddonated'] += $amount;
						} else {
							if ($checkdeposit) {
								output("Deposit completed`n`n");
								output("Status of the Vault: `n");
								if (get_module_setting("allowgoldinvault")==1) {
									output("`^Gold: `&%s",$gold);
									output_notl("+%s`n",$deposit);
								}
								if (get_module_setting("allowgemsinvault")==1) {
									output("`@Gems: `&%s",$gems);
									output_notl("`n");
								}
								//Added by Aaron per dreports.php
								$session['user']['golddonated'] += $amount;
							}
						}
					} else {
						output("Minus deposits are not allowed");
					}
				}
			break;
			case "gems":
				if ($session['user']['gems'] < $amount || ($gems + $amount) > $MAXAMOUNTOFGEMS) {
					if (isset($_POST['donate']) && $_POST['donate'] == 1) {
						output("You don't have enough gems to donate or there's no room for more gems in your Corporation's vault.`n");
					} else {
						output("You don't have enough gems to deposit or there's no room for more gems in your Corporation's vault.`n");
					}
				}elseif(!is_numeric($amount)) {
					output("That is not a number.`n");
				} else {
					if ($amount>=0) {
						$checkdeposit = true;
						if ($amount==0) {
							if (($gems + $session['user']['gems']) <= $MAXAMOUNTOFGEMS) {
								set_module_objpref("clans", $session['user']['clanid'], "vaultgems",($gems+$session['user']['gems']),"clanvault");
								$deposit = $session['user']['gems'];
								$session['user']['gems']=0;
								debuglog("put ".$deposit." in clan vault");
							} else {
								output("There's no room for more gems in your clan's vault.`n");
								$checkdeposit = false;
							}
						} else {
							$session['user']['gems']-=$amount;
							set_module_objpref("clans", $session['user']['clanid'], "vaultgems",($gems+$amount),"clanvault");
							$deposit = $amount;
							debuglog("put ".$deposit." gems in clanvault");
						}
						if (isset($_POST['donate']) && $_POST['donate'] == 1 && $checkdeposit) {
							output("Donation completed`n`n");
							//Added by Aaron per dreports.php
							$session['user']['gemsdonated'] += $amount;
						} else {
							if ($checkdeposit) {
								output("Deposit completed`n`n");
								output("Status of the Vault: `n");
								if (get_module_setting("allowgoldinvault")==1) {
									output("`^Gold: `&%s",$gold);
									output_notl("`n");
								}
								if (get_module_setting("allowgemsinvault")==1) {
									output("`@Gems: `&%s",$gems);
									output_notl("+%s`n",$deposit);
								}
								//Added by Aaron per dreports.php
								$session['user']['gemsdonated'] += $amount;
							}
						}
					} else {
					output("Minus deposits are not allowed");
				}
			}
			break;
		}
		//if (isset($_POST['donate']) && $_POST['donate'] == 1 && $checkdeposit) {
			//$sql="SELECT acctid FROM ".db_prefix("accounts")." WHERE clanid=".$session['user']['clanid']." and clanrank>=".CLAN_LEADER."";
			//$result = db_query($sql);
			/*
			while ($row = db_fetch_assoc($result)) {
				if (get_module_pref('check_showNot','clanvault',$row['acctid'])==1) {
					$subject = array("A Donation");
					$msg = array("Here's a little donation for our Corporation, %s %s `n`n%s", $deposit, $type, $session['user']['name']);
					systemmail($row['acctid'], $subject, $msg);
				}
			}*/
	//	}
	break;
}
?>