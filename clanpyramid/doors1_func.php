<?php
global $session;
$owned1 = get_module_setting("owned1");
$clanid = $session['user']['clanid'];
if ($owned1==0){
	$sql4="SELECT * FROM " .db_prefix("clans"). " WHERE clanid <> '$clanid'";
	$res4=db_query($sql4);
	for ($i=0;$i<db_num_rows($res4);$i++){
		$row4 = db_fetch_assoc($res4);
		$oppcid = $row4['clanid'];
		$doorno="32";
		$doorso="206";
		$dooreo="80";
		$doorwo="64";
		$doorni="60";
		$doorsi="161";
		$doorwi="121";
		$doorei="140";
		$doort="111";
	}
}else{
	$doorno=get_module_objpref("clans",$owned1,"doorno","clanpyramid");
	$doorso=get_module_objpref("clans",$owned1,"doorso","clanpyramid");
	$dooreo=get_module_objpref("clans",$owned1,"dooreo","clanpyramid");
	$doorwo=get_module_objpref("clans",$owned1,"doorwo","clanpyramid");
	$doorni=get_module_objpref("clans",$owned1,"doorni","clanpyramid");
	$doorsi=get_module_objpref("clans",$owned1,"doorsi","clanpyramid");
	$doorwi=get_module_objpref("clans",$owned1,"doorwi","clanpyramid");
	$doorei=get_module_objpref("clans",$owned1,"doorei","clanpyramid");
	$doort=get_module_objpref("clans",$owned1,"doort","clanpyramid");
}
?>