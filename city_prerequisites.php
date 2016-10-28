<?php
/**
	25/10/2010 - v1.0.0
	Based on Iori's 'mountprereq' module v1.01
	01/06/2013 - v1.0.1
	+ Added 'donationconfig' code.
	12/07/2014 - v1.0.2
	+ Added brackets to some IF lines to order the requirements better. Thanks Doctor. :)
*/
function city_prerequisites_getmoduleinfo()
{
	$info = array(
		"name"=>"City Prerequisites",
		"description"=>"Required conditions for cities.",
		"version"=>"1.0.2",
		"author"=>"`@MarcTheSlayer`2, based on`#Iori's `2'mountprereq'`0",
		"category"=>"Cities",
		"download"=>"",
		"requires"=>array(
			"city_creator"=>"1.0.1|`@MarcTheSlayer`2, available on Dragonprime.net"
		),
		"prefs-city"=>array(
			"Dragon Kills,title",
				"dks"=>"Dragon kill condition for availability:,enum,0,Ignore,1,Below Low DK,2,Between Low and High,3,Above High DK",
				"dkslo"=>"`\$Low Dragon Kills:,int",
				"dkshi"=>"`2High Dragon Kills:,int",
			"Alignment,title",
				"`i`2Requires the 'alignment' module to be installed.`i,note",
				"alignment"=>"Alignment condition for availability:,enum,0,Ignore,1,Below Low alignment,2,Between Low and High,3,Above High allignment",
				"alignlo"=>"`\$Low Alignment:,int",
				"alignhi"=>"`2High Alignment:,int",
			"Required Charm,title",
				"charmreq"=>"`&Charm:,int",
			"Required Gender,title",
				"sexreq"=>"Player gender:,enum,0,Ignore,1,Male,2,Female",
			"Donation Points,title",
				"donationreq"=>"Required available Donation Points:,int",
				"donationcost"=>"or Cost to purchase city access at the Lodge:,int",
				"`2City will always be available if they've bought it&#44; else all the other requirements must be met.,note",
		),
		"prefs"=>array(
			"bought"=>"Cities bought at the Lodge:,viewonly",
		),
	);
	return $info;
}

function city_prerequisites_install()
{
	output("`c`b`Q%s 'city_prerequisites' Module.`b`n`c", translate_inline(is_module_active('city_prerequisites')?'Updating':'Installing'));
	module_addhook('cityinvalidatecache');
	module_addhook('citydeleted');
	module_addhook('cityprerequisite');
	module_addhook('cityvalidlocations');
	module_addhook('cityrequirements');
	module_addhook('lodge');
	module_addhook('lodge_incentives');
	module_addhook('pointsdesc');
	return TRUE;
}

function city_prerequisites_uninstall()
{
	output("`n`c`b`Q'city_prerequisites' Module Uninstalled`0`b`c");
	return TRUE;
}

