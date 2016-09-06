<?php
	$name = httppost('petname');
	if( !empty($name) )
	{
		$find = array('\'','"');
		$name = str_replace($find, '', $name);
		$allprefs['petname'] = strip_tags($name);
	}
	$gender = httppost('petgender');
	$allprefs['petgender'] = ( $gender == 1 ) ? 1 : 0;

	set_module_pref('allprefs',serialize($allprefs));

	output('`3You decide to name your new %s %s`3, %s`3!`n`n', strtolower(genders($allprefs['petgender'], 0)), $allprefs['pettype'], $allprefs['petname']);
	output('`#%s `3offers her congratulates and wishes both you and your pet well. ', $ownersname);

	$sql = "SELECT upkeepgold, upkeepgems
			FROM " . db_prefix('pets') . "
			WHERE petid = '{$allprefs['haspet']}'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	if( $row['upkeepgold'] > 0 || $row['upkeepgems'] > 0 )
	{
		output('`#"Just remember to have enough money each newday to buy %s food, or %s will run away, and a checkup every so often wont hurt either."`0`n`n', genders($allprefs['petgender'], 1), genders($allprefs['petgender'], 2));
	}

	addnav('Back');
	addnav('Go Back','runmodule.php?module=petshop');
	addnav('Leave');
	addnav('Exit the Pet Shop','village.php');
?>