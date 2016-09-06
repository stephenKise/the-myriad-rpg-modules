<?php
function pyramid3_rand(){
global $session;
output("You have located a transport portal");
addnav("Transport","runmodule.php?module=clanpyramid&op=transport");
blocknav("runmodule.php?module=clanpyramid&op=wall&hit=south");
	blocknav("runmodule.php?module=clanpyramid&op=move&move=south");
	blocknav("runmodule.php?module=clanpyramid&op=move&move=east");
	blocknav("runmodule.php?module=clanpyramid&op=wall&hit=east");
	blocknav("runmodule.php?module=clanpyramid&op=move&move=north");
	blocknav("runmodule.php?module=clanpyramid&op=wall&hit=north");
	blocknav("runmodule.php?module=clanpyramid&op=wall&hit=west");
	blocknav("runmodule.php?module=clanpyramid&op=move&move=west");
}
function pyramid3_transport(){
	global $session;
	switch(e_rand(1,14)){
		case 1:
		case 2:
		//sends to pyramid 1
		set_module_pref("square",135);
		//set_module_pref("pyramid",1);
		addnav("Continue","runmodule.php?module=clanpyramid&op=move&move=transport&p=1");
		output("You disappear from one spot and appear in another vault");
		break;
		case 3:
		case 4:
		case 13:
		//sends to pyramid 2
		set_module_pref("square",1156);
		//set_module_pref("pyramid",2);
		addnav("Continue","runmodule.php?module=clanpyramid&op=move&move=transport&p=2");
		output("You disappear from one spot and appear in another vault");
		break;
		case 5:
		case 10:
		case 14:
		//inside pyramid 3
		set_module_pref("square",2001);
		addnav("Continue","runmodule.php?module=clanpyramid&op=move&move=transport&p=3");
		output("You return to the Main entrance");
		break;
		case 6:
		case 11:
		case 12:
		//inside pyramid 3
		set_module_pref("square",2169);
		addnav("Continue","runmodule.php?module=clanpyramid&op=move&move=transport&p=3");
		output("You return to the secondary entrance");
		break;
		case 7:
		//send to throne pyramid 3
		set_module_pref("square",2085);
		addnav("Continue","runmodule.php?module=clanpyramid&op=move&move=thronec&p=3");
		output("Hmmmm you found a good transport portal");
		break;
		case 8:
		//send to near throne pyramid 2
		set_module_pref("square",1124);
		set_module_pref("pyramid",2);
		addnav("Continue","runmodule.php?module=clanpyramid&op=move&move=transport&p=2");
		output("Wow back to another Vault..pssst, go north if its not yours");
		break;
		case 9:
		//send to near throne pyramid 3
		set_module_pref("square",2111);
		addnav("Continue","runmodule.php?module=clanpyramid&op=move&move=transport&p=3");
		output("Well, that was lucky, almost all the way to the throne now");
		break;
	}
	
}
?>
	

			
