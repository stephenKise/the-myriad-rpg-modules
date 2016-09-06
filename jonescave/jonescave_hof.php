<?php
function jonescave_hof(){
	global $session;
	page_header("Hall of Fame");
	$pp = get_module_setting("pp");
	$page = httpget('page');
	$pageoffset = (int)$page;
	if ($pageoffset > 0) $pageoffset--;
	$pageoffset *= $pp;
	$limit = "LIMIT $pageoffset,$pp";
	$sql = "SELECT COUNT(*) AS c FROM " . db_prefix("module_userprefs") . " WHERE modulename = 'jonescave' AND setting = 'treasurehof' AND value > 0";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$total = $row['c'];
	$count = db_num_rows($result);
	if (($pageoffset + $pp) < $total){
		$cond = $pageoffset + $pp;
	}else{
		$cond = $total;
	}
	$sql = "SELECT ".db_prefix("module_userprefs").".value, ".db_prefix("module_userprefs").".userid, ".db_prefix("accounts").".name FROM " . db_prefix("module_userprefs") . "," . db_prefix("accounts") . " WHERE acctid = userid AND modulename = 'jonescave' AND setting = 'treasurehof' AND value > 0 ORDER BY (value+0) DESC $limit";
	$result = db_query($sql);
	$rank = translate_inline("Rank");
	$name = translate_inline("Name");
	$title = translate_inline("Title");
	$numtreas = translate_inline("Artifacts Found");
	$none = translate_inline("No Archaeologists");
	output("`b`c`@Most Famous Archaeologists In The Land`c`b`n`n");
	rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
	rawoutput("<tr class='trhead'><td>$rank</td><td>$name</td><td>$title</td><td>$numtreas</td></tr>");
	if (db_num_rows($result)==0) output_notl("<tr class='trlight'><td colspan='4' align='center'>`&$none`0</td></tr>",true);
	else{
		for($i = $pageoffset; $i < $cond && $count; $i++) {
			$row = db_fetch_assoc($result);
			if ($row['name']==$session['user']['name']){
				rawoutput("<tr class='trhilight'><td>");
			}else{
				rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td>");
			}
			$j=$i+1;
			output_notl("$j.");
			rawoutput("</td><td>");
			output_notl("`&%s`0",$row['name']);
			rawoutput("</td><td>");
			//Code for this section by Arieswind
			$artfinder=get_module_pref("artfinder","jonescave",$row['userid']);
			if ($artfinder>9) $newtitle="`5`bWorld Renowned Archaeologist`b";
			elseif ($artfinder==9) $newtitle="`6Dean of Archaeology";
			elseif ($artfinder==8) $newtitle="`QDirector of Archaeology";
			elseif ($artfinder==7) $newtitle="`&Senior Professor of Archaeology";
			elseif ($artfinder==6) $newtitle="`\$Professor of Archaeology";
			elseif ($artfinder==5) $newtitle="`%Associate Professor of Archaeology";
			elseif ($artfinder==4) $newtitle="`!Assistant Professor of Archaeology";
			elseif ($artfinder==3) $newtitle="`#Teaching Assistant of Archaeology";
			elseif ($artfinder==2) $newtitle="`@Graduate Student of Archaeology";
			elseif ($artfinder==1) $newtitle="`^Student of Archaeology";
			elseif ($artfinder==0) $newtitle="`^Initiate of Archaeology";
			output_notl("`c`Q%s`c`0",$newtitle);
			rawoutput("</td><td>");
			output_notl("`c`b`Q%s`c`b`0",get_module_pref("treasurenum","jonescave",$row['userid']));
			rawoutput("</td></tr>");
        }
	}
	rawoutput("</table>");
	if ($total>$pp){
		addnav("Pages");
		for ($p=0;$p<$total;$p+=$pp){
			addnav(array("Page %s (%s-%s)", ($p/$pp+1), ($p+1), min($p+$pp,$total)), "runmodule.php?module=jonescave&op=hof&page=".($p/$pp+1));
		}
	}
	addnav("Return");
	addnav("Back to HoF", "hof.php");
	villagenav();
}
?>