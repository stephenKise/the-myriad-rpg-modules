<?php
global $session;
$u=&$session['user'];
$clanid=$u['clanid'];
$p=2;
$square = get_module_pref("square");
$userid=$u['acctid'];
$owned2=get_module_setting("owned2");
$defender=get_module_pref("defender");
$move = httpget('move');
$wall=httpget('wall');
$last = date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT", 900)." sec"));
$loggedin=1;
$acc= db_prefix("accounts");
$mp=db_prefix("module_userprefs");
//check for a change of owner.
// walls array
$northarray=array(1001,1002,1003,1004,1005,1006,1007,1008,1009,1010,1011,1012,1013);
$southarray=array(1170,1171,1172,1173,1174,1175,1176,1177,1178,1179,1180,1181,1182);
$westarray=array(1001,1014,1027,1040,1053,1066,1079,1092,1105,1118,1131,1144,1157,1170);
$eastarray=array(1013,1026,1039,1052,1065,1078,1091,1104,1117,1130,1143,1156,1169,1182);
//all
$wallarray=array(1002,1012,1014,1015,1018,1020,1021,1023,1025,1026,1029,1032,1035,1037,1041,1043,1044,1045,1047,1051,1053,1056,1059,1061,1062,1063,1067,1069,1073,1076,1080,1081,1083,1088,1084,1097,1099,1101,1103,1107,1110,1113,1115,1119,1121,1123,1126,1132,1135,1138,1139,1141,1144,1147,1149,1150,1153,1157,1158,1165,1168,1169,1171,1172,1174,1181);
//throne
$tarray1=array(1072,1085,1098);
if ($owned2==$clanid){
	set_module_pref("defender",1,"clanpyramid",$userid);
}elseif ($owned2<>$clanid && $defender==1){
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
if ($move=="back"){
	$squarenew=$square;
}
if ($move=="passage1"){
	$squarenew=1001;
}
if ($move=="passage2"){
	$squarenew=1013;
}
if ($move=="passage3"){
	$squarenew=1170;
}
if ($move=="passage4"){
	$squarenew=1182;
}
if ($move=="transport"){
	$squarenew=$square;
	if ($owned2==$clanid){
		set_module_pref("defender",1,"clanpyramid",$userid);
	}
}
if ($move=="guardian"){
	$squarenew=$wall;
	page_header("Level Guardian");
	output("You move through the wall, only to be confronted by a guardian");
	output_notl("`n");
	addnav("Guardian Fight","runmodule.php?module=clanpyramid&op=attack&p=2&f=2&wall=$wall");
	page_footer();
}
if($move=="throneg"){
	$squarenew=1085;
	page_header("Throne Guardian");
	output("You move through the wall, only to be confronted by the Throne Guardian");
	output_notl("`n");
	addnav("Guardian Fight","runmodule.php?module=clanpyramid&op=attack&p=2&f=3&wall=$wall");
	page_footer();
}
if($move=="thronec"){
	page_header("The Throne");
	if ($clanid<>$owned2){
		output("You have captured the Vault in the name of your clan");
		output_notl("`n");
		//add in the reset of pyramid to new clan
		set_module_setting("owned2",$clanid);
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
		AND $mp.value > 1000
		AND $mp.value < 2000
		";
		$resd=db_query($sqld);
		$countrow=db_num_rows($resd);
		$membersin = $countrow;
		$mpoints = $membersin*4;
		if (is_module_active("clanhof")){
			if ($countrow>0){
				for ($i=0;$i<$countrow;$i++){
					$rowd = db_fetch_assoc($resd);
					$allid = $rowd['acctid'];
					set_module_pref("cp",(get_module_pref("cp","clanhof",$allid)+4),"clanhof",$allid);
				}
			}
			set_module_pref("cp",(get_module_pref("cp","clanhof",$userid)+4),"clanhof",$userid);
		}
		$cw=get_module_objpref("clans",$clanid,"clanwins","clanpyramid")+$mpoints;
		set_module_objpref("clans",$clanid,"clanwins",$cw,"clanpyramid");
		output_notl("`n`n");
		output("You recieve %s Guild Points",$mpoints);
		$sql9="SELECT * FROM " .db_prefix ("module_objprefs"). " WHERE `modulename` = 'clanpyramid' AND `setting` > 1000 AND 'setting' < 1183";
		$res9 = db_query($sql9);
		$countrow=db_num_rows($res9);
		if ($countrow>0){
			for ($i=0;$i<$countrow;$i++){
				$row9 = db_fetch_assoc($res9);
				$setting=$row9['setting'];
				$sql10="DELETE	FROM ".db_prefix("module_objprefs")." WHERE `modulename` = 'clanpyramid' AND `setting` = '$setting'";
				db_query($sql10);
			}
		}
		$sqle = "SELECT $acc.name AS name,
		$acc.acctid AS acctid,
		$mp.value AS square,
		$mp.userid FROM $mp INNER JOIN $acc
		ON $acc.acctid = $mp.userid
		WHERE $mp.modulename = 'clanpyramid'
		AND $mp.setting = 'pyramid'
		AND $mp.userid <> ".$u['acctid']."
		AND $acc.loggedin = $loggedin
		AND $acc.laston>'$last'
		AND $mp.value = 2
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
	}elseif ($clanid==$owned2){
		output("Your clan is successful and the Vault has been captured.");
		output_notl("`n");
	}
	villagenav();
	page_footer();
}
clanpyramid_squarenames($squarenew);
$wallsouth = get_module_objpref("clans",$clanid,($squarenew+13),"clanpyramid");
if ((in_array(($squarenew+13),$wallarray) && $defender<>1)	|| (($squarenew+13)==1072 && $defender<>1)){
	if ($wallsouth>0){
		output("There is a wall to the south, that way is blocked.");
		output_notl("`n");
		blocknav("runmodule.php?module=clanpyramid&op=move&move=south&p=2");
		addnav("Hit the South Wall","runmodule.php?module=clanpyramid&op=wall&hit=south&p=2");
	}elseif ($wallsouth==0){
		if (is_module_active("clanwarvault")){
			$wallhp = get_module_objpref("clans",$owned2,"att","clanwarvault")*250;
			if ($wallhp<25000){
				$wallhp=25000;
			}
		}else{
			$wallhp = 25000;
		}
		set_module_objpref("clans",$clanid,($squarenew+13),$wallhp,"clanpyramid");
		output("There is a wall to the south, that way is blocked.");
		output_notl("`n");
		blocknav("runmodule.php?module=clanpyramid&op=move&move=south&p=2");
		addnav("Hit the South Wall","runmodule.php?module=clanpyramid&op=wall&hit=south&p=2");
	}elseif ($wallsouth<0){
		if (in_array(($squarenew+13),$tarray1)){
			addnav("Through the Wall","runmodule.php?module=clanpyramid&op=move&move=throneg&p=2");
			blocknav("runmodule.php?module=clanpyramid&op=move&move=south&p=2");
		}else{
			output("Your clan has already taken down this wall");
			output_notl("`n");
		}
	}
}
$walleast=get_module_objpref("clans",$clanid,($squarenew+1),"clanpyramid");
if ((in_array(($squarenew+1),$wallarray) && $defender<>1) || (in_array(($squarenew+1),$tarray1) && $defender<>1)){
	if ($walleast>0){
		blocknav("runmodule.php?module=clanpyramid&op=move&move=east&p=2");
		output("There is a wall to the east, that way is blocked.");
		output_notl("`n");
		addnav("Hit the East Wall","runmodule.php?module=clanpyramid&op=wall&hit=east&p=2");
	}elseif ($walleast==0){
		if (is_module_active("clanwarvault")){
			$wallhp = get_module_objpref("clans",$owned2,"att","clanwarvault")*250;
			if ($wallhp<25000){
				$wallhp=25000;
			}
		}else{
			$wallhp = 25000;
		}
		set_module_objpref("clans",$clanid,($squarenew+1),$wallhp,"clanpyramid");
		blocknav("runmodule.php?module=clanpyramid&op=move&move=east&p=2");
		output("There is a wall to the east, that way is blocked.");
		output_notl("`n");
		addnav("Hit the East Wall","runmodule.php?module=clanpyramid&op=wall&hit=east&p=2");
	}elseif ($walleast<0){
		if (in_array(($squarenew+1),$tarray1)){
			addnav("Through the Wall","runmodule.php?module=clanpyramid&op=move&move=throneg&p=2");
			blocknav("runmodule.php?module=clanpyramid&op=move&move=east&p=2");
		}else{
			output("Your clan has already taken down this wall");
			output_notl("`n");
		}
	}
}
$wallnorth=get_module_objpref("clans",$clanid,($squarenew-13),"clanpyramid");
if ((in_array(($squarenew-13),$wallarray)&&$defender<>1) || (in_array(($squarenew-13),$tarray1) && $defender<>1)){
	if ($wallnorth>0){
		blocknav("runmodule.php?module=clanpyramid&op=move&move=north&p=2");
		output("There is a wall to the north, that way is blocked.");
		output_notl("`n");
		addnav("Hit the North Wall","runmodule.php?module=clanpyramid&op=wall&hit=north&p=2");
	}elseif ($wallnorth==0){
		if (is_module_active("clanwarvault")){
			$wallhp = get_module_objpref("clans",$owned2,"att","clanwarvault")*250;
			if ($wallhp<25000){
				$wallhp=25000;
			}
		}else{
			$wallhp = 25000;
		}
		set_module_objpref("clans",$clanid,($squarenew-13),$wallhp,"clanpyramid");
		blocknav("runmodule.php?module=clanpyramid&op=move&move=north&p=2");
		output("There is a wall to the north, that way is blocked.");
		output_notl("`n");
		addnav("Hit the North Wall","runmodule.php?module=clanpyramid&op=wall&hit=north&p=2");
	}elseif (get_module_objpref("clans",$clanid,($squarenew-13),"clanpyramid")<=0){
		if (in_array(($squarenew-13),$tarray1)){
			addnav("Through the Wall","runmodule.php?module=clanpyramid&op=move&move=throneg&p=2");
			blocknav("runmodule.php?module=clanpyramid&op=move&move=north&p=2");
		}else{
			output("Your clan has already taken down this wall");
			output_notl("`n");
		}
	}
}
$wallwest=get_module_objpref("clans",$clanid,($squarenew-1),"clanpyramid");
if ((in_array(($squarenew-1),$wallarray)&&$defender<>1) || (in_array(($squarenew-1),$tarray1) && $defender<>1)){
	if ($wallwest>0){
		blocknav("runmodule.php?module=clanpyramid&op=move&move=west&p=2");
		output("There is a wall to the west, that way is blocked.");
		output_notl("`n");
		addnav("Hit the West Wall","runmodule.php?module=clanpyramid&op=wall&hit=west&p=2");
	}elseif ($wallwest==0){
		if (is_module_active("clanwarvault")){
			$wallhp = get_module_objpref("clans",$owned2,"att","clanwarvault")*250;
			if ($wallhp<25000){
				$wallhp=25000;
			}
		}else{
			$wallhp = 25000;
		}
		blocknav("runmodule.php?module=clanpyramid&op=move&move=west&p=2");
		output("There is a wall to the west, that way is blocked.");
		output_notl("`n");
		addnav("Hit the West Wall","runmodule.php?module=clanpyramid&op=wall&hit=west&p=2");
		set_module_objpref("clans",$clanid,($squarenew-1),$wallhp,"clanpyramid");
	}elseif ($wallwest<0){
		if (in_array(($squarenew-1),$tarray1)){
			blocknav("runmodule.php?module=clanpyramid&op=move&move=west&p=2");
			addnav("Through the Wall","runmodule.php?module=clanpyramid&op=move&move=throneg&p=2");
		}else{
			output("Your clan has already taken down this wall");
			output_notl("`n");
		}
	}
}
if (!in_array($squarenew,$northarray)){
	addnav("North","runmodule.php?module=clanpyramid&op=move&move=north&p=2");
}
if (!in_array($squarenew,$southarray)){
	addnav("South","runmodule.php?module=clanpyramid&op=move&move=south&p=2");
}
if (!in_array($squarenew,$westarray)){
	addnav("West","runmodule.php?module=clanpyramid&op=move&move=west&p=2");
}
if (!in_array($squarenew,$eastarray)){
	addnav("East","runmodule.php?module=clanpyramid&op=move&move=east&p=2");
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
$countrow1=db_num_rows($res);
if ($countrow1>0){
	$opp=$countrow1;
}elseif (!$countrow1){
	$opp=0;
}
if ($opp<>0){
	output("There are %s warriors from other clans rushing towards you",$opp);
	output_notl("`n");
	addnav("Attack Warriors","runmodule.php?module=clanpyramid&op=warriors&p=2");
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
if ($squarenew==1001){
	blocknav("runmodule.php?module=clanpyramid&op=move&move=north&p=2");
	villagenav();
}
if ($defender==1){
	require_once("modules/clanpyramid/defender_func.php");
	clanpyramid_defender($squarenew);
}
addnav("Give Up","runmodule.php?module=clanpyramid&op=giveup");
?>