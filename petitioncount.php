<?php

function petitioncount_getmoduleinfo(){
	$info = array(
		"name"=>"Petition Display",
		"author"=>"`b`&Stephen Kise`b`0",
		"category"=>"Administrative",
		"download"=>"nope",
		"version"=>"1.0",
		"settings"=>array(
			"Petition Display Settings,title",
			"viewcharstats"=>"Show petitions/UE in charstats?,bool|0",
		),
	);
	return $info;
}

function petitioncount_install(){
	module_addhook_priority("charstats",99);
	return TRUE;
}

function petitioncount_uninstall(){
	return TRUE;
}

function petitioncount_dohook($hookname,$args){
	global $session,$template;
	switch ($hookname){
		case "charstats":
			$viewcharstats = get_module_setting('viewcharstats');
			if ($viewcharstats){
				/***
				**** PETITION COUNT SQL BELOW
				***/
				
				$result = db_query("SELECT * FROM petitions WHERE status != 7");
				$num_rows = db_num_rows($result);

				/***
				**** CHARSTAT BELOW
				***/

				$sql2 = "SELECT count(petitionid) AS c,status FROM " . db_prefix("petitions") . " GROUP BY status";
				$result2 = db_query($sql2);
				$petitions=array(0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0);
				while ($row = db_fetch_assoc($result2)) {
					$petitions[(int)$row['status']] = $row['c'];
				}
				$area = "Administration";
				$ued2 = "<a href='user.php'>`&`bEdit Users`b</a>";
				$pet = "<a href='viewpetition.php'>`&$num_rows Petitions Open</a>";
				$p .= "<center>`b`&{$petitions[0]}`b`&|`\${$petitions[1]}`&|`Q{$petitions[2]}`&|`^{$petitions[3]}`&|`@{$petitions[4]}`&|`#{$petitions[5]}`&|`L{$petitions[6]}`&|`)`i{$petitions[7]}`i`n<a href='viewpetition.php'>`&View Petitions</a></center>`0";
				$pout = $p;
				$pout2 = "<center>`b`&{$petitions[0]}`b`&|`\${$petitions[1]}`&|`Q{$petitions[2]}`&|`^{$petitions[3]}`&|`@{$petitions[4]}`&|`#{$petitions[5]}`&|`L{$petitions[6]}`&|`)`i{$petitions[7]}`i`n<a href='viewpetition.php'>`&View Petitions</a></center>`0";
				
				if ($session['user']['superuser'] & SU_EDIT_PETITIONS && $session['user']['superuser'] & SU_EDIT_USERS){
					setcharstat($area, $ued2, $pout2);
				} else if ($session['user']['superuser'] & SU_EDIT_PETITIONS){
					setcharstat($area, $pet, $pout);
				}
				addnav("","viewpetition.php");
				addnav("","user.php");
			}
		break;
	}
	return $args;
}
?>