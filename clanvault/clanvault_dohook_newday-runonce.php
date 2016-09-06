<?php
       		if (get_module_setting('stipendrunonce')) {
      			$value = get_module_setting('maxstipends');
       			$sql = "update ".db_prefix("module_userprefs")." set value='$value' where value<>'$value' and setting='stipends' and modulename='clanvault'";
       			db_query($sql);
      			$value = get_module_setting('maxrequests');
       			$sql = "update ".db_prefix("module_userprefs")." set value='$value' where value<>'$value' and setting='requests' and modulename='clanvault'";
       			db_query($sql);
       		}
			if (get_module_setting('resetlimitrunonce')) {
       			$sql = "update ".db_prefix("module_userprefs")." set value=0 where value<>0 and setting='withdrawgoldtoday' and modulename='clanvault'";
       			db_query($sql);
       			$sql = "update ".db_prefix("module_userprefs")." set value=0 where value<>0 and setting='withdrawgemstoday' and modulename='clanvault'";
       			db_query($sql);
       		}
			if (get_module_setting('resetreceiverunonce')) {
       			$sql = "update ".db_prefix("module_userprefs")." set value=0 where value<>0 and setting='stipendreceive' and modulename='clanvault'";
       			db_query($sql);
       		}
?>