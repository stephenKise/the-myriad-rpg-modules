<?php
	if( $allprefs['haspet'] > 0 )
	{
		$moodlist = translate_inline(array('Lonely','Irritated','Happy','Curious','Content','Hyper','Bored','Sleepy','Wants to Potty'));

		addcharstat('Pet Info');
		addcharstat('Pet Type', $allprefs['pettype']);
		addcharstat('Pet Name', $allprefs['petname']);
		addcharstat('Pet Gender', genders($allprefs['petgender'], 0));

		if( $session['user']['alive'] == FALSE )
		{
			addcharstat('Pet Mood', $moodlist[0]);
		}
		elseif( $allprefs['neglect'] == 1 )
		{
			addcharstat('Pet Mood', $moodlist[1]);
		}
		else
		{
			$moodnum = rand(2,8);
			addcharstat('Pet Mood', $moodlist[$moodnum]);
		}

		if( $allprefs['petattack'] == 1 )
		{
			addcharstat('Pet Points', $allprefs['petturns']);
		}
		elseif( $allprefs['petattack'] == 2 )
		{
			addcharstat('Pet Rounds', $allprefs['petturns']);
		}

		if( $session['user']['superuser'] & SU_DEVELOPER )
		{
			$attack = translate_inline(array('No','Yes - Manually','Yes - Automatic'));
			addcharstat('Attacks', $attack[$allprefs['petattack']]);
			addcharstat('Wildpet ID', $allprefs['wildpet']);
			addcharstat('Special', $allprefs['special']);
		}
	}
?>