<?php
global $session;
$u=&$session['user'];
$which=httpget('p');
$p=1;
$clanid=$u['clanid'];
//output("$clanid");
$square = get_module_pref("square");
$userid=$u['acctid'];
$owned1=get_module_setting("owned1");
$defender=get_module_pref("defender");
$move = httpget('move');
$wall=httpget('wall');
$last = date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT", 900)." sec"));
$loggedin=1;
$acc= db_prefix("accounts");
$mp=db_prefix("module_userprefs");
//floating doors and check for a change of owner.
//pyramid 1 walls array
$northarray=array(1,2,3,4,5,6,7,8,9,10,11,12,13);
$southarray=array(222,223,224,225,226,227,228,229,230,231,232,233,234);
$westarray=array(1,14,27,40,53,66,79,92,105,118,131,144,157,170,183,196,209,222);
$eastarray=array(13,26,39,52,65,78,91,104,117,130,143,156,169,182,195,208,221,234);
//outer
$wallsarray1=array(229,30,31,32,33,34,35,36,37);
$wallsarray2=array(198,199,200,201,202,203,204,205,206);
$wallsarray3=array(41,54,67,80,93,106,119,132,145,158,171,184);
$wallsarray4=array(51,64,77,90,103,116,129,142,155,168,181,194);
//inner
$wallsarray5=array(57,58,59,60,61);
$wallsarray6=array(161,162,163,164,165);
$wallsarray7=array(69,82,95,108,121,134,147);
$wallsarray8=array(75,88,101,114,127,140,153);
//all
$wallarray=array(28,29,30,31,32,33,34,35,36,37,38,197,198,199,200,201,202,203,204,205,206,207,28,41,54,67,80,93,106,119,132,145,158,171,184,197,38,51,64,77,90,103,116,129,142,155,168,181,194,207,56,57,58,59,60,61,62,160,161,162,163,164,165,166,56,69,82,95,108,121,134,147,160,62,75,88,101,114,127,140,153,166);
//blocks
$blockarray=array(84,86,109,110,125,136,138);
//throne
$tarray1=array(98,111,124);
if ($which==1){
	require_once("modules/clanpyramid/doors1_func.php");
}
//doors array
$darray1=array($doorno,$doorso,$dooreo,$doorwo,$doorni,$doorsi,$doorwi,$doorei);
if ($owned1==$clanid){
	set_module_pref("defender",1,"clanpyramid",$userid);
}elseif ($owned1<>$clanid && $defender==1){
	page_header("Vault");
	clear_module_pref("defender","clanpyramid",$userid);
	output("You are no longer defending and must return to the village");
	villagenav();
	page_footer();
}
if ($move=="north"){
	$squarenew = $square-13;
}
if ($move=="south"){
	$squarenew=$square+13;
}
if ($move=="west"){
	$squarenew=$square-1;
}
if ($move=="east"){
	$squarenew=$square+1;
}
if ($move=="return"){
	$squarenew=$wall;
}
if ($move=="transport"){
	$squarenew=$square;
	if ($owned1==$clanid){
		set_module_pref("defender",1,"clanpyramid",$userid);
	}
}
if ($move=="entry1"){
	$squarenew=1;
}
if ($move=="entry" && $which==2){
	$squarenew=235;
}
if ($move=="entry" && $which==3){
	$squarenew=417;
}
if ($move=="passage"){
	$squarenew=$square;
}
if ($move=="northg"){
	$squarenew=$square+13;
	page_header("Level Guardian");
	output("You move through the wall, only to be confronted by a guardian");
	output_notl("`n");
	addnav("Guardian Fight","runmodule.php?module=clanpyramid&op=attack&f=2&p=1");
	page_footer();
}
if ($move=="southg"){
	$squarenew=$square-13;
	page_header("Level Guardian");
	output("You move through the wall, only to be confronted by a guardian");
	output_notl("`n");
	addnav("Guardian Fight","runmodule.php?module=clanpyramid&op=attack&f=2&p=1");
	page_footer();
}
if ($move=="westg"){
	$squarenew=$square++;
	page_header("Level Guardian");
	output("You move through the wall, only to be confronted by a guardian");
	output_notl("`n");
	addnav("Guardian Fight","runmodule.php?module=clanpyramid&op=attack&f=2&p=1");
	page_footer();
}
if ($move=="eastg"){
	$squarenew=$square--;
	page_header("Level Guardian");
	output("You move through the wall, only to be confronted by a guardian");
	output_notl("`n");
	addnav("Guardian Fight","runmodule.php?module=clanpyramid&op=attack&f=2&p=1");
	page_footer();
}
if($move=="throneg"){
	if ($which = 1){
		$squarenew=111;
		page_header("Level Guardian");
		output("You move through the wall, only to be confronted by the Throne Guardian");
		output_notl("`n");
		addnav("Guardian Fight","runmodule.php?module=clanpyramid&op=attack&f=3&p=1");
		page_footer();
	}
}
if($move=="thronec"){
	page_header("The Throne");
	if ($clanid<>$owned1){
		output("You have captured the Vault in the name of your Guild");
		output_notl("`n");
		//add in the reset of pyramid to new clan
		set_module_setting("owned1",$clanid);
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
		AND $mp.value > 0
		AND $mp.value < 900
		";
		$resd=db_query($sqld);
		$membersin = db_num_rows($resd);
		$mpoints = $membersin*3;
		if (is_module_active("clanhof")){
			$countrow=db_num_rows($resd);
			if ($countrow>0){
				for ($i=0;$i<$countrow;$i++){
					$rowd = db_fetch_assoc($resd);
					$allid = $rowd['acctid'];
					set_module_pref("cp",(get_module_pref("cp","clanhof",$allid)+3),"clanpyramid",$allid);
				}
			}
			set_module_pref("cp",(get_module_pref("cp","clanpyramid",$userid)+3),"clanpyramid",$userid);
		}
		$cw=get_module_objpref("clans",$clanid,"clanwins","clanpyramid")+$mpoints;
		set_module_objpref("clans",$clanid,"clanwins",$cw,"clanpyramid");
		output_notl("`n`n");
		output("You receive %s Guild Points",$mpoints);
		//set floating outer doors
		$num=$wallsarray3[e_rand(0,8)];
		if ($num==0){
			$num="64";
		}
		set_module_objpref("clans",$clanid,"doorwo",$num,"clanpyramid");
		$num1=$wallsarray1[e_rand(0,11)];
		if ($num1==0){
			$num1="32";
		}
		set_module_objpref("clans",$clanid,"doorno",$num1,"clanpyramid");
			$num2=$wallsarray2[e_rand(0,8)];
		if ($num2==0){
			$num2="206";
		}
		set_module_objpref("clans",$clanid,"doorso",$num2,"clanpyramid");
			$num3=$wallsarray4[e_rand(0,11)];
		if ($num3==0){
			$num3="80";
		}
			set_module_objpref("clans",$clanid,"dooreo",$num3,"clanpyramid");
		//set floating inner doors
		$num4=$wallsarray5[e_rand(0,6)];
		if ($num4==0){
			$num4="60";
		}
		set_module_objpref("clans",$clanid,"doorni",$num4,"clanpyramid");
		$num5=$wallsarray6[e_rand(0,6)];
		if ($num5==0){
			$num5="161";
		}
		set_module_objpref("clans",$clanid,"doorsi",$num5,"clanpyramid");
		$num6=$wallsarray7[e_rand(0,6)];
		if ($num6==0){
			$num6="121";
		}
		set_module_objpref("clans",$clanid,"doorwi",$num6,"clanpyramid");
		$num7=$wallsarray8[e_rand(0,6)];
		if ($num7==0){
			$num7="140";
		}
		set_module_objpref("clans",$clanid,"doorei",$num7,"clanpyramid");
		//set throne door
		$num8=$tarray1[e_rand(0,2)];
		if ($num8==0){
			$num8="111";
		}
		set_module_objpref("clans",$clanid,"doort",$num8,"clanpyramid");
		//doors set
		$clanarray=array($num,$num1,$num2,$num3,$num4,$num5,$num6,$num7,$num8);
		$sql9="SELECT * FROM " .db_prefix ("module_objprefs"). " WHERE `modulename` = 'clanpyramid' AND `setting` > 0 AND `setting` < 234";
		$res9 = db_query($sql9);
		if (db_num_rows($res9)>0){
			$countrow=db_num_rows($res9);
			for ($i=0;$i<$countrow;$i++){
				$row9 = db_fetch_assoc($res9);
				$setting=$row9['setting'];
				if (!in_array($setting,$clanarray)){
					$sql10="DELETE  FROM ".db_prefix("module_objprefs")." WHERE `modulename` = 'clanpyramid' AND `setting` = '$setting'";
					db_query($sql10);
				}
				$sql11="DELETE  FROM " .db_prefix("module_objprefs"). " WHERE `modulename` = 'clanpyramid' AND `setting` = '$setting' AND `objid` = '$clanid'";
				db_query($sql11);
			}
		}
		if (is_module_active("clanwarvault")){
				$cdef = get_module_objpref("clans",$clanid,"def","clanwarvault");
				$wallhp = $cdef*250;
				if ($wallhp<25000){
					$wallhp=25000;
				}
			}else{
				$wallhp = 25000;
			}
		//}
		output_notl("`n`n`^");
		output("Your Guilds walls are $wallhp");
		$sql="SELECT * FROM " .db_prefix("clans") . " WHERE `clanid` <> $clanid";
		$res=db_query($sql);
		$countrow=db_num_rows($res);
		for ($i=0;$i<$countrow;$i++){
			$row=db_fetch_assoc($res);
			$wallid=$row['clanid'];
			set_module_objpref("clans",$wallid,$num1,$wallhp,"clanpyramid");
			set_module_objpref("clans",$wallid,$num2,$wallhp,"clanpyramid");
			set_module_objpref("clans",$wallid,$num3,$wallhp,"clanpyramid");
			set_module_objpref("clans",$wallid,$num4,$wallhp,"clanpyramid");
			set_module_objpref("clans",$wallid,$num5,$wallhp,"clanpyramid");
			set_module_objpref("clans",$wallid,$num6,$wallhp,"clanpyramid");
			set_module_objpref("clans",$wallid,$num7,$wallhp,"clanpyramid");
			set_module_objpref("clans",$wallid,$num8,$wallhp,"clanpyramid");
			set_module_objpref("clans",$wallid,$num,$wallhp,"clanpyramid");
		}
	$sqle = "SELECT $acc.name AS name,
	$acc.acctid AS acctid,
	$mp.value AS square,
	$mp.userid FROM $mp INNER JOIN $acc
	ON $acc.acctid = $mp.userid
	WHERE $mp.modulename = 'clanpyramid'
	AND $mp.setting = 'square'
	AND $mp.userid <> ".$u['acctid']."
	AND $mp.value > 0
	AND $mp.value < 900
	";
	$rese=db_query($sqle);
	if (db_num_rows($rese)>0){
		$countrow=db_num_rows($rese);
		for ($i=0;$i<$countrow;$i++){
			$rowe = db_fetch_assoc($rese);
			$allid = $rowe['acctid'];
			set_module_pref("square",900,"clanpyramid",$allid);
		}
	}
}elseif ($clanid==$owned1){
	output("Your Guild is successful and the Vault has been captured.");
	output_notl("`n");
}
villagenav();
page_footer();
}
clanpyramid_squarenames($squarenew);
$doort1=get_module_objpref("clans",$owned1,"doort","clanpyramid");
if ((in_array(($squarenew+13),$wallarray)  || in_array(($squarenew+13),$blockarray) ||($squarenew+13)==98 || (in_array(($squarenew+13),$tarray1) && ($squarenew+13)<>$doort1)) && $defender<>1){
	if (in_array(($squarenew+13),$darray1)){
		if (get_module_objpref("clans",$clanid,($squarenew+13),"clanpyramid")>0){
			output("There is a wall to the south, that way is blocked.");
			output_notl("`n");
			blocknav("runmodule.php?module=clanpyramid&op=move&move=south&p=1");
			addnav("Hit the Wall","runmodule.php?module=clanpyramid&op=wall&hit=south&p=1");
		}elseif (get_module_objpref("clans",$clanid,($squarenew+13),"clanpyramid")<=0){
			output("Your Guild has already taken down this wall");
			output_notl("`n");
		}
	}elseif (!in_array(($squarenew+13),$darray1)){
		output("There is a wall to the south, it is not a door.");
		output_notl("`n");
		blocknav("runmodule.php?module=clanpyramid&op=move&move=south&p=1");
	}elseif (($squarenew+13)==98 && ($squarenew+13)<>$doorto){
		output("There is a wall to the south, it is the throne, you must now find the throne door");
		output_notl("`n");
		blocknav("runmodule.php?module=clanpyramid&op=move&move=south&p=1");
}
}
if ((in_array(($squarenew+1),$wallarray) || in_array(($squarenew+1),$blockarray) || (in_array(($squarenew+1),$tarray1) && ($squarenew+1)<>$doort1)) && $defender<>1){
	if (in_array(($squarenew+1),$darray1)){
		if (get_module_objpref("clans",$clanid,($squarenew+1),"clanpyramid")>0){
			blocknav("runmodule.php?module=clanpyramid&op=move&move=east&p=1");
			output("There is a wall to the east, that way is blocked.");
			output_notl("`n");
			addnav("Hit the Wall","runmodule.php?module=clanpyramid&op=wall&hit=east&p=1");
		}elseif (get_module_objpref("clans",$clanid,($squarenew+1),"clanpyramid")<=0){
			output("Your Guild has already taken down this wall");
			output_notl("`n");
		}
	}elseif (!in_array(($squarenew+1),$darray1)){
		output("There is a wall to the east, it is not a door.");
		output_notl("`n");
		blocknav("runmodule.php?module=clanpyramid&op=move&move=east&p=1");
	}elseif (in_array(($squarenew+1),$tarray1) && ($squarenew+1)<>$doort1){
		output("There is a wall to the east, it is the throne, you must now find the throne door");
		output_notl("`n");
		blocknav("runmodule.php?module=clanpyramid&op=move&move=east&p=1");
	}
}
if ((($squarenew+1)==$doort1 || ($squarenew+13)==$doort1 || ($squarenew-1)==$doort1 || ($squarenew-13)==$doort1) && $defender<>1){
	if (get_module_objpref("clans",$clanid,"doort","clanpyramid")>0){
		output("You have found the throne door");
		output_notl("`n");
		addnav("Hit the Throne","runmodule.php?module=clanpyramid&op=wall&hit=throne&p=1");
		output("There is a wall to the north, that way is blocked.");
	}elseif (get_module_objpref("clans",$clanid,"doort","clanpyramid")<=0){
		output("Your Guild has already taken down the throne.");
		output_notl("`n");
		addnav("Through the wall","runmodule.php?module=clanpyramid&op=move&move=throneg&p=1");
	}
	blocknav("runmodule.php?module=clanpyramid&op=wall&hit=south");
	blocknav("runmodule.php?module=clanpyramid&op=move&move=south&p=1");
	blocknav("runmodule.php?module=clanpyramid&op=move&move=east&p=1");
	blocknav("runmodule.php?module=clanpyramid&op=wall&hit=east&p=1");
	blocknav("runmodule.php?module=clanpyramid&op=move&move=north&p=1");
	blocknav("runmodule.php?module=clanpyramid&op=wall&hit=north");
	blocknav("runmodule.php?module=clanpyramid&op=wall&hit=west");
	blocknav("runmodule.php?module=clanpyramid&op=move&move=west&p=1");
}
if ((in_array(($squarenew-13),$wallarray) || in_array(($squarenew-13),$blockarray) || ($squarenew-13)==124 || (in_array(($squarenew-13),$tarray1) && ($squarenew-13)<>$doort1)) && $defender<>1){
	if (in_array(($squarenew-13),$darray1)){
		if (get_module_objpref("clans",$clanid,($squarenew-13),"clanpyramid")>0){
			blocknav("runmodule.php?module=clanpyramid&op=move&move=north&p=1");
			output("There is a wall to the north, that way is blocked.");
			output_notl("`n");
			addnav("Hit the Wall","runmodule.php?module=clanpyramid&op=wall&hit=north&p=1");
		}elseif (get_module_objpref("clans",$clanid,($squarenew-13),"clanpyramid")<=0){
			output("Your Guild has already taken down this wall");
			output_notl("`n");
		}
	}elseif (!in_array(($squarenew-13),$darray1)){
		output("There is a wall to the north, it is not a door.");
		output_notl("`n");
		blocknav("runmodule.php?module=clanpyramid&op=move&move=north&p=1");
	}elseif (($squarenew+13)==124 && ($squarenew+13)<>$doort1){
		output("There is a wall to the north, it is the throne, you must now find the throne door");
		output_notl("`n");
		blocknav("runmodule.php?module=clanpyramid&op=move&move=north&p=1");
	}
}
if ((in_array(($squarenew-1),$wallarray) || in_array(($squarenew-1),$blockarray) || (in_array(($squarenew-1),$tarray1) && ($squarenew-1)<>$doort1)) && $defender<>1){
	if (in_array(($squarenew-1),$darray1)){
		if (get_module_objpref("clans",$clanid,($squarenew-1),"clanpyramid")>0){
			blocknav("runmodule.php?module=clanpyramid&op=move&move=west&p=1");
			output("There is a wall to the west, that way is blocked.");
			output_notl("`n");
			addnav("Hit the Wall","runmodule.php?module=clanpyramid&op=wall&hit=west&p=1");
		}elseif (get_module_objpref("clans",$clanid,($squarenew-1),"clanpyramid")<=0){
			output("Your Guild has already taken down this wall");
			output_notl("`n");
		}
	}elseif (!in_array(($squarenew-1),$darray1)){
		output("There is a wall to the west, it is not a door.");
		output_notl("`n");
		blocknav("runmodule.php?module=clanpyramid&op=move&move=west&p=1");
	}elseif (in_array(($squarenew-1),$tarray1) && ($squarenew-1)<>$doort1){
		output("There is a wall to the west, it is the throne, you must now find the throne door");
		output_notl("`n");
		blocknav("runmodule.php?module=clanpyramid&op=move&move=west&p=1");
	}
}
if (!in_array($squarenew,$northarray)){
	addnav("North","runmodule.php?module=clanpyramid&op=move&move=north&p=1");
}
if (!in_array($squarenew,$southarray)){
	addnav("South","runmodule.php?module=clanpyramid&op=move&move=south&p=1");
}
if (!in_array($squarenew,$westarray)){
	addnav("West","runmodule.php?module=clanpyramid&op=move&move=west&p=1");
}
if (!in_array($squarenew,$eastarray)){
	addnav("East","runmodule.php?module=clanpyramid&op=move&move=east&p=1");
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
AND $mp.value = '$squarenew'
AND $acc.loggedin = $loggedin
AND $acc.laston>'$last'
";
$res=db_query($sql);
if (db_num_rows($res)>0){
	$opp=db_num_rows($res);
}elseif (!db_num_rows($res)){
	$opp=0;
}
if ($opp<>0){
	output("There are %s warriors from other Guilds rushing towards you",$opp);
	output_notl("`n");
	addnav("Attack Warriors","runmodule.php?module=clanpyramid&op=warriors&p=1");
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
AND $acc.loggedin = $loggedin
AND $acc.laston>'$last'
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
if ($squarenew==1){
	blocknav("runmodule.php?module=clanpyramid&op=move&move=north&p=1");
	villagenav();
}
if ($defender==1){
	require_once("modules/clanpyramid/defender_func.php");
	clanpyramid_defender($squarenew);
}
?>
