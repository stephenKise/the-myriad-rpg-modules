<?php
function clanvault_MakeRequest($type,$amount) {
	global $session;
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
			break;
		}
	}
	if ($hit==0) {
		output("There are too many request already in a line!`n");
		output("Try again later. Maybe the Guild leaders will have shortened the line by then.");
	}
}
?>