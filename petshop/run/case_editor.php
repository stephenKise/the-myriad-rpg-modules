<?php
	$userid = httpget('userid');
	$from = 'runmodule.php?module=petshop&op=editor';

	if( !empty($userid) )
	{
		require_once('modules/allprefseditor.php');
		allprefseditor_search();
		modulehook('allprefnavs');

		$yom = FALSE;
		$user_allprefs = get_allprefs($userid);

		if( httpget('subop') == 'save' )
		{
			// Get all post data.
			$postdata = httpallpost();
			foreach( $postdata as $key => $value )
			{
				// Only accept the data that we want.
				if( isset($user_allprefs[$key]) )
				{
					// If you gift someone a Pet then send them a YoM.
					if( $key == 'giftid' )
					{
						if( $value > 0 && $value != $user_allprefs['giftid'] )
						{
							$yom = TRUE;
							$giftid = $value;
						}
					}
					$user_allprefs[$key] = $value;
				}
			}

			if( $postdata['populate'] == 1 )
			{
				$sql = "SELECT pettype, petcharm, petturns, petattack, mindamage, maxdamage
						FROM " . db_prefix('pets') . "
						WHERE petid = '{$user_allprefs['haspet']}'";
				$result = db_query($sql);
				$row = db_fetch_assoc($result);
				$user_allprefs['pettype'] = $row['pettype'];
				$user_allprefs['petcharm'] = $row['petcharm'];
				$user_allprefs['petturns'] = $row['petturns'];
				$user_allprefs['petattack'] = $row['petattack'];
				$user_allprefs['mindamage'] = $row['mindamage'];
				$user_allprefs['maxdamage'] = $row['maxdamage'];
			}
			set_module_pref('allprefs',serialize($user_allprefs),'petshop',$userid);

			output('`#Allprefs Updated`0`n');
		}

		output('`n`3Best way to give a player a pet is just to gift it to them, only touch the other settings if you\'re tweaking a pet they already have, or want to mess with them. Heh.`0`n`n');

		$sql = "SELECT name, race, loggedin
				FROM " . db_prefix('accounts') . "
				WHERE acctid = '$userid'";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$username = $row['name'];
		$user_allprefs['playerrace'] = $row['race'];
		if( $row['loggedin'] == 1 )
		{
			output('`@WARNING: This player is currently `bonline`b!`0`n`n');
		}

		$sql = "SELECT petid, petrace, pettype
				FROM " . db_prefix('pets') . "
				WHERE petid > 0
				ORDER BY petid";
		$result = db_query($sql);
		$types = '';
		$types2 = '';
		$loyalty = '`@Will make a very loyal pet.`0';
		while( $row = db_fetch_assoc($result) )
		{
			$types .= ','.$row['petid'].',('.$row['petid'].') '.full_sanitize($row['pettype']).' ('.$row['pettype'].')';
			$types2 .= ','.$row['petid'].',('.$row['petid'].') '.full_sanitize($row['pettype']);
			if( $row['petid'] == $user_allprefs['haspet'] )
			{
				$user_allprefs['petrace'] = $row['petrace'];
				if( $user_allprefs['playerrace'] != $user_allprefs['petrace'] && $user_allprefs['petrace'] != 'All' )
				{
					$loyalty = '`$Warning: Pet will not be loyal!`0';
				}
			}
		}

		if( $yom == TRUE )
		{
			require_once('lib/systemmail.php');
			$subject = translate_mail("`#You've Been Bought a Pet!`0");
			$message = translate_mail(array('`QDear %s`Q,`n`n`q%s `Qhas bought you a %s`Q!`n`nPlease stop by the %s `Qin `q%s `Qto collect it.`n`nYou will lose your gift if you slay the Dragon before then.`n`nSincerely, %s', $username, $session['user']['name'], $row['pettype'], $petshopname, get_module_setting('petshoploc'), $ownersname));
			systemmail($userid, $subject, $message);
			output('`@A YoM has been sent to the player informing them of the gift waiting for them.`n');
		}

		$user_allprefs['populate'] = 0;

		$form = array(
			"Pet Stats - Main,title",
				"haspet"=>"Has a Pet?,enum,0,No$types",
				"`^(ID) Breed Type (Breed Type with colour tags),note",
				"pettype"=>"Breed:,string,30",
				"petattack"=>"Can Attack?,enum,0,No,1,Yes - Manually,2,Yes - Automatic",
				"mindamage"=>"Min Damage:,int",
				"maxdamage"=>"Max Damage:,int",
				"petturns"=>"Points/Rounds:,int",
				"petrace"=>"Race Loyalty:,viewonly",
				"playerrace"=>"Player Race:,viewonly",
				"$loyalty,note",
				"populate"=>"Populate above fields?,bool",
				"`^If you change the breed then you can populate the fields above with that breed's attack data.,note",
			"Pet Stats - Other,title",
				"petname"=>"Name:,string,30",
				"petgender"=>"Gender:,enum,0,".genders(0,0).",1,".genders(1,0),
				"petage"=>"Age:,int",
				"neglect"=>"Been Neglected?,bool",
				"checkup"=>"How many checkups today?,int",
				"special"=>"Special:,string,50",
				"wildpet"=>"Last WildPet Encounter:,enum,0,None$types2",
				"giftid"=>"Has a gift waiting?,enum,0,No$types2",
		);

		require_once('lib/showform.php');
		rawoutput('<form action="'.$from.'&subop=save&userid='.$userid.'" method="POST">');
		addnav('',$from.'&subop=save&userid='.$userid);
		showform($form,$user_allprefs,TRUE);
		$submit = translate_inline('Save');
		rawoutput('<input type="submit" class="button" value="'.$submit.'" /></form>');

		addnav('Option');
		addnav('Edit user','user.php?op=edit&userid='.$userid);
	}
	else
	{
		$categories = explode('::', trim(get_module_setting('categories')));
		$categories[99] = translate_inline('Storage');

		$op2 = httpget('op2');
		$petid = httpget('petid');
		$cat = ( httpget('cat') ) ? httpget('cat') : 0;

		if( $op2 == 'view' )
		{
			$sql = "SELECT petid, pettype, petwild, petrace, petdk, valuegold, valuegems
					FROM " . db_prefix('pets') . "
					WHERE petcat = '$cat'
					ORDER BY valuegold+0, valuegems+0 DESC, pettype DESC";
			$result = db_query($sql);
			if( db_num_rows($result) > 0 )
			{
				$ops = translate_inline('Ops');
				$name = translate_inline('Breed Name');
				$race = translate_inline('Loyal To Race');
				$petdk = translate_inline('DK Req.');
				$wild = translate_inline('Wild');
				$attacks = translate_inline('Attacks');
				$goldc = translate_inline('Gold cost');
				$gemsc = translate_inline('Gem cost');
				$petview = translate_inline('View');
				$edit = translate_inline('Edit');
				$del = translate_inline('Delete');
				$delconfirm = translate_inline('Are you sure you wish to delete this breed?');
				$gems = translate_inline(array('gem','gems'));
				$yesno = translate_inline(array('Yes','No'));

				output('`b`c`3Below is a Listing of `#%s `3Breeds`0`c`b`n', $categories[$cat]);

				rawoutput('<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center">');
				rawoutput("<tr class=\"trhead\"><td>$ops</td><td>$name</td><td align=\"center\">$race</td><td align=\"center\">$petdk</td><td align=\"center\">$attacks</td><td align=\"center\">$wild</td><td align=\"center\">$goldc</td><td align=\"center\">$gemsc</td></tr>");

				$i = 1;
				while( $row = db_fetch_assoc($result) )
				{
					rawoutput('<tr class="'.($i%2?'trdark':'trlight').'"><td>');
					rawoutput('[<a href="'.$from.'&op2=viewpet&cat='.$cat.'&petid='.$row['petid'].'">'.$petview.'</a>|<a href="'.$from.'&op2=edit&cat='.$cat.'&petid='.$row['petid'].'">'.$edit.'</a>|<a href="'.$from.'&op2=delete&cat='.$cat.'&type='.urlencode($row['pettype']).'&petid='.$row['petid'].'" onClick="return confirm(\''.$delconfirm.'\');">'.$del.'</a>]</td><td>');
					addnav('',$from.'&op2=viewpet&cat='.$cat.'&petid='.$row['petid']);
					addnav('',$from.'&op2=edit&cat='.$cat.'&petid='.$row['petid']);
					addnav('',$from.'&op2=delete&cat='.$cat.'&type='.urlencode($row['pettype']).'&petid='.$row['petid']);
					output_notl('%s', $row['pettype']);
					rawoutput('</td><td align="center">');
					output_notl('%s', $row['petrace']);
					rawoutput('</td><td align="center">');
					output_notl('%s', $row['petdk']);
					rawoutput('</td><td align="center">');
					output_notl('%s', ($row['petattack']==1?$yesno[0]:$yesno[1]));
					rawoutput('</td><td align="center">');
					output_notl('%s', ($row['petwild']==1?$yesno[0]:$yesno[1]));
					rawoutput('</td><td align="center">');
					output_notl('`^%s', $row['valuegold']);
					rawoutput('</td><td align="center">');
					output_notl('`% %s', $row['valuegems']);
					rawoutput('</td></tr>');
					$i++;
				}

				rawoutput('</table><br />');
				rawoutput('<form action="'.$from.'&op2=empty&cat='.$cat.'" method="POST">');
				addnav('',$from.'&op2=empty&cat='.$cat);
				$submit = translate_inline('Delete All Above');
				$delconfirm2 = translate_inline('Are you sure you wish to delete *ALL* breeds in this category?');
				rawoutput('<input type="submit" value="'.$submit.'" onClick="return confirm(\''.$delconfirm2.'\');" /></form>');
			}
			else
			{
				output("`3The `#%s `3category does not have any pets assigned to it.`0`n`n", $categories[$cat]);
				output('`3If you haven\'t already done so, you can install the pets that are included. These are the pets that were created by `#Eth `3and come from his `#\'wildpets\' `3and `#\'extrapets\' `3modules.`n`n');
				output('This link only appears when a category has no entries.`0`n');

				addnav('Extra');
				addnav('`^Install Pets','runmodule.php?module=petshop&op=addpets&cat='.$cat);
			}

			addnav('Option');
			addnav('Add a Pet',$from.'&op2=add');

			addnav('Display');
			if( !empty($categories) )
			{
				foreach( $categories as $key => $value )
				{
					addnav(array('Display %s',$value),$from.'&op2=view&cat='.$key);
				}
			}
			else
			{	// Failsafe.
				addnav('Display Common',$from.'&op2=view&cat=0');
			}
		}
		elseif( $op2 == 'viewpet' )
		{
			$sql = "SELECT *
					FROM " . db_prefix('pets') . "
					WHERE petid = '$petid'";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);

			$search = array('%N','%B','%P','%O');
			$replace = array($row['petname'],$row['pettype'],genders(0,1),genders(0,2));
			$iswild = translate_inline(array('Yes','No'));
			$attack = translate_inline(array('No','Yes - Manually','Yes - Automatic'));

			output('`2Previewing `@`b%s`b`2:`n`n', $row['pettype']);
			output('`2Can be found Wild?: `@%s`n', ($row['petwild']==1?$iswild[0]:$iswild[1]));
			output('`2Cost in Gold: `^%s`n', $row['valuegold']);
			output('`2Cost in Gems:`% %s`n', $row['valuegems']);
			output('`2Cost Per Day in Gold: `^%s`n', $row['upkeepgold']);
			output('`2Cost Per Day in Gems:`% %s`n', $row['upkeepgems']);
			output('`2Available to Race: `@%s`n', $row['petrace']);
			output("`2DK's Needed to Own: `@%s`n", $row['petdk']);
			output('`2Extra charm Granted: `@%s`n', $row['petcharm']);
			output('`2Age limit: `@%s newdays`n', $row['petage']);
			output('`2Pet Description:`n');
			output_notl('`3%s`n`n', $row['petdesc']);
			output('`2Newday Message:`0`n');
			output_notl('%s`n`n', str_replace($search, $replace, $row['newdaymsg']));
			output('`2Village Message:`0`n');
			output_notl('%s`0`n`n', str_replace($search, $replace, $row['villagemsg']));
			output('`2Garden Message:`0`n');
			output_notl('%s`0`n`n', str_replace($search, $replace, $row['gardenmsg']));
			output('`2Battle Message:`0`n');
			output_notl('%s`0`n`n', str_replace($search, $replace, $row['battlemsg']));
			if( $row['petattack'] == 1 )
			{
				output('`2Breed Attacks: `@%s`n', $attack[$row['petattack']]);
				output('`2Points Available: `@%s`n', $row['petturns']);
			}
			elseif( $row['petattack'] == 2 )
			{
				output('`2Breed Attacks: `@%s`n', $attack[$row['petattack']]);
				output('`2Rounds Available: `@%s`n', $row['petturns']);
			}
			else
			{
				output('`2Breed Attacks: `\$%s`n', $attack[$row['petattack']]);
			}
			output('`2Minimum Damage: `@%s`n', $row['mindamage']);
			output('`2Maximum Damage: `@%s`n', $row['maxdamage']);

			addnav('Return');
			addnav('Pet Editor',$from.'&op2=view&cat='.$cat);
		}
		elseif( $op2 == 'edit' || $op2 == 'add' )
		{
			require_once('lib/showform.php');

			$cats = '';
			foreach( $categories as $key => $value )
			{
				$cats .= ",$key,$value";
			}

			$racenames = modulehook('racenames');
			foreach( $racenames as $value )
			{
				$races .= ",$value,$value";
			}
			$blob = translate_inline('Horrible Gelatinous Blob');
			$races .= ",$blob,$blob";

			$petarray = array(
				"Breed Stats and Costs,title",
					"petid"=>"Breed ID:,hidden",
					"pettype"=>"Breed Type:,string,30",
					"petcat"=>"Breed Category:,enum$cats",
					"petwild"=>"Can be found in the wild?,bool",
					"petrace"=>"What race is this breed for?,enum,All,All Races$races",
					"petdk"=>"Dragon Kills Needed to Own:,int",
					"petcharm"=>"Charm granted each newday:,int",
					"petage"=>"Breed age before chance of death:,int",
					"petdesc"=>"Breed Description for Shop:,",
					"valuegold"=>"Cost in Gold:,int",
					"valuegems"=>"Cost in Gems:,int",
					"upkeepgold"=>"Daily Upkeep in Gold:,int",
					"upkeepgems"=>"Daily Upkeep in Gems:,int",
				"Custom Messages,title",
					"These are optional.,note",
					"newdaymsg"=>"New Day Message,string,150",
					"villagemsg"=>"Village Message,string,150",
					"gardenmsg"=>"Garden Message,string,150",
					"battlemsg"=>"Battle Message,string,150",
					"`^eg: `&%N `7the `&%B `7loves `&%P `7food.`n`^eg: `&Fido `7the `&poodle `7loves `&his `7food.`n`n`^%N = Pet's Name`n`@%B = Pet's Breed`n`#%P = Possessive pronoun for the player. (his her)`n`Q%O = Objective pronoun for the player. (he she)`n`n`&Colour codes can also be used.,note",
				"Battle Settings,title",
					"petattack"=>"Can this breed attack?,enum,0,No,1,Yes - Manually,2,Yes - Automatic",
					"mindamage"=>"Min attack damage per round:,int",
					"maxdamage"=>"Max attack damage per round:,int",
					"petturns"=>"This breed gets how many points/rounds?,int",
					"`^When attack is set to `bManual`b the player will get a pet specialty and this value will be used as points.`n
					When attack is set to `bAutomatic`b the player will get a buff each fight that will last this number of rounds.,note", 
			);

			if( $op2 == 'edit' )
			{
				$sql = "SELECT *
						FROM " . db_prefix('pets') . "
						WHERE petid = '$petid'";
				$result = db_query($sql);
				$row = db_fetch_assoc($result);
			}
			else
			{
				$row = array('petid'=>0,'pettype'=>'','petcat'=>0,'petwild'=>0,'petrace'=>'All','petdk'=>1,'petcharm'=>0,'petage'=>100,'petdesc'=>'','petattack'=>0,'petturns'=>0,'mindamage'=>0,'maxdamage'=>0,'valuegold'=>0,'valuegems'=>0,'upkeepgold'=>0,'upkeepgems'=>0,'newdaymsg'=>'','villagemsg'=>'','gardenmsg'=>'','battlemsg'=>'');
			}

			rawoutput('<form action="'.$from.'&op2=save" method="POST">');
			addnav('',$from.'&op2=save');
			showform($petarray,$row);
			rawoutput('</form>');

			addnav('Return');
			addnav('Pet Editor',$from.'&op2=view&cat='.$cat);
		}
		elseif( $op2 == 'save' )
		{
			$postdata = httpallpost();
			foreach( $postdata as $key => $value )
			{
				$$key = $value;
			}

			if( $petid > 0 )
			{
				db_query("UPDATE " . db_prefix('pets') . " SET pettype = '$pettype', petcat = '$petcat', petrace = '$petrace', petwild = '$petwild', petdk = '$petdk', petcharm = '$petcharm', petage = '$petage', petdesc = '$petdesc', petattack = '$petattack', petturns = '$petturns', mindamage = '$mindamage', maxdamage = '$maxdamage', valuegold = '$valuegold', valuegems = '$valuegems', upkeepgold = '$upkeepgold', upkeepgems = '$upkeepgems', newdaymsg = '$newdaymsg', villagemsg = '$villagemsg', gardenmsg = '$gardenmsg', battlemsg = '$battlemsg' WHERE petid = '$petid'");
				output("`3The breed `#%s `3has been successfully edited.`0`n`n", $pettype);
			}
			else
			{
				db_query("INSERT INTO " . db_prefix('pets') . " (pettype, petcat, petrace, petwild, petdk, petcharm, petage, petdesc, petattack, petturns, mindamage, maxdamage, valuegold, valuegems, upkeepgold, upkeepgems, newdaymsg, villagemsg, gardenmsg, battlemsg) VALUES ('$pettype', '$petcat', '$petrace', '$petwild', '$petdk', '$petcharm', '$petage', '$petdesc', '$petattack', '$petturns', '$mindamage', '$maxdamage', '$valuegold', '$valuegems', '$upkeepgold', '$upkeepgems', '$newdaymsg', '$villagemsg', '$gardenmsg', '$battlemsg')");
				output("`3The breed `#%s `3has been saved to the database.`0`n`n", $pettype);
			}

			addnav('Return');
			addnav('Pet Editor',$from.'&op2=view&cat='.$petcat);
		}
		elseif( $op2 == 'delete' )
		{
			$type = urldecode(httpget('type'));

			db_query("DELETE FROM " . db_prefix('pets') . " WHERE petid = '$petid'");
			output("`3The breed `#%s `3has been deleted!`0`n`n", $type);

			addnav('Return');
			addnav('Pet Editor',$from.'&op2=view&cat='.$cat);
		}
		elseif( $op2 == 'empty' )
		{
			db_query("DELETE FROM " . db_prefix('pets') . " WHERE petcat = '$cat'");
			output("`3All breeds in the `#%s `3category have been deleted!`0`n`n", $categories[$cat]);

			addnav('Return');
			addnav('Pet Editor',$from.'&op2=view&cat='.$cat);
		}
		elseif( $op2 == 'players' )
		{
			$sql = "SELECT a.acctid, a.name, b.value
					FROM " . db_prefix('accounts') . " a, " . db_prefix('module_userprefs') . " b
					WHERE b.modulename = 'petshop'
						AND b.setting = 'allprefs'
						AND a.acctid = b.userid";
			$result = db_query($sql);


			$ops = translate_inline('Ops');
			$playername = translate_inline('Player Name');
			$petname = translate_inline('Pet Name');
			$petbreed = translate_inline('Pet Breed');
			$gift = translate_inline('Has Gift');
			$edit = translate_inline('Edit');
			$yesno = translate_inline(array('Yes','No'));

			output('`b`c`3Below is a Listing of Players and their Pets`0`c`b`n');

			rawoutput('<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center">');
			rawoutput("<tr class=\"trhead\"><td>$ops</td><td>$playername</td><td align=\"center\">$petname</td><td align=\"center\">$petbreed</td><td align=\"center\">$gift</td></tr>");
			$i = 1;
			while( $row = db_fetch_assoc($result) )
			{
				if( !empty($row['value']) )
				{
					$player_allprefs = unserialize($row['value']);
					if( $player_allprefs['haspet'] > 0 || $player_allprefs['giftid'] > 0 )
					{
						rawoutput('<tr class="'.($i%2?'trdark':'trlight').'"><td>');
						rawoutput('[<a href="'.$from.'&subop=edit&userid='.$row['acctid'].'">'.$edit.'</a>]</td><td>');
						addnav('',$from.'&subop=edit&userid='.$row['acctid']);
						output_notl('%s', $row['name']);
						rawoutput('</td><td align="center">');
						output_notl('%s', $player_allprefs['petname']);
						rawoutput('</td><td align="center">');
						output_notl('%s', $player_allprefs['pettype']);
						rawoutput('</td><td align="center">');
						output_notl('%s', ($player_allprefs['giftid']>0?$yesno[0]:$yesno[1]));
						rawoutput('</td></tr>');
						$i++;
					}
				}
			}
			if( $i == 0 )
			{
				rawoutput('<td colspan="5">');
				output('`n`3There are currently no players with pets. Awww.');
				rawoutput('</td>');
			}
			rawoutput('</table><br />');
		}
	}
?>