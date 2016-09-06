<?php
$clan=$u['clanid'];
$sql = db_query("SELECT clanname FROM clans WHERE clanid = '{$clan}' LIMIT 0,1");
$row = db_fetch_assoc($sql);
debug($row['clanname']);
if ($row['clanname'] == get_module_setting("lastwinner")){
	$level = $u['level'];
	$bonus = $level*10;
	output_notl("`n");
	$u['experience']+=$bonus;
	output("`i`@You will receive a bonus experience boost since your guild was last month's winner.`i`n");
}
?>