<?php
			addnav("Back to the Vault","runmodule.php?module=clanvault&op=enter");			
			switch ($action) {
				case "form":
				output("You may only withdraw `^%s gold`0 per day which is %s per level.`n", get_module_setting('goldperlevel') * $session['user']['level'], get_module_setting('goldperlevel'));
				output("You may only withdraw `%%s gems`0 per day which is %s per level.`n`n", get_module_setting('gemsperlevel') * $session['user']['level'], get_module_setting('gemsperlevel'));
				if (get_module_setting('allowwithdraw'))
				{
					if ($session['user']['level'] >= get_module_setting("leaderwithdrawlevel")) {
			//END
			
						if (get_module_setting("allowgemsinvault")==1 and get_module_setting("allowgoldinvault")==1) {
							output("Withdraw Gold/Gems`n`n");
						}
						else {
							if (get_module_setting("allowgoldinvault")==1)
								output("Withdraw Gold`n`n");
							if (get_module_setting("allowgemsinvault")==1)
								output("Withdraw Gems`n`n");
						}
						rawoutput("<form action='runmodule.php?module=clanvault&op=withdraw&action=withdraw' method='POST'>");
						addnav("","runmodule.php?module=clanvault&op=withdraw&action=withdraw");
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
						output("(0 to withdraw all)`n");
						$text = translate_inline("Withdraw");
						rawoutput("<input type='submit' class='button' value='$text'></form>");
					}
					else {
						output("Maybe you shouldn't take advantage of your access to the vault until you're level ".get_module_setting("leaderwithdrawlevel")." or higher...");
					}
				}
			//END
				break;
				case "withdraw":
					$type = httppost("type");
					$amount = httppost("amount");
					switch ($type) {
						case "gold":
							if ($gold < $amount) {
								output("You can't withdraw more gold than there's available.`n");
							}

			//DAHZL - withdraw cap
							elseif(get_module_pref('withdrawgoldtoday') + $amount > get_module_setting('leaderlimitgold') || get_module_setting('goldperlevel') * $session['user']['level'] < get_module_pref('withdrawgoldtoday') + $amount)
							{
								output("Sorry, you can't withdraw that much or you have met or exceeded your withdraw limit.");
							}
							elseif($amount > get_module_setting('goldperlevel') * $session['user']['level'])
							{
								output("You may only withdraw %s gold per day which is %s per level.`n", get_module_setting('goldperlevel') * $session['user']['level'], get_module_setting('goldperlevel'));
							}
			//END
			
							else {
								if ($amount>=0) {

								//DAHZL - limit check for 0
									if ($amount==0 && $gold <= get_module_setting('goldperlevel') * $session['user']['level']) {
										$session['user']['gold']+=$gold;
										set_module_objpref("clans", $session['user']['clanid'], "vaultgold",0,"clanvault");
										$withdraw = $gold;
										set_module_pref('withdrawgoldtoday', get_module_pref('withdrawgoldtoday') + $amount);
										debuglog("withdrew ".$gold." gold from clan vault");
									}
									elseif ($amount==0 && $gold > get_module_setting('goldperlevel') * $session['user']['level'])
									{
										output("Sorry, you aren't allowed to withdraw that much.`n`n");
										$withdraw = 0;
									}
								//END
								
									else {
										$session['user']['gold']+=$amount;
										set_module_objpref("clans", $session['user']['clanid'], "vaultgold",($gold-$amount),"clanvault");
										$withdraw = $amount;
										debuglog("withdrew ".$amount." gold from clan vault");
										
								//DAHZL - withdraw cap
										set_module_pref('withdrawgoldtoday', get_module_pref('withdrawgoldtoday') + $amount);
									}
									if ($amount <> 0)
									{
										output("Withdraw completed`n`n");
									}
								//END
									output("Status of the Vault: `n");
									if (get_module_setting("allowgoldinvault")==1) {
										output("`^Gold: `&%s",$gold);
										output_notl("- %s`n",$withdraw);
									}
									if (get_module_setting("allowgemsinvault")==1) {
										output("`@Gems: `&%s",$gems);
										output_notl("`n");
									}
								}
								else
									output("Minus withdraws are not allowed");
							}
						break;
						case "gems":
							if ($gems < $amount) {
								output("You can't withdraw more gems than there's available.`n");
							}

			//DAHZL - withdraw cap
							elseif(get_module_pref('withdrawgemstoday') + $amount > get_module_setting('leaderlimitgems') || floor(get_module_setting('gemsperlevel') * $session['user']['level'] < get_module_pref('withdrawgoldtoday') + $amount))
							{
								output("You can't withdraw more than %s gems per day or you have reached your limit in withdraws today!`n", get_module_setting('leaderlimitgems'));
							}
							elseif($amount > get_module_setting('gemsperlevel') * $session['user']['level'])
							{
								output("You may only withdraw %s gems per day which is %s per level.`n", get_module_setting('gemsperlevel') * $session['user']['level'], get_module_setting('gemsperlevel'));
							}
			//END
			
							else {
								if ($amount>=0) {

								//DAHZL - limit check for 0
									if ($amount==0 && $gems <= get_module_setting('gemsperlevel') * $session['user']['level']) {
										$session['user']['gems']+=$gems;
										set_module_objpref("clans", $session['user']['clanid'], "vaultgems",0,"clanvault");
										$withdraw = $gems;
										set_module_pref('withdrawgemstoday', get_module_pref('withdrawgemstoday') + $gems);
										debuglog("withdrew ".$gems." gems from clan vault");
										
									}
									elseif ($amount==0 && $gems > get_module_setting('gemsperlevel') * $session['user']['level'])
									{
										output("Sorry, you aren't allowed to withdraw that much.`n`n");
										$withdraw = 0;
										debuglog("withdrew ".$gems." gems from clan vault", $session['user']['acctid'], false, 'vessabug', 10);
									}
								//END
								
									else {
										$session['user']['gems']+=$amount;
										set_module_objpref("clans", $session['user']['clanid'], "vaultgems",($gems-$amount),"clanvault");
										$withdraw = $amount;
										debuglog("withdrew ".$gems." gems from clan vault", $session['user']['acctid'], false, 'vessabug', 10);
								//DAHZL - withdraw cap
										set_module_pref('withdrawgemstoday', get_module_pref('withdrawgemstoday') + $amount);
									}
									if ($amount <> 0)
									{
										output("Withdraw completed`n`n");
									}
								//END
									output("Status of the Vault: `n");
									if (get_module_setting("allowgemsinvault")==1) {
										output("`^Gold: `&%s",$gold);
										output_notl("`n");

									}
									if (get_module_setting("allowgemsinvault")==1) {
										output("`@Gems: `&%s",$gems);
										output_notl("- %s`n",$withdraw);
									}
								}
								else
									output("Minus withdraws are not allowed");
							}
						break;
					}
					break;
				}

?>