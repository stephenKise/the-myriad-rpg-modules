<?php
	$save = FALSE;
	if( $allprefs['giftid'] > 0 )
	{
		$allprefs['giftid'] = 0;
		$save = TRUE;
	}
	if( $allprefs['haspet'] > 0 )
	{
		$msg = TRUE;
		if( get_module_setting('dklose') == 1 )
		{
			$losechance = rand(1,get_module_setting('losechance'));
			if( $losechance == 1 )
			{
				output("`n`2You stop and pause a moment as you see a %s `2running towards the village.`n", $allprefs['pettype']);
				output("You have a vague feeling of having once known this creature.`n");
				output("Shrugging your shoulders, you continue on your way.`n`n");

				$allprefs = get_allprefs();
				$msg = FALSE;
				$save = TRUE;
			}
		}

		if( $msg == TRUE )
		{
			output("`n`2You stop and pause a moment, finding a %s `2by your side. ", $allprefs['pettype']);
			output("It looks up at you quizically, concern showing in its eyes. ");
			output("Deciding you know this critter, you name %s %s `2and continue on with %s by your side.`n`n", genders($allprefs['petgender'], 3), $allprefs['petname'], genders($allprefs['petgender'], 3));
		}
	}
	if( $save == TRUE )
	{
		set_module_pref('allprefs',serialize($allprefs));
	}
?>