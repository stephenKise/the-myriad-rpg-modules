<?php
	if( $allprefs['haspet'] > 0 )
	{
		switch( rand(1,3) )
		{
			case 1:
				output("`n`@%s `2looks about the forest with some apprehension.`0`n", $allprefs['petname']);
			break;

			case 2:
				output("`n`@%s `2seems to hear something in the distance.`0`n", $allprefs['petname']);
			break;

			case 3:
				output("`n`@%s `2takes off to a nearby bush for a bathroom break.`0`n", $allprefs['petname']);
			break;
		}
	}
?>