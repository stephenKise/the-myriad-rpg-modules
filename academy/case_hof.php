<?php
	$mu = db_prefix("module_userprefs");
	$ac = db_prefix("accounts");
	page_header("Hall of Fame");
	$page = httpget('page');
	$pp = 25;
	$classarray = array(
				0=>translate_inline("Squire"),
				1=>translate_inline("Knight"),
				2=>translate_inline("Warlord")
	);
	$pageoffset = (int)$page;
	if ($pageoffset > 0) $pageoffset--;
	$pageoffset *= $pp;
	$limit = "LIMIT $pageoffset,$pp";
	$sql = "SELECT COUNT(*) AS c FROM $mu WHERE modulename = 'academy' AND setting = 'active' AND value > 0";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$total = $row['c'];
	$count = db_num_rows($result);
	if (($pageoffset + $pp) < $total){
		$cond = $pageoffset + $pp;
	}else{
		$cond = $total;
	}
	$sql = "SELECT a.value AS name, b.value AS class, (c.value+0) AS level, (d.value+0) AS acc, $ac.name AS fullname
			FROM $ac
			INNER JOIN $mu AS a ON $ac.acctid=a.userid
			INNER JOIN $mu AS b ON $ac.acctid=b.userid
			INNER JOIN $mu AS c ON $ac.acctid=c.userid
			INNER JOIN $mu AS d ON $ac.acctid=d.userid
			INNER JOIN $mu AS e ON $ac.acctid=e.userid
			WHERE (a.setting = 'name' AND a.modulename = 'academy')
			AND (b.setting = 'class' AND b.modulename = 'academy')
			AND (c.setting = 'level' AND c.modulename = 'academy')
			AND (d.setting = 'acc' AND d.modulename = 'academy')
			AND (e.setting = 'active' AND e.modulename = 'academy' AND e.value = '1')
			ORDER BY class DESC, level DESC, acc DESC $limit";
	$result = db_query($sql);
	$rank = translate_inline("Rank");
	$name = translate_inline("Name");
	$sname = translate_inline("Squire Name");
	$class = translate_inline("Class");
	output("`b`c`@Strongest Squires In The Land`c`b`n`n");
	rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
	rawoutput("<tr class='trhead'><td>$rank</td><td>$name</td><td>$sname</td><td>$class</td></tr>");
	if (db_num_rows($result)>0){
		$i = 0;
		while($row = db_fetch_assoc($result)){
			$i++;
			if ($row['fullname']==$session['user']['name']){
				rawoutput("<tr class='trhilight'><td>");
			}else{
				rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td>");
			}
			output_notl("$i.");
			rawoutput("</td><td>");
			output_notl("`&%s`0",$row['fullname']);
			rawoutput("</td><td>");
			output_notl("`c`b`Q%s`c`b`0",$row['name']);
			rawoutput("</td><td>");
			output_notl("`c`b`Q%s`c`b`0",$classarray[$row['class']]);
			rawoutput("</td></tr>");
		}
	}
	rawoutput("</table>");
	if ($total>$pp){
		addnav("Pages");
		for ($p=0;$p<$total;$p+=$pp){
			addnav(array("Page %s (%s-%s)", ($p/$pp+1), ($p+1), min($p+$pp,$total-1)), "runmodule.php?module=academy&op=hof&page=".($p/$pp+1));
		}
	}
	addnav("Other");
	addnav("Back to HoF", "hof.php");
?>