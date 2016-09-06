<?php
module_addhook("village");
module_addhook_priority('village-desc', 80);
module_addhook("vaultinfo");
module_addhook("charstats");
module_addhook("newday");
module_addhook("battle-victory");
module_addhook("forest-desc");
$sql="SELECT * FROM " .db_prefix("clans"). " WHERE clanid <> 0";
$res = db_query($sql);
$countrow=db_num_rows($res);
for ($i=0;$i<$countrow;$i++){ 
	$row = db_fetch_assoc($res);
	$oppcid = $row['clanid'];
	$wallhp=25000;
	set_module_objpref("clans",$oppcid,32,$wallhp,"clanpyramid");
	set_module_objpref("clans",$oppcid,206,$wallhp,"clanpyramid");
	set_module_objpref("clans",$oppcid,80,$wallhp,"clanpyramid");
	set_module_objpref("clans",$oppcid,64,$wallhp,"clanpyramid");
	set_module_objpref("clans",$oppcid,60,$wallhp,"clanpyramid");
	set_module_objpref("clans",$oppcid,161,$wallhp,"clanpyramid");
	set_module_objpref("clans",$oppcid,121,$wallhp,"clanpyramid");
	set_module_objpref("clans",$oppcid,140,$wallhp,"clanpyramid");
	set_module_objpref("clans",$oppcid,111,$wallhp,"clanpyramid");
}
return true;
?>