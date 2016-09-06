<?php
// updated to 1.1 added clan and overall HoF's, added output for fortune when forging
// updated to 1.3 split module run to a func, changed gems payment from once at start to paying amt of gems per attempt
function clanforge_getmoduleinfo(){
	$info = array(
		"name" => "Clan Forge",
		"author" => "`b`&Ka`6laza`&ar`b, text modifications by EdwardCullen`7, modified by `i`b`&BakerX`b`i",
		"version" => "1.3",
		"download" => "http://dragonprime.net/index.php?module=Downloads;catd=20",
		"category" => "Clan",
		"description" => "Clan Members can create custom weapons and armor",
		"settings"=>array(
			"Clan Forge,title",
			"cost"=>"cost per attempt to create in gems,int|1000",
			"list"=>"how many on hof,int|25",
			),
		"prefs" => array(
			"level" => "What weapon level is the player up to?, int|0",
			"levela" => "What armor level is the player up to?, int|0",
			"name" => "Name of weapon,text|",
			"namea" => "Name of armor,text|",
			"value" => "atk or def value,int|0",
		),
		);
	return $info;
}
function clanforge_install(){
	require_once("lib/tabledescriptor.php");
	 $clanshop = array(
		'shopid'=>array('name'=>'shopid', 'type'=>'int unsigned',	'extra'=>'not null auto_increment'),
		'type'=>array('name'=>'type', 'type'=>'int unsigned',	'extra'=>'not null'),
		'name'=>array('name'=>'name', 'type'=>'text',	'extra'=>'not null'),
		'value'=>array('name'=>'value', 'type'=>'int unsigned',	'extra'=>'not null'),
		'cost'=>array('name'=>'cost', 'type'=>'int unsigned',	'extra'=>'not null'),
		'clan'=>array('name'=>'clan', 'type'=>'int unsigned',	'extra'=>'not null'),
		'creator'=>array('name'=>'creator', 'type'=>'int unsigned',	'extra'=>'not null'),
		'buyer'=>array('name'=>'buyer', 'type'=>'int unsigned',	'extra'=>'not null'),
		'key-PRIMARY'=>array('name'=>'PRIMARY', 'type'=>'primary key',	'unique'=>'1', 'columns'=>'shopid'));
		synctable(db_prefix('clanshop'), $clanshop, true);
	//module_addhook("footer-clan");
	module_addhook("village");
	module_addhook("footer-hof");
	module_addhook("biostat");
	module_addhook("newday");
	return true;
}
function clanforge_uninstall(){
	debug("Dropping clanshop table");
    $sql = "DROP TABLE IF EXISTS " . db_prefix("clanshop");
	return true;
}
function clanforge_dohook($hookname,$args){
	global $session;
	$op = httpget('op');
	switch ($hookname){
		case "village":
			addnav($args['fightnav']);
			addnav("`bT`b`7h`)e `b`~S`b`)m`b`7i`b`&t`)h`i`~y`i", "runmodule.php?module=clanforge&op=enter");
			break;
		//case "footer-clan":
		//	if ($session['user']['clanrank'] >= CLAN_MEMBER){
		//		addnav("Forge");
		//		addnav("Guild Forge", "runmodule.php?module=clanforge&op=enter");
		//		addnav("Weapon Forge HoF","runmodule.php?module=clanforge&op=weaponhofc");
		//		addnav("Armor Forge HoF","runmodule.php?module=clanforge&op=armorhofc");
		//	}
		//	break;
		case "footer-hof":
			addnav("Civilian Rankings");
			addnav("Forge Weapon HoF", "runmodule.php?module=clanforge&op=weaponhof");
			addnav("Forge Armor HoF", "runmodule.php?module=clanforge&op=armorhof");
			break;
		case "biostat":
			$char = httpget("char");
            $wl = get_module_pref("level","clanforge", $char);
            $al = get_module_pref("levela","clanforge", $char);
            if ($wl>0) $args['tablebiostat']['Companions/Items']['Forge Weapons Level'] = $wl;
            if ($al>0) $args['tablebiostat']['Companions/Items']['Forge Armor Level'] = $al;
            break;
		case "newday":
			if (get_module_pref("name") != ""){
			$fname=get_module_pref("name");
			$fvalue=get_module_pref("value");
			$weapondamage=$session['user']['weapondmg'];
			$session['user']['attack']-=$weapondamage;
			$session['user']['attack']+=$fvalue;
			$session['user']['weapon']=$fname;
			$session['user']['weapondmg']=$fvalue;
			}
			if (get_module_pref("namea") != ""){
			$fname=get_module_pref("namea");
			$fvalue=get_module_pref("value");
			$armor=$session['user']['armordef'];
			$session['user']['defense']-=$armor;
			$session['user']['defense']+=$fvalue;
			$session['user']['armor']=$fname;
			$session['user']['armordef']=$fvalue;
			}
			break;
			
	}
	return $args;
}
function clanforge_run(){
	global $SCRIPT_NAME;
	if ($SCRIPT_NAME == "runmodule.php"){
		$module=httpget("module");
		if ($module == "clanforge") {
			include("modules/clanforge/clanforge_func.php");
		}
	}
}

?>