<?php
			//Edited by Aaron
			
			addnav("Back to the Vault","runmodule.php?module=clanvault&op=enter");
			
			if (httpget('action')=='on') set_module_pref('showNot',1);
			elseif (httpget('action')=='off') set_module_pref('showNot',0);
			
			/*
			if (httpget('action')=='on') {
				set_module_pref('showNot',1);
				output("`%`b`cNotifications are ON.`c`b`n");
			} elseif (httpget('action')=='off') {
				set_module_pref('showNot',0);
				output("`%`b`cNotifications are OFF.`c`b`n");
			}
			*/
			
			$showNot = get_module_pref("showNot");
			if (($showNot)==1) $msg = "On.";
			elseif (($showNot)==0) $msg = "Off.";
			
			output("`@This is where you can choose to show `^Mail`@ notifications.`n`n`c`b`&Currently, your mail notifications are `\$$msg`n`c");
			addnav("Actions");
			
			if (get_module_pref('showNot','clanvault')==1) addnav("`@Turn Notifications `\$OFF","runmodule.php?module=clanvault&op=preference&action=off");
			else addnav("`@Turn Notifications `\$ON","runmodule.php?module=clanvault&op=preference&action=on");
?>