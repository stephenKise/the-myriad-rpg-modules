<?php
	$action = httpget('action');
	$op = httpget('op');
	page_header("Newly Weds");
	addnav('Navigation');
	if (get_module_setting("newlist")==0) addnav("To the Loveshack","runmodule.php?module=marriage&op=loveshack");
	elseif (get_module_setting("newlist")==1 || (get_module_setting("newlist")==0 && get_module_setting("flirttype")==0)) addnav('To the Garden','gardens.php');
	elseif (get_module_setting("newlist")==3){
		addnav("Other");
		addnav("Back to HoF","hof.php");
	}
	villagenav();
	addnav("List");
	addnav("Currently Online","runmodule.php?module=marriage&op=newlyweds");
	$playersperpage=50;

	$sql = "SELECT count(acctid) AS c FROM " . db_prefix("accounts") . " WHERE (marriedto<>0 AND marriedto<>4294967295) AND locked=0";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$totalplayers = $row['c'];

	$action = httpget('action');
	$page = httpget('page');
	$search = "";

	if ($op=="search"){
		$search="%";
		$n = httppost('name');
		for ($x=0;$x<strlen($n);$x++){
			$search .= substr($n,$x,1)."%";
		}
		$search=" AND a.login LIKE '".addslashes($search)."'";
	} else {
		$pageoffset = (int)$page;
		if ($pageoffset>0) $pageoffset--;
		$pageoffset*=$playersperpage;
		$from = $pageoffset+1;
		$to = min($pageoffset+$playersperpage,$totalplayers);
	}

	$limit=" LIMIT $pageoffset,$playersperpage ";
	addnav("Pages");
	for ($i=0;$i<$totalplayers;$i+=$playersperpage){
		addnav(array("Page %s (%s-%s)", $i/$playersperpage+1, $i+1, min($i+$playersperpage,$totalplayers)), "runmodule.php?module=marriage&op=newlyweds&page=".($i/$playersperpage+1));
	}

	if ($page=="" && $action==""){
		$title = translate_inline("Married Warriors Currently Online");
		$sql = "SELECT a.name as name,b.name as partnername,a.acctid as acctid,a.login as login,a.alive as alive,a.location as location,a.race as race,a.sex as sex,a.marriedto as marriedto,a.laston as laston,a.loggedin as loggedin,a.lastip as lastip,a.uniqueid as uniqueid FROM " . db_prefix("accounts") . " as a LEFT JOIN ".db_prefix("accounts")." as b ON a.marriedto=b.acctid WHERE a.locked=0 AND (a.marriedto<>0 AND a.marriedto<>4294967295) AND a.loggedin=1 AND a.laston>'".date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds"))."' ORDER BY a.level DESC, a.dragonkills DESC, a.login ASC";
		$result = db_query_cached($sql,"marriage-marriedonline");
	} else {
		$title = sprintf_translate("Married Warriors in the realm (Page %s: %s-%s of %s)", ($pageoffset/$playersperpage+1), $from, $to, $totalplayers);
		rawoutput(tlbutton_clear());
		$sql = "SELECT a.name as name,b.name as partnername,a.acctid as acctid,a.login as login,a.alive as alive,a.location as location,a.race as race,a.sex as sex,a.marriedto as marriedto,a.laston as laston,a.loggedin as loggedin,a.lastip as lastip,a.uniqueid as uniqueid FROM " . db_prefix("accounts") . " as a LEFT JOIN ".db_prefix("accounts")." as b ON a.marriedto=b.acctid WHERE a.locked=0 AND (a.marriedto<>0 AND a.marriedto<>4294967295) $search"."ORDER BY a.level DESC, a.dragonkills DESC, a.login ASC $limit";
		$result = db_query_cached($sql,"marriage-marriedrealm$page");
	}



	$max = db_num_rows($result);
	if ($max>100) {
		output("`\$Too many names match that.  Showing only the first 100.`0`n");
		$max = 100;
	}

	output_notl("`c`b".$title."`b");

	$name = translate_inline("Name");
	$who = translate_inline("Married To");
	$marriagedate = translate_inline("Marriage Date");

	rawoutput("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>",true);
	rawoutput("<tr class='trhead'><td>$name</td><td>$who</td><td>$marriagedate</td></tr>");
	if ($max==0) {
		output_notl("<tr class='trhilight'><td colspan='8'><center>`^`b`c%s`c`b</center></td></tr>",translate_inline("No one is married!"),true);
	}
	for($i=0;$i<$max;$i++){
		$row = db_fetch_assoc($result);
		$show_marriage = true;
		
		if (get_module_pref("user_bio","marriage",$row['acctid']) == 0 || get_module_pref("user_bio","marriage",$row['marriedto']) == 0) $show_marriage = false;
		
		rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td>",true);
		if ($show_marriage == true)
			output_notl("`&%s`0", $row['name']);
		else
			output_notl("`i`7-hidden-`i`0");
			
		rawoutput("</td><td>");
		if ($show_marriage == true)
			output_notl("%s",$row['partnername']);
		else
			output_notl("`i`7-hidden-`i`0");
			
		rawoutput("</td><td>");
		$reg=get_module_objpref("marriage",$row['acctid'],"marriagedate");
		if ($reg=="0000-00-00 00:00:00" ||$reg==0) {
			$reg=$unlogged;
		}
		output_notl("%s",$reg);
		rawoutput("</td></tr>");
	}
	rawoutput("</table>");
	output_notl("`n");
	$search = translate_inline("Search by name: ");
	$search2 = translate_inline("Search");
	rawoutput("<form action='runmodule.php?module=marriage&op=newlyweds&action=search' method='POST'>$search<input name='name'><input type='submit' class='button' value='$search2'></form>");
	addnav("","runmodule.php?module=marriage&op=newlyweds&action=search");
	output_notl("`c");
	page_footer();
?>