<?php

// Very Sloppy... Definitely needs revised. >.>

function banking_getmoduleinfo(){
	$info = array(
		"name"=>"Banking",
		"version"=>"2.0",
		"author"=>"Maverick",
		"category"=>"General",
		"override_forced_nav"=>true,
	);
    return $info;
}

function banking_install(){
	return TRUE;
}

function banking_uninstall(){
	return TRUE;
}

function banking_dohook($hookname,$args){
	return $args;
}

function banking_run(){
	global $session;
	$op = httpget("op");
	$subop = httpget("subop");
	$a = httpget("a");
	$sql = "SELECT ownerid,gold,gems FROM dwellings WHERE ownerid='{$session['user']['acctid']}'";
	$result = db_query($sql);
	$num = db_num_rows($result);
	$onhandgems = $session['user']['gems'];
	$onhandgold = $session['user']['gold'];
	$bankedgems = get_module_pref("gemsinbank","bankmod");
	if ($bankedgems <= 0 || !is_numeric($bankedgems)) $bankedgems = 0;
	$bankedgold = $session['user']['goldinbank'];
	$dwgold = $session['user']['dwgold'];
	$dwgems = $session['user']['dwgems'];
	$acctid = $session['user']['acctid'];
	$wgemstat = ($bankedgems>0) ? "<a href='runmodule.php?module=banking&op=main&subop=wgbank'>Withdraw</a>" : "`i`~Withdraw`i";
	$dgemstat = ($onhandgems>0) ? "<a href='runmodule.php?module=banking&op=main&subop=dgbank'>Deposit</a>" : "`i`~Deposit`i";
	$wgoldstat = ($bankedgold>0) ? "<a href='runmodule.php?module=banking&op=main&subop=wgdbank'>Withdraw</a>" : "`i`~Withdraw`i";
	$dgoldstat = ($onhandgold>0) ? "<a href='runmodule.php?module=banking&op=main&subop=dgdbank'>Deposit</a>" : "`i`~Deposit`i";
	if ($num <= 0){
		$wdwgem = "`iInvalid Data`i";
		$ddwgem = "`iInvalid Data`i";
		$wdwgold = "`iInvalid Data`i";
		$ddwgold = "`iInvalid Data`i";
	}else{
		$wdwgem = ($dwgems>0) ? "<a href='runmodule.php?module=banking&op=main&subop=dwwgbank'>Withdraw</a>" : "`i`GWithdraw`i";
		$ddwgem = ($onhandgems>0) ? "<a href='runmodule.php?module=banking&op=main&subop=dwdgbank'>Deposit</a>" : "`i`3Deposit`i";
		$wdwgold = ($dwgold>0) ? "<a href='runmodule.php?module=banking&op=main&subop=dwwgdbank'>Withdraw</a>" : "`i`GWithdraw`i";
		$ddwgold = ($onhandgold>0) ? "<a href='runmodule.php?module=banking&op=main&subop=dwdgdbank'>Deposit</a>" : "`i`3Deposit`i";
	}
	
	popup_header("Bank Accounts");
	output("`n`n<center>[ <a href='runmodule.php?module=banking&op=main'>Main</a> ]</center>",true);
	switch($op){
		case "main":
		output("<hr>`n`c`^You have {$onhandgold} `^Gold and {$onhandgems} `^Gems on hand`c`n<hr>",true);
		if (!$subop){
			
			output("`n`n<table align='center' width='500px' nowrap><tr><td>Account</td><td>Amount</td><td>Actions</td></tr><tr><td>`^Banked Gold</td><td>%s</td><td>%s</td></tr><tr><td>`#Banked Gems</td><td>%s</td><td>%s</td></tr><tr><td>`eDwelling Gold</td><td>%s</td><td>%s</td></tr><tr><td>`EDwelling Gems</td><td>%s</td><td>%s</td></tr>",$bankedgold,"[ ".$wgoldstat." ] - [ ".$dgoldstat." ]",$bankedgems,"[ ".$wgemstat." ] - [ ".$dgemstat." ]",$dwgold,"[ " .$wdwgold." ] - [ ".$ddwgold." ]",$dwgems,"[ ".$wdwgem." ] - [ ".$ddwgem." ]", true);
			output("</table>`n`n",true);
			
			output("<center>[<a href='runmodule.php?module=banking&op=withdraw_all'>Withdraw From All</a>]</center>",true);
		}else{
			switch($subop){
				case "wgbank":
				output("<div style='letter-spacing: 2px'><center>Option Selected - Withdraw Gems From Account: Bank</center></div>`n",true);
				output("<center><form action='runmodule.php?module=banking&op=confirm&a=withdrawgemsbank' method='POST'><input type='text' name='withdraw' Placeholder='Amount of Gems:'><input type='submit' value='Withdraw'></center>",true);
				addnav("","runmodule.php?module=banking&op=confirm&a=withdrawgemsbank");
				break;
				case "dgbank":
				output("<div style='letter-spacing: 2px'><center>Option Selected - Deposit Gems To Account: Bank</center></div>",true);
				output("<center><form action='runmodule.php?module=banking&op=confirm&a=depositgemsbank' method='POST'><input type='text' name='deposit' Placeholder='Amount of Gems:'><input type='submit' value='Deposit'></center>",true);
				addnav("","runmodule.php?module=banking&op=confirm&a=depositgemsbank");
				break;
				case "wgdbank":
				output("<div style='letter-spacing: 2px'><center>Option Selected - Withdraw Gold From Account: Bank</center></div>",true);
				output("<center><form action='runmodule.php?module=banking&op=confirm&a=withdrawgoldbank' method='POST'><input type='text' name='withdraw' Placeholder='Amount of Gold:'><input type='submit' value='Withdraw'></center>",true);
				addnav("","runmodule.php?module=banking&op=confirm&a=withdrawgoldbank");
				break;
				case "dgdbank":
				output("<div style='letter-spacing: 2px'><center>Option Selected - Deposit Gold To Account: Bank</center></div>",true);
				output("<center><form action='runmodule.php?module=banking&op=confirm&a=depositgoldbank' method='POST'><input type='text' name='deposit' Placeholder='Amount of Gold:'><input type='submit' value='Deposit'></center>",true);
				addnav("","runmodule.php?module=banking&op=confirm&a=depositgoldbank");
				break;
				case "dwwgbank":
				output("<div style='letter-spacing: 2px'><center>Option Selected - Withdraw Gems From Account: Dwelling</center></div>",true);
				output("<center><form action='runmodule.php?module=banking&op=confirm&a=withdrawgemsdwell' method='POST'><input type='text' name='withdraw' Placeholder='Amount of Gems:'><input type='submit' value='Withdraw'></center>",true);
				addnav("","runmodule.php?module=banking&op=confirm&a=withdrawgemsdwell");
				break;
				case "dwdgbank":
				output("<div style='letter-spacing: 2px'><center>Option Selected - Deposit Gems To Account: Dwelling</center></div>",true);
				output("<center><form action='runmodule.php?module=banking&op=confirm&a=depositgemsdwell' method='POST'><input type='text' name='deposit' Placeholder='Amount of Gems:'><input type='submit' value='Deposit'></center>",true);
				addnav("","runmodule.php?module=banking&op=confirm&a=depositgemsdwell");
				break;
				case "dwwgdbank":
				output("<div style='letter-spacing: 2px'><center>Option Selected - Withdraw Gold From Account: Dwelling</center></div>",true);
				output("<center><form action='runmodule.php?module=banking&op=confirm&a=withdrawgolddwell' method='POST'><input type='text' name='withdraw' Placeholder='Amount of Gold:'><input type='submit' value='Withdraw'></center>",true);
				addnav("","runmodule.php?module=banking&op=confirm&a=withdrawgolddwell");
				break;
				case "dwdgdbank":
				output("<div style='letter-spacing: 2px'><center>Option Selected - Deposit Gold To Account: Dwelling</center></div>",true);
				output("<center><form action='runmodule.php?module=banking&op=confirm&a=depositgolddwell' method='POST'><input type='text' name='deposit' Placeholder='Amount of Gold:'><input type='submit' value='Deposit'></center>",true);
				addnav("","runmodule.php?module=banking&op=confirm&a=depositgolddwell");
				break;
			}
		}
		break;
		
		case "withdraw_all":
		
			$onhandgold = 0;
			$onhandgems = 0;
			
			output("You have withdrawn all gold and gems from your accounts.");
			
			// Banked Gold
			$total = ($bankedgold-$bankedgold);
			output("<center>`n`nYour Gold Bank Account now has: $total Gold.`n",true);
			$session['user']['goldinbank']-=$bankedgold;
			$session['user']['gold']+=$bankedgold;
			$onhandgold+=$bankedgold;
			$total = 0;
			
			// Dwelling Gold
			$total = ($dwgold-$dwgold);
			output("<center>`n`nYour Dwellings Gold Bank Account now has: $total Gold.`n",true);
			$session['user']['dwgold']-=$dwgold;
			$session['user']['gold']+=$dwgold;
			$onhandgold += $dwgold;
			$total = 0;
			
			// Banked Gems
			$total = ($bankedgems-$bankedgems);
			output("<center>`n`nYour Gem Bank Account now has: $total Gems.`n",true);
			set_module_pref("gemsinbank",$total,"bankmod");
			$onhandgems += $bankedgems;
			$session['user']['gems']+=$bankedgems;
			$total = 0;
			
			// Dwelling Gems
			$total = ($dwgems-$dwgems);
			output("<center>`n`nYour Dwellings Gem Bank Account now has: $total Gems.`n",true);
			$session['user']['dwgems']-=$dwgems;
			$onhandgems += $dwgems;
			$session['user']['gems']+=$dwgems;
			$total = 0;
			
			output("You now have $onhandgold Gold on hand.`n`n",true);
			output("You now have $onhandgems Gems on hand.</center>`n`n",true);
			
		break;

		case "confirm":
		$post = httpallpost();
		$w = $post['withdraw'];
		$d = $post['deposit'];
		switch($a){
			case "withdrawgemsbank":
				debug($w);
				if ($w>$bankedgems || $w<0){
					output("<center>Invalid Operation`n`nYou must select a number that exists in your account!</center>",true);
					break;
				}else if ($w == "" || $w == 0){
					$total = ($bankedgems-$bankedgems);
					output("<center>`n`nYour Gem Bank Account now has: $total Gems.`n",true);
					set_module_pref("gemsinbank",$total,"bankmod");
					$onhandgems += $bankedgems;
					output("You now have $onhandgems Gems on hand.</center>`n`n",true);
					$session['user']['gems']+=$bankedgems;
				}else{
					$total = ($bankedgems-$w);
					output("<center>`n`nYour Gem Bank Account now has: $total Gems.`n",true);
					set_module_pref("gemsinbank",$total,"bankmod");
					$onhandgems += $w;
					output("You now have $onhandgems Gems on hand.</center>`n`n",true);
					$session['user']['gems']+=$w;
				}
			break;
			case "depositgemsbank":
				debug($d);
				if ($d>$onhandgems || $d<0){
					output("<center>Invalid Operation`n`nYou must select a number that exists in your account!</center>",true);
					break;
				}else if ($d == "" || $d == 0){
					$bankedgems+=$onhandgems;
					output("<center>`n`nYour Gem Bank Account now has: $bankedgems Gems.`n",true);
					$total = $bankedgems;
					set_module_pref("gemsinbank",$total,"bankmod");
					$total2 = $session['user']['gems']-=$onhandgems;
					output("You now have $total2 Gems on hand.</center>`n`n",true);
				}else{
					$bankedgems+=$d;
					output("<center>`n`nYour Gem Bank Account now has: $bankedgems Gems.`n",true);
					$total = $bankedgems;
					set_module_pref("gemsinbank",$total,"bankmod");
					$total2 = $session['user']['gems']-=$d;
					output("You now have $total2 Gems on hand.</center>`n`n",true);
				}
			break;
			case "withdrawgoldbank":
				debug($w);
				if ($w>$bankedgold || $w<0){
					output("<center>Invalid Operation`n`nYou must select a number that exists in your account!</center>",true);
					break;
				}else if ($w == "" || $w == 0){
					$total = ($bankedgold-$bankedgold);
					output("<center>`n`nYour Gold Bank Account now has: $total Gold.`n",true);
					$session['user']['goldinbank']-=$bankedgold;
					$session['user']['gold']+=$bankedgold;
					$onhandgold+=$bankedgold;
					output("You now have $onhandgold Gold on hand.</center>`n`n",true);
				}else{
					$total = ($bankedgold-$w);
					output("<center>`n`nYour Gold Bank Account now has: $total Gold.`n",true);
					$session['user']['goldinbank']-=$w;
					$session['user']['gold']+=$w;
					$onhandgold+=$w;
					output("You now have $onhandgold Gold on hand.</center>`n`n",true);
				}
			break;
			case "depositgoldbank":
				debug($d);
				if ($d>$onhandgold || $d<0){
					output("<center>Invalid Operation`n`nYou must select a number that exists in your account!</center>",true);
					break;
				}else if ($d == "" || $d == 0){
					$bankedgold+=$onhandgold;
					output("<center>`n`nYour Gold Bank Account now has: $bankedgold Gold.`n",true);
					$session['user']['goldinbank']+=$onhandgold;
					$total = ($onhandgold-$onhandgold);
					output("You now have $total Gold on hand.</center>`n`n",true);
					$session['user']['gold']-=$onhandgold;
				}else{
					$bankedgold+=$d;
					output("<center>`n`nYour Gold Bank Account now has: $bankedgold Gold.`n",true);
					$session['user']['goldinbank']+=$d;
					$total = ($onhandgold-$d);
					output("You now have $total Gold on hand.</center>`n`n",true);
					$session['user']['gold']-=$d;
				}
			break;
			case "withdrawgemsdwell":
				debug($w);
				if ($w>$dwgems || $w<0){
					output("<center>Invalid Operation`n`nYou must select a number that exists in your account!</center>",true);
					break;
				}else if ($w == "" || $w == 0){
					$total = ($dwgems-$dwgems);
					output("<center>`n`nYour Gem Bank Account now has: $total Gems.`n",true);
					$session['user']['dwgems']-=$dwgems;
					$onhandgems += $dwgems;
					output("You now have $onhandgems Gems on hand.</center>`n`n",true);
					$session['user']['gems']+=$dwgems;
				}else{
					$total = ($dwgems-$w);
					output("<center>`n`nYour Gem Bank Account now has: $total Gems.`n",true);
					$session['user']['dwgems']-=$w;
					$onhandgems += $w;
					output("You now have $onhandgems Gems on hand.</center>`n`n",true);
					$session['user']['gems']+=$w;
				}
			break;
			case "depositgemsdwell";
					debug($d);
				if ($d>$onhandgems || $d<0){
					output("<center>Invalid Operation`n`nYou must select a number that exists in your account!</center>",true);
					break;
				}else if ($d == "" || $d == 0){
					$dwgems+=$onhandgems;
					output("<center>`n`nYour Gem Bank Account now has: $dwgems Gems.`n",true);
					$session['user']['dwgems']+=$onhandgems;
					$total2 = $session['user']['gems']-=$onhandgems;
					output("You now have $total2 Gems on hand.</center>`n`n",true);
				}else{
					$dwgems+=$d;
					output("<center>`n`nYour Gem Bank Account now has: $dwgems Gems.`n",true);
					$total = $dwgems;
					$session['user']['dwgems']+=$d;
					$total2 = $session['user']['gems']-=$d;
					output("You now have $total2 Gems on hand.</center>`n`n",true);
				}
			break;
			case "withdrawgolddwell":
				debug($w);
				if ($w>$dwgold || $w<0){
					output("<center>Invalid Operation`n`nYou must select a number that exists in your account!</center>",true);
					break;
				}else if ($w == "" || $w == 0){
					$total = ($dwgold-$dwgold);
					output("<center>`n`nYour Gold Bank Account now has: $total Gold.`n",true);
					$session['user']['dwgold']-=$dwgold;
					$session['user']['gold']+=$dwgold;
					$onhandgold += $dwgold;
					output("You now have $onhandgold Gold on hand.</center>`n`n",true);
				}else{
					$total = ($dwgold-$w);
					output("<center>`n`nYour Gold Bank Account now has: $total Gold.`n",true);
					$session['user']['dwgold']-=$w;
					$session['user']['gold']+=$w;
					$onhandgold += $w;
					output("You now have $onhandgold Gold on hand.</center>`n`n",true);
				}
			break;
			case "depositgolddwell";
				debug($d);
				if ($d>$onhandgold || $d<0){
					output("<center>Invalid Operation`n`nYou must select a number that exists in your account!</center>",true);
					break;
				}else if ($d == "" || $d == 0){
					$dwgold+=$onhandgold;
					$session['user']['dwgold']+=$onhandgold;
					output("<center>`n`nYour Gold Bank Account(Dwelling) now has: $dwgold Gold.`n",true);
					$total = ($onhandgold-$onhandgold);
					$session['user']['gold']-=$onhandgold;
					output("You now have $total Gold on hand.</center>`n`n",true);
				}else{
					$dwgold+=$d;
					$session['user']['dwgold']+=$d;
					output("<center>`n`nYour Gold Bank Account(Dwelling) now has: $dwgold Gold.`n",true);
					$total = ($onhandgold-$d);
					$session['user']['gold']-=$d;
					output("You now have $total Gold on hand.</center>`n`n",true);
				}
			break;
		}
		break;
	}
	popup_footer();
}
?>