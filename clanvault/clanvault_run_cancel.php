<?php
addnav("Back to the Guild Vault","runmodule.php?module=clanvault&op=enter");
clanvault_RemoveRequest($session['user']['acctid']);
$sql="SELECT acctid FROM ".db_prefix("accounts")." WHERE acctid<>".$session['user']['acctid']." AND clanid=".$session['user']['clanid']." and clanrank>=".CLAN_OFFICER."";
$result = db_query($sql);
/*
while ($row = db_fetch_assoc($result)) {
	if (get_module_pref('check_showNot','clanvault',$row['acctid'])==1) {
		$subject = "Cancelled Request";
		$msg = array("`^I requested some money and have now cancelled it.`n%s %s`0`^.", $amount, $type, $ranks[$session['user']['clanrank']], $name);
		systemmail($row['acctid'], $subject, $msg);
	}
}*/
output("`@Any pending requests you may have had, have been cancelled.");
addnav("Back to the Guild Vault","runmodule.php?module=clanvault&op=enter");
?>