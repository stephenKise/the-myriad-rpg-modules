<?php
	if (!is_module_installed('clanvault')){
		output("`n`c`b`QGuildvault Module - Installed`0`b`c");
	}else{
		output("`n`c`b`QGuildvault Module - Updated`0`b`c");
		$sql = "SELECT * FROM ".db_prefix("module_objprefs")." WHERE modulename='clanvault' AND objtype='clan'";
		$result = db_query($sql);
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			set_module_objpref("clans", $row['objid'], $row['setting'],$row['value'],"clanvault");
		}
		$sql = "DELETE FROM " . db_prefix("module_objprefs") . " WHERE objtype='clan' AND modulename='clanvault'";
		db_query($sql);
	}
	module_addhook("footer-clan");
	module_addhook("newday");
	module_addhook("dragonkill");
	module_addhook("checkuserpref");
	module_addhook("newday-runonce");
	return true;
?>