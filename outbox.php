<?php
//this is mainly a copy of mail.php
//took a good look at cortalux friendlist to cope with the forced navs...
define("OVERRIDE_FORCED_NAV",true);


function outbox_getmoduleinfo(){
	$info = array(
		"name"=>"Outbox",
		"override_forced_nav"=>true,
		"version"=>"1.01",
		"author"=>"`2Oliver Brendel",
		"category"=>"Mail",
		"download"=>"http://dragonprime.net/dls/outbox.zip",
		"description"=>"Adds an outbox to the users YOM. Yet this does not change any mails. You can only view mails that are not already deleted by the recipient.",
		"settings"=>array(
		"Outbox - Preferences,title",
		"Note that this is no real outbox yet a -view from the recipient-,note",
		"if the recipient deleted the message... its gone. Also true if the sender deletes it,note",
		"allowdelete"=>"Allow users to delete sent mails (undo their sent in a way),bool|1",
		"this stores messages additionally in an extra table,note",
		"if active then the setting above will not delete the message from the recipients inbox,note",
		"realoutbox"=>"Use a real seperate outbox (uses space + cpu time),bool|0",
		),
		);
	return $info;
}

function outbox_install(){
	module_addhook("mailfunctions");
	module_addhook("newday-runonce");
	if (db_table_exists(db_prefix("mailoutbox"))){
	} else {
		db_query("CREATE TABLE ".db_prefix("mailoutbox")."(
`messageid` int( 11 ) unsigned NOT NULL AUTO_INCREMENT ,
`msgfrom` int( 11 ) unsigned NOT NULL default '0',
`msgto` int( 11 ) unsigned NOT NULL default '0',
`subject` varchar( 255 ) NOT NULL default '',
`body` text NOT NULL ,
`sent` datetime NOT NULL default '0000-00-00 00:00:00',
`seen` tinyint( 1 ) NOT NULL default '0',
PRIMARY KEY ( `messageid` ) ,
KEY `msgto` ( `msgto` ) ,
KEY `seen` ( `seen` )
)Engine = MYISAM");
	}
	return true;
}

function outbox_uninstall()
{
  output_notl ("Performing Uninstall on Outbox Module. Thank you for using!`n`n");
   if(db_table_exists(db_prefix("mailoutbox"))){
	db_query("DROP TABLE ".db_prefix("mailoutbox"));
	}
   return true;
}


function outbox_dohook($hookname, $args){
	global $session,$SCRIPT_NAME;
	switch ($hookname)
	{
	case "mailfunctions":
		$outbox = translate_inline("Outbox");
		$atable=db_prefix('accounts');
		if (get_module_setting('realoutbox')) {
			$op=httpget('op');
			if ($op=="send") {
				$to = httppost('to');
				$sql = "SELECT acctid FROM " . $atable . " WHERE login='$to'";
				$result = db_query($sql);
				if (db_num_rows($result)>0){
					$row1 = db_fetch_assoc($result);
					$sql = "SELECT count(messageid) AS count FROM " . db_prefix("mail") . " WHERE msgto='".$row1['acctid']."' AND seen=0";
					$result = db_query($sql);
					$row = db_fetch_assoc($result);
					$sql2 = "SELECT count(messageid) AS count FROM " . db_prefix("mailoutbox") . " WHERE msgfrom='".$session['user']['acctid']."' AND seen=0";
					$result2 = db_query($sql2);
					$row2 = db_fetch_assoc($result2);
					if ($row['count']>=getsetting("inboxlimit",50)) {
						//do nothing in this module
					}elseif ($row['count']>=getsetting("inboxlimit",50)){
						output("Sorry, this mail won't be saved in your outbox. You have to delete mails there.");
					} else {
						$subject =  str_replace("`n","",httppost('subject'));
						$body = str_replace("`n","\n",httppost('body'));
						$body = str_replace("\r\n","\n",$body);
						$body = str_replace("\r","\n",$body);
						$body = addslashes(substr(stripslashes($body),0,(int)getsetting("mailsizelimit",1024)));
						$sql = "INSERT INTO " . db_prefix("mailoutbox") . " (msgfrom,msgto,subject,body,sent) VALUES ('".(int)$session['user']['acctid']."','".(int)$row1['acctid']."','$subject','$body','".date("Y-m-d H:i:s")."')";
						db_query($sql);
					}
				}
			}
		}
		if ($op=="read") {
			$id=httpget('id');
			$sql = "SELECT " . db_prefix("mail") . ".*,". $atable. ".name FROM " . db_prefix("mail") ." LEFT JOIN " . $atable . " ON ". $atable . ".acctid=" . db_prefix("mail"). ".msgfrom WHERE msgto=\"".$session['user']['acctid']."\" AND messageid=\"".$id."\"";
			$result = db_query($sql);
			$row=db_fetch_assoc($result);
			$sql = "SELECT " . db_prefix("mailoutbox") . ".*,". $atable. ".name FROM " . db_prefix("mailoutbox") ." LEFT JOIN " . $atable . " ON ". $atable . ".acctid=" . db_prefix("mailoutbox"). ".msgfrom WHERE msgto='".$session['user']['acctid']."' AND subject='".addslashes($row['subject'])."' AND body='".addslashes($row['body'])."';";
			$result = db_query($sql);
			if (db_num_rows($result)>0){
				$row = db_fetch_assoc($result);
				$sql = "UPDATE " . db_prefix("mailoutbox") . " SET seen=1 WHERE  msgto=\"".$session['user']['acctid']."\" AND messageid=\"".$row['messageid']."\"";
				if (!$row['seen']) db_query($sql);
			}
		}
		break;

	case "newday-runonce":
		$sql = "DELETE FROM " . db_prefix("mailoutbox") . " WHERE sent<'".date("Y-m-d H:i:s",strtotime("-".getsetting("oldmail",14)."days"))."'";
		db_query($sql); //do this here because this won't be called often
		break;
	default:

		break;
	}
	return $args;
}

