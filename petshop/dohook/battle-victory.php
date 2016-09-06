<?php
	if( $allprefs['haspet'] > 0 )
	{
		if( $allprefs['petattack'] > 0 )
		{
			output("`#%s `3seems a bit tired, but happy over your victory!`n", $allprefs['petname']);

			require_once('lib/buffs.php');
			strip_buff('petattack');
		}
		else
		{
			output("`#%s `3returns after the battle and is quite happy with your victory!`n", $allprefs['petname']);
		}
	}
?>