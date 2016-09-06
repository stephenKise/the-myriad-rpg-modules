<?php
function privatebeta_getmoduleinfo(){
	$info = array(
		"name"=>"Private/Closed BETA",
		"version"=>"1.0",
		"author"=>"`@KaosKaizer",
		"category"=>"Administrative",
		"description"=>"Requires keys for account creation so you can have a Private BETA testing server.",
		"settings"=>array(
			"Private/Closed BETA Settings,title",
			"keys"=>"Keys,textarea|",
			"Comma(&#44;) separated. Each key can only be used once.,note",
			"Keys must be 100% unique as each key can only be used once and is then removed from this list.,note",
			"msg"=>"Message to display in the form,text|(you can petition the admin of this site for a Testing Key if you don't have one)",
		),
	);
	return $info;
}

function privatebeta_install(){
	module_addhook("create-form");
	module_addhook("check-create");
	return true;
}

function privatebeta_uninstall(){
	return true;
}

function privatebeta_dohook($hook,$args){
	global $session;
	switch($hook){
		case "create-form":
			rawoutput("Testing Key: <input type='text' name='betakey'>&nbsp;");
			output_notl("`i%s`i`n",get_module_setting("msg"));
		break;
		case "check-create":
			$allkeys = explode(",",get_module_setting("keys"));
			$key = httppost('betakey');
			if (in_array($key,$allkeys)){
				$args['blockaccount'] = false;
				$devo = array_search($key,$allkeys);
				unset($allkeys[$devo]);
				$allkeys = join(",",$allkeys);
				set_module_setting("keys",$allkeys);
			}else{
				$args['blockaccount'] = true;
				$args['msg'] .= "This server is in Private BETA and requires a valid Testing Key to access.`n";
			}
		break;
	}
	return $args;
}