<?php
function medcontest_reset(){
	global $session;
	require_once("lib/systemmail.php");
	set_module_setting('lastreset',date("Y-m-d"));
	$sql = "SELECT value FROM ".db_prefix("module_userprefs")." WHERE modulename = 'medcontest' and setting = 'medhunt' and value > 0 ORDER BY value+0 DESC";
	$result = db_query($sql);
	$totalpot=15000;
	$firstplace=7500;
	$secondplace=5000;
	$thirdplace=2500;
	// $firstplace=$firstplace-$thirdplace;
	$sql = "SELECT userid,value FROM ".db_prefix("module_userprefs")." WHERE modulename = 'medcontest' and setting = 'medpoints' ORDER BY value+0 DESC";
	//first place
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$sql2="SELECT name FROM ".db_prefix("accounts")." WHERE acctid ='".$row['userid']."'";
    $result2 = db_query($sql2);
    $row2 = db_fetch_assoc($result2);
    addnews("%s `7has won %s gems by finding %s stars and winning the Star Catching contest!",($row2['name']?$row2['name']:"`&No-One`7"),$firstplace,$medpoints);
    if ($row2['name']==$session['user']['name']){
	    $session['user']['gems']+=$firstplace;
    }else{
	    $sql3 = "UPDATE ".db_prefix("accounts")." SET gems=gems+$firstplace where acctid = '".$row['userid']."'";
    	db_query($sql3);
	}
    $topmed=$row['value'];
    $topplayer=$row2['name'];
    systemmail($row['userid'],"`4Congratulations!","You have won the Star Catching Contest!  You have been awarded $firstplace gems!");
    //second place
    $row = db_fetch_assoc($result);
    $sql2="SELECT name FROM ".db_prefix("accounts")." WHERE acctid ='".$row['userid']."'";
    $result2 = db_query($sql2);
    $row2 = db_fetch_assoc($result2);
    addnews("%s `7has placed second in the Star Catching contest by finding %s stars, winning %s gems!",($row2['name']?$row2['name']:"`&No-One`7"),$medpoints,$secondplace);
    if ($row2['name']==$session['user']['name']){
	    $session['user']['gems']+=$secondplace;
    }else{
	   $sql3 = "UPDATE ".db_prefix("accounts")." SET gems=gems+$secondplace where acctid = '".$row['userid']."'";
	   db_query($sql3);
	}
    systemmail($row['userid'],"`4Congratulations!","You have placed second in the Star Catching!  You have been awarded $secondplace gems!");
    //third place
    $row = db_fetch_assoc($result);
    $sql2="SELECT name FROM ".db_prefix("accounts")." WHERE acctid ='".$row['userid']."'";
    $result2 = db_query($sql2);
    $row2 = db_fetch_assoc($result2);
    addnews("%s `7has placed third the Star Catching contest by finding %s stars, winning %s gems!",($row2['name']?$row2['name']:"`&No-One`7"),$medpoints,$thirdplace);
    if ($row2['name']==$session['user']['name']){
	    $session['user']['gems']+=$thirdplace;
    }else{
    $sql3 = "UPDATE ".db_prefix("accounts")." SET gems=gems+$thirdplace where acctid = '".$row['userid']."'";
    db_query($sql3);
	}
    systemmail($row['userid'],"`4Congratulations!","You have placed third in the Star Catching Contest!  You have been awarded $thirdplace gems!");
    //now check and set top score
    if ($settings['medconthigh'] < $topmed){
	    savesetting("medconthigh",$topmed);
    	savesetting("medcontplay",$topplayer);
	}
    //now clear everything
    set_module_pref('medallion', 0);
    set_module_pref('medhunt', 0);
    set_module_pref('medpoints', 0);
    set_module_pref('medfind', 0);
    $sql3 = "DELETE FROM ".db_prefix("module_userprefs")." WHERE modulename = 'medcontest' and setting <> 'administrate' and userid <> ".$session['user']['acctid'];
    db_query($sql3);
    if (get_module_pref('administrate') == 1){
	    output("`4`bResetting Star Catching Contest!`b`n");
	    if (db_affected_rows()>0){
			output("`^Row Modified: %s !",db_affected_rows());
		}else{
			output("`#Table not revised: $sql3");
		}
	}
}
?>