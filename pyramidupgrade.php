<?php
function pyramidupgrade_getmoduleinfo(){
	$info = array(
		"name" => "Upgrade Pyramids",
		"author" => "`b`&Ka`6laza`&ar`b",
		"version" => "1.0",
		"download" => "http://dragonprime.net/index.php?module=Downloads;catd=20",
		"category" => "Clan",
		"description" => "Upgrader for Clan Pyramids and Clan hof",
		"requires"=>array("clanpyramid"=>"Clan Pyramid by Kalazaar",
		),
		);
	return $info;
}
function pyramidupgrade_install(){
	$sql="UPDATE " . db_prefix("module_userprefs"). " SET modulename = 'clanhof' WHERE modulename = 'clanpyramid' AND setting = 'kills'";
	db_query($sql);
	$sql="UPDATE " .db_prefix("module_userprefs"). " SET modulename = 'clanhof' WHERE modulename = 'clanpyramid' AND setting = 'cp'";
	db_query($sql);
	$sql="DELETE FROM " .db_prefix("module_userprefs"). " WHERE modulename = 'clanpyramid' AND setting = 'square1'";
	db_query($sql);
	$sql="DELETE FROM " .db_prefix("module_userprefs"). " WHERE modulename = 'clanpyramid' AND setting = 'leave'";
	db_query($sql);
	$sql="DELETE FROM " .db_prefix("module_userprefs"). " WHERE modulename = 'clanpyramid' AND setting = 'wall'";
	db_query($sql);
	$sql="DELETE FROM " .db_prefix("module_settings"). " WHERE modulename = 'clanpyramid' AND setting = 'doorno'";
	db_query($sql);
	$sql="DELETE FROM " .db_prefix("module_settings"). " WHERE modulename = 'clanpyramid' AND setting = 'doorso'";
	db_query($sql);
	$sql="DELETE FROM " .db_prefix("module_settings"). " WHERE modulename = 'clanpyramid' AND setting = 'dooreo'";
	db_query($sql);
	$sql="DELETE FROM " .db_prefix("module_settings"). " WHERE modulename = 'clanpyramid' AND setting = 'doorwo'";
	db_query($sql);
	$sql="DELETE FROM " .db_prefix("module_settings"). " WHERE modulename = 'clanpyramid' AND setting = 'doorni'";
	db_query($sql);
	$sql="DELETE FROM " .db_prefix("module_settings"). " WHERE modulename = 'clanpyramid' AND setting = 'doorsi'";
	db_query($sql);
	$sql="DELETE FROM " .db_prefix("module_settings"). " WHERE modulename = 'clanpyramid' AND setting = 'doorei'";
	db_query($sql);
	$sql="DELETE FROM " .db_prefix("module_settings"). " WHERE modulename = 'clanpyramid' AND setting = 'doorwi'";
	db_query($sql);
	$sql="DELETE FROM " .db_prefix("module_settings"). " WHERE modulename = 'clanpyramid' AND setting = 'doort'";
	db_query($sql);
	return true;
}
function pyramidupgrade_uninstall(){
	return true;
}
?>