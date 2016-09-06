<?php
function city_creator_array_check($city=FALSE)
{
	//
	// Make sure that all the variables exist.
	//
	$mods = array('all'=>0,'other'=>'');
	$navs = array('other'=>'','forest_php'=>0,'pvp_php'=>0,'mercenarycamp_php'=>0,'train_php'=>0,'lodge_php'=>0,'weapons_php'=>0,'armor_php'=>0,'bank_php'=>0,'gypsy_php'=>0,'inn_php'=>0,'stables_php'=>0,'gardens_php'=>0,'rock_php'=>0,'clan_php'=>0,'news_php'=>0,'list_php'=>0,'hof_php'=>0);
	$vill = array('title'=>'','text'=>'','clock'=>'','newest1'=>'','newest2'=>'','talk'=>'','sayline'=>'','gatenav'=>'','fightnav'=>'','marketnav'=>'','tavernnav'=>'','infonav'=>'','othernav'=>'','innname'=>'','stablename'=>'','mercenarycamp'=>'','armorshop'=>'','weaponshop'=>'','pvpstart'=>'','pvpwin'=>'','pvploss'=>'');
	$stab = array('title'=>'','desc'=>'','nosuchbeast'=>'','finebeast'=>'','toolittle'=>'','replacemount'=>'','newmount'=>'','nofeed'=>'','nothungry'=>'','halfhungry'=>'','hungry'=>'','mountfull'=>'','nofeedgold'=>'','confirmsale'=>'','mountsold'=>'','offer'=>'','lass'=>'','lad'=>'');
	$arm = array('title'=>'','desc'=>'','tradein'=>'','nosuchweapon'=>'','tryagain'=>'','notenoughgold'=>'','payarmor'=>'');
	$weap = array('title'=>'','desc'=>'','tradein'=>'','nosuchweapon'=>'','tryagain'=>'','notenoughgold'=>'','payweapon'=>'');
	$merc = array('title'=>'','desc'=>'','buynav'=>'','healnav'=>'','healtext'=>'','healnotenough'=>'','healpaid'=>'','toomanycompanions'=>'','manycompanions'=>'','onecompanion'=>'','nocompanions'=>'');

	if( !isset($city['cityactive']) )						$city['cityactive'] = 0;
	if( !isset($city['cityname']) )							$city['cityname'] = '';
	if( !isset($city['citytype']) )							$city['citytype'] = '';
	if( !isset($city['cityauthor']) )						$city['cityauthor'] = '';
	if( !isset($city['cityid']) )							$city['cityid'] = 0;
	if( !isset($city['citychat']) )							$city['citychat'] = 0;
	if( !isset($city['citytravel']) )						$city['citytravel'] = 0;
	if( !isset($city['module']) )							$city['module'] = '';
	if( !isset($city['mods']) || !is_array($city['mods']) )	$city['mods'] = $mods;
	if( !isset($city['navs']) || !is_array($city['navs']) )	$city['navs'] = $navs;
	if( !isset($city['vill']) || !is_array($city['vill']) )	$city['vill'] = $vill;
	if( !isset($city['stab']) || !is_array($city['stab']) )	$city['stab'] = $stab;
	if( !isset($city['arm']) || !is_array($city['arm']) )	$city['arm'] = $arm;
	if( !isset($city['weap']) || !is_array($city['weap']) )	$city['weap'] = $weap;
	if( !isset($city['merc']) || !is_array($city['merc']) )	$city['merc'] = $merc;

    // Check the arrays for missing fields and add them if not found.
	$names = array('mods','navs','vill','stab','arm','weap','merc');
	foreach( $names as $name )
	{
		foreach( ${$name} as $key => $value )
		{
			if( !array_key_exists($key, $city[$name]) )
			{
				$city[$name][$key] = $value;
			}
		}
	}

	// Go through all the data and stripslashes.
	foreach( $city as $key => $value )
	{
		if( is_array($value) )
		{
			foreach( $value as $key2 => $value2 )
			{
				$city[$key.$key2] = ( is_string($value2) ) ? stripslashes($value2) : (int)$value2;
			}
			unset($city[$key]);
		}
		else
		{
			$city[$key] = ( is_string($value) ) ? stripslashes($value) : (int)$value;
		}
	}

	return $city;
}
?>