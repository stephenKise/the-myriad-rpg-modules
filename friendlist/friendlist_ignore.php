<?php
function friendlist_ignore() {
	global $session;
	if (httpget('bio') != "yes"){
	output("<table width='100%' border='0' cellpadding='0' cellspacing='10px'>",TRUE);
	rawoutput("<tr><td valign=\"top\" width='150px' nowrap>");
	output("&bull;<a href='mail.php'>`tInbox</a>`n",TRUE);
	output("&bull;<a href='runmodule.php?module=outbox'>`tOutbox</a>`n",TRUE);
	output("&bull;<a href='mail.php?&op=address'>`tCompose</a>`n",TRUE);
	output("&bull;<a href='petition.php'>`\$Petition for Help</a>`n",TRUE);
	modulehook("mailfunctions");
	output_notl("</td><td>",TRUE);
	}
	$iveignored = rexplode(get_module_pref('iveignored'));
	$friends = rexplode(get_module_pref('friends'));
	$request = rexplode(get_module_pref('request'));
	$ac = httpget('ac');
	$sql = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid=$ac AND locked=0";
	$result = db_query($sql);
	if (db_num_rows($result)>0&&in_array($ac,$friends)) {
		$row=db_fetch_assoc($result);
		require_once("lib/systemmail.php");
		$t = "`\$Friend List Ignore";
		$mailmessage=array("%s`0`@ has chosen to ignore mail communications with you. If you continue to attempt to contact them despite their request, you may be warned and muted by staff.",$session['user']['name'],($session['user']['sex']?translate_inline("her"):translate_inline("his")));
		systemmail($ac,$t,$mailmessage);
		
		$friends = array_diff($friends, array($ac));
		invalidatedatacache("friendliststat-".$session['user']['acctid']);
		invalidatedatacache("friendliststat-".$ac);
	}
	$friends = rimplode( $friends);
	set_module_pref('friends',$friends);
	$ignored = rexplode(get_module_pref('ignored','friendlist',$ac));
	$ignored[]=$session['user']['acctid'];
	$ignored = rimplode( $ignored);
	set_module_pref('ignored',$ignored,'friendlist',$ac);
	$act = $session['user']['acctid'];
	$friends = rexplode(get_module_pref('friends','friendlist',$ac));
	$friends = array_diff($friends, array($act));
	$friends = rimplode( $friends);
	set_module_pref('friends',$friends,'friendlist',$ac);
	if (in_array($ac,$request)) {
		$request = array_diff($request, array($ac));
		$request = rimplode( $request);
		set_module_pref('request',$request);
	}
	$iveignored[]=$ac;
	$iveignored = rimplode( $iveignored);
	set_module_pref('iveignored',$iveignored);
	output("You can no longer exchange mail with this user.");
	if (httpget('bio') != "yes") rawoutput("</td></tr></table>",TRUE);
}
?>
