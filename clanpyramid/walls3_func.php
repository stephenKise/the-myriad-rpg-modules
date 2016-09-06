<?php
function walls3_hit(){
	global $session;
	$u=&$session['user'];
	$p=httpget('p');
	$owned3=get_module_setting("owned3");
	$square=get_module_pref("square");
	$northarray=array(2001,2002,2003,2004,2005,2006,2007,2008,2009,2010,2011,2012,2013);
	$westarray=array(2001,2014,2027,2040,2053,2066,2079,2092,2105,2118,2131,2144,2157);
	$eastarray=array(2013,2026,2039,2052,2065,2078,2091,2104,2117,2130,2143,2156,2169);
	$southarray=array(2157,2158,2159,2160,2161,2162,2163,2164,2165,2166,2167,2168,2169);
	//throne
	$tarray1=array(2085,2098);
	clanpyramid_enemylist($square);
	//clanmembers list
	$clanid=$u['clanid'];
	$last = date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT", 900)." sec"));
	$loggedin=1;
	$lastip = $u['lastip'];
	$lastid = $u['uniqueid'];
	$acc = db_prefix("accounts");
	$mp = db_prefix("module_userprefs");
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
	AND $acc.lastip <> '$lastip'
	AND $acc.uniqueid <> '$lastid'
	AND $mp.value = '$square'
	";
	$res2=db_query($sql2);
	$countrow=db_num_rows($res2);
	if (!$countrow){
		$members=0;
	}else{
		$members=$countrow;
	}
	if ($members>0){
		output("There are %s Guild members with you",$members);
		output_notl("`n");
	}
	$hit=httpget('hit');
	if ($hit=="return"){
		$wall=httpget('wall');
	}elseif ($hit=="north"){
		$wall = $square-13;
	}elseif ($hit=="south"){
		$wall=$square+13;
	}elseif ($hit=="west"){
		$wall=$square-1;
	}elseif ($hit=="east"){
		$wall=$square+1;
	}
	set_module_pref("wall",$wall);
	page_header("The Wall");
	$res2=db_query($sql2);
	$number = db_num_rows($res2);
	if ($number==0){
		$number=1;
	}
	$num = $number*0.35;
	$atk = $u['attack'];
	if (is_module_active("clanwarvault")){
		$catk = get_module_objpref("clans",$clanid,"att","clanwarvault")*$num;
	}else{
		$catk = 500*$num;
	}
	output("`nYour clan helps you by boosting your attack by $catk`n");
	$tatk=$atk+$catk;
	$def=$u['defence'];
	$hp = get_module_objpref("clans",$owned3,$wall,"clanpyramid");
	if (is_module_active("clanwarvault")){
		$watk = get_module_objpref("clans",$clan,"def","clanwarvault");
	}else{
		$watk = 500;
	}
	$aa=$tatk*0.45;
	$adam=e_rand($aa,$tatk);
	$dd=$watk*0.25;
	$d=e_rand($dd,$watk);
	$damage = ($adam-$d)*5;
	if ($damage==0){
		$dam=100;
	}elseif($damage<0){
		$dam=0-$damage;
	}elseif($damage>0){
		$dam=$damage;
	}
	if ($dam>0){
		$hp1=$hp-$dam;
		set_module_objpref("clans",$owned3,$wall,$hp1,"clanpyramid");
		if ($hp1>0){
			output("You hit the wall for %s damage, there is %s hp left on the wall",$dam,$hp1);
			output_notl("`n");
			addnav("Hit","runmodule.php?module=clanpyramid&op=wall&hit=return&wall=$wall&p=$p");
			addnav("Return to Vault","runmodule.php?module=clanpyramid&op=move&move=return&wall=$square&p=$p");
		}elseif ($hp1<=0){
			output("You hit the wall for %s damage, the wall crumbles in front of you",$dam);
			output_notl("`n");
			if (in_array($wall,$tarray1)){
				addnav("Through the wall","runmodule.php?module=clanpyramid&op=move&move=throneg&p=$p");
				set_module_pref("fightnum",3);
			}
		}
		blocknav("runmodule.php?module=clanpyramid&op=move");
	}
	page_footer();
}
?>
