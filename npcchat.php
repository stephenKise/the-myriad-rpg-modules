<?php
function npcchat_getmoduleinfo()
{
	$info = array(
		"name" => "NPC Chat",
		"version" => "0.31+Aeolus",
		"author" => "Danny Moules (Rushyo), revamped by `i`)Ae`7ol`&us`i`0",
		"category" => "Commentary",
		"description" => "Allows players to chat on behalf of their NPC compatriates.",
		"download" => "http://www.rushyo.com/lotgd/",
		"settings" => array(
			"NPC Chat - Settings,title",
			"enablenpc" => "Allow use of generic NPC emotes (/npc)?,bool|1",
			"npctoken" => "A piece of text to prefix to a generic NPC's name when speaking/emoting",
			"`n`b`i`&The following settings apply only when the Dycedarg's Academy module is active`&`i`b`n`n,note",
			"enablesq" => "Allow squires to emote (/sq)?,bool|1",
			"enablesqsay" => "Allow squires to speak (/sqsay)?,bool|1",
			"deadspeak" => "Allow dead Squires to speak/emote?,bool|0",
			"squiretoken" => "A piece of text to prefix to a Squire's name when speaking/emoting",
			"`n`b`i`&The following settings apply only when the Pet Shop module is active`&`i`b`n`n,note",
			"enablepet" => "Allow pets to emote (/pet)?,bool|1",
			"pettoken" => "A piece of text to prefix to a pet's name when emoting",
			"`n`b`i`&The following setting is to replace the regular mail button with a non-clickable scroll image`&`i`b`n`n,note",
			"useimg"=>"Does commentary use a mail link at the beginning of each commentary line?,bool|1",
			"`^Include the file extension - this setting is case sensitive! Only use if above setting is YES,note",
			"scrollimg"=>"File name of replacement scroll image?,text|noscroll.gif",
		),
		"prefs" => array(
			"NPC,title",
			"recenturi"=>"Most recent URI,text|",
			"user_npcsaycolour" => "Choose a colour code to use for your generic NPC commentary (if any)",
			"user_npcemcolour" => "Choose a colour code to use for your generic NPC emoting (if any)",
			"user_squiresaycolour" => "Choose a colour code to use for your Squire commentary (if any)",
			"user_squireemcolour" => "Choose a colour code to use for your Squire emoting (if any)",
			"user_petemcolour" => "Choose a colour code to use for your Pet emoting (if any)"
		),
	);
	return $info;
}

function npcchat_install()
{
	module_addhook("commentary");
	module_addhook("viewcommentary");
	module_addhook("everyheader-loggedin");
	return true;
}

function npcchat_uninstall()
{	
	$sql = "DELETE FROM ".db_prefix("commentary")." WHERE 
			comment LIKE '%;;SQ;;%' OR 
			comment LIKE '%;;SQSAY;;%' OR 
			comment LIKE '%;;PET;;%' OR 
			comment LIKE '%;;NPC;;%'";
	db_query($sql);
	debug($sql);
	return true;
}

