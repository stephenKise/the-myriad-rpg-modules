<?php
	$gift = httpget('gift');
	$petid = ( $gift == 'yes' ) ? $allprefs['giftid'] : httpget('petid');

	$sql = "SELECT pettype, petcharm, petturns, petattack, mindamage, maxdamage, valuegold, valuegems
			FROM " . db_prefix('pets') . "
			WHERE petid = '$petid'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);

	require_once('lib/showform.php');

	$gems = translate_inline(array('gem','gems'));
	$payment = translate_inline(array('`^gold','`$gems'));

	$end = TRUE;

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
				$row2 = db_fetch_assoc($result);

				$sellpricegold = round($row2['valuegold']/2);
				$sellpricegems = round($row2['valuegems']/2);
				$session['user']['gold'] += $sellpricegold;
				$session['user']['gems'] += $sellpricegems;

				output("`#%s `3takes your %s `3and hands you `^%s gold `3and `% %s %s`3.`0`n`n ", $ownersname, $allprefs['pettype'], $sellpricegold, $sellpricegems, ($sellpricegems==1?$gems[0]:$gems[1]));
			}
			else
			{
				output('`3You had `#%s `3your %s `3and then take the %s `3from her.`n`n', $ownersname, $allprefs['pettype'], $row['pettype']);
			}
		}
	}
	elseif( $session['user']['gold'] < $row['valuegold'] || $session['user']['gems'] < $row['valuegems'] )
	{
		output("`3You start emptying your pockets looking for enough gold to purchase the %s `3while `#%s `3gives you a disapproving stare as she looks at the lint and strange coinage that now litters her once clean counter.`n`n", $row['pettype'], $ownersname);
		output("`#\"I'm sorry,\" `3she informs you, `#\"but you haven't enough %s `#to purchase a %s`#.\"`0`n`n", ($session['user']['gold']<$row['valuegold']?$payment[0]:$payment[1]), $row['pettype']);
		$end = FALSE;
	}
	else
	{
		$session['user']['gold'] -= $row['valuegold'];
		$session['user']['gems'] -= $row['valuegems'];
		output('`3You hand over `^%s gold `3and `% %s %s`3', $row['valuegold'], $row['valuegems'], ($row['valuegems']==1?$gems[0]:$gems[1]));
	}


	if( $end == TRUE )
	{
		output("`#%s `3smiles warmly and congratulates you on your new %s`3. ", $ownersname, $row['pettype']);

		if( $row['petcharm'] > 0 )
		{
			output("`#\"This pet also carries with it a charm bonus of %s!\" `3she chirps in.", $row['petcharm']);
			$session['user']['charm'] += $row['petcharm'];
		}

		output("`n`n`#\"Now choose a gender and give your new pet a name!\" `3she tells you.`0`n`n");
		rawoutput('<form action="runmodule.php?module=petshop&op=finalbuy" method="POST">');
		addnav('','runmodule.php?module=petshop&op=finalbuy');

		$row['petname'] = translate_inline('`&F`7i`&d`7o');
		$name = translate_inline('Your New Pet');
		$petinfo = array("$name,title",'petname'=>'Pet Name:,string,30','petgender'=>"Pet Gender,enum,0,".genders(0,0).",1,".genders(1,0));
		$data = array('petname'=>$row['petname'],'petgender'=>0);
		showform($petinfo,$data);
		rawoutput('</form>');

		$row['haspet'] = $petid;

		$allprefs = get_allprefs(FALSE, $row);
		set_module_pref('allprefs',serialize($allprefs));
	}

	addnav('Back');
	addnav('Go Back','runmodule.php?module=petshop');
?>