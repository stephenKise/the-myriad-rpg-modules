<?php
$which=httpget('p');
output("You enter the Vault to defend it, walls are no barrier to you, you may move freely, to find, attack and kill your enemies.  One thing however, you must FIND them FIRST!");
output_notl("`n`n");
output("You start in the throne");
if ($which==1){
	set_module_pref("square",111);
}elseif ($which==2){
	set_module_pref("square",1098);
}elseif ($which==3){
	set_module_pref("square",2085);
}
set_module_pref("defender",1);
addnav("Move North","runmodule.php?module=clanpyramid&op=move&move=north&p=$which");
?>