<?php
	if( $allprefs['haspet'] > 0 )
	{
		switch( e_rand(1,4) )
		{
			case 1:
				output("`n`3Your pet keeps an eye out as you wander about the village.`0`n`n");
			break;

			case 2:
				output("`n`3%s `2looks about the village in a state of boredom.`0`n`n", $allprefs['petname']);
			break;

			case 3:
				output("`n`2Villagers give `3%s `2a smile as they pass on by.`0`n`n", $allprefs['petname']);
			break;

			case 4:
				$sql = "SELECT villagemsg
						FROM " . db_prefix('pets') . "
						WHERE petid = '" . $allprefs['haspet'] . "'";
				$result = db_query($sql);
				$row = db_fetch_assoc($result);
				if( !empty($row['villagemsg']) )
				{
					pet_messages($row['villagemsg'], $allprefs);
				}
			break;
		}
	}
?>