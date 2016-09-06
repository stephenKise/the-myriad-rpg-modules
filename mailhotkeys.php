<?php
/*
Reply: R
Delete: DEL (Outbox included)
Mark Unread: U
Report to Admin: A
Previous: Left Arrow (Outbox included)
Next: Right Arrow (Outbox included)

(Added to v1.1 for all inbox/outbox pages):
Inbox: I
Write: W
Outbox: O (if active)
Friend List: F (if active)
*/

function mailhotkeys_getmoduleinfo(){
	$info = array(
		"name" => "Mail Hot Keys",
		"author" => "`i`)Ae`7ol`&us`i`0",
		"version" => "1.1b",
		"category" => "Mail",
		"download" => "http://dragonprime.net/index.php?topic=12509,0",
		"prefs" => array(
		"Mail Preferences,title",
		"user_usehotkeys"=>"Do you want to use the Hot Keys in your Mailbox?,bool|",
		),
	);
	return $info;
}

function mailhotkeys_install(){
	module_addhook("header-popup");
	return true;
}

function mailhotkeys_uninstall(){
	return true;
}

function mailhotkeys_dohook($hookname, $args){
	global $session,$SCRIPT_NAME;
	
	if(get_module_pref("user_usehotkeys")){
	if ($SCRIPT_NAME == "mail.php" || ($SCRIPT_NAME == "runmodule.php" && httpget('module') == "outbox")){
		$op = httpget('op');
		$where = ( $SCRIPT_NAME == "mail.php" ? "inbox" : "outbox" );
		rawoutput("
		<script language='JavaScript'>
			document.onkeydown = function(e) {
				var object;
				
				e = e || window.event;
				object = e.originalTarget || window.event.srcElement;
				
				if (object.nodeName.toLowerCase()=='input' || object.nodeName.toLowerCase()=='textarea') return;
				if (e.ctrlKey || e.altKey || e.shiftKey) return;
				
				switch (e.keyCode) {
					case 73: // Inbox
						window.location='mail.php'; return false;
					break;
					case 87: // Write
						window.location='mail.php?op=address'; return false;
					break; ".(is_module_active("outbox")?"
					case 79: // Outbox
						window.location='runmodule.php?module=outbox'; return false;
					break;":"").(is_module_active("friendlist")?"
					case 70: // Friend List
						window.location='runmodule.php?module=friendlist&op=list'; return false;
					break;":""));
					
		if (($where == "inbox" && ($op == 'read' || $op == 'send')) || ($where == "outbox" && $op == 'readown')){
			$id = ( httpget('id') ? httpget('id') : httppost('returnto'));
			$mail = db_prefix('mail');
			$accounts = db_prefix('accounts');
			if ($where == "inbox"){
				$sql = "SELECT
							(SELECT messageid FROM $mail WHERE msgto = {$session['user']['acctid']} AND messageid < $id ORDER BY messageid DESC LIMIT 1) AS prev,
							(SELECT messageid FROM $mail WHERE msgto = {$session['user']['acctid']} AND messageid > $id ORDER BY messageid ASC  LIMIT 1) AS next,
							(SELECT msgfrom FROM $mail WHERE messageid = $id) AS msgfrom
						FROM $mail";
			} else {
				$table = (get_module_setting('realoutbox','outbox') ? "mailoutbox" : "mail");
				$ptable = db_prefix($table);
				$sql = "SELECT
						(SELECT messageid FROM $ptable WHERE msgfrom='{$session['user']['acctid']}' AND messageid < '$id' ORDER BY messageid DESC LIMIT 1) as prev,
						(SELECT messageid FROM $ptable WHERE msgfrom='{$session['user']['acctid']}' AND messageid > '$id' ORDER BY messageid  LIMIT 1) as next,
						(SELECT msgto FROM $ptable WHERE messageid = $id) AS msgfrom
					FROM $ptable";
			}
			$res = db_query($sql);
			$row = db_fetch_assoc($res);

			$sqlm = "SELECT $mail.*, $accounts.name FROM $mail LEFT JOIN $accounts ON $accounts.acctid = $mail.msgfrom WHERE messageid = $id";
			$resm = db_query($sqlm);
			$rowm = db_fetch_assoc($resm);
			$problem = "Abusive Email Report:\nFrom: {$rowm['name']}\nSubject: {$rowm['subject']}\nSent: {$rowm['sent']}\nID: {$rowm['messageid']}\nBody:\n{$rowm['body']}";

			rawoutput((($row['msgfrom'] > 0 && is_numeric($row['msgfrom']) && $where == "inbox")?"
					case 82: // Reply
						window.location='mail.php?op=write&replyto=$id'; return false;
					break;
					case 65: // Report to Admin 
						window.location='petition.php?problem=".rawurlencode($problem)."&abuse=yes'; return false;
					break; ":""
					)."
					case 46: // Delete ".($where == "inbox" ? "
						window.location='mail.php?op=del&id=$id'; return false;" : "
						window.location='runmodule.php?module=outbox&op=delown&id=$id&rec={$row['msgfrom']}'; return false;"
					)."
					break; ".($where == "inbox" ? "
					case 85: // Mark Unread
						window.location='mail.php?op=unread&id=$id'; return false;
					break;" : "").($row['prev'] ? "
					case 37: // Previous ".($where == "inbox" ? "
						window.location='mail.php?op=read&id={$row['prev']}'; return false;" : "
						window.location='runmodule.php?module=outbox&op=readown&id={$row['prev']}'; return false;"
					)."
					break; ":"").($row['next']?"
					case 39: // Next ".($where == "inbox" ? "
						window.location='mail.php?op=read&id={$row['next']}'; return false;" : "
						window.location='runmodule.php?module=outbox&op=readown&id={$row['next']}'; return false;"
					)."
					break; ":""
					)."
				");
		}
		
		rawoutput("
				}
			}
		</script>");
	}
	}
	return $args;
}
?>