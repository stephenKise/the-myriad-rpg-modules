<?php

function adfuncbio_getmoduleinfo(){
	$info = array(
		"name"=>"Admin Functions from Bio",
		"author"=>"Chris Vorndran",
		"version"=>"0.21",
		"category"=>"Administrative",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=48",
		"vertxtloc"=>"http://dragonprime.net/users/Sichae/",
		"description"=>"Brings many of the Admin Functions (Newday, Navs and Killing) into a user's bio. Controlled by pref.",
		"settings"=>array(
			"Admin Functions from Bio Settings,title",
			"runfrom"=>"Navs appear when which condition is met,enum,0,Flag for Edit Users,1,Preference Set,2,Both|0",
			"hakil"=>"Is Kill Player enabled,bool|1",
		),
		"prefs"=>array(
			"Admin Functions From Bio Preferences,title",
			"ha"=>"Does this user have access to Give and Take functions,bool|0",
			"kp"=>"Has player been killed?,bool|0",
		),
	);
    return $info;
}
function adfuncbio_install(){
    module_addhook("biostat");
    module_addhook("village");
	module_addhook("superuser");
	module_addhook("forest");
	module_addhook("newday");
    return true;
}
function adfuncbio_uninstall(){
    return true;
}
function adfuncbio_dohook($hookname,$args){
    global $session;
    switch ($hookname){
        case "biostat":
		    $char = httpget('char');
			$ret = urlencode(httpget('ret'));
			if (!is_numeric($char)){
				$sql = "SELECT acctid FROM ".db_prefix("accounts")." WHERE login='$char' LIMIT 1";
				$res = db_query($sql);
				$row = db_fetch_assoc($res);
				$char = $row['acctid'];
			}
            if ((get_module_setting("runfrom") == 0 && $session['user']['superuser'] & SU_EDIT_USERS) || (get_module_setting("runfrom") == 1 && get_module_pref("ha") == 1) || (get_module_setting("runfrom") == 2 && $session['user']['superuser'] & SU_EDIT_USERS && get_module_pref("ha") == 1)){
                addnav("Admin Functions");
                addnav("2?Give Newday","runmodule.php?module=adfuncbio&op=opt&act=nd&id=$char&ret=$ret");
                if (get_module_setting("hakil") == 1)addnav("3?Kill Player","runmodule.php?module=adfuncbio&op=opt&act=kp&id=$char&ret=$ret");
            }
			if ($session['user']['superuser'] & SU_GIVE_GROTTO){
				addnav("Admin Functions");
				addnav("4?Fix Navs","runmodule.php?module=adfuncbio&op=opt&act=fn&id=$char&ret=$ret");
			}
		break;
		case "forest":
		case "village":
            if (get_module_pref("kp") == 1){
                $session['user']['alive']=0;
                $session['user']['hitpoints']=0;
                redirect("shades.php");
            }
		break;
		case "newday":
			set_module_pref("kp",0);
		break;
    }
    return $args;
}
function adfuncbio_run(){
    global $session;

    $op = httpget('op');
    $act = httpget('act');
    $id = httpget('id');
	$ret = urlencode(httpget('ret'));

	$sql = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid=$id LIMIT 1";
	$res = db_query($sql);
	$row = db_fetch_assoc($res);
	$name = $row['name'];
    page_header("Give and Take");

    switch ($op){
        case "opt":
            switch ($act){
                case "nd":
                    $offset = "-".(24 / (int)getsetting("daysperday",4))." hours";
                    $newdate = date("Y-m-d H:i:s",strtotime($offset));
                    $sql = "UPDATE " . db_prefix("accounts") . " SET lasthit='$newdate' WHERE acctid=$id LIMIT 1";
                    db_query($sql);
                    rawoutput("<big>");
                    output("New Day successfully given to %s!",$name);
                    rawoutput("</big>");
					debuglog("has granted a newday to $name");
				break;
                case "kp":
                    set_module_pref("kp",1,"adfuncbio",$id);
                    rawoutput("<big>");
                    output("%s has been successfully killed!",$name);
                    rawoutput("</big>");
					debuglog("has killed $name via Kill Player function");
				break;
				case "fn":
					$sql = "UPDATE " . db_prefix("accounts") . " SET allowednavs='', restorepage='', specialinc='' WHERE acctid='$id' LIMIT 1";
					db_query($sql);
					$sql = "DELETE FROM ".db_prefix("accounts_output")." WHERE acctid='$id' LIMIT 1";
					db_query($sql);
                    rawoutput("<big>");
                    output("Navs have been fixed for %s!",$name);
                    rawoutput("</big>");
					require_once('lib/systemmail.php');
					systemmail($id,array("Your Navs"),array("Your navs have been fixed, you should be able to navigate from the stuck page now. If not, please petition again. (This is an automatic message).`n`nRegards %s",$session['user']['name']));
					debuglog("has fixed the navs for $name");
				break;
            }
		break;
	}
	addnav('Return');
	addnav("Return to bio","bio.php?char=$id&ret=$ret");
	villagenav();
	
	page_footer();
}
?>
