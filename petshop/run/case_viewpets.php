<?php
	$cat = ( httpget('cat') ) ? httpget('cat') : 0;

	$sql = "SELECT petid, pettype, petdesc, valuegold, valuegems
			FROM " . db_prefix('pets') . "
			WHERE petcat = '" . $cat . "'
				AND ( petrace = '" . $session['user']['race'] . "' OR petrace = 'All' )
				AND petdk <= '" . $session['user']['dragonkills'] . "'
			ORDER BY valuegold+0, valuegems+0 DESC, pettype DESC";
	$result = db_query($sql);
	$count = db_num_rows($result);

	$categories = explode('::', trim(get_module_setting('categories')));
	if( $session['user']['superuser'] & SU_DEVELOPER )
	{	// Storage category.
		$categories[99] = translate_inline('Storage');
	}
	$count_cats = count($categories);

	if( empty($count) )
	{
		output("`#%s `3offers her apologies, `#\"I'm sorry, but I don't appear to have any %s pets for sale just yet. Try back at a later date.\"`0`n`n", $ownersname, $categories[$cat]);
	}
	else
	{
		output('`b`c`3Below is a Listing of `#%s `3Pets`0`c`b`n', $categories[$cat]);

		$choice = translate_inline('Choice');
		$name = translate_inline('Breed Name');
		$goldc = translate_inline('Gold cost');
		$gemsc = translate_inline('Gem cost');
		$view = translate_inline('View');
		$sell = translate_inline('Sell Current');
		$gift = translate_inline('Gift');
		$gems = translate_inline(array('gem','gems'));

		rawoutput('<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center">');
		rawoutput("<tr class=\"trhead\"><td>$choice</td><td>$name</td><td align=\"center\">$goldc</td><td align=\"center\">$gemsc</td></tr>");

		$i = 1;
		while( $row = db_fetch_assoc($result) )
		{
			rawoutput('<tr class="'.($i%2?'trdark':'trlight').'"><td>');
			$i++;
			if( $allprefs['haspet'] > 0 )
			{
				rawoutput('[<a href="runmodule.php?module=petshop&op=sellpet&petid='.$allprefs['haspet'].'">'.$sell.'</a>] | [<a href="runmodule.php?module=petshop&op=giftpet&cat='.$cat.'&what=search&petid='.$row['petid'].'">'.$gift.'</a>]</td>');
				addnav('','runmodule.php?module=petshop&op=sellpet&petid='.$allprefs['haspet']);
				addnav('','runmodule.php?module=petshop&op=giftpet&cat='.$cat.'&what=search&petid='.$row['petid']);
			}
			else
			{
				rawoutput('[<a href="runmodule.php?module=petshop&op=petdetail&cat='.$cat.'&petid='.$row['petid'].'">'.$view.'</a>] | [<a href="runmodule.php?module=petshop&op=giftpet&cat='.$cat.'&what=search&petid='.$row['petid'].'">'.$gift.'</a>]</td>');
				addnav('','runmodule.php?module=petshop&op=petdetail&cat='.$cat.'&petid='.$row['petid']);
				addnav('','runmodule.php?module=petshop&op=giftpet&cat='.$cat.'&what=search&petid='.$row['petid']);
			}

			rawoutput('<td>'.appoencode($row['pettype']).'</td><td align="center">');
			output('`^%s Gold`0', $row['valuegold']);
			rawoutput('</td><td align="center">');
			output('`% %s %s`0', $row['valuegems'], ($row['valuegems']==1?$gems[0]:$gems[1]));
			rawoutput('</td></tr>');

			rawoutput('<tr class="'.($i%2?'trdark':'trlight').'"><td colspan="4">');
			if( $row['petdesc'] > '' )
			{
				output('`i`3%s`0`i', $row['petdesc']);
			}
			else
			{
				output('`i`3There is no information about this pet.`0`i');
			}
				rawoutput('</td></tr>');
			$i++;
		}

		rawoutput('</table>');
	}

	addnav('View Pets');
	if( !empty($count_cats) )
	{
		foreach( $categories as $key => $value )
		{
			addnav(array('%s',$value),'runmodule.php?module=petshop&op=viewpets&cat='.$key);
		}
	}
	else
	{	// Failsafe.
		addnav('Common Pets','runmodule.php?module=petshop&op=viewpets&cat=0');
	}
	addnav('Back');
	addnav('Go Back','runmodule.php?module=petshop');
?>