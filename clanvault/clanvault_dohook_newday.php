<?php
       		if (!get_module_setting('stipendrunonce')) {
				if ($session['user']['clanid']!=0) {
					set_module_pref("stipends", get_module_setting("maxstipends"));
					set_module_pref("requests", get_module_setting("maxrequests"));
				}
			}
       		if (!get_module_setting('resetlimitrunonce')) {
				if ($session['user']['clanid']!=0) {
					set_module_pref("withdrawgoldtoday", 0);
					set_module_pref("withdrawgemstoday", 0);
				}
			}		
			if (!get_module_setting('resetreceiverunonce')) {
				if ($session['user']['clanid']!=0)
				{
					set_module_pref("stipendreceive", 0);
				}
			}
?>