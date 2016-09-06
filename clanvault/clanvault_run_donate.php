<?php
addnav("Back to the Vault","runmodule.php?module=clanvault&op=enter");
 			if (get_module_setting("allowgemsinvault")==1 and get_module_setting("allowgoldinvault")==1) {
				output("Donate Gold/Gems`n`n");
			}
			else {
				if (get_module_setting("allowgoldinvault")==1)
					output("Donate Gold`n`n");
				if (get_module_setting("allowgemsinvault")==1)
					output("Donate Gems`n`n");
			}
		 	if ($gold == $MAXAMOUNTOFGOLD && $gems == $MAXAMOUNTOFGEMS) {

				output("The vault is full, no more gold or gems donations are allowed.`n");
			}
			else {
				rawoutput("<form action='runmodule.php?module=clanvault&op=deposit&action=deposit' method='POST'>");
				addnav("","runmodule.php?module=clanvault&op=deposit&action=deposit");
				if (get_module_setting("allowgoldinvault")==1) {
					if ($gold == $MAXAMOUNTOFGOLD) {
						output("`iThere's no room for more gold!`i`n");
					}
					else {
						rawoutput("<input type='radio' name='type' value='gold' checked>");
						output("Gold`n");
					}
				}
				if (get_module_setting("allowgemsinvault")==1) {
					if ($gems == $MAXAMOUNTOFGEMS) {
						output("`iThere's no room for more gems!`i`n");
					}
					else {
						rawoutput("<input type='radio' name='type' value='gems'>");
						output("Gems`n");
					}
				}
				output("Amount: ");
				rawoutput("<input type='text' name='amount' size='5'>");
				output("(0 to donate all)`n");
				$text = translate_inline("Donate");
				rawoutput("<input type='hidden' name='donate' value='1'>");
				rawoutput("<input type='submit' class='button' value='$text'></form>");
			}
?>