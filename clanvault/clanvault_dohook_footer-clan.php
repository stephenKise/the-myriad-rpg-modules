<?php
$vaulttime = get_module_setting('vaulttime') * 3600;

//Added in damnbloat.php if smart vault is being used (less queries if not used)
if (get_module_setting('vaultbonusgold') || get_module_setting('vaultbonusgems'))
{
	//RPGee.com - check for minumum members setting
	$minmem = get_module_setting('minmem');
	require_once("modules/clanvault/clanvault_damnbloat.php");
	$memberlow = get_module_objpref('clans', $session['user']['clanid'], 'memberlow', 'clanvault');
}
//If smart vault not enabled, then we must set the $minmem variable to equal the $memberlow variable so nav shows
else 
{
	$minmem = 0;
	$memberlow = 0;
}
//Added in check for at least X members in the clan before vault is available	when smart vault is enabled

if($memberlow >= $minmem)
{
	if ($session['user']['clanid']!=0 and httpget("op")=="" && $session['user']['clanjoindate'] 
	 < date("Y-m-d H:i",time()-$vaulttime))
	{
		if ($session['user']['clanrank']>0)
		{
					addnav("Finances");
					addnav("Guild Funds","runmodule.php?module=clanvault&op=enter");
		}
	}
}
///END RPGee.com
?>