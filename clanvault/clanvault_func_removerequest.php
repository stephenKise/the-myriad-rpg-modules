<?php
function clanvault_RemoveRequest($id) {
	global $session;
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
}
?>