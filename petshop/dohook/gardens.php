<?php
	if( $allprefs['haspet'] > 0 )
	{
		switch( e_rand(1,6) )
		{
			case 1:
				output("`@%s `2plays among the flowers.`0`n", $allprefs['petname']);
			break;

			case 2:
				output("`@%s `2tries to chase down a fairy but fails!`0`n", $allprefs['petname']);
			break;

			case 3:
				output("`@%s `2makes a meal out of a poor fairy!`0`n", $allprefs['petname']);
			break;

			case 4:
				output("`2Your pet flushes out a `&small white rabbit `2from the flower patches and gives chase.`0`n");
			break;

			case 5:
				output("`2Your pet looks for a comfy place to take a nap.`0`n");
			break;

			case 6:
				$sql = "SELECT gardenmsg
						FROM " . db_prefix('pets') . "
						WHERE petid = '" . $allprefs['haspet'] . "'";
				$result = db_query($sql);
				$row = db_fetch_assoc($result);
				if( !empty($row['gardenmsg']) )
				{
					pet_messages($row['gardenmsg'], $allprefs);
				}
			break;
		}
	}
?>