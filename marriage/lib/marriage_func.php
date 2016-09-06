<?php
function marriage_innflirt() {
	global $session;
	$iname = getsetting("innname", LOCATION_INN);
	page_header($iname);
	rawoutput("<span style='color: #9900FF'>");
	output_notl("`c`b");
	output($iname);
	output_notl("`b`c");
	if ($session['user']['sex']==SEX_MALE) {
		output("As you start to approach Violet, you remember your marriage.`nViolet turns around to give you an evil stare.`nViolet slaps you, then walks off, as she says, \"%s`^Who're you married to then.%s\"","</span>","</span><span style='color: #9900FF'>",true);
	} else {
		output("As you start to approach Seth, you remember your marriage.`nSeth walks over to give you the evil eye.`nSeth whispers an evil song to you, then walks off, as he says, \"%s`^Who're you married to then.%s\"","</span>","</span><span style='color: #9900FF'>",true);
	}
	marriage_flirtdec();
	addnav("Return");
	addnav("I?Return to the Inn","inn.php");
	villagenav();
	rawoutput("</span>");
	page_footer();
}

function marriage_loveshack() {
	global $session;
	set_module_pref('inShack',1);
	page_header("The Loveshack");
	$link = "runmodule.php?module=marriage&op=loveshack";
	$ty = httpget('ty');
	$ac = httpget('ac');
	$n = get_module_setting("bname");
	$g = (get_module_setting('gbtend')?translate_inline("she"):translate_inline("he"));
	$s = (get_module_setting('gbtend')?translate_inline("herself"):translate_inline("himself"));
	$ti = (get_module_setting('gbtend')?translate_inline("Lady"):translate_inline("Lord"));
	addnav("Navigation");
	villagenav();
	addnav("Actions");
	addnav(array("`^Talk to %s `@`i(Your Flirt Status)`i",$n),$link."&ty=talk");
	addnav("The Bar",$link."&ty=bar");
	addnav("Flirt Actions");
	addnav("Buy someone a Drink",$link."&ty=flirt&w=drink");
	addnav("Buy someone some Roses",$link."&ty=flirt&w=roses");
	addnav("Slap someone",$link."&ty=flirt&w=slap");
	switch ($ty) {
		default:
			output("`n`@As you stroll towards an imposing building, you notice a red heart-shaped door in the side...");
			output("`nWalking towards the garish portal, a strange feeling comes over you, and knowledge pours into your head.");
			output("`n`^Maybe a chance possibility, collapsed.. what would have happened?");
			output("`nWith this knowledge, a catchy little song sounds in your head... \"`&Bang, bang, bang on the door baby..`^\" ... \"`&The Love shack, is a little old place where.. we can get together..`^\"");
			output("`n`@Shivering, you snap out of a semi-trance.. what on earth was that?");
			output("`nKnocking on the ornamented gateway, you wait for the door to open, and enter the Loveshack.");
			output("`n`nSomeone strolls up to you, and begins to speak...");
			output("`n`3\"`&Hello, I am %s`&, and I am a part-time Bartender, as well as owner of this establishment.`3\"",$n);
			output("`n`^%s `&enquires as to how %s may help you.",$n,$g);
		break;
		case "flirt":
			$cost = array('roses','drink');
			$mailtext=array("roses"=>"`%{mname}`^ bought some expensive roses for you, at the loveshack!`n{mname}`^'s flirt points increased by `@{pts}`^ with you!","drink"=>"`%{mname}`^ bought you a drink at the loveshack!`n{mname}`^'s flirt points increased by `@{pts}`^ with you!",'slap'=>'`^{mname}`\$ just `^slapped`\$ you, at the loveshack! You aren\'t going to stand for that, are you?`n{mname}`^\'s flirt points, decreased by {pts} points with you.','ignore'=>'`^{mname} just `^ignored you at the loveshack. You have been wiped from {mname}`^\'s list.',"kiss"=>"`%{mname}`^ planted a kiss on your lips!`n{mname}`^'s flirt points increased by `@{pts}`^ with you!",'fail'=>"`^{mname}`@ attempted to flirt with you, but having heard `^{mname}`@ saying '`&{gen} is slightly substandard compared to my usual fare`@', you walk off in an understandable huff. `^{mname}`@'s flirt points have decreased by decreased by {pts} points with you.");
			$mailtitle=array('roses'=>'`%Roses`^ from {mname}`^!','drink'=>'`^A Drink`% from {mname}`%!','slap'=>'`@A SLAP!!!','ignore'=>'`^BYE BYE!','kiss'=>'`@A KISS!','fail'=>'`@Failed Flirt!');
			$s = httpget('stage');
			$w = httpget('w');
			if ($s=='') $s = 0;
			if ($s==0) {
				marriage_fform($w);
			} else {
				$g = (httpget('g')?translate_inline("she"):translate_inline("he"));
				$gp = (httpget('g')?translate_inline("her"):translate_inline("his"));
				$name = urldecode(httpget('name'));
				set_module_pref('flirtsToday',get_module_pref('flirtsToday')+1);
				if (get_module_pref('flirtsToday')<=get_module_setting('maxDayFlirt')) {
					if ($w!='ignore') $pts = get_module_setting('po'.$w);
					$b = true;
					if (in_array($w, $cost)) {
						$pr = get_module_setting('pr'.$w);
						if ($session['user']['gold']>=$pr&&$pr>0) {
							$session['user']['gold']-=$pr;
							output("`@You pay `^%s`@ Gold...`n`n",$pr);
						} elseif ($session['user']['gold']<$pr&&$pr>0) {
							$b=false;
							output("`@Cheapo! You don't have enough gold for that! You need `^%s`@ Gold!",$pr);
						}
					}
					if ($b) {
						$m = get_module_setting('fail');
						$i = e_rand(1,100);
						if ($i<=$m&&$w!='ignore'&&$w!='slap') $w = "fail";
						switch ($w) {
							case "fail":
								marriage_fpadd($ac,$pts,false);
								output("`%%s`% realizes your intentions and looks disgustedly at you.`nYour Flirt Points decrease with `^%s`% by `^%s`%.",$name,$gp,$pts);
								marriage_flirtdec();
							break;
							case "ignore":
								$xmy = get_module_pref('flirtssen');
								$xmy = explode(',',$xmy);
								$astr = "";
								foreach ($xmy as $bval) {
									if ($bval!='') {
										$my = explode(',',$sen);
										$ac = $bval;
										$sen = get_module_pref('flirtsrec','marriage',$ac);
										$str="";
										$i=false;
										foreach ($my as $val) {
											if ($val!='') {
												$stf = explode('|',$val);
												$pts = $stf[1];
												if ($my[0]!=$ac) {
													$str .= $val.",";
													$i=true;
												}
											}
										}
										set_module_pref('flirtsrec',$str,'marriage',$ac);
										if ($i===true) $astr .= $bval.",";
									}
								}
								set_module_pref('flirtssen',$astr);
								output("`@You ignore %s`@, and %s walks off, forgetting your flirts.",$name,$g);
							break;
							case "drink":
								marriage_fpadd($ac,$pts);
								output("%s`@ emphatically thanks you for the drink.`nYour points increase with %s`@ by `^%s`@.",$name,$name,$pts);
							break;
							case "roses":
								marriage_fpadd($ac,$pts);
								output("%s`@ gasps with delight!`nYour points increase with %s`@ by `^%s`@.",$name,$name,$pts);
							break;
							case "slap":
								marriage_fpadd($ac,$pts,false);
								output("`%%s`% stares at you in anger, as %s`% feels the slap.. Your Flirt Points decrease with `^%s`% by `^%s`%.",$name,$g,$name,$pts);
							break;
							case "kiss":
								marriage_fpadd($ac,$pts);
								output("`@As `^%s`@ nods at you, you reach for `&%s`@ and kiss %s lucious lips!!`nYour points increase with %s`@ by `^%s`@.", $name, $gp, $gp, $name, $pts);
							break;
						}
						if (isset($mailtext[$w])) {
							$title = translate_inline($mailtitle[$w]);
							$text = translate_inline($mailtext[$w]);
							$subst = array('{name}'=>$name,'{gen}'=>$g,'{mname}'=>$session['user']['name'],'{pts}'=>$pts);
							foreach ($subst as $key=>$rep) {
								$text = str_replace($key,$rep,$text);
							}
							foreach ($subst as $key=>$rep) {
								$title = str_replace($key,$rep,$title);
							}
							systemmail($ac,$title,$text);
						}
					}
				} else {
					output("`@Erm.. you can't flirt any more today, pal!");
				}
			}
		break;
		case "talk":
			if (get_module_pref('flirtsToday')>get_module_setting('maxDayFlirt')) set_module_pref('flirtsToday',get_module_setting('maxDayFlirt'));
			output("`^%s`3 says, \"`&I am a %s of love.. I can help you with problems.. here is some information for you.`3\"`n`&You've flirted `%%s`& times today, out of a possible `%%s`&.`n",$n,$ti,get_module_pref('flirtsToday'),get_module_setting('maxDayFlirt'));
			marriage_flist();
		break;
		case "bar":
			addnav("Drinks");
			modulehook("ale", array());
			addcommentary();
			output("`@As you sit down at the bar, %s`@ inquires as to if you would like a drink.`nLooking around, you nod and talk to other patrons.`n",$n);
			viewcommentary("loveshack","`#Discourse?`@",25,"discourses");
		break;
	}
	page_footer();
}
			

