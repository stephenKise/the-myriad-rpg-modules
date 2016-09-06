<?php
	$sql = "SELECT petrace, petattack, petturns, valuegold, valuegems, upkeepgold
			FROM " . db_prefix('pets') . "
			WHERE petid = '{$allprefs['haspet']}'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);

	if( httpget('pay') == 'yes' )
	{
		$payment = $row['upkeepgold'] * 2;
		if( $session['user']['gold'] > $payment )
		{
			output('`3You hand over the `^%s gold `3to `#%s `3which she quickly puts into a money drawer under the counter before leading your %s `3into a back room and closing the door.`n`n', $payment, $ownersname, $allprefs['pettype']);
			output('%s minutes pass before `#%s `3comes out of the room with %s`3, who now looks extremely happy.`n`n', rand(2,40), $ownersname, $allprefs['petname']);

			if( !empty($allprefs['special']) )
			{
				output('`#%s `3exclaims loudly, `#"You\'re not going to believe this, but I found a `@%s `#lodged inside %s\'s `#stomach"`n`n', $ownersname, $allprefs['special'], $allprefs['petname']);
				output('"How on earth did that get in there?" `3she asks with a look that says she probably doesn\'t want to know.`n`n');
				$allprefs['special'] = '';
			}

			if( $session['user']['race'] != $row['petrace'] && $row['petrace'] != 'All' )
			{
				$sellpricegold = round($row['valuegold']/2);
				$sellpricegems = round($row['valuegems']/2);
				$gems = translate_inline(array('gem','gems'));

				output('`#"You may have noticed that this %s `#doesn\'t seem to like you." `3she says to you. `#"That\'s because it\'s not loyal to your Race!" `3she explains. ', $allprefs['pettype']);
				output('`#"I recommend you sell this pet before it runs away. I\'ll give you `^%s gold `#and `% %s %s `#for %s."`n`n', $sellpricegold, $sellpricegems, ($sellpricegems==1?$gems[0]:$gems[1]), genders($allprefs['petgender'], 3));
				debug("Your Race: ".$session['user']['race']." Pet Race: ".$row['petrace']);

				addnav('Sell');
				addnav('Accept','runmodule.php?module=petshop&op=finalsell&choice=yes');
				addnav('Decline','runmodule.php?module=petshop&op=finalsell&choice=no');
			}
			else
			{
				output('`3You thank her for the checkup and head back out into %s with %s `3your %s `3right behind you.`0`n', $session['user']['location'], $allprefs['petname'], $allprefs['pettype']);

				addnav('Leave');
				addnav('Exit the Pet Shop','village.php');
			}

			$session['user']['gold'] -= $payment;
			if( $allprefs['petattack'] > 0 )
			{
				$allprefs['petturns'] = $row['petturns'];
			}
			$allprefs['checkup']++;
			set_module_pref('allprefs',serialize($allprefs));
		}
		else
		{
			output('`#%s `3scolds you for wasting her time. `#"Maybe check that you have the gold on hand next time!"`n`n`3You quickly leave.`0`n', $ownersname);

			addnav('Leave');
			addnav('Exit the Pet Shop','village.php');
		}
	}
	elseif( $allprefs['checkup'] >= get_module_setting('checkup') )
	{
		output('`3Worried about the health of your %s`3. You ask `#%s `3about a checkup.`n`n', $allprefs['pettype'], $ownersname);
		output('`#"There\'s nothing wrong with that %s`#," `3says `#%s `3peering at %s, `#"%s is a fine looking creature in the perfect of health"`n`n', $allprefs['pettype'], $ownersname, genders($allprefs['petgender'], 3), ucfirst(genders($allprefs['petgender'], 2)));

		if( !empty($allprefs['special']) )
		{
			output('`3You\'re still not convinced %s is. Maybe tomorrow you\'ll be able to talk `#%s `3into doing a checkup.`0`n', genders($allprefs['petgender'], 2), $ownersname);
		}
		else
		{
			output('`3Feeling much better, you head back into %s to show off your %s`3.', $session['user']['location'], $allprefs['pettype']);
		}

		addnav('Leave');
		addnav('Exit the Pet Shop','village.php');
	}
	else
	{
		output('`3You explain to `#%s `3as best you can that your %s `3seems to be off %s game.`n`n', $ownersname, $allprefs['pettype'], genders($allprefs['petgender'], 1));
		output('`#%s `3tells you that she isn\'t a veterinarian, but will take a quick look at you pet for a small fee of `^%s gold`3.', $ownersname, ($row['upkeepgold']*2));

		addnav('Pay');
		addnav('Yes','runmodule.php?module=petshop&op=checkup&pay=yes');
		addnav('No','runmodule.php?module=petshop');
	}
?>