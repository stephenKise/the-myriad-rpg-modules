<?php
function friendlist_list() {
	global $session;
	rawoutput("<script language='JavaScript'>
		function nicknameDisplay(ac) {
			var name = document.getElementById('name'+ac).style.display;
			var input = document.getElementById('input'+ac).style.display;
			var changeplus = (input == 'inline') ? '[+]' : '[-]';
			var changename = (name == 'inline') ? 'none' : 'inline';
			var changeinput = (input == 'inline') ? 'none' : 'inline';
			document.getElementById('plus'+ac).innerHTML = changeplus;
			document.getElementById('name'+ac).style.display = changename;
			document.getElementById('input'+ac).style.display = changeinput;
		}
		</script>");
	if (httpget('bio') != "yes"){
		output("<table width='100%' border='0' cellpadding='0' cellspacing='10px'>",TRUE);
		rawoutput("<tr><td valign=\"top\" width='150px' nowrap>");
		output("&bull;<a href='mail.php'>`tInbox</a>`n",TRUE);
		output("&bull;<a href='runmodule.php?module=outbox'>`tOutbox</a>`n",TRUE);
		output("&bull;<a href='mail.php?&op=address'>`tCompose</a>`n",TRUE);
		output("&bull;<a href='petition.php'>`\$Petition for Help</a>`n",TRUE);
		modulehook("mailfunctions");
		output_notl("</td><td>",true);
	$friends = rexplode(get_module_pref('friends'));
	$request = rexplode(get_module_pref('request'));
	$ignored = rexplode(get_module_pref('ignored'));
	$iveignored = rexplode(get_module_pref('iveignored'));
	$nnames = unserialize(get_module_pref('nnames'));
	if (!$nnames) $nnames = array();
	rawoutput("<table style='width:60%;text-align:center;' cellpadding='3' cellspacing='0' border='0'>");
	rawoutput("<tr><td colspan='3'>");
	output("`<`b`@Friends:`b `<");
	output("`><a href='runmodule.php?module=friendlist&op=search'>`2[`0Add / Ignore users`2]`n</a>`>",true);
	rawoutput("</td></tr>");
	rawoutput("<tr class='trhead'><td>".translate_inline("Name")."</td><td>".translate_inline("Last On")."</td><td>".translate_inline("Operations")."</td></tr>");
	$last = date("Y-m-d H:i:s", strtotime("-".getsetting("LOGINTIMEOUT", 900)." sec"));
	$x=0;
	if (implode(",",$friends)!='') {
		$sql = "SELECT name,acctid,login,laston FROM ".db_prefix("accounts")." WHERE acctid IN (".implode(',',$friends).") AND locked=0 ORDER BY login";
		$result = db_query($sql);
		while ($row=db_fetch_assoc($result)) {
				$ac=$row['acctid'];
				$unname = $nnames[$ac];
				$x++;
				rawoutput("<tr class='".($x%2?"trlight":"trdark")."'>");
				rawoutput("<td><a href='mail.php?op=write&to=".rawurlencode($row['login'])."'>".appoencode("`&".$row['name'],false)."</a>");
				output_notl("`n`7`iNickname: 
						<div id='name$ac' style='display:inline'>
							".($unname?$unname:'`7None`0')."
						</div> 
						<div id='input$ac' style='display:none'>
							<form action='runmodule.php?module=friendlist&op=nname&id=$ac' method='post' style='display:inline'>
							<input name='newnn$ac' width='10px' value='$unname'><input type='submit' value='&#10004;'>
							</form>
						</div> 
						`i`&<span onClick='javascript:nicknameDisplay($ac);' style='cursor:pointer;color:grey;'>
							<div id='plus$ac' style='display:inline'>[+]</div>
						</span>`0",TRUE);
				rawoutput("</td>");
				addnav("","mail.php?op=write&to=".rawurlencode($row['login']));
				$ops = "[<a href='runmodule.php?module=friendlist&op=deny&ac=$ac' class='colDkGreen'>".translate_inline("Remove")."</a>] - [<a href='runmodule.php?module=friendlist&op=ignore&ac=$ac' class='colDkGreen'>".translate_inline("Ignore")."</a>]";
				addnav("","runmodule.php?module=friendlist&op=deny&ac=$ac");
				addnav("","runmodule.php?module=friendlist&op=ignore&ac=$ac");
				rawoutput("<td>");
				$laston = relativedate($row['laston']);
				output_notl("%s", $laston);
				rawoutput("<td>$ops</td></tr>");
		}
	}
	if ($x==0) {
		rawoutput("<tr class='trlight'><td colspan='6'>");
		output("`^You have no friends");
		rawoutput("</td></tr>");
	}
	rawoutput("</table>");
	$friends = rimplode( $friends);
	set_module_pref('friends',$friends);
	output("`n`b`@Friend Requests:`b`n");
	rawoutput("<table style='width:60%;text-align:center;' cellpadding='3' cellspacing='0' border='0'>");
	rawoutput("<tr class='trhead'><td>".translate_inline("Name")."</td><td>".translate_inline("Last On")."</td><td>".translate_inline("Operations")."</td></tr>");
	$x=0;
	$request=array_unique($request);
	if (implode(",",$request)!='') {
		$sql = "SELECT name,acctid,login,laston,alive,loggedin,location FROM ".db_prefix("accounts")." WHERE acctid IN (".implode(',',$request).") AND locked=0 ORDER BY login";
		$result = db_query($sql);
		while ($row=db_fetch_assoc($result)) {
			$ac=$row['acctid'];
			$x++;
			rawoutput("<tr class='".($x%2?"trlight":"trdark")."'>");
			rawoutput("<td>".appoencode($row['name'],false)."</td>");
			$ops = "[<a href='runmodule.php?module=friendlist&op=accept&ac=$ac' class='colDkGreen'>".translate_inline("Accept")."</a>] - [<a href='runmodule.php?module=friendlist&op=deny&ac=$ac' class='colDkGreen'>".translate_inline("Deny")."</a>] - [<a href='runmodule.php?module=friendlist&op=ignore&ac=$ac' class='colDkGreen'>".translate_inline("Ignore")."</a>]";
			addnav("","runmodule.php?module=friendlist&op=accept&ac=$ac");
			addnav("","runmodule.php?module=friendlist&op=deny&ac=$ac");
			addnav("","runmodule.php?module=friendlist&op=ignore&ac=$ac");
			rawoutput("<td>");
			$laston = relativedate($row['laston']);
			output_notl("%s", $laston);
			rawoutput("<td>$ops</td></tr>");
		}
	}
	if ($x==0) {
		rawoutput("<tr class='trlight'><td colspan='3'>");
		output("`^You have no requests");
		rawoutput("</td></tr>");
	}
	rawoutput("</table>");
	/*
	$request = rimplode( $request);
	set_module_pref('request',$request);
	output("`n`b`@Ignored You:`b`n");
	rawoutput("<table style='width:60%;text-align:center;' cellpadding='3' cellspacing='0' border='0'>");
	rawoutput("<tr class='trhead'><td>".translate_inline("Name")."</td><td>".translate_inline("Last On")."</td><td>".translate_inline("Operations")."</td></tr>");
	$x=0;
	$ignored=array_unique($ignored);
	if (implode(",",$ignored)!='') {
		$sql = "SELECT name,acctid,login,laston,alive,loggedin,location FROM ".db_prefix("accounts")." WHERE acctid IN (".implode(',',$ignored).") AND locked=0 ORDER BY login";
		$result = db_query($sql);
		while ($row=db_fetch_assoc($result)) {
			$x++;
			$ac=$row['acctid'];
			rawoutput("<tr class='".($x%2?"trlight":"trdark")."'>");
			rawoutput("<td>".appoencode($row['name'],false)."</td>");
			if (!in_array($ac,$iveignored)) {
				$ops = "[<a href='runmodule.php?module=friendlist&op=ignore&ac=$ac' class='colDkGreen'>".translate_inline("Ignore")."</a>]";
				addnav("","runmodule.php?module=friendlist&op=ignore&ac=$ac");
			} else {
				$ops = appoencode("`i[".translate_inline("Nothing")."]`i",false);
			}
			rawoutput("<td>");
			$laston = relativedate($row['laston']);
			output_notl("%s", $laston);
			rawoutput("<td>$ops</td></tr>");
		}
	}
	if ($x==0) {
		rawoutput("<tr class='trlight'><td colspan='3'>");
		output("`^No one has ignored you");
		rawoutput("</td></tr>");
	}
	rawoutput("</table>");
	*/
	$ignored = rimplode( $ignored);
	set_module_pref('ignored',$ignored);
	output("`n`b`@You've Ignored:`b`n");
	rawoutput("<table style='width:60%;text-align:center;' cellpadding='3' cellspacing='0' border='0'>");
	rawoutput("<tr class='trhead'><td>".translate_inline("Name")."</td><td>".translate_inline("Last On")."</td><td>".translate_inline("Operations")."</td></tr>");
	$x=0;
	$iveignored=array_unique($iveignored);
	if (implode(",",$iveignored)!='') {
	$sql = "SELECT name,acctid,login,laston,alive,loggedin,location FROM ".db_prefix("accounts")." WHERE acctid IN (".implode(',',$iveignored).") AND locked=0 ORDER BY login";
	$result = db_query($sql);
		while ($row=db_fetch_assoc($result)) {
			$x++;
			$ac=$row['acctid'];
			rawoutput("<tr class='".($x%2?"trlight":"trdark")."'>");
			rawoutput("<td>".appoencode($row['name'],false)."</td>");
			$ops = "[<a href='runmodule.php?module=friendlist&op=unignore&ac=$ac' class='colLtRed'>".translate_inline("Unignore")."</a>]";
			addnav("","runmodule.php?module=friendlist&op=unignore&ac=$ac");
			rawoutput("<td>");
			$laston = relativedate($row['laston']);
			output_notl("%s", $laston);
			rawoutput("<td>$ops</td></tr>");
		}
	}
	if ($x==0) {
		rawoutput("<tr class='trlight'><td colspan='3'>");
		output("`^You've haven't ignored anyone");
		rawoutput("</td></tr>");
	}
	rawoutput("</table>");
	$iveignored = rimplode( $iveignored);
	set_module_pref('iveignored',$iveignored);
	output_notl("`n`n");
rawoutput("</td></tr></table>",TRUE);
}
}
?>
