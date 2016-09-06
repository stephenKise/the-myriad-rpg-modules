<?php
	if( $allprefs['haspet'] > 0 )
	{
		output("`3The last thing you remember seeing is your pet running off into the nearest bushes.`n");

		$losechance = rand(1,get_module_setting('petchance'));
		if( get_module_setting('battlelose') == 1 && $losechance == 1 )
		{
			addnews("`6%s's `2pet %s `2was slain in battle today!`0", $session['user']['name'], $allprefs['pettype']);

			$allprefs = get_allprefs();
			set_module_pref('allprefs',serialize($allprefs));
		}
		else
		{	
			output("Your pet is very faithful and will come back to you upon your resurrection.`0`n");
		}

		require_once('lib/buffs.php');
		if( has_buff('petattack') )
		{
			strip_buff('petattack');
		}
	}
?>