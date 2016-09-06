<?php

function inboxpetitions_getmoduleinfo(){
	$info = array(
		"name" => "Petitions in YoM",
		"override_forced_nav"=>true,
		"author" => "`&Srch",
		"version" => "1.0",
		"download" => "nope",
		"category" => "Mail",
		"prefs" => array(
			"Petition in YoM,title",
			"hidepetitions"=>"Has user hidden Closed Petitions?,bool|0",
		),
	);
	return $info;
}
function inboxpetitions_install(){
	return true;
}
function inboxpetitions_uninstall(){
	return true;
}
function inboxpetitions_dohook($hookname,$args){
    return $args;
}
function inboxpetitions_run(){
	
	global $session;
	$op=httpget('op');
	popup_header("Petition for Help");
	rawoutput("<table width='25%' border='0' cellpadding='0' cellspacing='2'>");
	rawoutput("<tr><td>");
	$t = translate_inline("Back to the Mailbox");
	rawoutput("<a href='mail.php'>$t</a></td><td>");
	rawoutput("</td></tr></table>");
	output_notl("`n");;
	require_once("lib/systemmail.php");
	switch($op){
		case "hidepetitions":
			$hidepetitions = get_module_pref("hidepetitions");
			set_module_pref("hidepetitions", !$hidepetitions);
			header("Location: ".httpget('ret'));
		break;
		case "view":
		$sql = "SELECT * FROM petitions WHERE author=".$session['user']['acctid'];
		$res = db_query($sql);
		$max = db_num_rows($res);
		$min = 0;
		$tit = translate_inline("`b<big>`&`i`cPetition`c`i</big>`b");
		$lastup = translate_inline("`>`b<big>`&`iLast Update`i</big>`b`>");
	
		output("`n");
		output("<table border='0' align='center' width='40%'><tr><td>$tit</td><td>`c`b<big>`&`iComments`i</big>`b`c</td><td>$lastup</td></tr>",true);
		while ($max>0){
			$row = db_fetch_assoc($res);
			$pn = $row['pname'];
			if ($pn == "")
				$pn = "No Subject";
			output("<tr><td>",true);
			output("`c<a href='runmodule.php?module=inboxpetitions&op=viewpet&petid=".$row['petitionid']."'>`@".$pn."</a>`n",true);
			output("`2(Recieved: ".relativedate($row['date']).")`c");
			output("</td><td>",true);
			$coms = db_query("SELECT * FROM commentary WHERE section = 'pet-".$row['petitionid']."'");
			$commentz = db_num_rows($coms);
			output("`c`^".$commentz." `Qcomments!`c");
			
			output("</td><td>",true);
			$csq = "SELECT * FROM accounts WHERE acctid=".$row['closeuserid'];
			$crez = db_query($csq);
			$cro = db_fetch_assoc($crez);
			if ($cro['acctid'])
				$closed = reltime(strtotime($row['closedate']));
			else
				$closed = "Never";
			output("`^`>".$closed."`>");
			output("</td></tr>",true);
			$max--;
		}
		output("</table>`n`n`c`7`iNOTE!`i Closed petitions will be cleared up at the end of the month by an admin, or after seven days.`c",true);
		break;
		
		case "viewpet":
		require_once("lib/commentary.php");
		$viewid = httpget('petid');
		$sql = "SELECT * FROM petitions WHERE petitionid=".$viewid;
		$res = db_query($sql);
		$row = db_fetch_assoc($res);
		$sq = "SELECT * FROM accounts WHERE acctid=".$row['author'];
		$rez = db_query($sq);
		$ro = db_fetch_assoc($rez);
		$csq = "SELECT * FROM accounts WHERE acctid=".$row['closeuserid'];
		$crez = db_query($csq);
		$cro = db_fetch_assoc($crez);
		
		if (httpget('comment')) {
			/* Update the bug if someone adds comments as well */
			db_query("UPDATE " . db_prefix("petitions") . " SET closeuserid = {$session['user']['acctid']}, closedate = '".date("Y-m-d H:i:s")."' WHERE petitionid = $viewid");
		}

		
		$statuses=array(
			0=>"`&`bUnhandled`b",
			1=>"`\$Errors",
			2=>"`QVotes",
			3=>"`^Contest",
			4=>"`@Donation",
			5=>"`#Progressive",
			6=>"`!Miscellaneous",
			7=>"`)`iClosed`i",
		);
		//CHECKING AUTHOR ALLOWANCE HERE!!!
		//This is because we do an if to check if the player is allowed to view this or not.
		$char = $row['author'];
		$allowed = explode(" ",$char);
		$numallowed = count($allowed);
		$allowedstring = '';
		for ($in=0;$in < $numallowed;$in++){
			//output($in);
			$players = db_fetch_assoc(db_query("SELECT name FROM accounts WHERE acctid = ".$allowed[$in]));
			if ($in != floor($numallowed-1)) $punctuality = ', ';
				else $punctuality = '.';
			$allowedstring .= $players[name].$punctuality;
		}
		if (db_num_rows($res) == 0){
			output("We have a problem. That petition does not exist!");
		}else{
			//OOOH@ I LOVE ARRAYS. Check to see if the user can view this.
			
			if (!in_array($session['user']['acctid'],$allowed)){
				output("You are quite the sneaky snake.. But trying to sneak like that can get you banned. :)");
			}else{
				//Commentary. Vital for this module.
				addcommentary();
				//Start with petition name.. That is not hard.
				$issue = $row['pname'];
				
				
				//Convert body to compatibility with old petitions.. For now.
				$body = $row['body'];
				$string = @unserialize($body);
				$serialnote = NULL;
				if ($string === false && $string !== "b:0;") {
				    $serialnote = "`4This message looks different from other petitions because it is from `i`4before`i`4 the remake of the petition system!";
				   	$description = $body;
				}else{
					$body = unserialize($body);
					$description = stripslashes($body['description']);
				}
				
// 				//Tricky.. Users..
// 				$char = $row['author'];
// 				$allowed = explode(" ",$char);
// 				$numallowed = count($allowed);
// 				$allowedstring = '';
// 				for ($in=0;$in<$numallowed;$in++){
// 					//output($in);
// 					$players = db_fetch_assoc(db_query("SELECT name FROM accounts WHERE acctid = ".$allowed[$in]));
// 					if ($in != floor($numallowed-1)) $punctuality = ', ';
// 						else $punctuality = '.';
// 					$allowedstring .= $players[name].$punctuality;
// 				}
// 				//Cute.
				
				//Show our petition. It's pretty.
				output("`@From: `^%s`n", $allowedstring,TRUE);
				output("`@Issue: `\$`b".$issue."`b`n",TRUE);
				output("`@Status: %s`n", $statuses[$row['status']]);	
				if($row['closedate'] != '0000-00-00 00:00:00') output("`@Updated: `^%s`@ on `^%s `n", $row['closer'], date($session['user']['prefs']['timeformat'],strtotime($row['closedate'])));
					else output("`@Updated: `^`iNot updated yet!`i`n");
				output("`@Received: `0%s`n", date($session['user']['prefs']['timeformat'],strtotime($row['date'])));
				rawoutput("<hr color='#C11B17' align='left' width='200px'/>");
				output("`n`0".nl2br($description)."`n`n",TRUE);
				rawoutput("<hr color='#C11B17' align='left' width='200px'/>");
				
				//Make users less scared.
				if (isset($serialnote) != FALSE) output("`n`n`b`\$Notice`b`4: ".$serialnote."`^");
				
				//Let players talk. Solid, and dope. End of view.
				// commentdisplay("", "pet-$viewid","",200);
				viewcommentary("pet-$viewid","",200,"says",false,false);
			}
		}
		break;
	}
	popup_footer();
}
?>
