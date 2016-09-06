<?php
/**
	I was planning on having two files for forest and travel, but both would
	have been identical apart from the odd piece of text. Would have been
	double the work for translators though so I just neutralised the text as
	much as possible and used IF/ELSE statements where I couldn't. :)
*/
	$session['user']['specialinc'] = 'module:petshop';

	$op = httpget('op');

	if( empty($op) )
	{
		// Get random wild pet.
		$sql = "SELECT petid, pettype
				FROM " . db_prefix('pets') . "
				WHERE petwild = 1
				ORDER BY RAND() LIMIT 1";
	}
	else
	{
		// Get wild pet's data.
		$sql = "SELECT petid, pettype, petrace, petcharm, petturns, petattack, mindamage, maxdamage
				FROM " . db_prefix('pets') . "
				WHERE petid = '" . $allprefs['wildpet'] . "'";
	}
	$result = db_query($sql);
	$row = db_fetch_assoc($result);

	if( empty($row['petid']) || (empty($op) && rand(1,3) == 1) )
	{
		// Incase no wild animals are returned, exit stage left.
		// Or you want to give their pet a special.
		$op = 'wind';
	}

	if( $type == 'forest')
	{
		$eventtype = translate_inline('return to searching the forest for creatures');
	}
	else
	{
		$eventtype = translate_inline('continue on your travels');
	}

	if( $op == '' )
	{
		switch( rand(1,4) )
		{
			case 1:
				if( $type == 'forest')
				{
					output('`2While venturing through the forest, ');
				}
				else
				{
					output('`2Whilst travelling towards %s, ', httpget('city'));
				}
				output('you stumble upon a small clearing. You see a `@%s `2resting in the shade of a towering oak tree!`n`n', $row['pettype']);
			break;

			case 2:
				output('`2Stopping for a moment at the shore of a river to refill your canteen, '); 
				output('you happen to notice a `@%s `2nearby grabbing a drink from the chilled waters.`n`n', $row['pettype']);
			break;

			case 3:
				output('`2Taking a moment to rest your sore feet, you take a seat upon the trunk of a fallen tree. ');
				output('Looking ahead a ways, you happen to catch sight of a `@%s `2staring back at you quizically.`n`n', $row['pettype']);
			break;

			case 4:
				if( $type == 'forest')
				{
					output('`2While following a trail that leads deeper into the forest, ');
				}
				else
				{
					output('`2While following a trail that takes you on a short cut to %s, ', httpget('city'));
				}
				output('a `@%s `2wanders across your path. It sits on a low tree branch, silently staring into your eyes.`n`n', $row['pettype']);
			break;
		}
		output('Would you like to approach this animal?`0`n`n', $row['pettype']);

		$allprefs['wildpet'] = $row['petid'];
		set_module_pref('allprefs',serialize($allprefs));

		addnav('Approach Animal');
		addnav('Yes',$from.'op=approach');
		addnav('No',$from.'op=leavealone');
	}
	elseif( $op == 'approach' )
	{
		$caught = httpget('caught');

		if( $allprefs['haspet'] > 0 && rand(1,3) == 1 && empty($caught) )
		{	// Your pet scares the animal away.
			output('`2Weary of your %s`2, the %s `2takes off through some bushes and out of sight. ', $allprefs['pettype'], $row['pettype']);
			output('You know giving chase would be futile, so you %s.`0`n`n', $eventtype);
			$session['user']['specialinc'] = '';
		}
		else
		{
			$rand = ( empty($caught) ) ? rand(1,4) : $caught;
			switch( $rand )
			{
				case 1: 
					output('`2The %s `2stares at you for a moment, and allows you to approach. ', $row['pettype']);
					if( $allprefs['haspet'] > 0 )
					{
						output('You compare it to your own %s `2and wonder if it will make a better pet.`n`n', $allprefs['pettype']);
					}
					else
					{
						output('It seems quite tame, and would make for a nice pet you think.`n`n');
					}
					output('Would you like to keep the %s`2?`0`n`n', $row['pettype']);

					addnav('Keep');
					addnav('Yes',$from.'op=name');
					addnav('No',$from.'op=leavealone');
				break;

				case 2:
					output('`2Suddenly, the %s `2grows frightened of you and takes off through some bushes and out of sight. ', $row['pettype']);
					output('It doesn\'t look very fast, and there\'s a chance you just might be able to catch it if you put enough time into the chase.`n`n', $allprefs['pettype']);
					output('Would you like to chase the %s`2?`0`n`n', $row['pettype']);

					addnav('Give Chase');
					addnav('Yes',$from.'op=givechase');
					addnav('No',$from.'op=leavealone');
				break;

				case 3:
					output('`2Suddenly, the %s `2grows frightened of you and takes off through some bushes and out of sight. ', $row['pettype']);
					output('You give chase for a moment, but realize it\'s futile as the animal is long gone by now.`n`n');
					output('With a brief sigh, you %s.`n`n', $eventtype);
					$session['user']['specialinc'] = '';
				break;

				case 4:
					output('`2Approaching closer, you extend your hand towards the %s`2. ', $row['pettype']);
					output('However, the %s `2suddenly grows fearful and snaps at your hand before taking off into the forest!`n`n', $row['pettype']);
					output('As you nurse the wound on your wrist, you curse the animal.`0`n`n');

					addnews('`@%s `2got too close to a wild %s `2and was hurt.`0', $session['user']['name'], $row['pettype']);

					$session['user']['hitpoints'] -= round($session['user']['hitpoints'] * 0.15);

					if( $session['user']['hitpoints'] < 1 )
					{
						$session['user']['hitpoints'] = 1;
					}
					$session['user']['specialinc'] = '';
				break;
			}
		}
	}
	elseif( $op == 'givechase' )
	{
		if( $session['user']['turns'] > 0 )
		{
			output('`2You run wildly after the %s`2, closing the distance pretty quickly. You reach out your hands to grab it, ', $row['pettype']);
			if( rand(1,3) == 1 )
			{
				output('and `@successfully catch it!`n`n`2You hold the %s `2up in the air and grin broadly.`n`n', $row['pettype']);
				output('It doesn\'t seem that wild up close so you decide to let it go and see if it will accept you, now that it knows you mean it no harm.');
				addnav('Release it',$from.'op=approach&caught=1');
			}
			else
			{
				output('but it swiftly changes direction and you miss it.`n`nWill you keep chasing it?`0`n`n');

				addnav('Keep Chasing');
				addnav('Yes',$from.'op=givechase');
				addnav('No',$from.'op=leavealone&giveup=1');
			}
			$session['user']['turns']--;
		}
		else
		{
			output('`2You start to give chase, but find yourself tiring quickly. After a few minutes you give up.`0`n`n');
			$session['user']['specialinc'] = '';
		}
	}
	elseif( $op == 'name' )
	{
		$gender = rand(0,1);
		output('`2Looking at the %s`2, you try to think of a good name for %s.`0`n`n', $row['pettype'], genders($gender, 3));

		$submit = translate_inline('Named');
		rawoutput('<form action="'.$from.'op=named&sex='.$gender.'" method="POST">');
		addnav('',$from.'op=named&sex='.$gender);
		rawoutput('<input type="text" name="petname" size="30" maxlength="30" value="" />&nbsp;<input type="submit" value="'.$submit.'" /></form>');

		addnav('Skip',$from.'op=named&sex='.$gender);
	}
	elseif( $op == 'named' )
	{
		$row['petname'] = translate_inline('`&F`7i`&d`7o');
		$name = httppost('petname');
		if( !empty($name) )
		{
			$find = array('\'','"');
			$name = str_replace($find, '', $name);
			$row['petname'] = strip_tags($name);
		}
		$row['petgender'] = httpget('sex');

		output('`2You decide to name your new pet `@%s`2!`n`n', $row['petname']);
		output('Seeming content with %s name, your new pet follows as you %s.`n`n', genders($row['petgender'], 1), $eventtype);

		if( $allprefs['haspet'] > 0 )
		{
			output('`@%s `2sits on the ground and watches the two of you leave. A single tear falls from %s eye.`n`n', $allprefs['petname'], genders($allprefs['petgender'], 1));
			if( !empty($allprefs['special']) )
			{
				output('`2Weeks from now, a traveller will tell a story in the %s `2about how he came across a dead %s `2in a clearing and how he found the cause of death to be a %s `2in its stomach.`0`n`n', getsetting('innname', LOCATION_INN), $allprefs['pettype'], $allprefs['special']);
			}

			if( $type == 'forest' )
			{
				addnews('`@%s `2abandoned his %s `2in the forest today for a %s `2that %s found!`0', $session['user']['name'], $allprefs['pettype'], $row['pettype'], genders($session['user']['sex'], 2));
			}
			else
			{
				addnews('`@%s `2abandoned his %s `2whilst travelling today for a %s `2that %s found!`0', $session['user']['name'], $allprefs['pettype'], $row['pettype'], genders($session['user']['sex'], 2));
			}
		}
		else
		{
			if( $type == 'forest' )
			{
				addnews('`@%s `2discovered a `@%s `2in the forest today and kept %s as a pet!`0', $session['user']['name'], $row['pettype'], genders($allprefs['petgender'], 3));
			}
			else
			{
				addnews('`@%s `2discovered a `@%s `2whilst travelling today and kept %s as a pet!`0', $session['user']['name'], $row['pettype'], genders($allprefs['petgender'], 3));
			}

			if( $row['petcharm'] > 0 )
			{	// Only give charm if they haven't abandoned a pet.
				output('`@%s `2makes you feel more charming.`0`n`n', $row['petname']);
				$session['user']['charm'] += $row['petcharm'];
			}
		}

		$row['haspet'] = $allprefs['wildpet'];
		$row['wildpet'] = $allprefs['wildpet'];

		$allprefs = get_allprefs(FALSE, $row);
		set_module_pref('allprefs',serialize($allprefs));

		$session['user']['specialinc'] = '';
	}
	elseif( $op == 'leavealone' )
	{
		output('`2Deciding to leave the wild animal alone, you %s.`n`n', $eventtype);
		output('The %s `2stares at you a moment before disappearing through some bushes and out of sight.`0`n`n', $row['pettype']);

		if( httpget('giveup') == 1 )
		{
			if( $type == 'forest' )
			{
				addnews('`@%s `2gave up chasing a wild %s `2in the forest.`0', $session['user']['name'], $row['pettype']);
			}
			else
			{
				addnews('`@%s `2gave up chasing a wild %s `2while travelling.`0', $session['user']['name'], $row['pettype']);
			}
		}
		$session['user']['specialinc'] = '';
	}
	elseif( $op == 'wind' )
	{
		if( $allprefs['haspet'] > 0 )
		{
			// Each name should be no longer than 50 characters!
			$special = translate_inline(array('`QR`^ubber `QD`^uck','`eLarge Twig','`lDinosaur `&Bone','`$S`&q`$u`&e`$a`&k`$y `LT`&o`Ly','`2Z`7o`2m`7b`2i`7e `3H`7e`3a`7d','`qBar of Chocolate','`RM`vartha `RS`vtewart `7C`&ookery `7B`&ook','`%P`5ari`Vs `%H`5ilto`Vn'));
			$key = array_rand($special);
			switch( rand(1,3) )
			{
				case 1:
					output('`@%s `2heads off into some bushes and comes back a short while later carrying a %s`2.', $allprefs['petname'], $special[$key]);
				break;

				case 2:
					output('`@%s `2sniffs the ground nearby and starts to dig. A short while later %s pulls out a %s`2.', $allprefs['petname'], genders($allprefs['petgende'], 2), $special[$key]);
				break;

				case 3:
					output('`@%s `2comes up to you carrying a %s`2. You have no idea where it came from.', $allprefs['petname'], $special[$key]);
				break;
			}
			$allprefs['special'] = $special[$key];
			set_module_pref('allprefs',serialize($allprefs));

			output('`n`nYou decide to just let %s keep it. After all, what harm could a %s `2possibly do?`0`n', genders($allprefs['petgende'], 3), $special[$key]);

			addnews('`@%s`2\'s pet %s `2found a %s `2in the forest.`0', $session['user']['name'], $allprefs['pettype'], $special[$key]);
		}
		else
		{
			$default_msg = TRUE;
		}
		$session['user']['specialinc'] = '';
	}
?>