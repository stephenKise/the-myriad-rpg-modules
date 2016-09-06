<?php
	$petid = httpget('petid');

	if( ($session['user']['level'] < get_module_setting('selllevel')) || empty($petid) )
	{
		output("`#%s `3tells you that she isn't buying any pets at this time and that you should try asking again at a later date.`0`n`n", $ownersname);

		addnav('Back');
		addnav('Go Back','runmodule.php?module=petshop');
	}
	else
	{
		$sql = "SELECT valuegold, valuegems
				FROM " . db_prefix('pets') . "
				WHERE petid = '$petid'";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);

		$sellpricegold = round($row['valuegold']/2);
		$sellpricegems = round($row['valuegems']/2);
		$gems = translate_inline(array('gem','gems'));

		output("`3With a twinge of regret, you inquire about selling `#%s`3. ", $allprefs['petname']);
		output("`#%s `3asks if you're sure, and quotes you a price of `^%s gold `3and `% %s %s`3.`0`n`n", $ownersname, $sellpricegold, $sellpricegems, ($sellpricegems==1?$gems[0]:$gems[1]));

		addnav('Sell');
		addnav('Yes','runmodule.php?module=petshop&op=finalsell&choice=yes');
		addnav('No','runmodule.php?module=petshop&op=finalsell&choice=no');
	}
?>