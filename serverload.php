<?php

function serverload_getmoduleinfo(){
	$info = array(
		"name"=>"Server Performance Statistics",
		"author"=>"Dan Hall",
		"version"=>"2009-10-29",
		"category"=>"Administrative",
		"download"=>"",
		"allowanonymous"=>"true",
		"override_forced_navs"=>"true",
		"settings"=>array(
			"time"=>"Total pagegen time since last update,hidden,float|0",
			"count"=>"Total page load count since last update,hidden,int|0",
			"ltime"=>"Total pagegen time at last update,hidden,float|0",
			"lcount"=>"Total page load count at last update,hidden,int|0",
			"lnoobtime"=>"Total pagegen time among new players at last update,hidden,float|0",
			"lnoobcount"=>"Total page load count among new players at last update,hidden,int|0",
		),
	);
	
	return $info;
}

function serverload_install(){
	$performance = array(
		'numplayers'=>array('name'=>'numplayers', 'type'=>'int(11) unsigned'),
		'totaltime'=>array('name'=>'totaltime', 'type'=>'double unsigned'),
		'totalpages'=>array('name'=>'totalpages', 'type'=>'int(11) unsigned'),
		'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'numplayers'),
	);
	require_once("lib/tabledescriptor.php");
	synctable(db_prefix('performance'), $performance, true);
	$performancetime = array(
		'timeslice'=>array('name'=>'timeslice', 'type'=>'int(11) unsigned'),
		'totaltime'=>array('name'=>'totaltime', 'type'=>'double unsigned'),
		'totalpages'=>array('name'=>'totalpages', 'type'=>'int(11) unsigned'),
		'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'timeslice'),
	);
	synctable(db_prefix('performancetime'), $performancetime, true);
	module_addhook("player-login");
	module_addhook("player-logout");
	module_addhook("superuser");
	return true;
}

function serverload_dohook($hookname, $args){
	global $session;
	switch($hookname){
		case "player-login":
		case "player-logout":
			serverload_update();
		break;
		case "superuser":
		if ($session['user']['superuser'] & SU_MEGAUSER)
		{
			addnav("Mechanics");
			addnav("Check Server Stats","runmodule.php?module=serverload");
		}
		break;
	}
	return $args;
}

function serverload_uninstall(){
	return true;
}

