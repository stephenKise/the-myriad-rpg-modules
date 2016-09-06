<?php
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

$hover = "<div class='htmltooltip'><table cellpadding='2' cellspacing='1' align='center'>";
	
$hover .= ("<tr><td align='center'>`^Name:`& ".get_module_pref("name")."`n</td></tr>");
$hover .= ("<tr><td align='center'>`^Status:`& ".translate_inline(get_module_pref("dead")==1?"Dead":"Alive")."`n</td></tr>");
$hover .= ("<tr><td align='center'>`^Level:`& ".get_module_pref("level")."`n</td></tr>");
$hover .= ("<tr><td align='center'>`^Damage:`& $minstat-$maxstat`n</td></tr>");
$hover .= ("<tr><td align='center'>`^Accuracy:`& ".get_module_pref("acc")."%"."`n</td></tr>");
$cond = "No";
if (get_module_pref("lsl") == get_module_setting("level")) $cond = "Yes";
$hover .= ("<tr><td align='center'>`^Ready to Level:`& $cond`n</td></tr>");
if ($acc<get_module_setting("miss"))
	$hover .= ("<tr><td align='center'>`^Train Accuracy:`& ".translate_inline(get_module_pref("tacc")==1?"Unable":"Able")."`n</td></tr>");
	
$hover .= "</table></div>"; 
	
?>