function marriage_fpadd($ac=1,$am=0,$inc=true) {
	global $session;
	$my = get_module_pref('flirtssen');
	$my = explode(',',$my);
	$str = "";
	foreach ($my as $val) {
		if ($val!='') {
			if ($val!=$ac) {
				$str .= $val.",";
			}
		}
	}
	$str.=$ac;
	set_module_pref('flirtssen',$str);
	$sen = get_module_pref('flirtsrec','marriage',$ac);
	$i = false;
	$my = explode(',',$sen);
	$str="";
	foreach ($my as $val) {
		if ($val!='') {
			$stf = explode('|',$val);
			if ($stf[0]==$session['user']['acctid']) {
				$i=true;
				$pts = $stf[1];
			} else {
				$str .= $val.",";
			}
		}
	}
	$p=0;
	if ($i) {
		$p+=$pts;
	}
	if ($inc===true) {
		$p+=$am;
	} else {
		$p-=$am;
	}
	$str.=$session['user']['acctid']."|$p";
	set_module_pref('flirtsrec',$str,'marriage',$ac);
	if ($session['user']['marriedto']!=0&&$session['user']['marriedto']!=4294967295&&$ac!=$session['user']['marriedto']) {
		$mailmessage=array(translate_inline("%s`0`@ has been unfaithful to you!"),$session['user']['name']);
		$t = translate_inline("`%Uh oh!!");
		systemmail($ac,$t,$mailmessage);
		set_module_pref('flirtsfaith',get_module_pref('flirtsfaith')+1);
		output("`n`@`c`bShame on you! Don't be unfaithful!`b`c");;
		marriage_flirtdec();
	}
}

function marriage_flirtdec() {
	global $session;
	if (get_module_setting('flirtCharis')==1&&$session['user']['charm']>0) {
		if ($session['user']['charm']>0) $session['user']['charm']--;
		output("`n`n`^You LOSE a charm point!");
	}
}

function marriage_faq() {
	popup_header("Marriage Questions");
	$c = translate_inline("Contents");
	output_notl("`#<strong><center><a href='petition.php?op=faq'>$c</a></center></strong>`0",true);
	addnav("","petition.php?op=faq");
	output("`n`c`&`bQuestions about Marriage`b`c`n");
	output("`^1. What is Marriage?`n");
	output("`@Don't go there... you don't want to know!`n`n");
	output("`^2. Where can I get Married?`n");
	if (get_module_setting('all')==1&&get_module_setting('oc')==0) {
		output("`@You can just enter a convenient Chapel.");
	} elseif (get_module_setting('oc')==1) {
		output("`@Currently, only in the Old Church in %s.",get_module_setting('oldchurchplace','oldchurch'));
	} else {
		output("`@Currently, only in the Chapel in %s.",get_module_setting('chapelloc'));
	}
	output("`nHowever, you do need to have been proposed to.");
	if (get_module_setting('flirttype')) {
		output("`nFind more information at your local `iLoveshack`i.");
		if (get_module_setting('lall')) {
			output("`nOne in every place..");
		} else {
			output("`nThe closest loveshack to you is in `%%s`@.",get_module_setting('loveloc'));
		}
	}
	output_notl("`n`n");
	output("`^3. Can I get a divorce?`n");
	output("`@Yes, no .net marriage is binding `ithank god..`i Ah! Did I type that??!`n`n");
	output("`^4. Anything else?`n");
	if (get_module_setting("sg")==1) {
		output("`@Same-gender marriages are allowed");
	} else {
		output("`@Same-gender marriages are not allowed");
	}
	output(", and all Wedded couples can be viewed from the list in the Gardens.`n`n");
	output("`^5. What about Wedding Rings?`n");
	$n = '`5Capelthwaite';
	$g = (get_module_setting('gvica')?translate_inline("she"):translate_inline("he"));
	if (get_module_setting('oc')==0) $n = get_module_setting("vname");
	if (get_module_setting('oc')==1) $g = translate_inline('He');
	if (get_module_setting('cost') > 0) {
		output("`%%s`@ will sell a cheap one to you, for %s gold.`n`n",$n,get_module_setting('cost'));
	} else {
		output("`^%s`@ has a spare one, and %s will send it for you.`n`n",$n,$g);
	}
	output("`^6. Who's Married?`n");
	output("`@View the list in the Gardens!");
	popup_footer();
}

