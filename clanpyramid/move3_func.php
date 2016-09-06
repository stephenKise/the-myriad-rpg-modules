<?php
global $session;
$u=&$session['user'];
$p=3;
$clanid=$u['clanid'];
$square = get_module_pref("square");
$userid=$u['acctid'];
$owned3=get_module_setting("owned3");
$defender=get_module_pref("defender");
$move = httpget('move');
$wall=httpget('wall');
$last = date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT", 900)." sec"));
$loggedin=1;
$acc= db_prefix("accounts");
$mp=db_prefix("module_userprefs");
$northarray=array(2001,2002,2003,2004,2005,2006,2007,2008,2009,2010,2011,2012,2013);
$westarray=array(2001,2014,2027,2040,2053,2066,2079,2092,2105,2118,2131,2144,2157);
$eastarray=array(2013,2026,2039,2052,2065,2078,2091,2104,2117,2130,2143,2156,2169);
$southarray=array(2157,2158,2159,2160,2161,2162,2163,2164,2165,2166,2167,2168,2169);
//throne
$tarray1=array(2085,2098);
if ($owned3==$clanid){
	set_module_pref("defender",1,"clanpyramid",$userid);
}elseif ($owned3<>$clanid && $defender==1){
	page_header("Vault");
	clear_module_pref("defender","clanpyramid",$userid);
	output("You are no longer defending and must return to the village");
	villagenav();
	page_footer();
}
if ($move=="north"){
	$squarenew = $square-13;
}elseif ($move=="south"){
	$squarenew=$square+13;
}elseif ($move=="west"){
	$squarenew=$square-1;
}elseif ($move=="east"){
	$squarenew=$square+1;
}elseif ($move=="return"){
	$squarenew=$wall;
}elseif ($move=="back"){
	$squarenew=$square;
}elseif ($move=="entry"){
	$sn=e_rand(1,2);
	if ($sn==1){
		$squarenew=2001;
	}else{
		$squarenew=2169;
	}
}elseif ($move=="transport"){
	$squarenew=$square;
}elseif($move=="throneg"){
	$squarenew=2085;
	page_header("Throne Guardian");
	output("You move through the wall, only to be confronted by the Throne Guardian");
	output_notl("`n");
	addnav("Guardian Fight","runmodule.php?module=clanpyramid&op=attack&p=3&f=3");
	page_footer();
}elseif($move=="thronec"){
	page_header("The Throne");
	if ($clanid<>$owned3){
		output("You have captured the Vault in the name of your clan");
		output_notl("`n");
		//add in the reset of pyramid to new clan
		set_module_setting("owned3",$clanid);
		$acc= db_prefix("accounts");
		$mp=db_prefix("module_userprefs");
		$sqld = "SELECT $acc.name AS name,
		$acc.clanid AS clanid,
		$acc.acctid AS acctid,
		$mp.value AS square,
		$mp.userid FROM $mp INNER JOIN $acc
		ON $acc.acctid = $mp.userid
		WHERE $mp.modulename = 'clanpyramid'
		AND $mp.setting = 'square'
		AND $acc.clanid = $clanid
		AND $acc.loggedin = $loggedin
		AND $acc.laston>'$last'
		AND $mp.value > 2000
		";
		$resd=db_query($sqld);
		$membersin = db_num_rows($resd);
		$mpoints = $membersin*5;
		if (is_module_active("clanhof")){
			$countrow=db_num_rows($resd);
			if ($countrow>0){
				for ($i=0;$i<$countrow;$i++){
					$rowd = db_fetch_assoc($resd);
					$allid = $rowd['acctid'];
					set_module_pref("cp",(get_module_pref("cp","clanhof",$allid)+5),"clanhof",$allid);
				}
			}
			set_module_pref("cp",(get_module_pref("cp","clanhof",$userid)+5),"clanhof",$userid);
		}
		$cw=get_module_objpref("clans",$clanid,"clanwins","clanpyramid")+$mpoints;
		set_module_objpref("clans",$clanid,"clanwins",$cw,"clanpyramid");
		output_notl("`n`n");
		output("You receive %s Guild Points",$mpoints);
		if (is_module_active("clanwarvault")){
			$cdef = get_module_objpref("clans",$clanid,"def","clanwarvault");
			$wallhp = $cdef*250;
		}else{
			$wallhp = 25000;
		}
		output("`n`n`^Your Guilds Walls are now set at $wallhp");
		set_module_objpref("clans",$clanid,2085,$wallhp,"clanpyramid");
		set_module_objpref("clans",$clanid,2098,$wallhp,"clanpyramid");
		$sqle = "SELECT $acc.name AS name,
		$acc.acctid AS acctid,
		$mp.value AS square,
		$mp.userid FROM $mp INNER JOIN $acc
		ON $acc.acctid = $mp.userid
		WHERE $mp.modulename = 'clanpyramid'
		AND $mp.setting = 'square'
		AND $mp.userid <> ".$u['acctid']."
		AND $acc.loggedin = $loggedin
		AND $acc.laston>'$last'
		AND $mp.value >2000
		";
		$rese=db_query($sqle);
		$countrow=db_num_rows($rese);
		if ($countrow>0){
			for ($i=0;$i<$countrow;$i++){
				$rowe = db_fetch_assoc($rese);
				$allid = $rowe['acctid'];
				set_module_pref("square",900,"clanpyramid",$allid);
			}
		}
	}elseif ($clanid==$owned3){
		output("Your clan is successful and the Vault has been captured.");
		output_notl("`n");
	}
	villagenav();
	page_footer();
}
clanpyramid_squarenames($squarenew);
$tparray=array(2015,2016,2028,2029,2024,2025,2037,2038,2145,2146,2158,2159,2043,2044,2056,2057,2045,2046,2047,2058,2059,2060,2048,2049,2061,2062,2069,2070,2082,2083,2095,2096,2108,2109,2074,2075,2087,2088,2100,2101,2113,2114,2121,2122,2134,2135,2123,2124,2125,2136,2137,2138,2126,2127,2139,2140);
if (in_array($squarenew,$tparray)){
	$r=e_rand(1,8);
	if ($r==1){
		$portal=true;
	}
}
if ($portal==true){
	require_once("modules/clanpyramid/pyramid3.php");
	pyramid3_rand();
	blocknav("runmodule.php?module=clanpyramid&op=wall&hit=south");
	blocknav("runmodule.php?module=clanpyramid&op=move&move=south");
	blocknav("runmodule.php?module=clanpyramid&op=move&move=east");
	blocknav("runmodule.php?module=clanpyramid&op=wall&hit=east");
	blocknav("runmodule.php?module=clanpyramid&op=move&move=north");
	blocknav("runmodule.php?module=clanpyramid&op=wall&hit=north");
	blocknav("runmodule.php?module=clanpyramid&op=wall&hit=west");
	blocknav("runmodule.php?module=clanpyramid&op=move&move=west");
}
if (in_array(($squarenew-13),$tarray1)&&$defender<>1){
	output("You have found the throne");
	//$wall=$squarenew-13;
	addnav("Hit the Throne","runmodule.php?module=clanpyramid&op=wall&hit=north&p=3");
	blocknav("runmodule.php?module=clanpyramid&op=move&move=north&p=3");
}
if (in_array(($squarenew+13),$tarray1)&&$defender<>1){
	output("You have found the throne");
	//$wall=$squarenew+13;
	addnav("Hit the Throne","runmodule.php?module=clanpyramid&op=wall&hit=south&p=3");
	blocknav("runmodule.php?module=clanpyramid&op=move&move=south&p=3");
}
if (in_array(($squarenew-1),$tarray1)&&$defender<>1){
	output("You have found the throne");
	//$wall=$squarenew-1;
	addnav("Hit the Throne","runmodule.php?module=clanpyramid&op=wall&hit=west&p=3");
	blocknav("runmodule.php?module=clanpyramid&op=move&move=west&p=3");
}
if (in_array(($squarenew+1),$tarray1)&&$defender<>1){
	output("You have found the throne");
	//$wall=$squarenew+1;
	addnav("Hit the Throne","runmodule.php?module=clanpyramid&op=wall&hit=east&p=3");
	blocknav("runmodule.php?module=clanpyramid&op=move&move=east&p=3");
}
if (!in_array($squarenew,$northarray)){
	addnav("North","runmodule.php?module=clanpyramid&op=move&move=north&p=3");
}
if (!in_array($squarenew,$southarray)){
	addnav("South","runmodule.php?module=clanpyramid&op=move&move=south&p=3");
}
if (!in_array($squarenew,$westarray)){
	addnav("West","runmodule.php?module=clanpyramid&op=move&move=west&p=3");
}
if (!in_array($squarenew,$eastarray)){
	addnav("East","runmodule.php?module=clanpyramid&op=move&move=east&p=3");
}
set_module_pref("square",$squarenew);
//enemy list
$sql = "SELECT $acc.name AS name,
$acc.clanid AS clanid,
$acc.acctid AS acctid,
$mp.value AS square,
$mp.userid FROM $mp INNER JOIN $acc
ON $acc.acctid = $mp.userid
WHERE $mp.modulename = 'clanpyramid'
AND $mp.setting = 'square'
AND $mp.userid <> ".$u['acctid']."
AND $acc.clanid <> $clanid
AND $acc.loggedin = $loggedin
AND $acc.laston>'$last'
AND $mp.value = '$squarenew'
";
$res=db_query($sql);
$countrow=db_num_rows($res);
if ($countrow>0){
	$opp=$countrow;
}elseif (!$countrow){
	$opp=0;
}
if ($opp<>0){
	output("There are %s warriors from other clans rushing towards you",$opp);
	output_notl("`n");
	addnav("Attack Warriors","runmodule.php?module=clanpyramid&op=warriors&p=3");
}
//clanmembers list
$sql2 = "SELECT $acc.name AS name,
$acc.clanid AS clanid,
$acc.acctid AS acctid,
$mp.value AS square,
$mp.userid FROM $mp INNER JOIN $acc
ON $acc.acctid = $mp.userid
WHERE $mp.modulename = 'clanpyramid'
AND $mp.setting = 'square'
AND $mp.userid <> ".$u['acctid']."
AND $acc.clanid = $clanid
AND $acc.loggedin = $loggedin
AND $acc.laston>'$last'
AND $mp.value = '$squarenew'
";
$res2=db_query($sql2);
$countrow=db_num_rows($res2);
if (!$countrow){
	$members=0;
}else{
	$members=$countrow;
}
if ($members>0){
	output("There are %s clan members with you",$members);
	output_notl("`n");
}
if ($defender==1){
	require_once("modules/clanpyramid/defender_func.php");
	clanpyramid_defender($squarenew);
}
?>
