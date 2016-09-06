<?php
	if( $allprefs['haspet'] > 0 && $allprefs['petattack'] == 1 && $allprefs['petturns'] > 0 )
	{
		$skillname = translate_inline('`QCommand Pet to Attack`0');
		$attack1 = translate_inline('Timid Attack');
		$attack2 = translate_inline('Fierce Attack');
		$attack3 = translate_inline('Vicious Attack');

		$script = $args['script'];

		addnav(array("%s`0", $skillname),'');
		addnav(array("`Q&#149; %s`7 (%s)`0", $attack1, 1), $script."op=fight&petattack=1", TRUE);
		if( $allprefs['petturns'] > 1 )
		{
			addnav(array("`Q&#149; %s`7 (%s)`0", $attack2, 2), $script."op=fight&petattack=2", TRUE);
			if( $allprefs['petturns'] > 2 )
			{
				addnav(array("`Q&#149; %s`7 (%s)`0", $attack3, 3), $script."op=fight&petattack=3", TRUE);
			}
		}
	}
?>