function marriage_pform($w) {
	global $session;
	$n = httppost("n");
	rawoutput("<form action='runmodule.php?module=marriage&op=".$w."&ty=propose&stage=0' method='POST'>");
	addnav("","runmodule.php?module=marriage&op=".$w."&ty=propose&stage=0");
	if ($n!="") {
		$string="%";
		for ($x=0;$x<strlen($n);$x++){
			$string .= substr($n,$x,1)."%";
		}
		if (get_module_setting('sg')==1) {
			$sql = "SELECT login,name,acctid FROM ".db_prefix("accounts")." WHERE login LIKE '%$n%' AND acctid<>".$session['user']['acctid']." AND marriedto=0 ORDER BY level,login";
		} else {
			$sql = "SELECT login,name,acctid FROM ".db_prefix("accounts")." WHERE name LIKE '%$string%' AND acctid<>".$session['user']['acctid']." AND sex<>".$session['user']['sex']." AND marriedto=0 ORDER BY level,login";
		}
		$result = db_query($sql);
		if (db_num_rows($result)!=0) {
			output("`@These users were found `^(click on a name`@):`n");
			rawoutput("<table cellpadding='3' cellspacing='0' border='0'>");
			rawoutput("<tr class='trhead'><td>Name</td></tr>");
			for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td><a href='runmodule.php?module=marriage&ty=propose&op=".$w."&stage=1&ac=".$row['acctid']."'>");
			output_notl($row['name']);
			rawoutput("</td></tr>");
			addnav("","runmodule.php?module=marriage&ty=propose&op=".$w."&stage=1&ac=".$row['acctid']);
			}
			rawoutput("</table>");
		} else {
			output("`c`@`bA user was not found with that name.`b`c");
		}
		output_notl("`n");
	}
	output("`^`b`cMarriage..`c`b");
	output("`nWho do you want to propose to?");
	output("Name of user (cannot be married already): ");
	rawoutput("<input name='n' maxlength='50' value=\"".htmlentities(stripslashes(httppost('n')))."\">");
	$apply = translate_inline("Propose");
	rawoutput("<input type='submit' class='button' value='$apply'></form>");
}

function marriage_plist($op) {
	$stuff = explode(',',get_module_pref('proposals'));
	$n = 0;
	if (count($stuff)>0) {
		output("`@The following people have proposed to you... click to marry, or reject them!");
	}
	output_notl("<table><tr class='trhead'><td>%s</td></tr>",translate_inline('Operations'),true);
	foreach ($stuff as $val) {
		if ($val!="") {
			$n++;
			$sql = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid='$val' AND locked=0";
			$res = db_query($sql);
			if (db_num_rows($res)!=0) {
				$row = db_fetch_assoc($res);
				output_notl("<tr ".($n%2?"trlight":"trdark")."><td>[<a href='runmodule.php?module=marriage&op=".$op."&ac=".$val."&ty=marry'>%s</a>] - [<a href='runmodule.php?module=marriage&op=".$op."&ac=".$val."&ty=reject'>%s</a>]",str_replace("%s",$row['name'],translate_inline("Marry %s")),str_replace("%s",$row['name'],translate_inline("Reject %s")),true);
				addnav("","runmodule.php?module=marriage&op=".$op."&ac=".$val."&ty=marry");
				addnav("","runmodule.php?module=marriage&op=".$op."&ac=".$val."&ty=reject");
			}
		}
	}
	if ($n==0) {
		output_notl("<tr class='trhilight'><td>`^%s</td></tr>",translate_inline("Aww! No-one wants to marry you."),true);
	}
	output_notl('</table>',true);
}

