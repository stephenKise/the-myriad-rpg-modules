<?php
function medcontest_getmoduleinfo(){
	$info = array(
		"name"=>"Medallion Contest",
		"version"=>"2.22",
		"author"=>"`#Lonny Luberts",
		"category"=>"Village",
		"download"=>"http://www.pqcomp.com/modules/mydownloads/visit.php?cid=3&lid=77",
		"vertxtloc"=>"http://www.pqcomp.com/",
		"override_forced_nav"=>true,
		"prefs"=>array(
			"Medallion Contest,title",
			"medallion"=>"Medallions in possesion,int|0",
			"medhunt"=>"Joined Medallion Contest,bool|0",
			"medpoints"=>"Current Score,int|0",
			"medfind"=>"Medallions found today,int|0",
			"lastloc"=>"Last Player Location,text|",
			"seclastloc"=>"Second From Last Player Location,text|",
			//"administrate"=>"Admin/Moderator Access,bool|0",
			"user_stat"=>"Show Numberical Stat (JAWS Compatability),bool|0",
		),
		"settings"=>array(
			"Medallion Contest Module Settings,title",
			"medallionmax"=>"Max Medallions per Gameday (99 Max),int|12",
			"medconthigh"=>"High Score",
			"medconthighid"=>"High Score User ID",
			"Be sure to set Last reset date before turning this on or contest may auto reset now!,note",
			"autoreset"=>"Use Auto Reset,bool|0",
			"resettimer"=>"How long to run contest for auto reset,enum,4,4 Days,7,1 Week,14,2 Weeks,21,3 Weeks,28,4 Weeks",
			"Tweak the Reset Date to get the next reset where you want it.  Also set this Value before turning auto reset on or your contest may auto reset immediately. ,note",
			"lastreset"=>"Last Reset Date,text|2005-01-01",
			"Time of day reset is aproximate.  Actual reset happens on first new day after this time. ,note",
			"resettimeofday"=>"Time of Day to perform Reset,enum,00,12:00am,01,1:00am,02,2:00am,03,3:00am,04,4:00am,05,5:00am,06,6:00am,07,7:00am,08,8:00am,09,9:00am,10,10:00am,11,11:00am,12,12:00pm,13,1:00pm,14,2:00pm,15,3:00pm,16,4:00pm,17,5:00pm,18,6:00pm,19,7:00pm,20,8:00pm,21,9:00pm,22,10:00pm,23,11:00pm",
			"indexstats"=>"Show Leader on Login screen,bool|1",
		),
	);
	return $info;
}

function medcontest_install(){
	if (!is_module_active('medcontest')){
		output("`4Installing Medallion Contest Module.`n");
	}else{
		output("`4Updating Medallion Contest Module.`n");
	}
	module_addhook("charstats");
	module_addhook("newday");
	module_addhook("village");
	module_addhook("everyhit");
	module_addhook("superuser");
	//module_addhook("village-desc");
	module_addhook("index");
	return true;
}

function medcontest_uninstall(){
	output("`4Un-Installing Medallion Contest Module.`n");
	return true;
}

