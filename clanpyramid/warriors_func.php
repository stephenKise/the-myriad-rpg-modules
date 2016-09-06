<?php
function warrior_attack($p){
	global $session;
	$u=&$session['user'];
	$p=httpget('p');
	$clanid=$u['clanid'];
	$last = date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT", 905)." sec"));
	$loggedin=1;
	$lastip = $u['lastip'];
	$userid=$u['acctid'];
	$lastid = $u['uniqueid'];
	$acc = db_prefix("accounts");
	$mp = db_prefix("module_userprefs");
	$square=get_module_pref("square");
	$enemy=httpget('warrior');
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
	AND $mp.value = '$squarenew'
	";
	$res2=db_query($sql2);
	if (db_num_rows($res2)>0){
		$members=db_num_rows($resb);
	}elseif (!db_num_rows($res2)){
		$members=0;
	}
	if ($members>0){
		output("There are %s Guild members with you",$members);
		output_notl("`n");
	}
	if ($members==0){
		$clanbonus=1;
	}elseif($members<>0){
		$clanbonus=$members*0.5;
	}
	$atka = $u['attack']*$clanbonus;
	$defa=$u['defense']*$clanbonus;
	$sql3="SELECT * FROM " .db_prefix("accounts"). " WHERE acctid = '$enemy'";
	$res3 = db_query($sql3);
	$row3 = db_fetch_assoc($res3);
	$opatka = $row3['attack'];
	$opefa = $row3['defense'];
	$enemyname = $row3['name'];
	$adam=e_rand(1,$atka);
	$d=e_rand(1,$opdef);
			if ($adam<$d){
				$dam=($adam-$d*0.2);
			}elseif($adam==$d){
				$dam=0;
			}else{
				$dam=$adam-$d;
			}
	if ($dam>0){
		output("`^You hit %s `^for %s damage",$enemyname,$dam);
		$ophp = $row3['hitpoints']-=$dam;
		$sql = "UPDATE " . db_prefix("accounts") . " SET hitpoints='$ophp' WHERE acctid='$enemy'";
		db_query($sql);
		if ($ophp<=0){
			$optime=get_module_pref("square","clanpyramid",$enemy);
			if ($optime<>905){
				$sql = "UPDATE " . db_prefix("accounts") . " SET hitpoints=1 WHERE acctid='$enemy'";
				db_query($sql);
				$timenow = date("Y-m-d H:i:s");
				set_module_pref("time",$timenow,"clanpyramid",$enemy);
				set_module_pref("square",905,"clanpyramid",$enemy);
				output("and you kill them, you earn a Guild point for your Guild");
				set_module_pref("cp",(get_module_pref("cp","clanhof",$userid)+1),"clanhof",$userid);
				output_notl("`n");
				$cw=get_module_objpref("clans",$clanid,"clanwins","clanpyramid")+1;
				if ($cw<=0){
					$cw=0;
				}
				set_module_objpref("clans",$clanid,"clanwins",$cw,"clanpyramid");
				//add in clanpoint additions
			}elseif($optime==905){
				output(" you have killed them, however another has earnt the point");
				output_notl("`n");
			}
			$kills=get_module_pref("kills","clanhof",$userid)+1;
			set_module_pref("kills",$kills,"clanhof",$userid);
		}elseif ($ophp>0){
			$row3['hitpoints']=$ophp;
		}
	}elseif($dam==0){
		output("You miss");
	}elseif($dam<0){
		output("`4You are riposted by %s`4 for %s damage",$enemyname,$dam);
		$hp=$u['hitpoints']+=$dam;
		if ($hp<=0){
			$attime = get_module_pref("square","clanpyramid",$userid);
			if ($attime<>905){
				$u['hitpoints']=1;
				set_module_pref("square",905,"clanpyramid",$userid);
				$timenow = date("Y-m-d H:i:s");
				set_module_pref("time",$timenow,"clanpyramid",$userid);
				output(" and have been killed, you have just lost a Guild point for your Guild");
				output_notl("`n");
				$cw=get_module_objpref("clans",$clanid,"clanwins","clanpyramid")-1;
				if ($cw<=0){
					$cw=0;
				}
				set_module_objpref("clans",$clanid,"clanwins",$cw,"clanpyramid");
				$kills=get_module_pref("kills","clanhof",$enemy)+1;
				set_module_pref("kills",$kills,"clanhof",$enemy);
				$sqlz="SELECT * FROM " .db_prefix("accounts"). " WHERE acctid = '$enemy'";
				$resz=db_query($sqlz);
				$rowz=db_fetch_assoc($resz);
				$enemycid = $rowz['clanid'];
				$ecw = get_module_pref("clans",$enemycid,"clanwins","clanpyramid")+1;
				set_module_pref("clans",$enemycid,"clanwins",$ecw,"clanpyramid");
				//add in subtract clan point
			}elseif ($attime==905){
				output(" and killed again, luckily you can only loose one point per death");
				output_notl("`n");
			}
			villagenav();
			blocknav("runmodule.php?module=clanpyramid&op=warriors");
		}elseif ($hp>0){
			$u['hitpoints']=$hp;
		}
	}
	addnav("Warriors","runmodule.php?module=clanpyramid&op=warriors&p=$p");
}
function warriors_list($p){
	global $session;
	$u=&$session['user'];
	$p=httpget('p');
	$enemy = translate_inline("Enemy");
   $clan = translate_inline("Guild");
   $attack = translate_inline("Attack");
   $clanid=$u['clanid'];
   $square=get_module_pref("square");
	rawoutput("<table border='0' cellpadding='3' cellspacing='0' align='center'><tr class='trhead'><td style='width:250px' align=center>$enemy</td><td align=center>$clan</td><td align=centre>$attack</td></tr>"); 
   $last = date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT", 905)." sec"));
	$loggedin=1;
	//enemy list
	$acc = db_prefix("accounts");
	$mp = db_prefix("module_userprefs");
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
	AND $mp.value = '$square'
	";
	$res=db_query($sql);
	$opp=db_num_rows($res);
	if ($opp<>0){
		output("There are %s warriors from other Guilds rushing towards you",$opp);
		output_notl("`n");
	}
	if(!db_num_rows($res)){
		$none = translate_inline("None");
    	rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td align=center  colspan=4><i>$none</i></td></tr>");
   }else{
	   $countrow=db_num_rows($res);
   	for ($i = 0; $i < $countrow; $i++){ 
	    	$row = db_fetch_assoc($res);
			$enemyname = $row['name'];
   		$id = $row['acctid'];
    		$claneid=$row['clanid'];
    		$sql1="SELECT * FROM " .db_prefix("clans"). " WHERE clanid = '$claneid'";
	    	$res1 = db_query($sql1);
    		$row1 = db_fetch_assoc($res1);
    		$clanname = $row1['clanshort'];
	    	$num = $i+1;
    		rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>");
	    	output_notl($enemyname);
	    	output_notl($clanname);
	    	rawoutput("</td><td>");
		   rawoutput("<a href='runmodule.php?module=clanpyramid&op=warriorattack&warrior=$id&p=$p'>");
   		addnav("","runmodule.php?module=clanpyramid&op=warriorattack&warrior=$id&p=$p");
	      output_notl("`#[`&Attack`#]`0");
   	}
   }    
   rawoutput("</table>");
   addnav("Return to Vaults", "runmodule.php?module=clanpyramid&op=move&move=return&wall=$square&p=$p");
}
?>