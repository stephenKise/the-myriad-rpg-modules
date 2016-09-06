<?php
function walls_hit(){
global $session;
$u=&$session['user'];
$which=httpget('p');
$owned1=get_module_setting("owned1");
$square=get_module_pref("square");
if ($owned1==0){
	$sql4="SELECT * FROM " .db_prefix("clans"). " WHERE clanid <> '$clanid'";
	$res4=db_query($sql4);
	for ($i=0;$i<db_num_rows($res4);$i++){
		$row4 = db_fetch_assoc($res4);
		$oppcid = $row4['clanid'];
		$doorno="32";
		$doorso="206";
		$dooreo="80";
		$doorwo="64";
		$doorni="60";
		$doorsi="161";
		$doorwi="121";
		$doorei="140";
		$doort="111";
		}
	}elseif($owned1<>0){
		$doorno=get_module_objpref("clans",$owned1,"doorno","clanpyramid");
		$doorso=get_module_objpref("clans",$owned1,"doorso","clanpyramid");
		$dooreo=get_module_objpref("clans",$owned1,"dooreo","clanpyramid");
		$doorwo=get_module_objpref("clans",$owned1,"doorwo","clanpyramid");
		$doorni=get_module_objpref("clans",$owned1,"doorni","clanpyramid");
		$doorsi=get_module_objpref("clans",$owned1,"doorsi","clanpyramid");
		$doorwi=get_module_objpref("clans",$owned1,"doorwi","clanpyramid");
		$doorei=get_module_objpref("clans",$owned1,"doorei","clanpyramid");
		$doort=get_module_objpref("clans",$owned1,"doort","clanpyramid");
	}
	$doort1=get_module_objpref("clans",$owned1,"doort","clanpyramid");
	$darray1a=array($doorno,$doorso,$dooreo,$doorwo,$doorni,$doorsi,$doorwi,$doorei,$doort);
	$darray1=array($doorno,$doorso,$dooreo,$doorwo,$doorni,$doorsi,$doorwi,$doorei);
	$wallsarray1=array(229,30,31,32,33,34,35,36,37);
	$wallsarray2=array(198,199,200,201,202,203,204,205,206);
	$wallsarray3=array(41,54,67,80,93,106,119,132,145,158,171,184);
	$wallsarray4=array(51,64,77,90,103,116,129,142,155,168,181,194);
	//inner
	$wallsarray5=array(57,58,59,60,61);
	$wallsarray6=array(161,162,163,164,165);
	$wallsarray7=array(69,82,95,108,121,134,147);
	$wallsarray8=array(75,88,101,114,127,140,153);
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
	}else{
		$wall=$square;
	}
	page_header("The Door");
	if (in_array($wall,$darray1a)){
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
				addnav("Hit","runmodule.php?module=clanpyramid&op=wall&hit=return&wall=$wall&p=1");
				addnav("Return to Vault","runmodule.php?module=clanpyramid&op=move&move=return&wall=$wall&p=1");
			}elseif ($hp1<=0){
				output("You hit the wall for %s damage, the wall crumbles in front of you",$dam);
				output_notl("`n");
				if (in_array($wall,$wallsarray1) || in_array($wall,$wallsarray5)){
					addnav("Through the wall","runmodule.php?module=clanpyramid&op=move&move=southg&p=1&wall=$wall");
				}
				if (in_array($wall,$wallsarray2) || in_array($wall,$wallsarray6)){
					addnav("Through the wall","runmodule.php?module=clanpyramid&op=move&move=northg&p=1&wall=$wall");
				}
				if (in_array($wall,$wallsarray3) || in_array($wall,$wallsarray7)){
					addnav("Through the wall","runmodule.php?module=clanpyramid&op=move&move=eastg&p=1&wall=$wall");
				}
				if (in_array($wall,$wallsarray4) || in_array($wall,$wallsarray8)){
					addnav("Through the wall","runmodule.php?module=clanpyramid&op=move&move=westg&p=1&wall=$wall");
				}
				if ($wall==$doort1){
					addnav("Through the wall","runmodule.php?module=clanpyramid&op=move&move=throneg&p=1&wall=$wall");
				}
				blocknav("runmodule.php?module=clanpyramid&op=move");
			}
		}
		}elseif (!in_array($wall,$darray1a)){
			output("This wall is impenetrable, perhaps you should look for a door?");
			output_notl("`n");
			addnav("Return to Vault","runmodule.php?module=clanpyramid&op=move&move=return&wall=$wall&p=1");
	}
	page_footer();
}
function hidden_passage(){
	page_header("Hidden Passage");
	output("You have found a hidden passage which leads you back to....`");
	output_notl("`n`n");
	addnav("Hidden Passage","runmodule.php?module=clanpyramid&op=move&move=passage&p=1");
	set_module_pref("square",1);
	page_footer();
}
?>
