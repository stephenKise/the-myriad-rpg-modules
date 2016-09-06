<?php
page_header("Guild Points");
$cl = db_prefix("clans");
$mp = db_prefix("module_objprefs");
$sql = "SELECT $cl.clanname AS name,
$cl.clanid AS clanid,
$cl.clanname AS clanname,
$mp.value AS points,
$mp.objid FROM $mp INNER JOIN $cl
ON $cl.clanid = $mp.objid
WHERE $mp.modulename = 'clanpyramid'
AND $mp.setting = 'clanwins'
AND $mp.objtype = 'clans'
AND $mp.value > 0 ORDER BY ($mp.value+0)
DESC limit ".get_module_setting("list")."";
$result = db_query($sql);
$rank = translate_inline("Guild Points");
$name = translate_inline("Guild Name");
output("`n`b`c`4Guild Points`n`n`c`b");
rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center'>");
rawoutput("<tr class='trhead'><td align=center>$name</td><td align=center>$rank</td></tr>");
$countrow=db_num_rows($result);
for ($i=0;$i < $countrow;$i++){
	$row = db_fetch_assoc($result);
	if ($row['clanid']==$session['user']['clanid']){
		rawoutput("<tr class='trhilight'><td>");
	}else{
		rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td align=left>");
	}
	output_notl("%s",$row['name']);
	rawoutput("</td><td align=right>");
	output_notl("%s",$row['points']);
	rawoutput("</td></tr>");
}
rawoutput("</table>");
addnav("Back to HoF", "hof.php");
villagenav();
page_footer();
?>