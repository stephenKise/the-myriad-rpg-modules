<?php
	if( $allprefs['haspet'] > 0 && $allprefs['petattack'] == 1 )
	{
		$pet = httpget('petattack');
		if( empty($allprefs['special']) )
		{
			require_once('lib/buffs.php');
			if( $allprefs['petturns'] >= $pet && !has_buff('pet1') && !has_buff('pet2') && !has_buff('pet3') )
			{
				switch( $pet )
				{
					case 1:
						apply_buff('pet1', array(
								"name"=>"`Q{$allprefs['petname']} `QTimidly Attacks`0",
								"startmsg"=>"`QYou command `q{$allprefs['petname']} `Qto attack {badguy}`Q!",
								"rounds"=>5,
								"wearoff"=>"`QYour `q{$allprefs['pettype']} `Qstops attacking.`0",
								"minioncount"=>1,
								"effectmsg"=>"`q{$allprefs['petname']} `Qattacks {badguy} `Qfor `^{damage} `Qdamage.`0",
								"minbadguydamage"=>$allprefs['mindamage'],
								"maxbadguydamage"=>$allprefs['maxdamage'],
								"schema"=>"module-petshop"
							));
					break;

					case 2:
						apply_buff('pet2', array(
								"name"=>"`Q{$allprefs['petname']} `QFiercely Attacks`0",
								"startmsg"=>"`QYou command `q{$allprefs['petname']} `Qto attack {badguy}`Q!",
								"rounds"=>5,
								"wearoff"=>"`QYour `q{$allprefs['pettype']} `Qstops attacking.`0",
								"minioncount"=>1,
								"effectmsg"=>"`q{$allprefs['petname']} `Qattacks {badguy} `Qfor `^{damage} `Qdamage.`0",
								"minbadguydamage"=>($allprefs['mindamage']*2),
								"maxbadguydamage"=>($allprefs['maxdamage']*2),
								"schema"=>"module-petshop"
							));
					break;

					case 3:
						apply_buff('pet3', array(
								"name"=>"`Q{$allprefs['petname']} `QViciously Attacks`0",
								"startmsg"=>"`QYou command `q{$allprefs['petname']} `Qto attack {badguy}`Q!",
								"rounds"=>5,
								"wearoff"=>"`QYour `q{$allprefs['pettype']} `Qstops attacking.`0",
								"minioncount"=>1,
								"effectmsg"=>"`q{$allprefs['petname']} `Qattacks {badguy} `Qfor `^{damage} `Qdamage.`0",
								"minbadguydamage"=>($allprefs['mindamage']*3),
								"maxbadguydamage"=>($allprefs['maxdamage']*3),
								"schema"=>"module-petshop"
							));
					break;
				}
			}
			else
			{
				if( $pet > 0 )
				{
					apply_buff('pet0', array(
						"startmsg"=>"`QYou command your `q{$allprefs['pettype']} `Qto attack, but ".genders($allprefs['petgender'], 2)." just looks at you as if to say,`n\"`qWhat do you think I'm doing?`Q\"`0",
						"rounds"=>1,
						"schema"=>"module-petshop"
					));
				}
			}
		}
		else
		{
			apply_buff('pet0', array(
				"startmsg"=>"`QYou command your `q{$allprefs['pettype']} `Qto attack, but ".genders($allprefs['petgender'], 2)." doesn't look very well.`0",
				"rounds"=>1,
				"schema"=>"module-petshop"
			));
		}

		if( $allprefs['petturns'] >= $pet && $pet > 0 )
		{
			$allprefs['petturns'] -= $pet;
			set_module_pref('allprefs',serialize($allprefs));
		}
	}
?>