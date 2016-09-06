<?php
	if( get_module_setting('givegift') == 0 )
	{
		output("`3The shopkeeper frowns and reluctantly tells you that due to city restrictions, pets can't be given as gifts at this time.`n`n");
		output("`#\"Perhaps another day,\" `3she suggests.`0`n");
	}
	else
	{
		$cat = ( httpget('cat') ) ? httpget('cat') : 0;
		$petid = httpget('petid');

		$what = ( httpget('what') ) ? httpget('what') : 'search';
		$what = ( $what == 'results' &&	httppost('whom') == '' ) ? 'search' : $what;

		switch( $what )
		{
			case 'search':
				$sql = "SELECT petid, pettype, petrace, petdk, valuegold, valuegems
						FROM " . db_prefix('pets') . "
						WHERE petid = '$petid'";
				$result = db_query($sql);
				$row = db_fetch_assoc($result);
	
				if( $session['user']['gold'] < $row['valuegold'] && $session['user']['gems'] < $row['valuegems'] )
				{
					output("`3Sorry, you have neither enough gold or gems to purchase a `6%s `2as a gift.`0`n`n", $row['pettype']);
				}
				elseif( $session['user']['gold'] < $row['valuegold'] )
				{
					output("`3Sorry, you don't have enough gold to purchase a `#%s `3as a gift.`0`n`n", $row['pettype']);
				}
				elseif( $session['user']['gems'] < $row['valuegems'] )
				{
					output("`3Sorry, you don't have enough gems to purchase a `#%s `3as a gift.`0`n`n", $row['pettype']);
				}
				else
				{
					// If the player can afford to, let's allow them to search
					output("`3You have chosen to give a `#%s `3as a gift to another player.`n`n", $row['pettype']);
					output("To whom would you like to give it?`n`n");

					$submit = translate_inline('Search Players');
					rawoutput('<form action="runmodule.php?module=petshop&op=giftpet&what=results&petid='.$petid.'&dk='.$row['petdk'].'&race='.$row['petrace'].'" method="POST">');
					addnav('','runmodule.php?module=petshop&op=giftpet&what=results&petid='.$petid.'&dk='.$row['petdk'].'&race='.$row['petrace']);	
					rawoutput('<input type="text" name="whom" size="25" />&nbsp;<input type="submit" value="'.$submit.'"></form>');
				}
			break;

			case 'results':
				$whom = htmlentities(strip_tags(httppost('whom')), ENT_QUOTES);
				$dkreq = httpget('dk');
				$petrace = httpget('race');

				$sql = "SELECT a.acctid, a.name, a.race, a.level, a.dragonkills, b.value
						FROM " . db_prefix('accounts') . " a, " . db_prefix('module_userprefs') . " b
						WHERE b.modulename = 'petshop'
							AND b.setting = 'allprefs'
							AND a.acctid = b.userid
							AND a.acctid <> '" . $session['user']['acctid'] . "'
							AND a.locked = 0
							AND (a.name OR a.login LIKE '%$whom%')
						ORDER BY a.level, a.login";
				$result = db_query($sql);
				$count = db_num_rows($result);

				if( empty($count) )
				{
					output("`n`3Couldn't find a user by that name. Try again.`0`n");
				}
				else
				{
					$pass = translate_inline(array('`@Pass`0','`$Fail`0'));
					$yesno = translate_inline(array('Yes','No'));
					$name = translate_inline('Name');
					$level = translate_inline('Level');
					$haspet = translate_inline('Has Pet');
					$dkreq = translate_inline('DK Requirement');
					$loyal = translate_inline('Race Requirement');
					$gift = translate_inline('Gift');
					$confirm = translate_inline('Confirm');

					rawoutput('<table border="0" cellpadding="2" cellspacing="1" align="center">');
					rawoutput("<tr class=\"trhead\"><td>$name</td><td align=\"center\">$level</td><td align=\"center\">$haspet</td><td align=\"center\">$dkreq</td><td align=\"center\">$loyal</td><td align=\"center\">$gift</td></tr>");
					$i = 1;
					while( $row = db_fetch_assoc($result) )
					{
						$giftprefs = unserialize($row['value']);
						if( !is_array($giftprefs) )
						{
							$giftprefs = get_allprefs();
						}

						$ownpet = ( $giftprefs['haspet'] == 1 ) ? $yesno[1] : $yesno[0];
						$dkmet = ( $row['dragonkills'] >= $dkreq ) ? $pass[0] : $pass[1];
						$racemet = ( $row['race'] == $petrace || $petrace == 'All' ) ? $pass[0] : $pass[1];
						$gifted = ( empty($giftprefs['giftid']) ) ? $pass[0] : $pass[1];

						rawoutput('<tr class="'.($i%2?'trlight':'trdark').'"><td>');
						output_notl('%s', $row['name']);
						rawoutput('</td><td align="center">'.$row['level'].'</td><td align="center">'.$ownpet.'</td><td align="center">'.$dkmet.'</td><td align="center">'.$racemet.'</td><td align="center">');

						if( $gifted == $pass[0] && $dkmet == $pass[0]  && $racemet == $pass[0] )
						{
							rawoutput('<a href="runmodule.php?module=petshop&op=giftpet&what=confirm&petid='.$petid.'&playerid='.$row['acctid'].'">'.$confirm.'</a></td></tr>');
							addnav('','runmodule.php?module=petshop&op=giftpet&what=confirm&petid='.$petid.'&playerid='.$row['acctid']);
						}
						else
						{
							rawoutput('-</td></tr>');
						}

						if( $gifted == $pass[1] )
						{
							rawoutput('<tr class="'.($i%2?'trlight':'trdark').'"><td colspan="6">');
							output('`3This player has a gifted pet waiting for them already.`0');
							rawoutput('</td></tr>');
						}
						$i++;
					}
					rawoutput('</table><br /><br />');
					output('`3As you look at the list, `#%s `3informs you that each breed of pet comes with requirements and that the person you\'re gifting the pet to must meet these requirements. `#"When you\'re ready just confirm."`0`n', get_module_setting('ownersname'));
				}

				addnav('Option');
				addnav('Search Again?','runmodule.php?module=petshop&op=giftpet&cat='.$cat.'&what=search&petid='.$petid);
			break;

			case 'confirm':
				$playerid = httpget('playerid');

				$sql = "SELECT pettype, valuegold, valuegems
						FROM " . db_prefix('pets') . "
						WHERE petid=  '$petid'";
				$result = db_query($sql);
				$pet = db_fetch_assoc($result);

				$session['user']['gold'] -= $pet['valuegold'];
				$session['user']['gems'] -= $pet['valuegems'];

				$sql = "SELECT acctid, name,  sex
						FROM " . db_prefix('accounts') . "
						WHERE acctid = '$playerid'";
				$result = db_query($sql);
				$row = db_fetch_assoc($result);

				output("`#%s `3congratulates you, `#\"You've bought `Q%s `#a `Q%s `#as a gift! ", $ownersname,  $row['name'], $pet['pettype']);
				output("I'll send %s a YoM with details on how to collect it.\"`0`n`n", genders($row['sex'], 3));

				$allprefs = get_allprefs($playerid);
				$allprefs['giftid'] = $petid;
				set_module_pref('allprefs',serialize($allprefs),'petshop',$playerid);

				require_once('lib/systemmail.php');
				$subject = translate_mail("`#You've Been Bought a Pet!`0");
				$message = translate_mail(array('`QDear %s`Q,`n`n`q%s `Qhas bought you a %s`Q!`n`nPlease stop by the %s `Qin `q%s `Qto collect it.`n`nYou will lose your gift if you slay the Dragon before then.`n`nSincerely, %s', $row['name'], $session['user']['name'], $pet['pettype'], $petshopname, get_module_setting('petshoploc'), $ownersname));
				systemmail($row['acctid'], $subject, $message);
			break;

			case 'reject':
				$sql = "SELECT pettype
						FROM " . db_prefix('pets') . "
						WHERE petid = '{$allprefs['giftid']}'";
				$result = db_query($sql);
				$row = db_fetch_assoc($result);
				
				output("`#%s `3nods with a slight look of disappointment on her face.`n`n", $ownersname);
				output("`#\"A bit sad, really,\" `3she says, `#\"You could have given this %s `#a good home.\"`0`n`n", $row['pettype']);
				$allprefs['giftid'] = 0;
				set_module_pref('allprefs',serialize($allprefs));
			break;
		}
	}

	addnav('View');
	addnav('Pets For Sale','runmodule.php?module=petshop&op=viewpets&cat='.$cat);
	addnav('Back');
	addnav('Go Back','runmodule.php?module=petshop');
?>