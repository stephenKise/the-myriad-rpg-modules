<?php
function friendlist_request() {
	global $session;
	if (httpget('bio') != "yes"){
		output("<table width='100%' border='0' cellpadding='0' cellspacing='10px'>",TRUE);
		rawoutput("<tr><td valign=\"top\" width='150px' nowrap>");
		output("&bull;<a href='mail.php'>`tInbox</a>`n",TRUE);
		output("&bull;<a href='runmodule.php?module=outbox'>`tOutbox</a>`n",TRUE);
		output("&bull;<a href='mail.php?&op=address'>`tCompose</a>`n",TRUE);
		output("&bull;<a href='petition.php'>`\$Petition for Help</a>`n",TRUE);
		modulehook("mailfunctions");
		output_notl("</td><td>",true);
	}
	$ac = httpget('ac');
	$sql = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid=$ac AND locked=0";
	$result = db_query($sql);
	if (db_num_rows($result)>0) {
		$row=db_fetch_assoc($result);
		$info = translate_inline("You have successfully sent your request to %s`Q.");
		$info = str_replace('%s',$row['name'],$info);
	} else {
		$info = translate_inline("That user no longer exists...");
	}
	$request = get_module_pref('request','friendlist',$ac);
	$request = rexplode($request);
	$request[]=$session['user']['acctid'];
	$request = rimplode( $request);
	set_module_pref('request',$request,'friendlist',$ac);
	output_notl($info);
	if (httpget('bio') != "yes") rawoutput("</td></tr></table>",TRUE);
}
?>
