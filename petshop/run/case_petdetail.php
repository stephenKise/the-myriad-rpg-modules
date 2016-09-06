<?php
	$gift = httpget('gift');
	$petid = ( $gift == 'yes' ) ? $allprefs['giftid'] : httpget('petid');
	$cat = ( httpget('cat') ) ? httpget('cat') : 0;

	$sql = "SELECT pettype, petrace, petdesc, valuegold, valuegems, upkeepgold, upkeepgems
			FROM " . db_prefix('pets') . "
			WHERE petid = '$petid'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);

	output("`3Noting your interest in the `#%s`3, %s `3gives you more information about it.`n`n", $row['pettype'], $ownersname);

	if( $gift == 'yes' )
	{
		output("`#\"Would you like to accept this gift as your pet?\" `3she asks.`0`n`n");
	}
	else
	{
		output("`#\"Would you like to purchase this pet?\" `3she asks.`0`n`n");
	}

	if( $session['user']['race'] != $row['petrace'] && $row['petrace'] != 'All' )
	{
		output("`#\"I'd recommend you didn't, seeing as how they're not known for their loyalty towards your Race.\" `3finishes `#%s`3.`0`n`n", $ownersname);
	}

	output('`2Breed: `@%s`n', $row['pettype']);
	output('`2Loyal to Race: `@%s`n', $row['petrace']);
	output('`2Cost in Gold: `^%s`n', $row['valuegold']);
	output('`2Cost in Gems:`% %s`n', $row['valuegems']);
	output('`2Cost Per Day in Gold: `^%s`n', $row['upkeepgold']);
	output('`2Cost Per Day in Gems:`% %s`n', $row['upkeepgems']);
	if( $row['petattack'] == 1 )
	{
		output('`#%s `3adds that this pet can be ordered to attack in battle.`n`n', $ownersname);
	}
	elseif( $row['petattack'] == 2 )
	{
		output('`#%s `3adds that this pet will attack on its own accord in battle.`n`n', $ownersname);
	}			

	output('`2Pet Description:`n');
	if( !empty($row['petdesc']) )
	{
		output_notl('`3%s`n`n', $row['petdesc']);	
	}
	else
	{
		output('`3There is no description for this pet.`n`n');
	}

	if( $gift == 'yes' )
	{
		if( $allprefs['haspet'] > 0 )
		{
			if( $session['user']['level'] >= get_module_setting('selllevel') )
			{
				$sql = "SELECT valuegold, valuegems
						FROM " . db_prefix('pets') . "
						WHERE petid = '{$allprefs['haspet']}'";
				$result = db_query($sql);
				$row = db_fetch_assoc($result);

				$sellpricegold = round($row['valuegold']/2);
				$sellpricegems = round($row['valuegems']/2);

				$gems = translate_inline(array('gem','gems'));

				output("`#%s `3informs you that by accepting this gift, you will be selling your current pet as you can only have one at a time. ", $ownersname);
				output("She then quotes you a price of `^%s gold `3and `% %s %s `3for your %s`3.`0`n`n", $sellpricegold, $sellpricegems, ($sellpricegems==1?$gems[0]:$gems[1]), $allprefs['pettype']);
			}
			else
			{
				output("`#%s `3informs you that she isn't buying pets at the moment, but if you accept this gift, she'll take your current one off your hands.`0`n", $ownersname);
			}
		}
		addnav('Choices');
		addnav('Accept Gift','runmodule.php?module=petshop&op=buypet&gift=yes');
		addnav('Reject Gift','runmodule.php?module=petshop&op=giftpet&what=reject');
	}
	else
	{
		addnav('Purchase');
		addnav('Yes','runmodule.php?module=petshop&op=buypet&petid='.$petid);
		addnav('No','runmodule.php?module=petshop&op=viewpets&cat='.$cat);
	}
?>