<?php
	if( $allprefs['haspet'] > 0 )
	{
		switch( rand(1,3) )
		{
			case 1:
				output("`n`#%s `3seems somewhat unnerved by all the noise.`0`n", $allprefs['petname']);
			break;

			case 2:
				output("`n`#%s `3eats something off the dirty floor.`0`n", $allprefs['petname']);
			break;

			case 3:
				output("`n`#%s `3draws a few bemused glances from the patrons and %s`2.`0`n", $allprefs['petname'], ($session['user']['sex']==1?getsetting('bard','`^Seth'):getsetting('barmaid','`%Violet')));
				if( e_rand(1,4) == 1 )
				{
					output("`#As a result, you gain some charm!`0`n");
					$session['user']['charm']++;
				}
			break;
		}
	}
?>