function outbox_run(){
	global $session,$SCRIPT_NAME;
	$op=httpget('op');
	$id = httpget('id');
	require_once("lib/http.php");
	popup_header("Outbox");
	$realoutbox=get_module_setting('realoutbox');
	$allowdelete=get_module_setting('allowdelete');
	$table=($realoutbox?"mailoutbox":"mail"); //set the table
	$ptable= db_prefix($table);
	$atable= db_prefix("accounts");
	
output("<table width='100%' border='0' cellpadding='0' cellspacing='2'>",TRUE);
rawoutput("<tr><td valign=\"top\" width='150px' nowrap>");
output("&bull;<a href='mail.php'>`tInbox</a>`n",TRUE);
output("&bull;<a href='runmodule.php?module=outbox'>`tOutbox</a>`n",TRUE);
output("&bull;<a href='mail.php?&op=address'>`tCompose</a>`n",TRUE);
output("&bull;<a href='petition.php'>`\$Petition for Help</a>`n",TRUE);
modulehook("mailfunctions");
output_notl("</td><td>",true);
	switch ($op) {
		case "delown":
			$sql = "DELETE FROM " . $ptable . " WHERE msgfrom='".$session['user']['acctid']."' AND messageid='$id'";
			db_query($sql);
			invalidatedatacache("mail-".httpget('rec'));
			header("Location: mail.php");
			exit();
			break;
		case "readown":
			$sql = "SELECT " . $ptable . ".*,". $atable. ".name,". $atable. ".acctid FROM " . $ptable ." LEFT JOIN " . $atable . " ON ". $atable . ".acctid=" . $ptable. ".msgto WHERE msgfrom=\"".$session['user']['acctid']."\" AND messageid=\"".$id."\" AND seen != 2";
			$result = db_query($sql);
			if (db_num_rows($result)>0){
				$row = db_fetch_assoc($result);
				debug($row['seen']);
				if ($row['seen'] == 1) output("`b`#Not yet read by the recipient`b`n");
				else output_notl("`n");
				$tot=translate_inline("To: ");
				output_notl("`b`2$tot`b `^%s`n",$row['name']);
				output("`b`2Subject:`b `^%s`n",$row['subject']);
				output("`b`2Sent:`b `^%s`n",$row['sent']);
				output_notl("<img src='images/uscroll.GIF' width='182' height='11' alt='' align='center'>`n",true);
				output_notl(str_replace("\n","`n",$row['body']));
				output_notl("`n<img src='images/lscroll.GIF' width='182' height='11' alt='' align='center'>`n",true);
				$del = translate_inline("Delete");
				if ($allowdelete && !$realoutbox) output("`i`0Note: If you delete this message, the recipient won't see it anymore.`i");
				rawoutput("<table width='50%' border='0' cellpadding='0' cellspacing='5'><tr>");
				if ($allowdelete) rawoutput("<td><a href='runmodule.php?module=outbox&op=delown&id={$row['messageid']}&rec={$row['acctid']}' class='motd'>$del</a></td>");
				rawoutput("</tr><tr>");
				addnav("","runmodule.php?module=outbox&op=delown&id={$row['messageid']}&rec={$row['acctid']}");
				$sql = "SELECT messageid FROM ".$ptable." WHERE msgfrom='{$session['user']['acctid']}' AND messageid < '$id' AND seen = 0 ORDER BY messageid DESC LIMIT 1";
				$result = db_query($sql);
				if (db_num_rows($result)>0){
					$row = db_fetch_assoc($result);
					$pid = $row['messageid'];
				}else{
					$pid = 0;
				}
				$sql = "SELECT messageid FROM ".$ptable." WHERE msgfrom='{$session['user']['acctid']}' AND messageid > '$id' AND seen = 0 ORDER BY messageid  LIMIT 1";
				$result = db_query($sql);
				if (db_num_rows($result)>0){
					$row = db_fetch_assoc($result);
					$nid = $row['messageid'];
				}else{
					$nid = 0;
				}
				$prev = translate_inline("< Previous");
				$next = translate_inline("Next >");
				rawoutput("<td nowrap='true'>");
				if ($pid > 0) {
					rawoutput("<a href='runmodule.php?module=outbox&op=readown&id=$pid' class='motd'>".htmlentities($prev)."</a>");
					addnav("","runmodule.php?module=outbox&op=readown&id=$pid");
					}
				else rawoutput(htmlentities($prev));
				rawoutput("</td><td nowrap='true'>");
				if ($nid > 0) {
					rawoutput("<a href='runmodule.php?module=outbox&op=readown&id=$nid' class='motd'>".htmlentities($next)."</a>");
					addnav("","runmodule.php?module=outbox&op=readown&id=$nid");
					}
				else rawoutput(htmlentities($next));
				rawoutput("</td>");
				rawoutput("</tr></table>");
			}else{
				output("Eek, no such message was found!");
			}
			break;
		case "process":
			$msg = httppost('msg');
			if (!is_array($msg) || count($msg)<1){
			$session['message'] = "`\$`bYou cannot delete zero messages!  What does this mean?  You pressed \"Delete Checked\" but there are no messages checked!  What sort of world is this that people press buttons that have no meaning?!?`b`0";
				header("Location: mail.php");
			}else{
				$sql = "DELETE FROM " . db_prefix("mailoutbox") . " WHERE msgfrom='".$session['user']['acctid']."' AND messageid IN ('".implode("','",$msg)."')";
				db_query($sql);
				$sql2 = "DELETE FROM " . db_prefix("mail") . " WHERE msgfrom='".$session['user']['acctid']."' AND messageid IN ('".implode("','",$msg)."')";
				db_query($sql2);
				header("Location: mail.php");
				exit();
			}
			break;
		default:
			$hidepetitions = get_module_pref("hidepetitions", "inboxpetitions");
			$sql = "SELECT * FROM petitions WHERE author = {$session['user']['acctid']} ORDER BY status ASC";
			$res = db_query($sql);
			$max = db_num_rows($res);
			$tit = translate_inline("`b<big>`&`i`cPetition`c`i</big>`b");
			$lastup = translate_inline("`>`b<big>`&`iLast Update`i</big>`b`>");
			
			$statuses=array(
				0=>"`&`bUnhandled`b",
				1=>"`\$Coding",
				2=>"`QContest Entry",
				3=>"`^General Errors",
				4=>"`@Donation",
				5=>"`#Progressive",
				6=>"`!Miscellaneous",
				7=>"`)`iClosed`i",
			);
				
			output("<table><tr><td>`b`iPetitions`i`b</td></tr>",true);
			if ($max){
				while ($max>0){
					$row = db_fetch_assoc($res);
					if ($row['status'] == 7 && $hidepetitions) break;
					
					$pn = $row['pname'];
					if ($pn == "")
						$pn = "No Subject";
					output("<tr><td>",true);
					output("<a href='runmodule.php?module=inboxpetitions&op=viewpet&petid=".$row['petitionid']."'>`^`b".$pn."`b</a>",true);
					output("</td><td>",true);
					$coms = db_query("SELECT * FROM commentary WHERE section = 'pet-".$row['petitionid']."'");
					$commentz = db_num_rows($coms);
					
					output("`^`c".$statuses[$row['status']].".`c");
					output("</td><td>",true);
					$csq = "SELECT * FROM accounts WHERE acctid=".$row['closeuserid'];
					$crez = db_query($csq);
					$cro = db_fetch_assoc($crez);
					output("<a href='runmodule.php?module=inboxpetitions&op=viewpet&petid=".$row['petitionid']."'>".date("M d, h:i a",strtotime($row['date']))."</a>",true);
					output("</td></tr>",true);
					$max--;
				}
				output("<tr><td><small><a href='runmodule.php?module=inboxpetitions&op=hidepetitions&ret=".urlencode($_SERVER['REQUEST_URI'])."'>[".($hidepetitions?"Show":"Hide")." closed petitions]</a></small></td></tr>", true);
			}else{
				output("<tr><td>`iGood! You have no petitions that are incomplete!`i</td></tr>",true);
			}
			output("<tr><td colspan='3'>`n`n`b`iOut Box`i`b</td></tr>",true);
			if (isset($session['message'])) {
				output($session['message']);
			}
			$session['message']="";
			$sql = "SELECT subject,messageid," . $atable . ".name,msgto,msgfrom,seen,sent FROM " . $ptable . " LEFT JOIN " . $atable . " ON " . $atable . ".acctid=" . $ptable . ".msgto WHERE msgfrom=\"".$session['user']['acctid']."\" AND seen != 2 ORDER BY sent DESC";
			$result = db_query($sql);
			if (db_num_rows($result)>0){
				$i=-1;
				output_notl("<form action='runmodule.php?module=outbox&op=process' method='POST'>",true);
				addnav("","runmodule.php?module=outbox&op=process");
					while ($row = db_fetch_assoc($result)) {
					$i++;
					output_notl("<tr>",true);
					output_notl("<td nowrap><input id='checkbox$i' type='checkbox' name='msg[]' value='{$row['messageid']}'>&nbsp;<img src='images/".($row['seen']?"new":"old")."scroll.GIF' width='16' height='16' alt='".($row['seen']?"Old":"New")."'>",true);
					output_notl("<a href='runmodule.php?module=outbox&op=readown&id={$row['messageid']}'>",true);
					if (trim($row['subject'])=="")
						output("`i(No Subject)`i");
					else
						output_notl($row['subject']);
					output_notl("</a></td><td align='center'><a href='runmodule.php?module=outbox&op=readown&id={$row['messageid']}'>",true);
					addnav("","runmodule.php?module=outbox&op=readown&id={$row['messageid']}");
					output_notl($row['name']);
					output_notl("</a></td><td><a href='runmodule.php?module=outbox&op=readown&id={$row['messageid']}'>".date("M d, h:i a",strtotime($row['sent']))."</a></td>",true);
					addnav("","runmodule.php?module=outbox&op=readown&id={$row['messageid']}");
					output_notl("</tr>",true);
				}
				output_notl("</table>",true);
				$checkall = htmlentities(translate_inline("Check All"));
				$out="<input type='button' value=\"$checkall\" class='button' onClick='";
				for ($i=$i;$i>=0;$i--){
					$out.="document.getElementById(\"checkbox$i\").checked=true;";
				}
				$out.="'>";
				output_notl($out,true);
				$delchecked = htmlentities(translate_inline("Delete Checked"));
				output_notl("<input type='submit' class='button' value=\"$delchecked\">",true);
				output_notl("</form>",true);

			}else{
				output("<tr><td colspan='3'>`iYour outbox is currently empty!`i</td></tr></table>",true);
			}
			break;
		}
rawoutput("</td></tr></table>",TRUE);
popup_footer();
}

?>
