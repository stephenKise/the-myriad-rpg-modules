<?php
	
function hpmod_getmoduleinfo(){
	$info = array(
		"name"=>"Global HP Modifier",
		"version"=>"1.0",
		"author"=>"`&Senare",
		"category"=>"General",
		"download"=>"nope"
	);
	return $info;
}

function hpmod_install(){
	module_addhook_priority("everyhit",1);
	return true;
}

function hpmod_uninstall(){
	return true;
}

function hpmod_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "everyhit":
			$dkz = $session['user']['dragonkills'];
			$lvlz = $session['user']['level']*10;
			$hpmath = ($dkz*25)+$lvlz;
			if ($session['user']['dragonkills'] >= 500) $hpmath = 62500+((($dkz-500)*50)+$lvlz);
			if ($session['user']['maxhitpoints'] > $hpmath){
				$session['user']['maxhitpoints'] = $hpmath;
				$session['user']['hitpoints'] = $hpmath;
				output("`c<big>`QAttention!`n`^ Your health was reset to the global limits!</big>`c`n",true);
			}elseif ($session['user']['hitpoints'] > $hpmath){
				$session['user']['hitpoints'] = $hpmath;
			}
		break;
	}
	return $args;
}
	
	
?>