function marriage_general() {
	global $session;
	$ty = httpget('ty');
	$op = httpget('op');
	$ac = httpget('ac');
	$n = '`5Capelthwaite';
	$g = 'He';
	$s = 'himself';
	if ($op=='chapel') $n = get_module_setting("vname");
	if ($op=='chapel') $g = (get_module_setting('gvica')?translate_inline("She"):translate_inline("He"));
	if ($op=='chapel') $s = (get_module_setting('gvica')?translate_inline("herself"):translate_inline("himself"));
	if ($op=='chapel') {
		page_header("The Chapel");
	} else {
		page_header("The Marriage Wing");
	}
	if ($ty==''&&$op=='chapel') {
		output("`@In the centre of town stands a small Chapel..");
		output("`nAs you enter, a Minister walks over and introduces %s as `^%s`@. %s then walks you over to a somewhat shabby bench, sits down, and invites you to sit.",$s,$n,$g);
		output("`n`^%s `3says, \"`&This is a Marriage chapel. What do you wish me to do?`3\"",$n);
		if (get_module_setting('flirttype')) output("`n`^%s `3reminds you, \"`&You must have been proposed to already.. maybe the Loveshack can help you out.`3\"",$n);
	} elseif ($ty==''&&$op=='oldchurch') {
		output("`n`@Going through a small door in the side of the Church, you enter a vast side-wing..");
		output("`nAs you enter, %s `@enters, and walks over to a somewhat shabby bench, sits down, and stares at you.",$n);
		output("`nExamining the floor, under %s's`@ beady eye, you see bits of old confetti.",$n);
		output("`n%s `3says, \"`&This is our Marriage chamber. What do you wish me to do?`3\"",$n);
		if (get_module_setting('flirttype')) output("`n`^%s `3reminds you, \"`&You must have been proposed to already.. maybe the Loveshack can help you out.`3\"",$n);
	} elseif ($ty=='proposelist') {
		marriage_plist($op);
	} elseif ($ty=='propose') {
		if (get_module_setting('flirttype')) output("`n`@Having run to your local vicar, you propose..`n");

		if (get_module_setting('cost') == 0) { set_module_pref('buyring', 1); }

		if (httpget('stage') == 1 && get_module_pref('buyring') == 1) {
			$i = get_module_pref('proposals','marriage',$ac);
			$h = explode(',',$i);
			if (in_array($session['user']['acctid'], $h)) {
				output("`n`^%s `3frowns, \"`&You've already proposed to that person.`3\"",$n);
			} else {
				$mailmessage=array(translate_inline("%s`0`@ has proposed to you."),$session['user']['name']);
				$t = translate_inline("`@Proposal!");
				systemmail($ac,$t,$mailmessage);
				$i .= get_module_pref('proposals','marriage',$ac).",".$session['user']['acctid'];
				set_module_pref('proposals',$i,'marriage',$ac);
				set_module_pref('buyring',0);
				if (get_module_setting('cost') > 0) {
					output_notl("`n`^%s `3says, \"`&Ah, right! I forgot.. I sent them that wedding ring for you.`3\"",$n);
				} else {
					output_notl("`n`^%s `3says, \"`&Ah, right! I forgot.. I sent them a wedding ring for you.`3\"",$n);
				}
			}	
		} elseif (get_module_setting('cost') > 0 && get_module_pref('buyring') == 1 || get_module_setting('cost') == 0) {
			output("`^%s `3says, \"`&Sorry, who was it you wanted to propose to?`3\"`n",$n);
			marriage_pform($op);
		} else {
			output("`^%s `3says, \"`&You haven't bought an engagement ring! You can't propose.`3\"",$n);
			output("`n`^%s `3offers, \"`&I have some I could sell, and I'll sell you one.. for a small fee.`3\"",$n);
			addnav("Actions");
			addnav("Ask about a Ring","runmodule.php?module=marriage&op=".$op."&ty=ringbuy&stage=0&ac=".$ac);
		}
	} elseif ($ty=='ringbuy') {
		if (httpget('stage')==1) {
			set_module_pref('buyring',1);
			$session['user']['gold']-=get_module_setting('cost');
			output("`n`^%s `3takes %s gold from you, and hands you the Ring.",$n,get_module_setting('cost'));
			addnav("Actions");
			addnav("Propose","runmodule.php?module=marriage&op=".$op."&ty=propose&stage=1&ac=".$ac);
		} else {
			output("`^%s `3reaches into a pocket and takes out a ring.",$n);
			output("`^%s `3says, \"`&This ring costs %s gold.`3\"",$n,get_module_setting('cost'));
			if ($session['user']['gold']<get_module_setting('cost')) {
				output("`n`^%s `3looks at your gold pouch and says, \"`&You haven't got enough for this ring.`3\"",$n);
			} else {
				output("`n`^%s `3looks at your gold pouch and says, \"`&You've got got enough for this ring.`3\"",$n);
				addnav("Actions");
				addnav("Buy a Ring","runmodule.php?module=marriage&op=".$op."&ty=ringbuy&stage=1&ac=".$ac);
			}
		}
	} elseif ($ty=='talk') {
		addcommentary();
		output("`@You hear people whispering...`n",$n);
		viewcommentary("marriage","`#Whisper?`@",25,"whisper");
	} elseif ($ty=='marry') {
		set_module_pref('flirtsfaith',0);
		$stuff = explode(',',get_module_pref('proposals'));
		$i = "";
		foreach ($stuff as $val) {
			if ($val!=""&&$val!=$ac&&$val!=$session['user']['acctid']) {
				$i .= ",".$val;
			}
		}
		set_module_pref('proposals',$i);
		$stuff = explode(',',get_module_pref('proposals','marriage',$ac));
		$i = "";
		foreach ($stuff as $val) {
			if ($val!=""&&$val!=$ac&&$val!=$session['user']['acctid']) {
				$i .= ",".$val;
			}
		}
		set_module_pref('proposals',$i,'marriage',$ac);
		$mailmessage=array(translate_inline("%s`0`@ has married you!"),$session['user']['name']);
		$t = translate_inline("`@Marriage!");
		systemmail($ac,$t,$mailmessage);
		$session['user']['marriedto']=$ac;
		$sql = "UPDATE " . db_prefix("accounts") . " SET marriedto=".$session['user']['acctid']." WHERE acctid='$ac'";
		db_query($sql);
		$sql = "SELECT name,sex FROM ".db_prefix("accounts")." WHERE acctid='$ac' AND locked=0";
		$res = db_query($sql);
		$row = db_fetch_assoc($res);
		addnews("`&%s`0`& and %s`0`& were joined today, in joyous matrimony.", $session['user']['name'],$row['name']);
		$first = ($session['user']['sex']?translate_inline("wife"):translate_inline("husband"));
		$second = ($row['sex']?translate_inline("wife"):translate_inline("husband"));
		output("`^%s `3says \"`&And I pronounce thee %s and %s.. yada yada.`3\"",$n,$first,$second);
		output("`n`^%s `3says \"`&Don't look so suprised! Nothing is sacred anymore...`3\"",$n);
		apply_buff('marriage-start',
			array(
				"name"=>"`@Marriage",
				"rounds"=>100,
				"wearoff"=>"`&The elation wears off.",
				"defmod"=>1.83,
				"survivenewday"=>1,
				"roundmsg"=>"`@You are elated at your marriage",
				)
		);
	} elseif ($ty=='reject') {
		$stuff = explode(',',get_module_pref('proposals'));
		$i = "";
		foreach ($stuff as $val) {
			if ($val!=""&&$val!=$ac&&$val!=$session['user']['acctid']) {
				$i .= ",".$val;
			}
		}
		set_module_pref('proposals',$i);
		$stuff = explode(',',get_module_pref('proposals','marriage',$ac));
		$i = "";
		foreach ($stuff as $val) {
			if ($val!=""&&$val!=$ac&&$val!=$session['user']['acctid']) {
				$i .= ",".$val;
			}
		}
		set_module_pref('proposals',$i,'marriage',$ac);
		$mailmessage=array(translate_inline("%s`0`@ has rejected you as unfit for marriage! You lose some charm."),$session['user']['name']);
		set_module_pref('ringbuy', 0,'marriage',$ac);
		$t = translate_inline("`@Rejection!");
		systemmail($ac,$t,$mailmessage);
		if (get_module_setting('counsel')==1) {
			$mailmessage=array(translate_inline("`@Hallo. I am Professor van Lipvig, and I haf been paid by.. benefactors, to counsel you due to your Mishap vith %s`@.`nPlease visit me in ze village."),$session['user']['name']);
			$t = translate_inline("`@Professor");
			systemmail($ac,$t,$mailmessage);
			set_module_pref('counsel',1,'marriage',$ac);
		}
		$sql = "SELECT name,sex,charm FROM ".db_prefix("accounts")." WHERE acctid='$ac' AND locked=0";
		$res = db_query($sql);
		$row = db_fetch_assoc($res);
		if (get_module_setting('flirtCharis')==1&&$row['charm']!=0) {
			$row['charm']--;
			$sql = "UPDATE " . db_prefix("accounts") . " SET charm=".$row['charm']." WHERE acctid='$ac'";
			db_query($sql);
		}
		addnews("`&%s`0`& got a marriage proposal from %s`0`&, which %s`0`& rejected, seeing %s`0`& as '`@Unfit for Marriage.`&'",$session['user']['name'],$row['name'],$session['user']['name'],$row['name']);
		addnews("`&%s`0`& is currently moping around the inn.",$row['name']);
		$x = ($row['sex']?translate_inline("she's"):translate_inline("he's"));
		output("`@You say `3\"`&I don't want to marry %s`0`&...%s...not my type..`3\"",$row['name'],$x);
		if (get_module_setting('cansell')!=1) {
			output("`^%s `3takes the wedding ring from %s`0`&  and throws it into the Garderobe.",$n,$row['name']);
			output("`n`^%s `3grins, and says \"`&No second thoughts now.`3\"",$n);
		} else {
			output("`^%s `3takes the wedding ring from %s`0`& from you, and says `3\"`&Do you want to sell it back to me?`3\".",$n,$row['name']);
			addnav("Actions");
			addnav("Sell the Ring","runmodule.php?module=marriage&op=".$op."&ty=sellr&i=s&ac=".$ac);
			addnav("Keep the Ring as a Memento","runmodule.php?module=marriage&op=".$op."&ty=sellr&i=m&ac=".$ac);
		}
	} elseif ($ty=='sellr') {
		$sql = "SELECT name,sex FROM ".db_prefix("accounts")." WHERE acctid='$ac' AND locked=0";
		$res = db_query($sql);
		$row = db_fetch_assoc($res);
		if (httpget('i')=='s') {
			output("`^%s `3pockets the wedding ring from %s`0`& gives you %s gold.",$n,$row['name'],get_module_setting('cost'));
			$session['user']['gold']+=get_module_setting('cost');
		} else {
			set_module_pref('buyring',1);
			output("`^%s `3gives you the wedding ring from %s`0`& back, and ties it on a string around your neck.",$n,$row['name']);
		}
	} elseif ($ty=='divorce') {
		$who = $session['user']['marriedto'];
		$session['user']['marriedto'] = 0;
		output("`^%s `&nods..",$n);
		output("`n`^%s `3chants, \"`&Egairram siht dne ekactiurf taerg fo dog.`3\"",$n);
		output("`n`^%s `3chants, \"`&Aet rof snub yccohc ekil uoy dluow.`3\"",$n);
		output("`n`^%s `3bows solemnly, and a `^`bLightning Bolt`b`3 arches from the sky, hitting the ground before your feet..",$n);
		output("`n`@The Marriage is annulled!");
		if ($who===4294967295) break;
		$sql = "SELECT name,sex FROM ".db_prefix("accounts")." WHERE acctid='$who' AND locked=0";
		$res = db_query($sql);
		$row = db_fetch_assoc($res);
		$sql = "UPDATE " . db_prefix("accounts") . " SET marriedto='0' WHERE acctid='$who'";
		db_query($sql);
		$mailmessage=array(translate_inline("%s`0`@ has divorced you."),$session['user']['name']);
		$mailmessagg=array(translate_inline("%s`0`@ has divorced you.`nYou get %s gold."),$session['user']['name'],$session['user']['gold']);
		$t = translate_inline("`@Divorce!");
		addnews("`&%s`0`% and %s`0`% were divorced today...", $session['user']['name'],$row['name']);
		$msg = translate_inline("`^%s `@takes all of your gold.. \"`&I need to comfort %s`0`&...`^\"");
		if (get_module_setting('dmoney')==1&&$session['user']['gold']>0) {
			$sql = "UPDATE " . db_prefix("accounts") . " SET gold=gold + ".$session['user']['gold']." WHERE acctid='$who'";
			$session['user']['gold']=0;
			db_query($sql);
			systemmail($who,$t,$mailmessagg);
			output_notl($msg,$n,$row['name']);
		} else {
			systemmail($who,$t,$mailmessage);
		}
		output("`n`@You feel sorrow for the death of your spouse.");
		apply_buff('marriage-divorce',
			array(
				"name"=>"`4Divorce Guilt",
				"rounds"=>100,
				"wearoff"=>"`\$You are no longer guilty about your divorce.",
				"defmod"=>0.83,
				"survivenewday"=>1,
				"roundmsg"=>"`\$Guilt haunts you.",
				)
		);
	}
	addnav("Navigation");
	villagenav();
	addnav("Actions");
	addnav("Talk to Others","runmodule.php?module=marriage&op=".$op."&ty=talk");
	if ($session['user']['marriedto']==0) {
		if (get_module_setting('flirttype')==0) addnav("Propose","runmodule.php?module=marriage&op=".$op."&ty=propose");
		addnav("View Proposals","runmodule.php?module=marriage&op=".$op."&ty=proposelist");
	} else {
		addnav("Get a Divorce!","runmodule.php?module=marriage&op=".$op."&ty=divorce");
	}
	page_footer();
}

