<?php
function firstyom_getmoduleinfo(){
	$info = array(
		"name"=>"First YOM",
		"author"=>"<a href='http://www.joshuadhall.com' target=_new>Sixf00t4</a>",
		"version"=>"20070112",
		"category"=>"Administrative",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1174",
		"description"=>"Gives a welcome YOM to new players.",
		"vertxtloc"=>"http://www.legendofsix.com/",
    	"settings"=>array(
			"First YOM Settings,title",
			"subject"=>"What is the subject of the YOM?,text|Thanks for signing up!",            
			"from"=>"Who should the YOM come from? (0 for system message),int|0",
			"msg"=>"What is the body of the message?,textarea|Thanks for signing up!  If you have any questions, feel free to use the \"petition for help\" link.  Also be sure to read and understand the FAQ section.",
		),
	);
	return $info;
}

function firstyom_install(){
	module_addhook("process-create");
	return true;
}
function firstyom_uninstall(){
	return true;
}

function firstyom_dohook($hookname,$args){
    require_once("lib/systemmail.php");
    systemmail($args['acctid'],get_module_setting("subject"), get_module_setting("msg"),get_module_setting("from"));
	return $args;
}
?>