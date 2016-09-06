<?php
/**
	07/04/2013 - v1.0.0
	+ I had this in the 'city_creator' module, but thought it best to have it separate.
	  This allows other modules to block/create access to cities easily.
	03/07/2013 - v1.0.1
	+ Changed the input to checkboxes, less chance of errors this way.
	+ One less hook now.
*/
function city_routes_getmoduleinfo()
{
	$op = httpget('op');
	$bitfield = '';
	if( $op == 'edit' || $op == 'editmodule' )
	{	// Don't want this to run during installs or updates etc.
		$cityid = httpget('cityid');
		$sql = "SELECT cityid, cityname, cityactive
				FROM " . db_prefix('cities');
		$result = db_query($sql);
		$bitfield .= 'bitfield,'. 0xffffffff;
		while( $row = db_fetch_assoc($result) )
		{
			if( $row['cityid'] == $cityid ) continue;
			$inactive = ( getsetting('villagename', LOCATION_FIELDS) == $row['cityname'] ) ? translate_inline(' (Capital)') : (( isset($row['cityactive']) && $row['cityactive'] != 1 ) ? translate_inline(' (inactive)') : '');
			$val = pow(2, $row['cityid']);
			$bitfield .= ','.$val.','.$row['cityname'] . $inactive;
		}
	}

	$info = array(
		"name"=>"City Routes",
		"description"=>"Allow access to cities through certain routes.",
		"version"=>"1.0.1",
		"author"=>"`@MarcTheSlayer",
		"category"=>"Cities",
		"download"=>"",
		"requires"=>array(
			"city_creator"=>"1.0.1|`@MarcTheSlayer`2, available on Dragonprime.net"
		),
		"settings"=>array(
			"Route Settings,title",
				"blocknav"=>"Also block the nav link when a city is blocked?,bool",
				"`^Note: You may wish to do this if you also have other city modules installed like 'citygeneric1'&#44; or 'icetown'&#44; or 'ghosttown' as these output their own travel nav links.,note",
		),
		"prefs-city"=>array(
			"City Routes,title",
				"routes"=>"Routes:,".$bitfield,
				"`^Leave empty to travel from this city to anywhere&#44; or enter city names to restrict routes. CaSe SeNsItIvE.`n
				To stop all travel you can either block the travel link&#44; block the 'cities' module or simply turn travel to Off on the first tab.,note",
		),
	);
	return $info;
}

function city_routes_install()
{
	if( is_module_active('city_routes') )
	{
		output("`c`b`QUpdating 'city_routes' Module.`b`n`c");

		// Update to turn the string of names to a bitwise value.
		$sql = "SELECT cityid, cityname
				FROM " . db_prefix('cities');
		$result = db_query($sql);
		$cityid_array = array();
		while( $row = db_fetch_assoc($result) ) $cityid_array[$row['cityname']] = $row['cityid'];
		foreach( $cityid_array as $cityname => $cityid )
		{
			$routes = get_module_objpref('city', $cityid, 'routes', 'city_routes');
			if( is_numeric($routes) ) continue;
			$value = 0;
			$routes = explode(',', $routes);
			foreach( $routes as $city )
			{
				$val = pow(2, $cityid_array[$city]);
				$value += (int)$val;
			}
			set_module_objpref('city', $cityid, 'routes', $value, 'city_routes');
		}

	}
	else
	{
		output("`c`b`QInstalling 'city_routes' Module.`b`n`c");
	}

	module_addhook('cityprerequisite');
	return TRUE;
}

function city_routes_uninstall()
{
	output("`n`c`b`Q'city_routes' Module Uninstalled`0`b`c");
	return TRUE;
}

function city_routes_dohook($hookname, $args)
{
	if( $args['blocked'] == 1 ) exit; // If another module has already blocked it.

	$routes = get_module_objpref('city', $args['currentcityid'], 'routes', 'city_routes');

	// Is the city you can go to one of the routes available? If not then block it.
	if( !(pow(2, $args['cityid']) & $routes) )
	{
		$args['blocked'] = 1;
		debug("Blocked {$args['cityname']} - Route not allowed.");
		if( get_module_setting('blocknav') == 1 )
		{
			blocknav("runmodule.php?module=cities&op=travel&city=".urlencode($args['cityname']));
			blocknav("runmodule.php?module=cities&op=travel&city=".urlencode($args['cityname'])."&d=1");
		}
	}

	return $args;
}

function city_routes_run()
{
}
?>