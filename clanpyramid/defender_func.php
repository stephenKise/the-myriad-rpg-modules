<?php
function clanpyramid_defender($squarenew){
	global $session;
	$u=&$session['user'];
	$last = date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT", 900)." sec"));
			$loggedin=1;
			$clanid=$u['clanid'];
			$lastip = $u['lastip'];
			$lastid = $u['uniqueid'];
			$acc = db_prefix("accounts");
			$mp = db_prefix("module_userprefs");
			$square1 = $squarenew+13;
			$sql5 = "SELECT $acc.name AS name,
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
			AND $acc.lastip <> '$lastip'
			AND $acc.uniqueid <> '$lastid'
			AND $mp.value = '$square1'
			";
			$res5=db_query($sql5);
			$countrow=db_num_rows($res5);
			if ($countrow>0){
				$enemy = $countrow;
				output("  There are %s enemies to the south",$enemy);
				output_notl("`n");
			}
			$square2 = $squarenew-13;
			$sql6 = "SELECT $acc.name AS name,
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
			AND $acc.lastip <> '$lastip'
			AND $acc.uniqueid <> '$lastid'
			AND $mp.value = '$square2'
			";
			$res6=db_query($sql6);
			$countrow6=db_num_rows($res6);
			if ($countrow6>0){
				$enemy = $countrow6;
				output("  There are %s enemies to the north",$enemy);
				output_notl("`n");
			}
			$square3 = $squarenew-1;
			$sql7 = "SELECT $acc.name AS name,
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
			AND $acc.lastip <> '$lastip'
			AND $acc.uniqueid <> '$lastid'
			AND $mp.value = '$square3'
			";
			$res7=db_query($sql7);
			$countrow7=db_num_rows($res7);
			if ($countrow7>0){
				$enemy = $countrow7;
				output("  There are %s enemies to the west",$enemy);
				output_notl("`n");
			}
			$square4 = $squarenew+1;
			$sql8 = "SELECT $acc.name AS name,
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
			AND $acc.lastip <> '$lastip'
			AND $acc.uniqueid <> '$lastid'
			AND $mp.value = '$square4'
			";
			$res8=db_query($sql8);
			$countrow8=db_num_rows($res8);
			if ($countrow8>0){
				$enemy = $countrow8;
				output("  There are %s enemies to the east",$enemy);
				output_notl("`n");
			}
}
?>