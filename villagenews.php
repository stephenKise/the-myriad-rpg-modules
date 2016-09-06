<?php

function villagenews_getmoduleinfo(){
	$info = array(
		"name"=>"Village News",
		"version"=>"1.05",
		"author"=>"`#Lonny Luberts, `&modified by Oliver Brendel`ntlschema set at news for better translating",
		"category"=>"Village",
		"download"=>"http://www.pqcomp.com/modules/mydownloads/visit.php?cid=3&lid=23",
		"settings"=>array(
			"Village News Module Settings,title",
			"showhome"=>"Show news on Home Page,enum,0,No,1,Above Login,2,Below Login",
			"newslines"=>"Number of news lines to display in the villages,int|4",
		),
	);
	return $info;
}

function villagenews_install(){
	if (!is_module_active('villagenews')){
		output("`4Installing Village News Module.`n");
	}else{
		output("`4Updating Village News Module.`n");
	}
	module_addhook("village-desc");
	return true;
}

function villagenews_uninstall(){
	output("`4Un-Installing Village News Module.`n");
	return true;
}

function villagenews_dohook($hookname,$args){
switch($hookname){
	case "village-desc":
		tlschema("news");
		output("`n`2`c`bLatest News`b`c");
		$sql = "SELECT newstext,arguments FROM ".db_prefix("news")." ORDER BY newsid DESC LIMIT ".get_module_setting('newslines');
		$result = db_query($sql) or die(db_error(LINK));
		for ($i=0;$i<get_module_setting('newslines');$i++){
			$row = db_fetch_assoc($result);
			if ($row['arguments']>""){
		$arguments = array();
		$base_arguments = unserialize($row['arguments']);
		array_push($arguments,$row['newstext']);
		while (list($key,$val)=each($base_arguments)){
			array_push($arguments,$val);
		}
		$newnews = call_user_func_array("sprintf_translate",$arguments);
		}else{
			$newnews = $row['newstext'];
		}
			output("`c %s `c",stripslashes($newnews));
			if ($i <> get_module_setting('newslines')) output("`0");
		}
		output("`n");
		tlschema("user");
	break;
	case "index":
		if (get_module_setting('showhome') == 1){
		    tlschema("news");
		    output("`n`2`bLatest News`b`n");
			$sql = "SELECT newstext,arguments FROM ".db_prefix("news")." ORDER BY newsid DESC LIMIT ".get_module_setting('newslines');
			$result = db_query($sql) or die(db_error(LINK));
			for ($i=0;$i<get_module_setting('newslines');$i++){
				$row = db_fetch_assoc($result);
				if ($row['arguments']>""){
			$arguments = array();
			$base_arguments = unserialize($row['arguments']);
			array_push($arguments,$row['newstext']);
			while (list($key,$val)=each($base_arguments)){
				array_push($arguments,$val);
			}
			$newnews = call_user_func_array("sprintf_translate",$arguments);
			}else{
				$newnews = $row['newstext'];
			}
				output(" %s `n",stripslashes($newnews));
				if ($i <> get_module_setting('newslines')) output("`0`n");
			}
			output("`n");
		    tlschema();
		}
	break;
}
	return $args;
}
?>