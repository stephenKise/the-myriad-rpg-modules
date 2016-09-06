<?php
/**
	Got upkeepgems code to add in here somewhere...

*/
	if( $allprefs['haspet'] > 0 )
	{
		output("`n`2Your pet awakens and is ready for new adventures.`n");

		$sql = "SELECT petrace, petage, petturns, upkeepgold, upkeepgems
				FROM " . db_prefix('pets') . "
				WHERE petid = '{$allprefs['haspet']}'";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);

		if( $session['user']['race'] != $row['petrace'] && $row['petrace'] != 'All' )
		{
			output("`\$It takes one look at you and runs away with its tail between its legs, because it fears your new race!`0`n");
			debug("Your Race: ".$session['user']['race']." Pet Race: ".$row['petrace']);
			$allprefs = get_allprefs();
		}
		else
		{
			$alive = TRUE;
			if( get_module_setting('oldage') == 1 && $allprefs['petage'] > $row['petage'] && rand(1,get_module_setting('oldagechance')) == 1 )
			{
				output("`\$You call to %s `\$to wake, but %s doesn't. You look closer and see that your faithful pet has died of old age!`0`n", $allprefs['petname'], genders($allprefs['petgender'], 2));
				$turnslost = get_module_setting('turnslost');
				if( $session['user']['turns'] > $turnslost && $turnslost > 0 )
				{
					output("`\$You lose some time mourning %s`$'s passing. %s was the best %s you've ever had.`0`n`n", $allprefs['petname'], ucfirst(genders($allprefs['petgender'], 2)), $allprefs['pettype']);
					$session['user']['turns'] -= $turnslost;
				}
				$allprefs = get_allprefs();
				$alive = FALSE;
			}

			if( $alive == TRUE )
			{
				// Pet gets one more day older.
				$allprefs['petage']++;
				$allprefs['checkup'] = 0;

				if( $row['petattack'] > 1 && $row['petturns'] > 0 )
				{
					$allprefs['petturns'] = $row['petturns'];
				}

				$playergold = $session['user']['gold'] + $session['user']['goldinbank'];
				$gems = translate_inline(array('gem','gems'));

				$neglect = FALSE;
				if( $row['upkeepgold'] > 0 && $row['upkeepgems'] > 0 )
				{	// Upkeep requires both gold and gems.

					if( $playergold >= $row['upkeepgold'] && $session['user']['gems'] >= $row['upkeepgems'] )
					{	// Player has enough gold and gems.

						output('`2It costs you `^%s gold `2and`% %s %s `2to maintain your pet today. ', $row['upkeepgold'], $row['upkeepgems'], ($row['upkeepgems']==1?$gems[0]:$gems[1]));
						$session['user']['gems'] -= $row['upkeepgems'];
						if( $session['user']['gold'] >= $row['upkeepgold'] )
						{
							$session['user']['gold'] -= $row['upkeepgold'];
						}
						else
						{
							output('Some gold had to be taken from your bank.');
							$row['upkeepgold'] -= $session['user']['gold'];
							$session['user']['gold'] = 0;
							$session['user']['goldinbank'] -= $row['upkeepgold'];
						}
						output_notl('`0`n');
					}
					else
					{	// Player hasn't enough of one or the other.
						$neglect = TRUE;
					}
				}
				elseif( $row['upkeepgold'] == 0 && $row['upkeepgems'] > 0 )
				{	// Upkeep requires only gems.

					if( $session['user']['gems'] >= $row['upkeepgems'] )
					{	// Player has enough gems.

						output('`2It costs you `% %s %s `2to maintain your pet today.`0`n', $row['upkeepgems'], ($row['upkeepgems']==1?$gems[0]:$gems[1]));
						$session['user']['gems'] -= $row['upkeepgems'];
					}
					else
					{	// Player hasn't enough gems.
						$neglect = TRUE;
					}
				}
				elseif( $row['upkeepgold'] > 0 && $row['upkeepgems'] == 0 )
				{	// Upkeep requires only gold.

					if( $playergold >= $row['upkeepgold'] )
					{	// Player has enough gold.

						output('`2It costs you `^%s gold `2to maintain your pet today. ', $row['upkeepgold']);
						if( $session['user']['gold'] >= $row['upkeepgold'] )
						{
							$session['user']['gold'] -= $row['upkeepgold'];
						}
						else
						{
							output('Some gold had to be taken from your bank.');
							$row['upkeepgold'] -= $session['user']['gold'];
							$session['user']['gold'] = 0;
							$session['user']['goldinbank'] -= $row['upkeepgold'];
						}
						output_notl('`0`n');
					}
					else
					{	// Player hasn't enough of one or the other.
						$neglect = TRUE;
					}
				}
				else
				{	// No upkeep costs.
					output('`2You have a pet that takes care of itself. How about that.`0`n');
				}

				// Has pet been neglected?
				if( $neglect == TRUE )
				{
					if( $allprefs['neglect'] == 0 )
					{	// Give player one free pass.

						output("`^You can't afford to feed your pet today! However, you do manage to find something meager for %s `^to eat.`0`n", $allprefs['petname']);
						$allprefs['neglect'] = 1;
					}
					else
					{
						output("`^You can't afford to feed your pet today! As a result, %s has run away!`0`n", genders($allprefs['petgender'], 2));
						addnews("`3%s`2's pet ran away today due to neglect!`0", $session['user']['name']);
						$allprefs = get_allprefs();
					}
				}
				else
				{	// Make sure this is zero.
					$allprefs['neglect'] = 0;
				}

				// Check they still have a pet.
				if( $allprefs['haspet'] > 0 )
				{
					if( empty($allprefs['special']) )
					{
						$sql = "SELECT newdaymsg
								FROM " . db_prefix('pets') . "
								WHERE petid = '" . $allprefs['haspet'] . "'";
						$result = db_query($sql);
						$row = db_fetch_assoc($result);
						if( !empty($row['newsdaymsg']) )
						{
							pet_messages($row['newsdaymsg'], $allprefs);
						}
					}
					else
					{
						output('`2Your pet %s `2doesn\'t seem to be looking to well. Maybe a checkup is in order.`0`n', $allprefs['pettype']);
					}
				}
			}
		}

		set_module_pref('allprefs',serialize($allprefs));
	}
?>