function npcchat_dohook($hookname, $args)
{
	global $session;
	switch ($hookname){
		case "everyheader-loggedin":
			set_module_pref('recenturi',$_SERVER['REQUEST_URI']);
			// This hook is mainly for those who use the AJAX Commentary System,
			// and $_SERVER["REQUEST_URI"] returns "ajaxcommentary.php" instead of
			// the current URL (used for linking the players name to their bios).
		break;
		case "commentary":
			$commentline = $args["commentline"];

			if(is_module_active("academy"))
			{
				if(strpos($commentline, "/sq") !== false)
				{
					// /sqsay
					if(get_module_setting("enablesqsay"))
					{
						if(preg_match("/\\/sqsay\\s?(.+)/", $commentline, $matches))
						{
							$strSquireName = get_module_pref("name", "academy");
							$strSquireName = str_replace(";;", "", $strSquireName);
							
							$intSquireDead = get_module_pref("dead", "academy");
							if($intSquireDead && get_module_setting("deadspeak") == 0)
							{
								$strSquireName = "";
							}
							if($strSquireName == "")
							{
								$strSquireName = "NULL";
							}
							$strContent = $matches[1];
							$strContent = str_replace(";;", "", $strContent);
							
							$strColour = get_module_pref("user_squiresaycolour");
							if(strlen($strColour) != 2)
							{
								$strColour = "";
							}
							$intAuthor = $session["user"]["acctid"];
							
							$commentline = ";;SQSAY;;$strSquireName;;$strContent;;$strColour;;$intAuthor;;";
							$args["commentline"] = $commentline;
							$args["commenttalk"] = "";
						}
					}
					
					// /sq
					if(get_module_setting("enablesq"))
					{
						if(preg_match("/\\/sq\\s?(.+)/", $commentline, $matches))
						{
							$strSquireName = get_module_pref("name", "academy");
							$strSquireName = str_replace(";;", "", $strSquireName);
							
							$intSquireDead = get_module_pref("dead", "academy");
							if($intSquireDead && get_module_setting("deadspeak") == 0)
							{
								$strSquireName = "";
							}
							if($strSquireName == "")
							{
								$strSquireName = "NULL";
							}
						
							$strContent = $matches[1];
							$strContent = str_replace(";;", "", $strContent);
							
							$strColour = get_module_pref("user_squireemcolour");
							if(strlen($strColour) != 2)
							{
								$strColour = "";
							}
							$intAuthor = $session["user"]["acctid"];
							
							$commentline = ";;SQ;;$strSquireName;;$strContent;;$strColour;;$intAuthor;;";
							
							$args["commentline"] = $commentline;
						}
					}
				}
			}
			
			if(is_module_active("petshop"))
			{
				if(strpos($commentline, "/pet") !== false && get_module_setting("enablepet"))
				{
					// /pet
					if(preg_match("/\\/pet\\s?(.+)/", $commentline, $matches))
					{
						$strPetName = get_module_pref("customname", "petshop");
						$strPetName = str_replace(";;", "", $strPetName);
						if($strPetName == "" || get_module_pref("haspet", "petshop") == 0)
						{
							$strPetName = "NULL";
						}
						$strContent = $matches[1];
						$strContent = str_replace(";;", "", $strContent);
						
						$strColour = get_module_pref("user_petemcolour");
						if(strlen($strColour) != 2)
						{
							$strColour = "";
						}
						$intAuthor = $session["user"]["acctid"];
						
						$commentline = ";;PET;;$strPetName;;$strContent;;$strColour;;$intAuthor;;";
						$args["commentline"] = $commentline;
						$args["commenttalk"] = "";
					}
				}
			}
			
			if(strpos($commentline, "/npc") !== false)
			{
				if(get_module_setting("enablenpc"))
				{
					// /npc
					if(preg_match("/\\/npc\\s(.+)/", $commentline, $matches))
					{
						$strContent = $matches[1];
						$strContent = str_replace(";;", "", $strContent);
						if (substr_count($strContent,"`b")%2 == 1)
							$strContent = $strContent."`b";
						if (substr_count($strContent,"`i")%2 == 1)
							$strContent = $strContent."`i";
						if (substr_count($strContent,"`h")%2 == 1)
							$strContent = $strContent."`h";
						if (substr_count($strContent,"`H")%2 == 1)
							$strContent = $strContent."`H";
						
						$strColour = get_module_pref("user_npcemcolour");
						if(strlen($strColour) != 2)
						{
							$strColour = "";
						}
						$intAuthor = $session["user"]["acctid"];
						
						$commentline = ";;NPC;;$strContent;;$strColour;;$intAuthor;;";
						$args["commentline"] = $commentline;
						$args["commenttalk"] = "";
					}
				}
			}
		break;
		case "viewcommentary":
			$commentline = $args["commentline"];
			$scrollimg = get_module_setting("scrollimg");
			
			// /sqsay
			if(strpos($commentline, ";SQSAY;") !== false)
			{
				if(preg_match("/;;SQSAY;;(.+);;(.*);;(.*);;([0-9]*);;/", $commentline, $matches))
				{
					if(is_module_active("academy"))
					{		
						$strSquireName = $matches[1];
						$strContent = $matches[2];
						$strColour = $matches[3];
						$intAuthor = $matches[4];
						if($strSquireName != "NULL")
						{
							$strOldLine = $commentline;
							$commentline = "";
							if($session["user"]["superuser"] & SU_EDIT_COMMENTS)
							{
								if(preg_match("/(`.\\[.+Del.+\\]`.)/", $strOldLine, $matches))
								{
									$commentline .= $matches[1]." ";
								}
							}
							$strToken = get_module_setting("squiretoken");
							$strSquireName = str_replace("`c", "", $strSquireName);
							if($session["user"]["superuser"] & SU_EDIT_COMMENTS)
							{
								$strSQL = "SELECT name,login FROM ".db_prefix("accounts")." WHERE acctid = '$intAuthor';";
								$queAuthor = db_query($strSQL);
								$arrAuthor = db_fetch_assoc($queAuthor);
								$strAuthorName = $arrAuthor["name"];
								$strAuthorLgin = $arrAuthor["login"];
								$strAuthorComm = "`7(<a href='bio.php?char=$strAuthorLgin&ret=".urlencode(get_module_pref('recenturi'))."'>$strAuthorName</a>`7) ";
							}
							$commentline .= "$strToken $strAuthorComm `3$strSquireName`3 says, \"$strColour$strContent`3\"`0`n";
							
							if (get_module_setting("useimg")) $first = strpos($strOldLine, stripslashes("<\img"));
							else $first = 0;
							$last = strrpos($strOldLine, stripslashes("<\a"));
							$exbuttons = substr($strOldLine, $first, $last - $first);
							$exbuttons = str_replace("newscroll.GIF",$scrollimg,$exbuttons);
							$commentline = $exbuttons . $commentline;
						}
						else
						{
							$commentline = "";
						}
					}
					else
					{
						debug("npcchat: Academy module not detected");
						$commentline = "";
					}
					if ($session['user']['prefs']['spacedchat']) $commentline = $commentline . "</del>`n";
						else $commentline = $commentline."</del>";
					$args["commentline"] = appoencode(str_replace("`3\"","",$commentline),true);
				}
			}
			
			// /sq
			if(strpos($commentline, ";SQ;") !== false)
			{
				if(preg_match("/;;SQ;;(.+);;(.*);;(.*);;([0-9]*);;/", $commentline, $matches))
				{
					if(is_module_active("academy"))
					{		
						$strSquireName = $matches[1];
						$strContent = $matches[2];
						$strColour = $matches[3];
						$intAuthor = $matches[4];
						if($strSquireName != "NULL")
						{
							$strOldLine = $commentline;
							$commentline = "";
							$strToken = get_module_setting("squiretoken");
							$strSquireName = str_replace("`c", "", $strSquireName);
							if($session["user"]["superuser"] & SU_EDIT_COMMENTS)
							{
								$strSQL = "SELECT name,login FROM ".db_prefix("accounts")." WHERE acctid = '$intAuthor';";
								$queAuthor = db_query($strSQL);
								$arrAuthor = db_fetch_assoc($queAuthor);
								$strAuthorName = $arrAuthor["name"];
								$strAuthorLgin = $arrAuthor["login"];
								$strAuthorComm = "`7(<a href='bio.php?char=$strAuthorLgin&ret=".urlencode(get_module_pref('recenturi'))."'>$strAuthorName</a>`7) ";
							}
							
							$commentline .= "$strToken $strAuthorComm`3$strSquireName`3 $strColour$strContent`0`n";
							
							if (get_module_setting("useimg")) $first = strpos($strOldLine, stripslashes("<\img"));
							else $first = 0;
							$last = strrpos($strOldLine, stripslashes("<\a"));
							$exbuttons = substr($strOldLine, $first, $last - $first);
							$exbuttons = str_replace("newscroll.GIF",$scrollimg,$exbuttons);
							$commentline = $exbuttons . $commentline;
						}
						else
						{
							$commentline = "";
						}
					}
					else
					{
						debug("npcchat: Academy module not detected");
						$commentline = "";
					}
					if ($session['user']['prefs']['spacedchat']) $commentline = $commentline . "</del>`n";
						else $commentline = $commentline."</del>";
					$args["commentline"] = appoencode(str_replace("`3\"","",$commentline),true);
				}
			}
			
			// /pet
			if(strpos($commentline, ";PET;") !== false)
			{
				if(preg_match("/;;PET;;(.+);;(.*);;(.*);;([0-9]*);;/", $commentline, $matches))
				{
					if(is_module_active("petshop"))
					{		
						$strPetName = $matches[1];
						$strContent = $matches[2];
						$strColour = $matches[3];
						$intAuthor = $matches[4];
						if($strPetName != "NULL")
						{
							$strOldLine = $commentline;
							$commentline = "";
							$strToken = get_module_setting("pettoken");
							$strPetName = str_replace("`c", "", $strPetName);
							if($session["user"]["superuser"] & SU_EDIT_COMMENTS)
							{
								$strSQL = "SELECT name,login FROM ".db_prefix("accounts")." WHERE acctid = '$intAuthor';";
								$queAuthor = db_query($strSQL);
								$arrAuthor = db_fetch_assoc($queAuthor);
								$strAuthorName = $arrAuthor["name"];
								$strAuthorLgin = $arrAuthor["login"];
								$strAuthorComm = "`7(<a href='bio.php?char=$strAuthorLgin&ret=".urlencode(get_module_pref('recenturi'))."'>$strAuthorName</a>`7) ";
							}

							$commentline .= "$strToken $strAuthorComm `3$strPetName`3 $strColour$strContent`0`n";
							
							if (get_module_setting("useimg")) $first = strpos($strOldLine, stripslashes("<\img"));
							else $first = 0;
							$last = strrpos($strOldLine, stripslashes("<\a"));
							$exbuttons = substr($strOldLine, $first, $last - $first);
							$exbuttons = str_replace("newscroll.GIF",$scrollimg,$exbuttons);
							$commentline = $exbuttons . $commentline;
						}
						else
						{
							$commentline = "";
						}
					}
					else
					{
						debug("npcchat: Pet Shop module not detected");
						$commentline = "";
					}
					if ($session['user']['prefs']['spacedchat']) $commentline = $commentline . "</del>`n";
						else $commentline = $commentline."</del>";
					$args["commentline"] = appoencode(str_replace("`3\"","",$commentline),true);
				}
			}
			
			// /npc
			if(strpos($commentline, ";NPC;") !== false)
			{
				if(preg_match("/;;NPC;;(.*);;(.*);;([0-9]*);;/", $commentline, $matches))
				{
					$strContent = $matches[1];
					$strColour = $matches[2];
					$intAuthor = $matches[3];
					
					$strOldLine = $commentline;
					$commentline = "";
					$strToken = get_module_setting("npctoken");
					$strNPCName = str_replace("`c", "", $strNPCName);
					if (strstr($strContent,"/s")){
						$npcColour = get_module_pref("user_npcsaycolour");
						$strContent = str_replace("/s ", " `3says, \"$npcColour", $strContent);
						$strContent = $strContent . "`3\"";
						$npcsay = TRUE;
					} else {
						$npcsay = FALSE;
					}
					
					if($session["user"]["superuser"] & SU_EDIT_COMMENTS)
					{
						$strSQL = "SELECT name,login FROM ".db_prefix("accounts")." WHERE acctid = '$intAuthor';";
						$queAuthor = db_query($strSQL);
						$arrAuthor = db_fetch_assoc($queAuthor);
						$strAuthorName = $arrAuthor["name"];
						$strAuthorLgin = $arrAuthor["login"];
						$strAuthorComm = "`7(<a href='bio.php?char=$strAuthorLgin&ret=".urlencode(get_module_pref('recenturi'))."'>$strAuthorName</a>`7) ";
					}
					$commentline .= "$strToken $strAuthorComm `3$strColour$strContent`0`n";
					
					if (get_module_setting("useimg")) $first = strpos($strOldLine, stripslashes("<\img"));
					else $first = 0;
					$last = strrpos($strOldLine, stripslashes("<\a"));
					$exbuttons = substr($strOldLine, $first, $last - $first);
					$exbuttons = str_replace("newscroll.GIF",$scrollimg,$exbuttons);
					$commentline = $exbuttons . $commentline;
					if ($session['user']['prefs']['spacedchat']) $commentline = $commentline . "</del>`n";
						else $commentline = $commentline."</del>";
					$args["commentline"] = appoencode(str_replace("`3\"","",$commentline),true);
				}
			}
			
			addnav("","bio.php?char=$strAuthorLgin&ret=".urlencode(get_module_pref('recenturi')));
		break;
	}
	return $args;
}
?>