function city_prerequisites_dohook($hookname, $args)
{
	global $session;

	switch($hookname)
	{
		case 'cityinvalidatecache':
			invalidatedatacache('city_prerequisites-'.$args['cityid']);
			invalidatedatacache('city_prerequisites-lodge');
		break;

		case 'citydeleted':
			// If the deleted city was lodge point bought then remove it from the 'bought' array.
			$sql = "SELECT a.acctid, m.value AS bought
					FROM " . db_prefix('accounts') . " a
					INNER JOIN " . db_prefix('module_userprefs') . " m
						ON a.acctid = m.userid
					WHERE m.modulename = 'city_prerequisites'
						AND m.setting = 'bought'
						AND m.value != ''";
			$result = db_query($sql);
			while( $row = db_fetch_assoc($result) )
			{
				$bought_cities = @unserialize($row['bought']);
				if( is_array($bought_cities) )
				{
					$key = array_search($args['cityid'], $bought_cities);
					unset($bought_cities[$key]);
					set_module_pref('bought',serialize($bought_cities),'city_prerequisites',$row['acctid']);
					// Sorry, no lodge point refunds. :)
				}
			}
		break;

		case 'cityprerequisite':
		case 'cityvalidlocations':
			if( $args['blocked'] == 1 ) break; // If another module has already blocked it.
			$cityid = $args['cityid'];
			$alignment_active = is_module_active('alignment');
			$donationavail = $session['user']['donation'] - $session['user']['donationspent'];
			if( $donationavail < 0 ) $donationavail = 0;
			$bought_cities = @unserialize(get_module_pref('bought'));
			if( !is_array($bought_cities) ) $bought_cities = array();

			$sql = "SELECT setting, value
					FROM " . db_prefix('module_objprefs') . "
					WHERE modulename = 'city_prerequisites'
						AND objtype = 'city'
						AND objid = '$cityid'";
			$result = db_query_cached($sql,'city_prerequisites-'.$cityid,86400);
			$prereq = array('dks'=>0,'dkslo'=>'','dkshi'=>'','alignment'=>0,'alignlo'=>'','alignhi'=>'','charmreq'=>'','sexreq'=>0,'donationreq'=>0);
			while( $row = db_fetch_assoc($result) )
			{
				$prereq[$row['setting']] = $row['value'];
			}

			// DragonKills
			if( $prereq['dks'] != 0 )
			{
				if( $prereq['dks'] == 1 && $session['user']['dragonkills'] > $prereq['dkslo'] )
				{
					$args['blocked'] = 1;
					debug("Blocked {$args['cityname']} - Too many DKs.");
				}
				elseif( $prereq['dks'] == 2 && ($session['user']['dragonkills'] < $prereq['dkslo'] || $session['user']['dragonkills'] > $prereq['dkshi']) )
				{
					$args['blocked'] = 1;
					debug("Blocked {$args['cityname']} - Too many or not enough DKs.");
				}
				elseif( $prereq['dks'] == 3 && $session['user']['dragonkills'] < $prereq['dkshi'] )
				{
					$args['blocked'] = 1;
					debug("Blocked {$args['cityname']} - Not enough Dks.");
				}
			}
			// Alignment
			if( $alignment_active && $prereq['alignment'] != 0 )
			{
				$align = get_module_pref('alignment','alignment');
				if( $prereq['alignment'] == 1 && $align > $prereq['alignlo'] )
				{
					$args['blocked'] = 1;
					debug("Blocked {$args['cityname']} - Alignment too high.");
				}
				elseif( $prereq['alignment'] == 2 && ($align < $prereq['alignlo'] || $align > $prereq['alignhi']) )
				{
					$args['blocked'] = 1;
					debug("Blocked {$args['cityname']} - Alignment too low or too high.");
				}
				elseif( $prereq['alignment'] == 3 && $align < $prereq['alignhi'] )
				{
					$args['blocked'] = 1;
					debug("Blocked {$args['cityname']} - Alignment too low.");
				}
			}
			// Charm
			if( $prereq['charmreq'] > 0 && $session['user']['charm'] < $prereq['charmreq'] )
			{
				$args['blocked'] = 1;
				debug("Blocked {$args['cityname']} - Not enough charm.");
			}
			elseif( $prereq['charmreq'] < 0 && $session['user']['charm'] > $prereq['charmreq'] )
			{
				$args['blocked'] = 1;
				debug("Blocked {$args['cityname']} - Too much charm.");
			}
			// Sex
			if( $prereq['sexreq'] > 0 && ( ($prereq['sexreq'] == 1 && $session['user']['sex'] != 0) || ($prereq['sexreq'] == 2 && $session['user']['sex'] != 1) ) )
			{
				$args['blocked'] = 1;
				debug("Blocked {$args['cityname']} - wrong sex.");
			}
			// Donation
			if( !in_array($args['cityid'], $bought_cities) )
			{
				if( $donationavail < $prereq['donationreq'] && $prereq['donationreq'] > 0 )
				{
					$args['blocked'] = 1;
					debug("Blocked {$args['cityname']} - Not enough available lodge points.");
				}
			}
		break;

		case 'cityrequirements':
			$cityid = $args['cityid'];

			$sql = "SELECT setting, value
					FROM " . db_prefix('module_objprefs') . "
					WHERE modulename = 'city_prerequisites'
						AND objtype = 'city'
						AND objid = '{$args['cityid']}'";
			$result = db_query_cached($sql,'city_prerequisites-'.$args['cityid'],86400);
			$prereq = array('dks'=>0,'dkslo'=>'','dkshi'=>'','alignment'=>0,'alignlo'=>'','alignhi'=>'','charmreq'=>'','sexreq'=>0,'donationreq'=>0,'donationcost'=>0);
			while( $row = db_fetch_assoc($result) )
			{
				$prereq[$row['setting']] = $row['value'];
			}
			if( $prereq['dks'] > 0 )
			{
				if( $prereq['dks'] == 1 ) output('`3DKs <%s`0`n', $prereq['dkslo']);
				elseif( $prereq['dks'] == 2 ) output('`3DKs >%s <%s`0`n', $prereq['dkslo'], $prereq['dkshi']);
				elseif( $prereq['dks'] == 3 ) output('`3DKs >%s`0`n', $prereq['dkshi']);
			}
			if( $prereq['charmreq'] > 0 ) output('`2Charm >%s`0`n', $prereq['charmreq']);
			if( $prereq['charmreq'] < 0 ) output('`2Charm <%s`0`n', $prereq['charmreq']);
			if( is_module_active('alignment') )
			{
				if( $prereq['alignment'] > 0 )
				{
					if( $prereq['alignment'] == 1 ) output('`&Alignment <%s`0`n', $prereq['alignlo']);
					elseif( $prereq['alignment'] == 2 ) output('`&Alignment >%s <%s`0`n', $prereq['alignlo'], $prereq['alignhi']);
					elseif( $prereq['alignment'] == 3 ) output('`&Alignment >%s`0`n', $prereq['alignhi']);
				}
			}
			if( $prereq['sexreq'] == 1 ) output('`6Males only`0`n');
			elseif( $prereq['sexreq'] == 2 ) output('`6Females only`0`n');
			if( $prereq['donationreq'] > 0 ) output('`5Lodge points available: %s`0`n', $prereq['donationreq']);
			if( $prereq['donationcost'] > 0 ) output('`4Lodge points cost: %s`0`n', $prereq['donationcost']);
		break;

		case 'lodge':
		case 'lodge_incentives':
		case 'pointsdesc':
			$cities = db_prefix('cities');
			$objprefs = db_prefix('module_objprefs');
			$sql = "SELECT c.cityid, c.cityname, ob1.value AS cost
					FROM $cities c INNER JOIN $objprefs ob1
						ON c.cityid = ob1.objid
					WHERE ob1.modulename = 'city_prerequisites'
						AND ob1.objtype = 'city'
						AND ob1.setting = 'donationcost'
						AND ob1.value+0 > 0
						AND c.cityactive = 1";
			$result = db_query_cached($sql,'city_prerequisites-lodge',86400);
			if( db_num_rows($result) > 0 )
			{
				if( $hookname == 'lodge' ) 
				{
					$bought_cities = @unserialize(get_module_pref('bought'));
					if( !is_array($bought_cities) ) $bought_cities = array();
					$points = translate_inline(array('point','points'));
					addnav('Use Points');
					addnav('Buy City Access');
					while( $row = db_fetch_assoc($result) )
					{
						if( !in_array($row['cityid'], $bought_cities) )
						{
							addnav(array('%s (%s %s)', translate_inline($row['cityname']), $row['cost'], ($row['cost']==1?$point[0]:$points[1])),'runmodule.php?module=city_prerequisites&cityid='.$row['cityid']);
						}
					}
				}
				elseif( $hookname == 'lodge_incentives' )
				{
					while( $row = db_fetch_assoc($result) )
					{
						$points = $args['points'];
						$str = translate("`&%s Access.`0");
						$str = sprintf($str, translate_inline($row['cityname']));
						$points[$row['cost']][] = $str;
						$args['points'] = $points;
					}
				}
				elseif( $hookname == 'pointsdesc' )
				{
					while( $row = db_fetch_assoc($result) )
					{
						$args['count']++;
						$str = translate("%s access costs %s %s.");
						$points = translate_inline($row['cost']==1?'point':'points');
						$str = sprintf($str, translate_inline($row['cityname']), $row['cost'], $points);
						output($args['format'], $str, TRUE);
					}
				}
			}
		break;
	}

	return $args;
}