function medcontest_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "charstats":
			if ($session['user']['alive']){
				if (get_module_pref('user_stat') == 0){
					for ($i=0;$i<6;$i+=1){
						get_module_pref('medallion')>$i ? $medallion.="<img src=\"./images/medallion/medallion.gif\" title=\"\" alt=\"\" style=\"width: 14px; height: 16px;\">" : $medallion.="<img src=\"./images/medallion/medallionclear.gif\" title=\"\" alt=\"\" style=\"width: 14px; height: 16px;\">";
					}
				}else{
					$medallion = get_module_pref('medallion');
				}
				if (get_module_pref('medhunt')){
					$date=date_create(get_module_setting("lastreset","medcontest"));
					date_add($date,date_interval_create_from_date_string("7 days"));
					$reset = date_format($date,"F jS, Y")." at 12:00am";
					addcharstat("Collecting Rare Items");
					get_module_pref("medallion")>0 ? addcharstat("Items", $medallion."`n[<a href=\"runmodule.php?module=medcontest&op=turnin&from=1\" target=\"_blank\" onClick=\"".popup("runmodule.php?module=medcontest&op=turnin&from=1").";return false;\">Turn In</a>]") : addcharstat("Items","`iNone`i");
					addcharstat("Reset Day",$reset);
				}
			}
		break;
		case "newday":
			if (get_module_setting('medallionmax') > 99) set_module_setting('medallionmax',99);
			if ($session['user']['spirits'] != -6) set_module_pref('medfind',e_rand(round(get_module_setting('medallionmax') * .75),get_module_setting('medallionmax')));
			$olddt = date(get_module_setting('lastreset')." ".get_module_setting('resettimeofday').":00:00");
			$newdt = date("Y-m-d h:m:s",strtotime("-".get_module_setting('resettimer')." days"));
			if ($olddt < $newdt AND get_module_setting('autoreset')){
				require_once("modules/lib/medcontest_func.php");
				medcontest_reset();
			}
		break;
		case "village":
			tlschema($args['schemas']['fightnav']);
    		addnav($args['fightnav']);
    		tlschema();
			addnav("Collector Contest","runmodule.php?module=medcontest");
		break;
		case "everyhit":
			if ($session['user']['alive'] == 1){
				global $SCRIPT_NAME;
				if (get_module_setting('medallionmax') > 99) set_module_setting('medallionmax',99);
				if ($SCRIPT_NAME <> "mail.php" and $SCRIPT_NAME <> "motd.php" and $SCRIPT_NAME <> "petition.php" and $SCRIPT_NAME <> "superuser.php" and $SCRIPT_NAME <> "user.php"){
					if (e_rand(1,100) > ((100 - get_module_setting('medallionmax')) + get_module_pref('medfind')) and $session['user']['alive'] and $SCRIPT_NAME <> get_module_pref('lastloc') and $SCRIPT_NAME <> get_module_pref('seclastloc')){
						if (get_module_pref('medhunt') and get_module_pref('medfind') > 0){
							if (get_module_pref('medallion') < 5){
								output("`c`b`L<big><big><big><big>You Found One!</big></big></big></big>`b`c",true);
								set_module_pref('medallion',(get_module_pref('medallion') +1));
								set_module_pref('medfind',(get_module_pref('medfind') - 1));
								set_module_pref('seclastloc',get_module_pref('lastloc'));
								set_module_pref('lastloc',$SCRIPT_NAME);
							}else{
								output("`c`b`L<big><big>You Found One!</big></big>`b`c",true);
								output("`c`b`l<big><big>Too bad you are already carrying your limit!</big></big>`b`c",true);
								set_module_pref('seclastloc',get_module_pref('lastloc'));
								set_module_pref('lastloc',$SCRIPT_NAME);
							}
						}
					}
				}
			}
		break;
		case "superuser":
			addnav('Actions');
			if ($session['user']['superuser'] & SU_EDIT_COMMENTS) addnav("Collectors Contest","runmodule.php?module=medcontest&mode=super");
		break;
		case "index":
			if (get_module_setting("indexstats") == 1){
				$sql = "SELECT userid FROM ".db_prefix("module_userprefs")." WHERE modulename = 'medcontest' and setting = 'medpoints' and value > 0 ORDER BY value+0 DESC LIMIT 1";
				$result = db_query($sql);
    			$row = db_fetch_assoc($result);
				if ($row['userid'] <> ""){
	    			$sql2="SELECT name FROM ".db_prefix("accounts")." WHERE acctid ='".$row['userid']."'";
	    			$result2 = db_query($sql2);
	    			$row2 = db_fetch_assoc($result2);
					$plaque = $row2['name'];
					if ($plaque <> ""){
						output("`@The Master Collector is");
						output("$plaque`@.`n"); 
					}
				}
			}
		break;
		/*case "village-desc":
			$sql = "SELECT userid FROM ".db_prefix("module_userprefs")." WHERE modulename = 'medcontest' and setting = 'medpoints' and value > 0 ORDER BY value+0 DESC LIMIT 1";
				$result = db_query($sql);
    			$row = db_fetch_assoc($result);
    			if ($row['userid'] <> ""){
				$sql2="SELECT name FROM ".db_prefix("accounts")." WHERE acctid ='".$row['userid']."'";
    			$result2 = db_query($sql2);
    			$row2 = db_fetch_assoc($result2);
				$plaque = $row2['name'];
				if ($plaque <> ""){
					output("`@`cThe Master Collector is");
					output("$plaque`@.`c"); 
				}
				}
		break;*/
	}
	return $args;
}

function medcontest_run(){
	global $SCRIPT_NAME;
	if ($SCRIPT_NAME == "runmodule.php"){
		$module=httpget("module");
		if ($module == "medcontest"){
			require_once("modules/lib/medcontest_func.php");
			include("modules/lib/medcontest.php");
		}
	}
}

?>