function marriage_wholist() {
	global $session;
	$ty = httpget('ty');
	$op = httpget('op');
	page_header("Newly Weds");
	addnav('Navigation');
	villagenav();
	addnav('To the Garden','gardens.php');
	addnav("List");
	addnav("Currently Online","runmodule.php?module=marriage&op=newlyweds");
	$playersperpage=50;
	
	$sql = "SELECT count(acctid) AS c FROM " . db_prefix("accounts") . " WHERE (marriedto<>0 AND marriedto<>4294967295) AND locked=0";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$totalplayers = $row['c'];
	
	$ty = httpget('ty');
	$page = httpget('page');
	$search = "";

	if ($op=="search"){
		$search="%";
		$n = httppost('name');
		for ($x=0;$x<strlen($n);$x++){
			$search .= substr($n,$x,1)."%";
		}
		$search=" AND login LIKE '".addslashes($search)."'";
	} else {
		$pageoffset = (int)$page;
		if ($pageoffset>0) $pageoffset--;
		$pageoffset*=$playersperpage;
		$from = $pageoffset+1;
		$to = min($pageoffset+$playersperpage,$totalplayers);
	}
	
	$limit=" LIMIT $pageoffset,$playersperpage ";
	addnav("Pages");
	for ($i=0;$i<$totalplayers;$i+=$playersperpage){
		addnav(array("Page %s (%s-%s)", $i/$playersperpage+1, $i+1, min($i+$playersperpage,$totalplayers)), "runmodule.php?module=marriage&op=newlyweds&page=".($i/$playersperpage+1));
	}
	
	if ($page=="" && $ty==""){
		$title = translate_inline("Married Warriors Currently Online");
		$sql = "SELECT name,login,alive,location,race,sex,marriedto,laston,lastip,uniqueid FROM " . db_prefix("accounts") . " WHERE locked=0 AND (marriedto<>0 AND marriedto<>4294967295) AND loggedin=1 AND laston>'".date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds"))."' ORDER BY level DESC, dragonkills DESC, login ASC";
		$result = db_query_cached($sql,"list.php-warsonline");
	} else {
		$title = sprintf_translate("Married Warriors in the realm (Page %s: %s-%s of %s)", ($pageoffset/$playersperpage+1), $from, $to, $totalplayers);
		rawoutput(tlbutton_clear());
		$sql = "SELECT name,login,alive,location,marriedto,race,sex,loggedin,lastip,uniqueid FROM " . db_prefix("accounts") . " WHERE locked=0 AND (marriedto<>0 AND marriedto<>4294967295) $search"."ORDER BY level DESC, dragonkills DESC, login ASC $limit";
		$result = db_query($sql);
	}
	
	$max = db_num_rows($result);
	if ($max>100) {
		output("`\$Too many names match that.  Showing only the first 100.`0`n");
		$max = 100;
	}
	
	output_notl("`c`b".$title."`b");
	
	$alive = translate_inline("Alive");
	$name = translate_inline("Name");
	$loc = translate_inline("Location");
	$race = translate_inline("Race");
	$sex = translate_inline("Sex");
	$who = translate_inline("Married To");
	
	rawoutput("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>",true);
	rawoutput("<tr class='trhead'><td>$alive</td><td>$name</td><td>$loc</td><td>$race</td><td>$sex</td><td>$who</td></tr>");
	$writemail = translate_inline("Write Mail");
	if ($max==0) {
		output_notl("<tr class='trhilight'><td colspan='8'>`^`b`c%s`c`b</td></tr>",translate_inline("No one is married!"),true);
	}
	for($i=0;$i<$max;$i++){
		$row = db_fetch_assoc($result);
		rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td>",true);
		$a = translate_inline($row['alive']?"`1Yes`0":"`4No`0");
		output_notl("%s", $a);
		rawoutput("</td><td>");
		if ($session['user']['loggedin']) {
			rawoutput("<a href=\"mail.php?op=write&to=".rawurlencode($row['login'])."\" target=\"_blank\" onClick=\"".popup("mail.php?op=write&to=".rawurlencode($row['login'])."").";return false;\">");
			rawoutput("<img src='images/newscroll.GIF' width='16' height='16' alt='$writemail' border='0'></a>");
			rawoutput("<a href='bio.php?char=".rawurlencode($row['login'])."'>");
			addnav("","bio.php?char=".rawurlencode($row['login'])."");
		}
		output_notl("`&%s`0", $row['name']);
		if ($session['user']['loggedin'])
			rawoutput("</a>");
		rawoutput("</td><td>");
		$loggedin=(date("U") - strtotime($row['laston']) < getsetting("LOGINTIMEOUT",900) && $row['loggedin']);
		output_notl("`&%s`0", $row['location']);
		if ($loggedin) {
			$online = translate_inline("`#(Online)");
			output_notl("%s", $online);
		}
		rawoutput("</td><td>");
		output_notl("%s", $row['race']);
		rawoutput("</td><td>");
		$sex = translate_inline($row['sex']?"`%Female`0":"`!Male`0");
		output_notl("%s", $sex);
		rawoutput("</td><td>");
		$sql = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid='".$row['marriedto']."' AND locked=0";
		$res = db_fetch_assoc(db_query($sql));
		output_notl("%s",$res['name']);
		rawoutput("</td></tr>");
	}
	rawoutput("</table>");
	output_notl("`n");
	$search = translate_inline("Search by name: ");
	$search2 = translate_inline("Search");
	rawoutput("<form action='runmodule.php?module=marriage&op=newlyweds&ty=search' method='POST'>$search<input name='name'><input type='submit' class='button' value='$search2'></form>");
	addnav("","runmodule.php?module=marriage&op=newlyweds&ty=search");
	output_notl("`c");
	page_footer();
}

