<?php

function supermessage_getmoduleinfo(){
    $info = array(
        "name"=>"Superuser Messaging",
        "author"=>"Chris Vorndran, `7modified by `b`&Xpert`b",
        "version"=>"2.0",
        "allowanonymous"=>true,
		"override_forced_nav"=>true,
        "category"=>"Administrative",
        //"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=62",
		//"vertxtloc"=>"http://dragonprime.net/users/Sichae/",
		"description"=>"Allows for Megausers or Admins to be able to send out a bulletin to all users that are on the Admin Mailing List. List is controlled by a Pref.",
        //"settings"=>array(
		//	"Superuser Messaging Settings,title",
        //   "subject"=>"Subject line for Message,text|A Message from Staff, For Staff",
        //),
		//"prefs"=>array(
		//	"Superuser Messaging Preferences,title",
		//	"isuser"=>"Is this user on the mailing list,bool|0",
		//	"position"=>"Which position are they,enum,0,Not On,1,Moderator,2,Senior Moderator,3,Junior Admin,4,Admin,5,Senior Admin,6,Owner|0",
		//		)
        );
    return $info;
}
function supermessage_install(){
    module_addhook("superuser");
    module_addhook_priority("mailaddressoptions","55");
    return true;
}
function supermessage_uninstall(){
    return true;
}
function supermessage_dohook($hookname,$args){
    global $session;
    $admins = get_module_setting('admins');
    switch ($hookname){
        case "superuser":
	        if ($session['user']['superuser'] & SU_EDIT_USERS){
	            addnav("Actions");
	            addnav("Message Superusers","runmodule.php?module=supermessage&op=allstaff",false,true,"800x350");
	        }
        break;
        case "mailaddressoptions":
			if ($session['user']['superuser'] & SU_EDIT_USERS) output("`n`2[<a href='runmodule.php?module=supermessage&op=allstaff'>To staff</a>`2]`n",true);
        break;
    }
    return $args;
}
function supermessage_run(){
    global $session;
    popup_header(" ");
    $op = httpget('op');
    $subject = "A Message from Staff, For Staff";
    $body = httppost('body');
    switch ($op){
        case "allstaff":
			output("<table width='100%' border='0' cellpadding='0' cellspacing='10px'>",TRUE);
			rawoutput("<tr><td valign=\"top\" width='150px' nowrap>");
			output("&bull;<a href='mail.php'>`tInbox</a>`n",TRUE);
			output("&bull;<a href='runmodule.php?module=outbox'>`tOutbox</a>`n",TRUE);
			output("&bull;<a href='mail.php?&op=address'>`tCompose</a>`n",TRUE);
			output("&bull;<a href='petition.php'>`\$Petition for Help</a>`n",TRUE);
			modulehook("mailfunctions", $args);
			output_notl("</td><td valign='top' >",true);
		        if ($body == ""){
		            output("Send a message to all on the staff listing.`n`n");
					//$sql = "SELECT name FROM ".db_prefix("accounts")." 
					//		INNER JOIN ".db_prefix("module_userprefs")."
					//		ON userid = acctid 
					//		WHERE modulename = 'supermessage' 
					//		AND setting = 'position' 
					//		AND value >= '1'";
					$sql = "SELECT name FROM ".db_prefix("accounts")." WHERE (superuser&".SU_EDIT_COMMENTS.")";
					$result = db_query($sql);
					$num = 0;
					$num_rows = db_num_rows($result);
					while($row = db_fetch_assoc($result)){
						$num++;
						if ($num < $num_rows) $punct = ',';
							else $punct = '.';
						output("%s%s ",$row['name'],$punct);
					}
		            rawoutput("<form action='runmodule.php?module=supermessage&op=allstaff' method='POST'>");
		            output("`nMessage to All Staff:`n`n");
		            rawoutput("<textarea name=\"body\" rows=\"10\" cols=\"60\" class=\"input\"></textarea>");
		            rawoutput("<input type='submit' class='button' value='".translate_inline("Send")."'></form>");
		            rawoutput("</form>");
		        }else{
		            $sql = "SELECT acctid FROM ".db_prefix("accounts")." WHERE (superuser&".SU_EDIT_COMMENTS.")";
		            $result = db_query($sql);
		            while($row = db_fetch_assoc($result)){
						require_once("lib/systemmail.php");
			            systemmail($row['acctid'],$subject,$body);
					}
		            $session['message'] = "Your message to all the staff has been sent!";
		            header("Location: mail.php");
				}
			
			rawoutput("</td></tr></table>",TRUE);
			break;
    }
popup_footer();
}
?>