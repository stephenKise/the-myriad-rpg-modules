<?php
			if (get_module_setting("dktax")==1) {
				$gems = 0;
				$gold = 0;
				$sql = "SELECT COUNT(clanid) AS members FROM ".db_prefix("accounts")." WHERE clanid=".$session['user']['clanid']."";
			 	$result = db_query($sql);
			 	$row = db_fetch_assoc($result);
			 	$members = $row['members'];
				if (get_module_setting("allowgoldinvault")==1) {
					$gold = get_module_objpref("clans", $session['user']['clanid'], "vaultgold", "clanvault");
					if ($members==1)
						$goldaftertax =  round($gold*((100-(int)get_module_setting("oneplayertax"))/100));
					else
						$goldaftertax =  $gold - round($gold/$members/10);
					set_module_objpref("clans", $session['user']['clanid'], "vaultgold",$goldaftertax,"clanvault");
					$msg = sprintf("::`7killed `@the Tentromech`7 and the Guild has been taxed `$%s `^gold.", ($gold-$goldaftertax));
				}
				if (get_module_setting("allowgemsinvault")==1) {
					$gems = get_module_objpref("clans", $session['user']['clanid'], "vaultgems", "clanvault");
					if ($members==1)
						$gemsaftertax =  round($gems*((100-(int)get_module_setting("oneplayertax"))/100));
					else
						$gemsaftertax =  $gems - round($gems/$members/10);
					set_module_objpref("clans", $session['user']['clanid'], "vaultgems",$gemsaftertax,"clanvault");
					$msg = sprintf("::`7killed `@the Tentromech`7 and the Guild has been taxed `$%s `@gems.", ($gems-$gemsaftertax));
				}	
				if (get_module_setting("allowgemsinvault")==1 and get_module_setting("allowgoldinvault")==1) {
					$msg = sprintf("::`7killed `@the Tentromech`7 and the Guild has been taxed `$%s `^gold`7 and `$%s `@gems.", ($gold-$goldaftertax), ($gems-$gemsaftertax));
				}
				$name = $session['user']['name'];
				if ($gold>0) {
					if (get_module_setting("taxannouncement")==1) injectcommentary("clan-".$session['user']['clanid'], "", $msg, $schema=false);
					output("`nYour Guild has to pay taxes because of the tentromech kill.");
					$sql = "SELECT acctid FROM ".db_prefix("accounts")." WHERE acctid<>".$session['user']['acctid']." AND clanid=".$session['user']['clanid']." and clanrank=".CLAN_LEADER;
					$result = db_query($sql);
					/*
					while ($row = db_fetch_assoc($result)) {
						if (get_module_pref('check_showNot','clanvault',$row['acctid'])==1) {
							$subject = array("Tentromech Kill Tax");
							$msg = array("A member of your Guild, %s, `^has slain the tentromech which means that your Guild has to pay a special Tentromech kill tax.`nConsidering the amount of members in your Guild the tax is %s gold and %s gems.`n`nSincerely,`nTaxman", $session['user']['name'], ($gold-$goldaftertax), ($gems-$gemsaftertax));
							systemmail($row['acctid'], $subject, $msg);
						}
					}*/
				}
			}
?>