function serverload_run(){
	global $session;
	page_header("Current Server Load");
	require_once("lib/su_access.php");
	check_su_access("SU_MEGAUSER");
	addnav("Options");
	addnav("Return to Grotto","superuser.php");
	$load = exec("uptime");
	$load = explode("load average:", $load);
	$load = explode(", ", $load[1]);
	output("`bCPU Load averages`b`n");
	output("One minute: %s`nFive minutes: %s`nFifteen minutes: %s`n`n",$load[0],$load[1],$load[2]);
	
	$online = 0;
	$noobsonline = 0;
	$loggedintoday = 0;
	$loggedinthisweek = 0;
	$joinedtoday = 0;
	$totalplayers = 0;
	
	$sql = "SELECT regdate, dragonkills, laston, gentimecount, gentime, loggedin FROM " . db_prefix("accounts") . "";
	$result = db_query($sql);
	for ($i=0;$i<db_num_rows($result);$i++){
		$totalplayers++;
		$row = db_fetch_assoc($result);
		$lastontime = strtotime($row['laston']);
		$regtime = strtotime($row['regdate']);
		$curtime = date(U);
		$sincelogon = $curtime - $lastontime;
		$sincereg = $curtime - $regtime;
		if ($sincelogon < getsetting("LOGINTIMEOUT",900) && $row['loggedin'] == 1) $online++;
		if ($sincelogon < 86400) $loggedintoday++;
		if ($sincelogon < 604800) $loggedinthisweek++;
		if ($sincereg < 86400){
			$joinedtoday++;
			if ($sincelogon < getsetting("LOGINTIMEOUT",900) && $row['loggedin'] == 1) $noobsonline++;
		}
		$t_time += $row['gentime'];
		$t_count += $row['gentimecount'];
	}
	
	output("`bPlayer Count`b`n");
	output("Total Players: %s`n",$totalplayers);
	output("Joined today: %s`n",$joinedtoday);
	output("Logged in today: %s`n",$loggedintoday);
	output("Logged in this week: %s`n",$loggedinthisweek);
	output("Online players: %s`n",$online);
	output("New players online right now: %s`n`n",$noobsonline);
	
	$time = $t_time - get_module_setting("ltime");
	$count = $t_count - get_module_setting("lcount");	
	
	output("`bRecent performance statistics since last login/logout operation`b`n");
	output("Total pages loaded: %s`n",$count);
	output("Total page gen time: %s`n",$time);
	if ($count) output("Average page gen time: `b%s`b`n",$time/$count);
	output_notl("`n");
	
	//Show player number table
	$sql = "SELECT numplayers, totalpages, totaltime FROM " . db_prefix("performance") . "";
	$result = db_query($sql);
	output("`bAverage Page Generation Times by number of online players`b`n");
	rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' width='100%'>");
	rawoutput("<tr class='trhead'><td>Online Players</td><td>Total Count</td><td>Total Time</td><td>Average Time / Page</td></tr>");
	for ($i=0;$i<db_num_rows($result);$i++){
		$row = db_fetch_assoc($result);
		if ($row['totalpages']>=1){
			$avg = $row['totaltime']/$row['totalpages'];
			$max = 100;
			$bwidth = round($avg*100);
			$bnonwidth = $max-$bwidth;
			if ($bnonwidth>0){
				$bar = "<table style='border: solid 1px #000000' width='$max' height='7' bgcolor='#333333' cellpadding=0 cellspacing=0><tr><td width='$bwidth' bgcolor='#00ff00'></td><td width='$bnonwidth'></td></tr></table>";
			} else {
				$over = $bwidth-$max;
				$total = $max+$over;
				$bar = "<table style='border: solid 1px #000000' height='7' width='$total' cellpadding=0 cellspacing=0><tr><td width='$max' bgcolor='#990000'></td><td width='$over' bgcolor='#ff0000'></td></tr></table>";
			}
			
			$row['numplayers']==$online?rawoutput("<tr class='trhilight'>"):rawoutput("<tr class='".($i%2?"trdark":"trlight")."'>");
			rawoutput("<td>".$row['numplayers']."</td><td>".number_format($row['totalpages'])."</td><td>".$row['totaltime']."</td><td>".$bar.round($row['totaltime']/$row['totalpages'],4)."</td></tr>");
		}
	}
	rawoutput("</table>");
	
	//Show time of day table
	$timeslice = floor((time()-strtotime(date("Y-m-d 00:00:00")))/600);
	$sql = "SELECT * FROM " . db_prefix("performancetime") . "";
	$result = db_query($sql);
	$now = date("h:i a");
	output("`n`n`bAverage Page Generation Times by Time of Day (server time)`b`nResults are calculated whenever a player logs in or out, so if it's been a while since a login/logout operation, this data may be slightly inaccurate.  All times are GMT, and the current server time is %s.`n",$now);
	rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' width='100%'>");
	rawoutput("<tr class='trhead'><td>Time Period</td><td>Total Count</td><td>Total Time</td><td>Average Time / Page</td></tr>");
	for ($i=0;$i<db_num_rows($result);$i++){
		$row = db_fetch_assoc($result);
		if ($row['totalpages']>=1){
			$avg = $row['totaltime']/$row['totalpages'];
			$max = 100;
			
			$bwidth = round($avg*100);
			$bnonwidth = $max-$bwidth;
			
			if ($bnonwidth>0){
				$bar = "<table style='border: solid 1px #000000' width='$max' height='7' bgcolor='#333333' cellpadding=0 cellspacing=0><tr><td width='$bwidth' bgcolor='#00ff00'></td><td width='$bnonwidth'></td></tr></table>";
			} else {
				$over = $bwidth-$max;
				$total = $max+$over;
				$bar = "<table style='border: solid 1px #000000' height='7' width='$total' cellpadding=0 cellspacing=0><tr><td width='$max' bgcolor='#990000'></td><td width='$over' bgcolor='#ff0000'></td></tr></table>";
			}
			
			$row['timeslice']==$timeslice?rawoutput("<tr class='trhilight'>"):rawoutput("<tr class='".($i%2?"trdark":"trlight")."'>");
			$timedispstart = strtotime(date("Y-m-d 00:00:00"))+($row['timeslice']*600);
			$timedispend = $timedispstart+600;
			$timedisp = date("h:i a",$timedispstart)." to ".date("h:i a",$timedispstart+600);
			rawoutput("<td>".$timedisp."</td><td>".number_format($row['totalpages'])."</td><td>".$row['totaltime']."</td><td>".$bar.round($row['totaltime']/$row['totalpages'],4)."</td></tr>");
		}
	}
	rawoutput("</table>");

	
	
	page_footer();
}

function serverload_update(){
	$online=0;
	$t_time=0;
	$t_count=0;
	
	//get current ten-minute slice of time
	$timeslice = floor((time()-strtotime(date("Y-m-d 00:00:00")))/600);
	
	$sql = "SELECT laston, gentimecount, gentime, loggedin FROM " . db_prefix("accounts") . "";
	$result = db_query($sql);
	for ($i=0;$i<db_num_rows($result);$i++){
		$row = db_fetch_assoc($result);
		$lastontime = strtotime($row['laston']);
		$regtime = strtotime($row['regdate']);
		$curtime = date(U);
		$sincelogon = $curtime - $lastontime;
		if ($sincelogon < getsetting("LOGINTIMEOUT",900) && $row['loggedin'] == 1) $online++;
		$t_time += $row['gentime'];
		$t_count += $row['gentimecount'];
	}
	
	$time = $t_time - get_module_setting("ltime");
	$count = $t_count - get_module_setting("lcount");
	
	
	if ($online){
		//update player number table
		if ($count > 1 && $time > 0){
			$avg = $time/$count;
			$sql = "UPDATE " . db_prefix("performance") . " SET totalpages=totalpages+$count, totaltime=totaltime+$time WHERE numplayers=$online";
			db_query($sql);
			if (!db_affected_rows()) db_query("INSERT INTO " . db_prefix("performance") . " (numplayers, totalpages, totaltime) VALUES ($online, $count, $time)");
		}
		
		//update time of day table
		if ($count > 1 && $time > 0){
			$avg = $time/$count;
			$sql = "UPDATE " . db_prefix("performancetime") . " SET totalpages=totalpages+$count, totaltime=totaltime+$time WHERE timeslice=$timeslice";
			db_query($sql);
			if (!db_affected_rows()) db_query("INSERT INTO " . db_prefix("performancetime") . " (timeslice, totalpages, totaltime) VALUES ($timeslice, $count, $time)");
		}
	}
	
	set_module_setting("ltime",$t_time);
	set_module_setting("lcount",$t_count);
}

?>