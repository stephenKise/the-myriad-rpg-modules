<?php
	// If you enter the shop from the editor, you want to exit into the village
	// that has the shop and not the village you were last in before entering the grotto.
	$session['user']['location'] = get_module_setting('petshoploc');

	if( httpget('loc') == 'village' )
	{
		output("`3Stepping into the small shop, your ears are greeted by the sounds of barking dogs, singing birds, and other curious animal noises. ");
		output("A small woman standing behind a counter near the door gives you a smile as you approach.`n`n");

		if( empty($allprefs['haspet']) )
		{
			output("`#\"Welcome to my shop, friend. My name is %s`#\"`n`n", $ownersname);
		}
		else
		{
			output("`#\"Welcome back %s`#, and you as well %s`#\" `3%s `3adds as she pats %s on %s head.`n`n", $session['user']['name'], $allprefs['petname'], $ownersname, genders($allprefs['petgender'], 3), genders($allprefs['petgender'], 1));
		}

		// Hook will only be called when you enter from the village.
		$args = array('petshopname'=>$petshopname,'ownersname'=>$ownersname,'allprefs'=>$allprefs);
		modulehook('petshop', $args);
	}
	else
	{
		output("`3The small shop is filled with the sounds of barking dogs, singing birds, and other curious animal noises. ");
		output("A small woman standing behind a counter near the door gives you a smile as you approach.`n`n");
		output('`#%s `3looks at you, ', $ownersname);
	}

	output('`#"How may I be of service to you?" `3she asks.`0`n`n');

	addnav('Options');

	if( $allprefs['giftid'] > 0 )
	{
		output("`^\"%s`^!\" `3%s `3exclaims, `#\"Someone has bought you a lovely gift!\"`0`n`n", $session['user']['name'], $ownersname);
		addnav('`^Examine Gift!','runmodule.php?module=petshop&op=petdetail&gift=yes');
	}

	addnav('Pets For Sale','runmodule.php?module=petshop&op=viewpets&cat=0');
 
	if( $allprefs['haspet'] > 0 )
	{
		if( get_module_setting('checkup') > 0 )
		{
			addnav('Pet Checkup','runmodule.php?module=petshop&op=checkup');
		}
		addnav('Rename Your Pet','runmodule.php?module=petshop&op=petname&what=name');
	}

	addnav('Leave');
	addnav('Exit the Pet Shop','village.php');
?>