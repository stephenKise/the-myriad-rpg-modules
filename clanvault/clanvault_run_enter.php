<?php
addnav("Vault Options");

if (get_module_setting("allowgemsinvault")==0 and get_module_setting("allowgoldinvault")==0) {
   output("The vault exists but the gods won't allow the Guild members to use it!");
} else {
	if ($session['user']['clanrank'] >= CLAN_OFFICER) {
		output("Guards of the Guild Vault let you into the vault. This is the place where all gold and gems that members have donated exists.`n");
		output("`n`^Total vault capacity for gold: %s`n", $MAXAMOUNTOFGOLD);
		output("`%Total vault capacity for gems:  %s`n`0", $MAXAMOUNTOFGEMS);
		$sql="SELECT name,acctid,clanrank,clanjoindate FROM ".db_prefix("accounts")." WHERE clanid=".$session['user']['clanid']." ORDER BY clanrank DESC";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$withdrawtime = get_module_setting('withdrawtime') * 3600;
		$deposittime = get_module_setting('deposittime') * 3600;
		if ($session['user']['clanjoindate'] < date("Y-m-d H:i",time()-$deposittime)) {
addnav("Deposit","runmodule.php?module=clanvault&op=deposit&action=form");
if (get_module_setting('allowwithdraw') && $session['user']['clanjoindate'] < date("Y-m-d H:i",time()-$withdrawtime)){
	addnav("Withdraw","runmodule.php?module=clanvault&op=withdraw&action=form");
}
		}
		$stipendtime = get_module_setting('stipendtime') * 3600;
		if (get_module_setting("maxstipends")!=0 && $session['user']['clanjoindate'] < date("Y-m-d H:i",time()-$stipendtime)) {
addnav("Stipend","runmodule.php?module=clanvault&op=stipend&action=members");
addnav(array("Display Requests (%s)", $reqnumb),"runmodule.php?module=clanvault&op=request&action=displayrequests");
//addnav("Set Notification Preference","runmodule.php?module=clanvault&op=preference");
		} else {
output("There's a huge and thick steel door in front of you which is guarded by seven enchanted warriors.");
output("`nThe guards are allowed to let only leaders of the Guild in that have been a member of the Guild for 24 real-time hours.`n");
	
		}
	}
	$sql="SELECT name,acctid,clanrank,clanjoindate FROM ".db_prefix("accounts")." WHERE clanid=".$session['user']['clanid']." ORDER BY clanrank DESC";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$donatetime = get_module_setting('donatetime') * 3600;
	if ($session['user']['clanjoindate'] < date("Y-m-d H:i",time()-$donatetime)){
		addnav("Donate","runmodule.php?module=clanvault&op=donate&action=form");
	}
	$requesttime = get_module_setting('requesttime') * 3600;
	if ($session['user']['level'] >=	 get_module_setting("leaderwithdrawlevel")&&$session['user']['clanrank'] == CLAN_LEADER && $session['user']['clanjoindate'] < date("Y-m-d H:i",time()-$requesttime)||$session['user']['clanrank'] < CLAN_LEADER && $session['user']['clanjoindate'] < date("Y-m-d H:i",time()-$requesttime)) {
		if (get_module_setting("allowgemsinvault")==1 and get_module_setting("allowgoldinvault")==1) {
addnav("Request Gold/Gems","runmodule.php?module=clanvault&op=request&action=form");
		} else {
if ($session['user']['clanjoindate'])
{
	if (get_module_setting("allowgoldinvault")==1)
		addnav("Request Gold","runmodule.php?module=clanvault&op=request&action=form");
	if (get_module_setting("allowgemsinvault")==1)
		addnav("Request Gems","runmodule.php?module=clanvault&op=request&action=form");
}
		}
	}
	addnav("~");
	addnav("Return to your Guild","clan.php");
	villagenav();
	output("`nThere's a little book for recording the status of the vault.`n");
	output("`nLast markings in the book are:`n`n");
	if (get_module_setting("allowgoldinvault")==1) {
		output("`^Gold: `&%s",$gold);
		if ($gold == $MAXAMOUNTOFGOLD) {
output("(Full)");
		}
	}
	output_notl("`n");
	if (get_module_setting("allowgemsinvault")==1) {
		output("`@Gems: `&%s",$gems);
		if ($gems == $MAXAMOUNTOFGEMS) {
output("(Full)");
		}
	}
}
$requests=0;
for($i=1;$i<11;$i++) {
	if (($request = get_module_objpref("clans", $session['user']['clanid'], "request".$i, "clanvault"))!="empty") {
		if ($temp[3] == $session['user']['acctid']) {
$requests++;
		}
	}
}
if ($requests>0) {
	addnav("Cancel all of your Requests","runmodule.php?module=clanvault&op=cancel");
}
?>