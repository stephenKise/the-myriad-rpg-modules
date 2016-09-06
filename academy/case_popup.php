<?php
popup_header("Squire Info");

$div = (get_module_setting("div")/100);

$min = get_module_setting("mindmg");
$mindmg = $min+get_module_pref("level");
$mindmg = round($mindmg*$div);
$minstat = $mindmg;

$max = get_module_setting("maxdmg");
$bk = (get_module_setting("boost-knight")/100);
$bw = (get_module_setting("boost-warlord")/100);
switch (get_module_pref("class")){
	case 0:
		$maxdmg = $max+get_module_pref("level");
	break;
	case 1:
		$maxdmg = round($max+get_module_pref("level")*$bk);
	break;
	case 2:
		$maxdmg = round($max+get_module_pref("level")*$bw);
	break;
}
$maxdmg = round($maxdmg*$div);
$maxstat = $maxdmg;

$classarray = array(0=>translate_inline("Squire"),1=>translate_inline("Knight"),2=>translate_inline("Warlord"));
$class = get_module_pref("class");
	output("`n");
	$name = get_module_pref("name");
	$name_stat = translate_inline("Name");
	$status = translate_inline("Status");
	$level = translate_inline("Level");
	$attack = translate_inline("Damage");
	$ready = translate_inline("Ready to Level");
	$train = translate_inline("Train Accuracy");
	$acc = translate_inline("Accuracy");
	output($name_stat.": ".$name."`n");
	output($status.": ".translate_inline(get_module_pref("dead")==1?"Dead":"Alive")."`n");
	output($level.": ".get_module_pref("level")."`n");
	output($attack.": ".$minstat."-".$maxstat."`n");
	output($acc.": ".get_module_pref("acc")."%"."`n");
	$cond = translate_inline("No");
	if (get_module_pref("lsl") == get_module_setting("level")) $cond = translate_inline("Yes");
	output($ready.": ".$cond."`n");
	if (get_module_pref("acc")<get_module_setting("miss"))
		output($train.": ".translate_inline(get_module_pref("tacc")==1?"Unable":"Able")."`n");
	output("`n");

popup_footer();
?>