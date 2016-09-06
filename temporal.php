<?php
function temporal_getmoduleinfo()
{
	$info = array
	(
		"name"	=> "Temporal Mistress",
		"version"	=> "1.2",
		"author"	=> "<a href='http://www.rpgee.com' target=_blank>`&RPGee.com</a>",
		"category"	=> "Village",
		"download"	=> "http://www.rpgee.com/lotgd/temporal.zip",
		"vertxtloc"	=> "http://www.rpgee.com/lotgd/",
		"settings"	=> array
		(
			"Temporal Mistress - Settings,title",
				"chance"	=> "Chance in 1000 of obtaining a new day, range,0,1000,10|150",
				"dkdec"	=> "Decrease chance by X per Dragon Kill, int|1",
				"maxdays"	=> "Maximum amount of new days that can be saved?, int|3",
				"allvill"	=> "Does the Temporal Mistress appear in all villages?, bool|1",
				"temploc"	=> "Where does the Temporal Mistress appear if not in all villages?,
				 location|".getsetting("villagename", LOCATION_FIELDS)
		),
		"prefs"	=> array
		(
			"Temporal Mistress Settings,title",
				"new"		=> "Ever visited?, bool|0",
				"used"	=> "Used today?, bool|0",
				"saved"	=> "Days saved, int|0",
		)
	);
	return $info;
}
function temporal_install()
{
	module_addhook("changesetting");
	module_addhook("village");
	module_addhook("shades");
	module_addhook("newday-runonce");
	return true;
}
function temporal_uninstall(){return true;}
function temporal_dohook($hookname,$args)
{
	global $session;
	switch($hookname)
	{
   		case "changesetting":
			if ($args['setting'] == "villagename")
				if ($args['old'] == get_module_setting('temploc')) set_module_setting("temploc", $args['new']);
		break;
		case "village":
			if (get_module_setting('allvill'))
			{
				tlschema($args['schemas']['marketnav']);
				addnav($args['marketnav']);
				tlschema();
				addnav(" `b`^S`b`i`6o`i`&l`6`i`6a`i`^r `bM`b`7i`6s`&t`6r`7`i`^e`7s`6s`i ", "runmodule.php?module=temporal&op=enter");
			}
			elseif ($session['user']['location'] == get_module_setting("temploc"))
			{
				tlschema($args['schemas']['marketnav']);
				addnav($args['marketnav']);
				tlschema();
				addnav(" `b`^S`b`i`6o`i`&l`6`i`6a`i`^r `bM`b`7i`6s`&t`6r`7`i`^e`7s`6s`i ", "runmodule.php?module=temporal&op=enter");
			}
		break;
		case "shades":
			addnav(" `b`^S`b`i`6o`i`&l`6`i`6a`i`^r `bM`b`7i`6s`&t`6r`7`i`^e`7s`6s`i ", "runmodule.php?module=temporal&op=enter");
		break;
		case "newday-runonce":
			$sql = "update ".db_prefix("module_userprefs")." set value=0 where value<>0 and setting='used'
			 and modulename='temporal'";
			db_query($sql);
		break;
	}
	return $args;
}
function temporal_run()
{
	global $session;
	page_header("Solar Mistress");
	$op = httpget("op");
	$output = 0;
	if ($op == "enter")
	{
		$maxdays = get_module_setting('maxdays');
		$saved = get_module_pref('saved');
		if ($saved > $maxdays) set_module_pref('saved', $maxdays);
		if (!get_module_pref('used'))
		{
			if (!get_module_pref('new'))
			{
				increment_module_pref('saved');
				set_module_pref('new', 1);
			}
			elseif (e_rand(1,1000) <= get_module_setting('chance') - $session['user']['dragonkills'] *
			 get_module_setting('dkdec') && $saved < $maxdays)
				increment_module_pref('saved');
			else
			{
				if ($saved > 0) $output = 1;
				else $output = 2;
			}
			set_module_pref('used', 1);
		}
		else
		{
			if ($saved > 0) $output = 1;
			else $output = 2;
			set_module_pref('used', 1);
		}
		$saved = get_module_pref('saved');
		if ($saved > 0) addnav("Use Services", "runmodule.php?module=temporal&op=use");
		if ($session['user']['alive']) addnav("Move on", "village.php");
		else addnav("Move on", "shades.php");
		output("The Solar Mistress seems to wave with each passing ray of light. Her translucent form shimmers and ripples like a lake's surface. As you approach, you notice that the gowns enshrouding her emanate a silvery-black aura. It's difficult to even focus upon her visage.n`n");
		if (!$output)
		{
			output("Ah, the Adventurer of which I've heard.");
			output("Today, I will give you my blessings.");
			output("You may call upon my services when needed.`n`n");
		}
		elseif ($output == 1)
		{
			output("I'm terribly sorry Adventurer, you don't get any extra uses of my services today. At least you have credit with me.`n`n");
		}
		else output("`iI'm sorry Adventurer, I haven't the energy to assist you right now so come back tomorrow.`i`n`n `b`^You have %s out of a possible %s saved days.`0`b", $saved, $maxdays);
	}
	if ($op == "use")
	{
		output("The Solar Mistress looks toward you with an ethereal solemness before speaking.`n`n`i`bThe new day awaits`b`i, %s. `i`bGo forth and prevail.`b`i", $session['user']['name']);
		increment_module_pref('saved', -1);
		addnav("Continue", "newday.php");
	}
	page_footer();
}
?>