function marriage_counselling() {
	global $session;
	$ty = httpget('ty');
	page_header("Marriage Counselling");
	addnav("Actions");
	if ($ty=='') {
		output("`@Walking to the edge of town, you look around for `^Professor van Lipvig`@'s office.");
		output("`nNot finding it, you walk back, and see a large wooden building that you had never noticed before.");
		output("`nWalking up to it, you see a sign, and a voice yells out 'Come in,' from a nearby window.");
		villagenav();
		addnav("Enter the Office","runmodule.php?module=marriage&op=counselling&ty=enter");
	} elseif ($ty=='enter') {
		output("`@Pushing the carved door open ever so quitely, you enter into a spacious, yet opulent chamber.");
		output("`nLooking around, you see chairs, plush rugs, and hunting trophies.");
		output("`nWalking to the end of the warm room, you see many herbal candles, and a burning fire.");
		output("`nBehind you, a melodious voice says '`^Van Lipvig's office, how may I help you.`@'");
		output("`nAlmost jumping out of your skin, you turn around to see a small woman sat at her desk.");
		output("`nNodding imperceptibly, she speaks, '`^Ah, %s`0`^. Van Lipvig is with someone else at the moment. Please sit down.`@'",$session['user']['name']);
		villagenav();
		addnav("To the waiting Area","runmodule.php?module=marriage&op=counselling&ty=wait");
	} elseif ($ty=='wait') {
		addnews("%s`0`@ had to go to a Social Counsellor, due to a rejected Marriage Proposal!`&",$session['user']['name']);
		set_module_pref('counsel',0);
		output("`@Sitting on a stuffed bear, you study the room and it's paintings, many of which you feel you have seen before.");
		output("`nOne is of a Green Dragon fighting a constant battle, and yet another contains a scene of a street hawker....");
		output("`n`nClosing one eye to study the painting, you start to feel drowsy.. then someone wakes you up. '`^%s`0`^, the Doctor will see you now.`@",$session['user']['name']);
		addnav("See Van Lipvig","runmodule.php?module=marriage&op=counselling&ty=checkin");
	} elseif ($ty=='checkin') {
		output("`@Standing up, you walk past the same painting of a Dragon that you saw earlier, and through a brass door.");
		output("`nEmerging through it, you see yet another spacious chamber, and walk through it.");
		output("`nIf the other was Plush, this could only be described as minimalist.");
		output("`nAs you exit this chamber, you walk into an office where a short stumpy man sits at a large desk, with a moustache that stretches out on either side of him, while a couch and a clock stand at the other end of the chamber.");
		output("`n'`&Ah, mah fviend. Ah am here to prevent another Socielle Incident.. but fahgive me, I ahm Professor Van Lipvig, socielle extraordinaire.`@'");
		output("`n`6As he says this, you are reminded of a large toad, and have to stop yourself from laughing.");
		output("`n`@'`&Vell, mah tachitarn fah-wend, puh-lease seet on mah couch.`@'");
		addnav("Sit on the Couch","runmodule.php?module=marriage&op=counselling&ty=couch");
	} elseif ($ty=='couch') {
		$array=array("a green dragon","a set of frolicking sheep","a large piece of cake","a pixie","a gem");
		$array=$array[array_rand($array)];
		output("`@Once you are sitting on the couch, he asks you to stare at his clock, and tell him what you see..");
		output("`n'`^I see %s,`@' you say.",translate_inline($array));
		output("`n'`&Really? That is vierd. This is supposed to be a previliminary eye test.`@'");
		addnav("Stare into a Candle","runmodule.php?module=marriage&op=counselling&ty=see");
	} elseif ($ty=='see') {
		$array=array("a green dragon","a tan dragon","a large swamp","an elephant with green pixies jumping around it","a troll","a vivid sunset");
		$array=$array[array_rand($array)];
		output("`@With you still sitting on the couch, he asks you to stare at a candle, and tell him what you see..");
		output("`n'`^I see %s,`@' you say.",translate_inline($array));
		output("`n'`&Keep that in mind as ve do ze next stage.`@'");
		addnav("The next stage","runmodule.php?module=marriage&op=counselling&ty=dream");
	} elseif ($ty=='dream') {
		output("`@'`&Please lie down and try to go to sleep.`@' Lipvig says.");
		output("`nNot sure about going to sleep in the same room as this weird person, you manage to clear your head, and you are soon in the land of sleep..");
		output("`nHalfway through a weird dream, you are woken up and asked to explain what you saw.");
		output("`n'`^I saw a large room, filled with riches,`@' you say.");
		addnav("The next stage","runmodule.php?module=marriage&op=counselling&ty=materi");
	} elseif ($ty=='materi') {
		output("`@'`&I have it!`@' Lipvig exclaims.");
		output("`n'`&I think you hunger for material riches..`@' Lipvig states.");
		output("`n'`&I must help you on your road.. I was ordered..`@' Lipvig says, in an unhappy tone.");
		output("`nLipvig throws a powder over you..");
		output_notl("`c`b");

		/* (Alva) Check what should be given, and if possible to give. Not a very clever way of doing it. */
		$amt = 0;
		$counsGems = get_module_setting('counsGems');
		if ($counsGems != 0) $amt++;

		$counsGold = get_module_setting('counsGold');
		if ($counsGold != 0) $amt++;

		$counsExp = get_module_setting('counsExp');
		if ($counsExp != 0) $amt++;

		$counsFor = get_module_setting('counsFor');
		if ($counsFor != 0) $amt++;

		$counsLvl = get_module_setting('counsLvl');
		if ($counsLvl != 0) $amt++;

		$givenOK = 0;
		$tmpCounter = 0;

		if ($amt > 0) {
			/* (Alva) Sanity check, we don't want to give it too many chances. */
			while ($givenOK == 0 && $tmpCounter < 100) {
				$tmpCounter++;
				$outcome=e_rand(1, 5);
				switch ($outcome) {
					case "1":
						if ($counsGems != 0) {
							$session['user']['gems']++;
							output("`n`%You feel richer.");
							$givenOK = 1;
						}
					break;
					case "2":
						if ($counsGold != 0) {
							$session['user']['gold'] += $counsGold * $session['user']['level'];
							output("`n`%You feel richer.");
							$givenOK = 1;
						}
					break;
					case "3":
						if ($counsExp != 0) {
							$counsCurExp = $session['user']['experience'];
							$session['user']['experience'] = $counsCurExp + ($counsCurExp * ($counsExp/100));
							output("`n`%You feel more experienced.");
							$givenOK = 1;
						}
					break;
			 		case "4":
						if ($counsFor != 0) {
							$session['user']['turns'] += $counsFor;
							output("`n`%You feel stronger.");
							$givenOK = 1;
						}
					break;
			 		case "5":
						if ($counsLvl != 0) {
							$session['user']['level']++;
							output("`n`%You feel wiser.");
							$givenOK = 1;
						}
					break;
				}
			}
		}
		output_notl("`b`c");
		addnav("Leave","runmodule.php?module=marriage&op=counselling&ty=l");
	} else {
		output("`@You stand up, not really sure how this helped you, but you are sure it must have done.");
		output("`nYou leave..");
		villagenav();
	}
	page_footer();
}

