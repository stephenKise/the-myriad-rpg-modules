<?php
//thanks to XChrisX for the shortened square name code
function clanpyramid_squarenames($squarenew){
	$which=httpget('p');
	if ($which == 1) {
		$squarename = chr(floor(($squarenew-1)/13)+65) . ((($squarenew-1) % 13) + 1);
		output_notl("`b");
		output("`^You are on %s", $squarename);
		output_notl("`b`n");
		if ($squarename=="R1"){
			addnav("List Warriors","runmodule.php?module=clanpyramid&op=listwarriors&p=1");
			villagenav();
		}
	}elseif ($which==2){
		$squarename = chr(floor(($squarenew-1001)/13)+65) . ((($squarenew-1001) % 13) + 1);
		output_notl("`b");
		output("`^You are on %s", $squarename);
		output_notl("`b`n");
		if ($squarename=="H4"){
			addnav("List Warriors","runmodule.php?module=clanpyramid&op=listwarriors&p=2");
			villagenav();
		}
	}elseif ($which==3){
		$squarename = chr(floor(($squarenew-2001)/13)+65) . ((($squarenew-2001) % 13) + 1);
		output_notl("`b");
		output("`^You are on %s", $squarename);
		output_notl("`b`n");
		if ($squarename=="M1"){
			addnav("List Warriors","runmodule.php?module=clanpyramid&op=listwarriors&p=3");
			villagenav();
		}
		if ($squarename=="A1"){
			villagenav();
		}
	}
}
function clanpyramid_warriorslist(){
	global $session;
	$u=&$session['user'];
	$which=httpget("p");
	$who = translate_inline("Name");
  	$clan = translate_inline("Guild");
	$square1 = translate_inline("Where");
	rawoutput("<table border='0' cellpadding='3' cellspacing='0' align='center'><tr class='trhead'><td style='width:250px' align=center>$who</td><td align=center>$clan</td><td align=centre>$square1</td></tr>"); 
	//enemy list
	$last = date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT", 900)." sec"));
	$loggedin=1;
	//$lastip=$u['lastip'];
	//$lastid=$u['uniqueid'];
	$acc = db_prefix("accounts");
	$mp = db_prefix("module_userprefs");
	output("Which - $which");
	if ($which==1){
		$sql = "SELECT $acc.name AS name,
		$acc.clanid AS clanid,
		$acc.acctid AS acctid,
		$mp.value AS square,
		$mp.userid FROM $mp INNER JOIN $acc
		ON $acc.acctid = $mp.userid 
		WHERE $mp.modulename = 'clanpyramid' 
		AND $mp.setting = 'square' 
		AND $mp.userid <> ".$u['acctid']."
		AND $acc.loggedin = $loggedin 
		AND $acc.laston <>'$last'
		AND $mp.value < 235
		";
	}
	if ($which==2){
		$sql = "SELECT $acc.name AS name,
		$acc.clanid AS clanid,
		$acc.acctid AS acctid,
		$mp.value AS square,
		$mp.userid FROM $mp INNER JOIN $acc
		ON $acc.acctid = $mp.userid 
		WHERE $mp.modulename = 'clanpyramid' 
		AND $mp.setting = 'square' 
		AND $mp.userid <> ".$u['acctid']."
		AND $acc.loggedin = $loggedin 
		AND $acc.laston <>'$last'
		AND $mp.value > 1000
		AND $mp.value < 2000
		";
	}
	if ($which==3){
		$sql = "SELECT $acc.name AS name,
		$acc.clanid AS clanid,
		$acc.acctid AS acctid,
		$mp.value AS square,
		$mp.userid FROM $mp INNER JOIN $acc
		ON $acc.acctid = $mp.userid 
		WHERE $mp.modulename = 'clanpyramid' 
		AND $mp.setting = 'square' 
		AND $mp.userid <> ".$u['acctid']."
		AND $acc.loggedin = $loggedin 
		AND $acc.laston <>'$last'
		AND $mp.value > 2000
		";
	}
	$res=db_query($sql);
	if(!db_num_rows($res)){
		$none = translate_inline("None");
       	rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td align=center  colspan=4><i>$none</i></td></tr>");
    }else{			
		for ($i=0;$i<db_num_rows($res);$i++){
			$row = db_fetch_assoc($res);
			$whoid = $row['acctid'];
			$sqla="SELECT $acc.name AS name,
			$acc.clanid AS clanid,
			$acc.acctid AS acctid,
			$mp.value AS alive,
			$mp.userid FROM $mp INNER JOIN $acc
			ON $acc.acctid = $mp.userid 
			WHERE $mp.modulename = 'clanpyramid' 
			AND $mp.setting = 'leave' 
			AND $mp.userid = $whoid
			AND $acc.loggedin = $loggedin 
			AND $acc.laston>'$last'
			AND $acc.acctid = $whoid
			AND $mp.value = 0
			ORDER BY '$acc.clanid'";
			$resa = db_query($sqla);
			if(!db_num_rows($resa)){
				$none = translate_inline("None");
        		rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td align=center  colspan=4><i>$none</i></td></tr>");
    		}else{	
				$rowa = db_fetch_assoc($resa);
	        	$whoname = $rowa['name'];
	   		   	$whoid = $rowa['acctid'];
	   		   	$whocid=$rowa['clanid'];
	    	   	$squarenew=$row['square'];
	    	   	if ($squarenew < 1000) {
	    	   		$squarename = chr(floor(($squarenew-1)/13)+65) . ((($squarenew-1) % 13) + 1);
					} else if ($squarenew < 2000) {
						$squarename = chr(floor(($squarenew-1001)/13)+65) . ((($squarenew-1001) % 13) + 1);
					} else if ($squarenew > 2000) {
						$squarename = chr(floor(($squarenew-2001)/13)+65) . ((($squarenew-2001) % 13) + 1);
					}
		        $num = $i+1;
        	    $sqlb = "SELECT * FROM " .db_prefix("clans"). " WHERE clanid = '$whocid'";
            	$resb = db_query($sqlb);
            	$rowb=db_fetch_assoc($resb);
	            $whocname = $rowb['clanshort'];
		        rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td>");
				output_notl($whoname);
				rawoutput("</td><td>");
		    	output_notl($whocname);
		    	rawoutput("</td><td>");
				output_notl($squarename);
		    	rawoutput("</td><td>");
		    }
			rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td align=left>");
		}
	}
	rawoutput("</table>");
}
function clanpyramid_enemylist($square){
	global $session;
	$u=&$session['user'];
	$clanid=$u['clanid'];
	$last = date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT", 900)." sec"));
	$loggedin=1;
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
	AND $acc.laston <>'$last'
	AND $mp.value = '$square'
	";
	$res=db_query($sql);
	$countrow=db_num_rows($res);
	if ($countrow>0){
		for ($i=0;$i<$countrow;$i++){
			$row = db_fetch_assoc($res);
			$oppid = $row['acctid'];
			$sqla="SELECT $acc.name AS name,
			$acc.clanid AS clanid,
			$acc.acctid AS acctid,
			$mp.value AS alive,
			$mp.userid FROM $mp INNER JOIN $acc
			ON $acc.acctid = $mp.userid 
			WHERE $mp.modulename = 'clanpyramid' 
			AND $mp.setting = 'leave' 
			AND $mp.userid = $oppid
			AND $acc.clanid <> $clanid 
			AND $acc.loggedin = $loggedin 
			AND $acc.laston>'$last'
			AND $acc.acctid = $oppid
			AND $mp.value = 0
			";
			$resa = db_query($sqla);
			$countrowa=db_num_rows($resa);
		}
		if (!$countrowa){
			$opp=0;
		}
		if ($countrowa>0){
			$opp=$countrowa;
		}
	}elseif (!$countrow){
		$opp=0;
	}	
	if ($opp<>0){
		output("There are %s warriors from other Guilds rushing towards you`n",$opp);
		addnav("Attack Warriors","runmodule.php?module=clanpyramid&op=warriors");
	}
}
function clanpyramid_take(){
	page_header();
	output("The Vault has been taken, return to the village."); 
	villagenav();
	page_footer();
}
function map_pyramid2($squarenew){
	require_once("modules/clanpyramid/maps.php");
}	
?>