<?php

function keepfavor_getmoduleinfo(){
	$info = array(
		"name"=>"Keep Favor",
		"version"=>"1.1",
		"author"=>"Jigain To'lerean",
		"category"=>"Forest",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1061",
	);
	return $info;
}
function keepfavor_install(){
	module_addhook("dk-preserve");
	return true;
}
function keepfavor_uninstall(){
	return true;
}
function keepfavor_dohook($hookname,$args){
	$args['deathpower'] = 1;
	return $args;
}
?>