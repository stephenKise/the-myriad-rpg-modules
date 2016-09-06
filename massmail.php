<?php
function massmail_getmoduleinfo(){
	$info = array(
		"name" => "Mass Mail",
		"author" => "`b`&Ka`6laza`&ar`b",
		"version" => "1.1",
		"download" => "http://dragonprime.net/index.php?module=Downloads;catd=18",
		"category" => "Mail",
		"override_forced_nav"=>true,
		"description" => "clanonline all online members, admin mail all or online users",
	);
	return $info;
}

function massmail_install(){
	module_addhook("mailaddressoptions");
	return true;
}

function massmail_uninstall(){
	return true;
}

function massmail_dohook($hookname,$args){
	global $session;
	
	if ($session['user']['clanrank'] > 20){
		output("<a href='runmodule.php?module=massmail&op=clanonline'>`2[`0To all online Guild members`2]</a>`n",true);
		output("<a href='runmodule.php?module=massmail&op=clanall'>`2[`0To all Guild members`2]</a>`n",true);
	}
	if ($session['user']['superuser'] & SU_EDIT_PETITIONS){
		output("<a href='runmodule.php?module=massmail&op=adminonline'>`2[`0To all online players`2]</a>`n",true);
		output("<a href='runmodule.php?module=massmail&op=adminall'>`2[`0To everyone`2]</a>",true);
	}
		
    return $args;
}
function massmail_run(){
	global $session;
	require_once("lib/systemmail.php");
	
	popup_header("Mailbox");
	
	output("<table width='100%' border='0' cellpadding='0' cellspacing='2'>",TRUE);
	rawoutput("<tr><td valign=\"top\" width='150px' nowrap>");
	output("&bull;<a href='mail.php'>`tInbox</a>`n",TRUE);
	output("&bull;<a href='runmodule.php?module=outbox'>`tOutbox</a>`n",TRUE);
	output("&bull;<a href='mail.php?&op=address'>`tCompose</a>`n",TRUE);
	output("&bull;<a href='petition.php'>`\$Petition for Help</a>`n",TRUE);
	modulehook("mailfunctions");
	output_notl("</td><td>",true);
	
	$body = httppost('body');
	$name = $session['user']['name'];
	$title = httppost('title');
	$time = date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT", 900)." sec"));
	
	switch(httpget('op')){
		case "clanonline":
			if ($session['user']['clanrank'] > 20){
				if (!$body){
					rawoutput("<form action='runmodule.php?module=massmail&op=clanonline' method='POST'>");
					output("`^Coporation Mail for online members:`n");
					output("<input type=\"text\" name=\"title\" size=\"50\" placeholder=\"Title of Message:\">`n",true);
					require_once("lib/forms.php");
					previewfield("body", "`^", false, false, array("type"=>"textarea", "class"=>"input", "cols"=>"60", "rows"=>"9", "placeholder"=>"Body of Message:", "onKeyDown"=>"sizeCount(this);"), htmlentities($body, ENT_COMPAT, getsetting("charset", "ISO-8859-1")).htmlentities(stripslashes(httpget('body')), ENT_COMPAT, getsetting("charset", "ISO-8859-1")));
					rawoutput("<input type='submit' class='button' value='".translate_inline("Send")."'></form>");
				} else {
					$sql = "SELECT * FROM " . db_prefix("accounts"). " WHERE clanid = '{$session['user']['clanid']}' AND loggedin = 1 AND laston > '$time'";
					$res = db_query($sql);
					while($row=db_fetch_assoc($res)){
						systemmail($row['acctid'],$title,$body,$session['user']['acctid']);
					}
					output("`^Message has been sent.`0");
				}
				addnav("","runmodule.php?module=massmail&op=clanonline");
			} else {
				output("`QPlease send in a petition as to how you got to this page. You are not allowed to view it.");
			}
		break;
		case "clanall":
			if ($session['user']['clanrank'] > 20){
				if (!$body){
					rawoutput("<form action='runmodule.php?module=massmail&op=clanonline' method='POST'>");
					output("`^Corporation Mail for all members:`n");
					output("<input type=\"text\" name=\"title\" size=\"50\" placeholder=\"Title of Message:\">`n",true);
					require_once("lib/forms.php");
					previewfield("body", "`^", false, false, array("type"=>"textarea", "class"=>"input", "cols"=>"60", "rows"=>"9", "placeholder"=>"Body of Message:", "onKeyDown"=>"sizeCount(this);"), htmlentities($body, ENT_COMPAT, getsetting("charset", "ISO-8859-1")).htmlentities(stripslashes(httpget('body')), ENT_COMPAT, getsetting("charset", "ISO-8859-1")));
					rawoutput("<input type='submit' class='button' value='".translate_inline("Send")."'></form>");
				} else {
					$sql = "SELECT * FROM " . db_prefix("accounts"). " WHERE clanid = '{$session['user']['clanid']}'";
					$res = db_query($sql);
					while($row=db_fetch_assoc($res)){
						systemmail($row['acctid'],$title,$body,$session['user']['acctid']);
					}
					output("`^Message has been sent.`0");
				}
				addnav("","runmodule.php?module=massmail&op=clanonline");
			} else {
				output("`QPlease send in a petition as to how you got to this page. You are not allowed to view it.");
			}
		break;
		case "adminall":
			if ($session['user']['superuser'] & SU_EDIT_PETITIONS){
				if (!$body){
					rawoutput("<form action='runmodule.php?module=massmail&op=adminall' method='POST'>");
					output("`^Send to all players:`n");
					output("<input type=\"text\" name=\"title\" size=\"50\" placeholder=\"Title of Message:\">`n",true);
					require_once("lib/forms.php");
					previewfield("body", "`^", false, false, array("type"=>"textarea", "class"=>"input", "cols"=>"60", "rows"=>"9", "placeholder"=>"Body of Message:", "onKeyDown"=>"sizeCount(this);"), htmlentities($body, ENT_COMPAT, getsetting("charset", "ISO-8859-1")).htmlentities(stripslashes(httpget('body')), ENT_COMPAT, getsetting("charset", "ISO-8859-1")));
					rawoutput("<input type='submit' class='button' value='".translate_inline("Send")."'></form>");
				} else {
					$sql = "SELECT * FROM " . db_prefix("accounts");
					$res = db_query($sql);
					while($row=db_fetch_assoc($res)){
						systemmail($row['acctid'],$title,$body);
					}
					output("Your mail was sent to all players");
				}
				addnav("","runmodule.php?module=massmail&op=adminall");
			} else {
				output("`QPlease send in a petition as to how you got to this page. You are not allowed to view it.");
			}			
		break;
		case "adminonline":
			if ($session['user']['superuser'] & SU_EDIT_PETITIONS){
				if (!$body){
					rawoutput("<form action='runmodule.php?module=massmail&op=adminonline' method='POST'>");
					output("`^Send to all online:`n");
					output("<input type=\"text\" name=\"title\" size=\"50\" placeholder=\"Title of Message:\">`n",true);
					require_once("lib/forms.php");
					previewfield("body", "`^", false, false, array("type"=>"textarea", "class"=>"input", "cols"=>"60", "rows"=>"9", "placeholder"=>"Body of Message:", "onKeyDown"=>"sizeCount(this);"), htmlentities($body, ENT_COMPAT, getsetting("charset", "ISO-8859-1")).htmlentities(stripslashes(httpget('body')), ENT_COMPAT, getsetting("charset", "ISO-8859-1")));
					rawoutput("<input type='submit' class='button' value='".translate_inline("Send")."'></form>");
				} else {
					$sql = "SELECT * FROM " . db_prefix("accounts"). " WHERE loggedin = 1 AND laston > '$time'";
					$res = db_query($sql);
					while($row=db_fetch_assoc($res)){
						systemmail($row['acctid'],$title,$body);
					}
					output("Your Mail was sent to all online");
				}
				addnav("","runmodule.php?module=massmail&op=adminonline");
			} else {
				output("`QPlease send in a petition as to how you got to this page. You are not allowed to view it.");
			}
		break;
	}
	rawoutput("</td></tr></table>",TRUE);
	popup_footer();
}
?>