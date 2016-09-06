<?php
	$choice = httpget('choice');
	if( $choice == 'yes' )
	{
		$sql = "SELECT valuegold, valuegems
				FROM " . db_prefix("pets") . "
				WHERE petid = '{$allprefs['haspet']}'";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);

		output("`3You've come to the decision you would like to sell your pet. ");
		output("You relunctantly say goodbye to `#%s `3as `#%s `3hands you your money.`n`n", $allprefs['petname'], $ownersname);
		output("You wonder suddenly if you made the right choice.`0`n`n");

		$session['user']['gold'] += round($row['valuegold']/2);
		$session['user']['gems'] += round($row['valuegems']/2);

		$allprefs = get_allprefs();
		set_module_pref('allprefs',serialize($allprefs));
	}
	else
	{
		output("`3You decide not to sell as you just can't bear to part with `#%s `3at the moment.`0`n`n", $allprefs['petname']);
	}

	addnav('Back');
	addnav('Go Back','runmodule.php?module=petshop');
	addnav('Leave');
	addnav('Exit the Pet Shop','village.php');
?>