<?php
$oldtime = get_module_setting("lastreset");
$newtime = date("Y-m-d h:m:s",strtotime("-1 month"));
if ($oldtime<$newtime){
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
	DESC limit 1";
	$res = db_query($sql);
	$row = db_fetch_assoc($res);
	$clan = $row['clanname'];
	set_module_setting("lastwinner",$clan);
	if ($row['points']>0){
		$timen = date("Y-m-d h:m:s");
		set_module_setting("lastreset",$timen);
		$sql="DELETE  FROM ".db_prefix("module_objprefs")." WHERE `modulename` = 'clanpyramid' AND `setting` = 'clanwins'";
		db_query($sql);
	}
}
?>