function city_prerequisites_run()
{
	global $session;

	page_header("Hunter's Lodge");

	$cityid = httpget('cityid');
	if( $cityid > 0 )
	{
		$sql = "SELECT cityname
				FROM " . db_prefix('cities') . "
				WHERE cityid = '$cityid'";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$row['cityname'] = translate_inline($row['cityname']);
	}
	$cost = get_module_objpref('city',$cityid,'donationcost');

	$op = httpget('op');
	switch( $op )
	{
		case 'yes':
			output("`3J. C. Petersen hands you a gold key with %s inscribed on it.`n`n", $row['cityname']);
			output("\"`\$That's a magical key, just owning it will grant you access and show you the way.");
			output("Now, don't lose it!`3\"`n`n");
			output("You take the key and give it a once over before putting it into your pouch.");
			output("J. C. Petersen grins, \"`\$Now go and explore, you'll find that you're no longer in the city that you once were in.`3\"");

			$bought_cities = @unserialize(get_module_pref('bought'));
			if( !is_array($bought_cities) ) $bought_cities = array();
			$bought_cities[] = $cityid;
			set_module_pref('bought',serialize($bought_cities));
			$session['user']['location'] = $row['cityname'];
			$session['user']['donationspent'] += $cost;
			$dconfig = @unserialize($session['user']['donationconfig']);
			if( !is_array($dconfig) ) $dconfig = array();
			$dconfig = array_push($dconfig, "spent $cost points for access to the city of {$row['cityname']}.");
			$session['user']['donationconfig'] = serialize($dconfig);

			addnews(
					"`Q%s `qhas just bought access to `Q%s`q, maybe you should to?",
					[$session['user']['name'],
					$row['cityname']]
			);

			addnav('Return');
			addnav('L?Return to the Lodge','lodge.php');
		break;

		case 'no':
			output("`3J. C. Petersen looks at you and shakes his head and says, \"`\$Your loss, but %s is a very nice place to be able to go and visit.`3\"", $row['cityname']);
			addnav('Return');
			addnav('L?Return to the Lodge','lodge.php');
		break;

		default:
			$bought_cities = @unserialize(get_module_pref('bought'));
			if( !is_array($bought_cities) ) $bought_cities = array();
			$pointsavailable = $session['user']['donation'] - $session['user']['donationspent'];
			if( $pointsavailable >= $cost && !in_array($cityid, $bought_cities) && $cityid > 0 )
			{
				output("`3J. C. Petersen looks upon you with a caustic grin.`n`n\"`\$So, you wish to purchase access to %s?`3\" he says with a smile.", $row['cityname']);
				addnav('Choices');
				addnav('Yes','runmodule.php?module=city_prerequisites&op=yes&cityid='.$cityid);
				addnav('No','runmodule.php?module=city_prerequisites&op=no&cityid='.$cityid);
			}
			elseif( in_array($cityid, $bought_cities) )
			{
				output("`3J. C. Petersen stares at you for a moment then looks away as you realize that you've already bought access to %s.", $row['cityname']);
				addnav('Return');
				addnav('L?Return to the Lodge','lodge.php');
			}
			else
			{
				output("`3J. C. Petersen stares at you for a moment then looks away as you realize that you don't have enough points to purchase access.");
				addnav('Return');
				addnav('L?Return to the Lodge','lodge.php');
			}
		break;
	}

	page_footer();
}
?>