function marriage_flist() {
	global $session;
	$stuff = explode(',',get_module_pref('flirtsrec'));
	$n = 0;
	if (count($stuff)>0) {
		output("`@The following people have flirted with you... go ahead, flirt back, break hearts!");
		output("`nIf a user has at least `^%s`@ flirt points with you, you can propose.`nIgnoring will wipe that user from your list.",get_module_setting('flirtmuch'));
	}
	output_notl("<table><tr class='trhead'><td>%s</td></tr>",translate_inline('Data'),true);
	foreach ($stuff as $val) {
		if ($val!="") {
			$y = explode('|',$val);
			$id = $y[0];
			$pts = $y[1];
			$n++;
			$sql = "SELECT name,acctid FROM ".db_prefix("accounts")." WHERE acctid='$id' AND locked=0";
			$res = db_query($sql);
			if (db_num_rows($res)!=0) {
				$row = db_fetch_assoc($res);
				output_notl("<tr ".($n%2?"trlight":"trdark")."><td>",true);
				output("`^%s`@ has `^%s`@ flirt points with you.",$row['name'],$pts);
				$links = translate_inline("Links: ");
				$links .= " [".marriage_flink($row['acctid'],"Buy a Drink","drink")."]";
				$links .= " - [".marriage_flink($row['acctid'],"Buy some Roses","roses")."]";
				$links .= " - [".marriage_flink($row['acctid'],"Kiss","kiss")."]";
				$links .= " - [".marriage_flink($row['acctid'],"Slap","slap")."]";
				$links .= " - [".marriage_flink($row['acctid'],"Ignore","ignore")."]";
				if ($pts>=get_module_setting('flirtmuch')&&$session['user']['marriedto']==0) {
					$blah = "";
					if ($session['user']['location'] == get_module_setting("chapelloc")&&get_module_setting("all")==0&&get_module_setting('oc')==0) {
						$blah = 'chapel';
					} elseif (get_module_setting("all")==1&&get_module_setting('oc')==0) {
						$blah = 'chapel';
					} elseif (get_module_setting('oc')==1) {
						$blah = 'oldchurch';
					}
					if ($blah!='') {
						$links.=" - [<a href='runmodule.php?module=marriage&ty=propose&op=$blah&stage=1&ac=".$row['acctid']."'>".translate_inline("Propose")."</a>]";
						addnav("","runmodule.php?module=marriage&ty=propose&op=$blah&stage=1&ac=".$row['acctid']);
					} else {
						$links.=" - [<a href='runmodule.php?module=marriage&ty=propose&op=chapel&stage=1&ac=".$row['acctid']."'>".translate_inline("Propose")."</a>]";
						addnav("","runmodule.php?module=marriage&ty=propose&op=chapel&stage=1&ac=".$row['acctid']);
					}
				}
				rawoutput(marriage_hidedata($links));
				rawoutput("</td></tr>");
			}
		}
	}
	if ($n==0) {
		output_notl("<tr class='trhilight'><td>`^%s</td></tr>",translate_inline("Aww! No-one has flirted with you."),true);
	}
	output_notl('</table><br>',true);
	$stuff = explode(',',get_module_pref('flirtssen'));
	$n = 0;
	if (count($stuff)>0) {
		output("`@You've flirted with the following people.");
		output("`nIf you send least `^%s`@ flirt points, a person can propose to you.",get_module_setting('flirtmuch'));
	}
	output_notl("<table><tr class='trhead'><td>%s</td></tr>",translate_inline('Data'),true);
	$pts = 0;
	foreach ($stuff as $val) {
		if ($val!="") {
			$n++;
			$sql = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid='$val' AND locked=0";
			$res = db_query($sql);
			if (db_num_rows($res)!=0) {
				$row = db_fetch_assoc($res);
				$blah = get_module_pref('flirtsrec','marriage',$val);
				$blah = explode(',',$blah);
				foreach ($blah as $bval) {
					if ($bval!="") {
						$y = explode('|',$bval);
						$id = $y[0];
						if ($id==$session['user']['acctid']) $pts = $y[1];
					}
				}
				output_notl("<tr ".($n%2?"trlight":"trdark")."><td>",true);
				output("`@You have `^%s`@ flirt points with `^%s`@.",$pts,$row['name']);
				rawoutput("</td></tr>");
			}
		}
	}
	if ($n==0) {
		output_notl("<tr class='trhilight'><td>`^%s</td></tr>",translate_inline("Aww! You haven't flirted with anyone."),true);
	}
	output_notl('</table>',true);
}

