<?php
page_header("Pyramid Kills");
$acc = db_prefix("accounts");
$mp = db_prefix("module_userprefs");
$sql = "SELECT $acc.name AS name,
$acc.acctid AS acctid,
$mp.value AS kills,
$mp.userid FROM $mp INNER JOIN $acc
ON $acc.acctid = $mp.userid
WHERE $mp.modulename = 'clanhof'
AND $mp.setting = 'kills'
AND $mp.value > 0 ORDER BY ($mp.value+0)
DESC limit ".get_module_setting("list")."";
$result = db_query($sql);
$rank = translate_inline("Kills");
$name = translate_inline("Name");
output_notl("`n`b`c");
output("`6Pyramid Kills");
output_notl("`n`n`c`b");
rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center'>");
rawoutput("<tr class='trhead'><td align=center>$name</td><td align=center>$rank</td></tr>");
$countrow=db_num_rows($result);
for ($i=0;$i < $countrow;$i++){
	$row = db_fetch_assoc($result);
	if ($row['name']==$session['user']['name']){
		rawoutput("<tr class='trhilight'><td>");
	}else{
		rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td align=left>");
	}
	output_notl("%s",$row['name']);
	rawoutput("</td><td align=right>");
	output_notl("%s",$row['kills']);
	rawoutput("</td></tr>");
}
rawoutput("</table>");
addnav("Back to HoF", "hof.php");
villagenav();
page_footer();
?>