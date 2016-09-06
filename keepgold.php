<?php



function keepgold_getmoduleinfo(){

	$info = array(

		"name"=>"Keep Money",

		"version"=>"1.0",

		"author"=>"Christian Rutsch",

		"category"=>"Forest",

		"settings"=>array(

			"howmuch"=>"How much percent of gold should be kept after dk?,int|100",

			"gold"=>"Keep gold on hand?,bool|0",

			"bank"=>"Keep gold in bank?,bool|0",

		),

		"prefs"=>array(

			"gold"=>"Saved gold,hidden|0",

			"bank"=>"Saved goldinbank,hidden|0",

			"You will not see any values here because they are wiped instantly after the dragonkill,note",

		),

	);

	return $info;

}



function keepgold_install(){

	module_addhook("dk-preserve");

	module_addhook("dragonkill");

	return true;

}



function keepgold_uninstall(){

	return true;

}



function keepgold_dohook($hookname,$args){

	global $session;

	switch ($hookname) {

		case "dk-preserve":

			$modifier = get_module_setting("howmuch") /100;

			if (get_module_setting("gold")) set_module_pref("gold", round( $session['user']['gold'] * $modifier, 0));

			if (get_module_setting("bank")) set_module_pref("bank",  round( $session['user']['goldinbank'] * $modifier, 0));

			break;

		case "dragonkill":

		$gold = get_module_pref("gold");

			if ($gold > 2000000000)

			{

				$rand = mt_rand(1,8);

				debug($rand);

				if ($rand == 7)

				{

					output("`4You lose all of your gold!`n");

					$gold = 0;

				}

				else if ($rand == 5)

				{

					output("`\$You lose 25% of your gold!`n");

					$gold = round($session['user']['gold']*0.75,0);

				}

				else

				{

					$gold = 2000000000;

				}

			}

			$session['user']['gold'] = $gold;

			$session['user']['goldinbank'] = get_module_pref("bank");

			set_module_pref("gold", 0);

			set_module_pref("bank", 0);

			break;

	}

	return $args;

}

?>