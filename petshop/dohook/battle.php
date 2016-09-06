<?php
	if( $allprefs['haspet'] > 0 && $session['user']['alive'] == TRUE )
	{
		require_once('lib/buffs.php');

		if( $args['type'] == 'dragon' )
		{
			output("`2%s `2seems visibly nervous at the sight of `@The Green Dragon`0!`n", $allprefs['petname']);
		}
		elseif( $args['type'] == 'pvp' )
		{
			output("`2%s `2waits to one side while you do battle with a fellow warrior.`0`n", $allprefs['petname']);	
		}
		else
		{
			$sql = "SELECT battlemsg
					FROM " . db_prefix('pets') . "
					WHERE petid = '" . $allprefs['haspet'] . "'";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			if( !empty($row['battlemsg']) )
			{
				pet_messages($row['battlemsg'], $allprefs);
			}
		}

		if( $allprefs['petattack'] == 2 && $allprefs['petturns'] > 0 )
		{
			if( empty($allprefs['special']) )
			{
				if( !has_buff('petattack') )
				{			
					apply_buff('petattack',array(
						"name"=>"",
						"allowinpvp"=>0,
						"allowintrain"=>0,
						"rounds"=>$allprefs['petturns'],
						"wearoff"=>"`@{$allprefs['petname']} `Qis too tired to fight and retreats into some bushes.`0`n",
						"minioncount"=>1,
						"effectmsg"=>"`@{$allprefs['petname']} `Qattacks {badguy} `Qand causes `^{damage}`Q damage!`0`n",
						"effectnodmgmsg"=>"{badguy} `Qnarrowly dodges your pet's attack.",
						"effectfailmsg"=>"{badguy} `Qnarrowly dodges your pet's attack.",
						"minbadguydamage"=>$allprefs['mindamage'],
						"maxbadguydamage"=>$allprefs['maxdamage'],
						"schema"=>"petshop"
					));
				}
			}
			else
			{
				if( !has_buff('petattack') )
				{
					apply_buff('petattack', array(
						"startmsg"=>"`QYour `q{$allprefs['pettype']} `Qstops ".genders($allprefs['petgender'], 1)." attack, ".genders($allprefs['petgender'], 2)." doesn't look very well.`0`n",
						"rounds"=>$allprefs['petturns'],
						"schema"=>"module-petshop"
					));
				}
			}
			$allprefs['petturns']--;
			set_module_pref('allprefs',serialize($allprefs));
		}
	}
?>