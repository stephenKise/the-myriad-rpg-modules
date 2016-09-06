<?php
function walls2_hit(){
	global $session;
	$u=&$session['user'];
	$p=httpget('p');
	$owned2=get_module_setting("owned2");
	$square=get_module_pref("square");
	$northarray=array(1001,1002,1003,1004,1005,1006,1007,1008,1009,1010,1011,1012,1013);
	$southarray=array(1170,1171,1172,1173,1174,1175,1176,1177,1178,1179,1180,1181,1182);
	$westarray=array(1001,1014,1027,1040,1053,1066,1079,1092,1105,1118,1131,1144,1157,1170);
	$eastarray=array(1013,1026,1039,1052,1065,1078,1091,1104,1117,1130,1143,1156,1169,1182);
	//all
	$wallarray=array(1002,1012,1014,1015,1018,1020,1021,1023,1025,1026,1029,1032,1035,1037,1041,1043,1044,1045,1047,1051,1053,1056,1059,1061,1062,1063,1067,1069,1073,1076,1080,1081,1083,1088,1084,1097,1099,1101,1103,1107,1110,1113,1115,1119,1121,1123,1126,1132,1135,1138,1139,1141,1144,1147,1149,1150,1153,1157,1158,1165,1168,1169,1171,1172,1174,1181);
	//throne
	$tarray1=array(1072,1085,1098);
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
	if (!db_num_rows($res2)){
		$members=0;
	}else{
		$members=db_num_rows($res2);
	}
	if ($members>0){
		output("There are %s Guild members with you",$members);
		output_notl("`n");
	}
	$hit=httpget('hit');
	if ($hit=="north"){
		$wall = $square-13;
	}elseif ($hit=="south"){
		$wall=$square+13;
	}elseif ($hit=="west"){
		$wall=$square-1;
	}elseif ($hit=="east"){
		$wall=$square+1;
	}elseif ($hit=="return"){
		$wall=httpget('wall');
	}elseif ($hit=="throne"){
		$wall=$doort1;
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
	output("`nYour Guild helps you by boosting your attack by $catk`n");
	$tatk=$atk+$catk;
	$def=$u['defence'];
	$hp = get_module_objpref("clans",$clanid,$wall,"clanpyramid");
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
		set_module_objpref("clans",$clanid,$wall,$hp1,"clanpyramid");
		if ($hp1>0){
			output("You hit the wall for %s damage, there is %s hp left on the wall",$dam,$hp1);
			output_notl("`n");
			addnav("Hit","runmodule.php?module=clanpyramid&op=wall&hit=return&wall=$wall&p=$p");
			addnav("Return to Vault","runmodule.php?module=clanpyramid&op=move&move=return&p=$p&wall=$square");
		}elseif ($hp1<=0){
			output("You hit the wall for %s damage, the wall crumbles in front of you",$dam);
			output_notl("`n");
			if (in_array($wall,$wallarray)){
				addnav("Through the wall","runmodule.php?module=clanpyramid&op=move&move=guardian&p=$p&wall=$wall");
			}
			if (in_array($wall,$tarray1)){
				addnav("Through the wall","runmodule.php?module=clanpyramid&op=move&move=throneg&p=$p");
			}
		}
		blocknav("runmodule.php?module=clanpyramid&op=move");
	}
	page_footer();
}
?>
