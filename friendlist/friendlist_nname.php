<?php
function friendlist_nname(){
	require_once("lib/sanitize.php");
	$id = httpget("id");
	$nname = sanitize(httppost("newnn$id"));
	$nnames = unserialize(get_module_pref('nnames'));
	if (!$nnames) $nnames = array();
	if ($nname) $nnames[$id] = $nname;
	else unset($nnames[$id]);
	$nnamepref = serialize($nnames);
	set_module_pref("nnames",$nnamepref);
	header("Location: runmodule.php?module=friendlist&op=list");
}
?>