function marriage_lovedrinks() {
	$z = 2;
	$s = get_module_setting('loveDrinksAdd');
	if (is_module_installed('drinks')&&$s<$z) {
		$sql = array();
		$ladd=array();
		if ($s<1) { // We use 'lessthan' so more drinks can be packaged with this
			$sql[]="INSERT INTO " . db_prefix("drinks") . " VALUES (0, 'Love Brew', 1, 25, 5, 0, 0, 0, 20, 0, 5, 15, 0.0, 0, 0, 'Cedrik reaches under the bar, pulling out a purple cupid shaped bottle... as he pours it into a crystalline glass, the glass shines and he puts a pineapple within the liquid... \"Here, have a Love Brew..\" says Cedrik.. and as you try it, you feel uplifted!', '`%Love Brew', 12, 'You remember love..', 'Despair sets in.', '1.1', '.9', '1.5', '0', '', '', '')";
			$ladd[]="Love Brew";
		}
		if ($s<2) { // We use 'lessthan' so more drinks can be packaged with this
			$sql[]="INSERT INTO " . db_prefix("drinks") . " VALUES (0, 'Heart Mist', 1, 25, 5, 0, 0, 0, 20, 0, 5, 15, 0.0, 0, 0, 'Cedrik grabs for a rather garish looking bottle on the shelf behind him... as he pours it into a large yellow mug, the porcelain seems to dissolve.. ooh er.. he puts a tomato within the sweet smelling gunk... \"Here, have a Heart Mist..\" says Cedrik.. and as you try it, you see symbols of love!', '`\$Heart Mist', 18, '`%Misty hearts fly around you..', '`#The sky falls...', '1.1', '.9', '1.5', '0', '', '', '')";
			$ladd[]="Heart Misy";
		}
		foreach ($sql as $val) {
			db_query($val);
		}
		foreach ($ladd as $val) {
			$sql = "SELECT * FROM " . db_prefix("drinks") . " WHERE name='$val' ORDER BY costperlevel";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			set_module_objpref('drinks',$row['drinkid'],'loveOnly',1,'marriage');
		}
		set_module_setting('loveDrinksAdd',$z);
		output("`n`c`b`^Marriage Module - Drinks have been added to the Loveshack`0`b`c");
	} elseif (!is_module_active('drinks')) {
		set_module_setting('loveDrinksAdd',0);
	}
}

function marriage_lovedrinksrem() {
	if (is_module_installed('drinks')) {
		$ladd=array();
		$ladd[]="Love Brew";
		$ladd[]="Heart Mist";
		foreach ($ladd as $val) {
			$sql = "DELETE FROM " . db_prefix("drinks") . " WHERE name='$val'";
			db_query($sql);
		}
	}
}

function marriage_hidedata($data="") {
	static $num;
	$code = "";
	if (!is_numeric($num)||empty($num)) $num = 0;
	if ($num==0) rawoutput("<script language=\"JavaScript\">\nfunction marShowAndHide(theId)\n{\n   var el = document.getElementById(theId)\n\n   if (el.style.display==\"none\")\n   {\n      el.style.display=\"block\"; //show element\n   }\n   else\n   {\n      el.style.display=\"none\"; //hide element\n   }\n}\n</script>");
	$num++;
	$text = translate_inline("Show/Hide Data");
	$code .= "<a href=\"#\" onClick = marShowAndHide('marData$num')>$text</a>";
	$code .= "<div id='marData$num' style=\"display:none\">";
	$code .= $data;
	$code .= "</div>";
	return $code;
}

function marriage_flink($ac=1,$text="",$flir="") {
	global $session;
	$code = "";
	$sql = "SELECT login,sex,name,acctid FROM ".db_prefix("accounts")." WHERE acctid=$ac ORDER BY level,login";
	$result = db_query($sql);
	if (db_num_rows($result)!=0) {
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			$code = "<a href='runmodule.php?module=marriage&op=loveshack&ty=flirt&stage=1&g=".$row['sex']."&w=$flir&name=".urlencode($row['name'])."&ac=".$row['acctid']."'>".translate_inline($text)."</a>";
			addnav("","runmodule.php?module=marriage&op=loveshack&ty=flirt&stage=1&g=".$row['sex']."&w=$flir&name=".urlencode($row['name'])."&ac=".$row['acctid']);
		}
	}
	return $code;
}

function marriage_fform($w) {
	global $session;
	$n = httppost("n");
	rawoutput("<form action='runmodule.php?module=marriage&op=loveshack&ty=flirt&w=$w&stage=0' method='POST'>");
	addnav("","runmodule.php?module=marriage&op=loveshack&ty=flirt&w=$w&stage=0");
	if ($n!="") {
		$string="%";
		for ($x=0;$x<strlen($n);$x++){
			$string .= substr($n,$x,1)."%";
		}
		if (get_module_setting('sg')==1) {
			$sql = "SELECT login,sex,name,acctid FROM ".db_prefix("accounts")." WHERE login LIKE '%$n%' AND acctid<>".$session['user']['acctid']." ORDER BY level,login";
		} else {
			$sql = "SELECT login,sex,name,acctid FROM ".db_prefix("accounts")." WHERE name LIKE '%$string%' AND acctid<>".$session['user']['acctid']." AND sex<>".$session['user']['sex']." ORDER BY level,login";
		}
		$result = db_query($sql);
		if (db_num_rows($result)!=0) {
			output("`@These users were found `^(click on a name`@):`n");
			rawoutput("<table cellpadding='3' cellspacing='0' border='0'>");
			rawoutput("<tr class='trhead'><td>Name</td></tr>");
			for ($i=0;$i<db_num_rows($result);$i++){
				$row = db_fetch_assoc($result);
				rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td><a href='runmodule.php?module=marriage&ty=flirt&w=$w&op=loveshack&name=".urlencode($row['name'])."&stage=1&ac=".$row['acctid']."'>");
				output_notl($row['name']);
				rawoutput("</td></tr>");
				addnav("","runmodule.php?module=marriage&ty=flirt&w=$w&op=loveshack&name=".urlencode($row['name'])."&stage=1&ac=".$row['acctid']);
			}
			rawoutput("</table>");
		} else {
			output("`c`@`bA user was not found with that name.`b`c");
		}
		output_notl("`n");
	}
	output("`^`b`cFlirting..`c`b");
	output("`nWho do you want to do that with?");
	if (get_module_setting('sg')==1) {
		output("`nSame gender flirting is allowed.");
	} else {
		output("`nSame gender flirting is not allowed.");
	}
	output("`nName of user: ");
	rawoutput("<input name='n' maxlength='50' value=\"".htmlentities(stripslashes(httppost('n')))."\">");
	$apply = translate_inline("Flirt");
	rawoutput("<input type='submit' class='button' value='$apply'></form>");
}
?>
