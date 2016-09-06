<?php
	$memberlow = get_module_objpref('clans', $session['user']['clanid'], 'memberlow', 'clanvault');

//RPGee.com - set currentmem clan pref
	if (get_module_setting('smartid') || get_module_setting('smartip'))
	{
		$sql="SELECT COUNT(clanid) AS members FROM ".db_prefix("accounts")." WHERE clanid=".$session['user']['clanid']." AND clanrank>0";
		$resultx= db_query($sql);
		$row=db_fetch_assoc($resultx);
 		$resultx=$row['members'];
		if ($resultx <> get_module_objpref('clans', $session['user']['clanid'], 'currentmem', 'clanvault'))
		{
			set_module_objpref('clans', $session['user']['clanid'], 'memberlow', 9999);
		}
		set_module_objpref('clans', $session['user']['clanid'], 'currentmem', $resultx);
	}
//END RPGee.com

	if (get_module_setting('vaultbonusgold') || get_module_setting('vaultbonusgems')) {
		if (get_module_setting('smartid') && get_module_setting('smartip')) {
			$sql="SELECT COUNT(clanid) AS members FROM ".db_prefix("accounts")." WHERE clanid=".$session['user']['clanid']." AND clanrank>0 AND uniqueid<>'".$session['user']['uniqueid']."' AND lastip NOT LIKE '".$session['user']['lastip']."'";
			$plus=1;
 		} elseif (get_module_setting('smartid') && !get_module_setting('smartip')) {
 			$sql="SELECT COUNT(clanid) AS members FROM ".db_prefix("accounts")." WHERE clanid=".$session['user']['clanid']." AND clanrank>0 AND uniqueid<>'".$session['user']['uniqueid']."'";
			$plus=1;
		} elseif (!get_module_setting('smartid') && get_module_setting('smartip')) {
			$sql="SELECT COUNT(clanid) AS members FROM ".db_prefix("accounts")." WHERE clanid=".$session['user']['clanid']." AND clanrank>0 AND lastip NOT LIKE '".$session['user']['lastip']."'";
			$plus=1;
		} else {
			$sql="SELECT COUNT(clanid) AS members FROM ".db_prefix("accounts")." WHERE clanid=".$session['user']['clanid']." AND clanrank>0";
		}
 		$result= db_query($sql);
 		$row=db_fetch_assoc($result);
 		$memberstotal=$row['members'] + $plus;
		$memberlow=get_module_objpref('clans', $session['user']['clanid'], 'memberlow', 'clanvault');
 		if ($memberstotal < $memberlow) {
			set_module_objpref('clans', $session['user']['clanid'], 'memberlow', $memberstotal);
 		}
		$memberlow=get_module_objpref('clans', $session['user']['clanid'], 'memberlow', 'clanvault');
		if ($memberlow >= get_module_setting('minmembersgold') && get_module_setting('vaultbonusgold')) {
	 		$membersgold = $row['members'] + $plus;
	 		$membersgold *= get_module_setting('vaultpermembergold');
	 	}
	 	if ($memberlow >= get_module_setting('minmembersgems') && get_module_setting('vaultbonusgems')){
	 		$membersgems 	= $row['members'] + $plus;
	 		$membersgems 	*= get_module_setting('vaultpermembergems');
	 	}
	 } else {
	 	$membersgold 	= 0;
	 	$membersgems 	= 0;
	 }
	$MAXAMOUNTOFGOLD = get_module_setting("maxgoldinvault") + $membersgold;
	$MAXAMOUNTOFGEMS = get_module_setting("maxgemsinvault") + $membersgems;
	$ranks = array(CLAN_APPLICANT=>"`!Applicant`0",CLAN_MEMBER=>"`#Member`0",CLAN_OFFICER=>"`^Officer`0",CLAN_LEADER=>"`&Leader`0");
	$ranks = translate_inline($ranks);
	$gold = get_module_objpref("clans", $session['user']['clanid'], "vaultgold", "clanvault");
	if ($gold == "") $gold=0;
	$gems = get_module_objpref("clans", $session['user']['clanid'], "vaultgems", "clanvault");
	if ($gems == "") $gems=0;
	$op = httpget("op");
	if (!isset($_POST['action'])) {
		$action = httpget("action");
	}
	$reqnumb=0;
	for($i = 0; $i < 10; $i++) {
		if (get_module_objpref("clans", $session['user']['clanid'], "request".($i+1), "clanvault")!="empty")
			$reqnumb++;
	}
?>