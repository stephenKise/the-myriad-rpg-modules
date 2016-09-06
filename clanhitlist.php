<?php

function clanhitlist_getmoduleinfo() {
	$info = array(
		"name"=>"Clan Hitlist",
		"description"=>"Lets each Clan put 10 people on a hitlist.",
		"author"=>"<a href='http://www.sixf00t4.com' target=_new>Sixf00t4</a>",
		"version"=>"20070201",
		"category"=>"Clan",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1450",
		"prefs-clans"=>array(
			"Clan Hitlist Preferences,title",
			"hitlist1"=>"ACCTID of first user on the list,int|0",
			"reason1"=>"Reason for listing,textarea|No Reason",
			"hitlist2"=>"ACCTID of second user on the list,int|0",
			"reason2"=>"Reason for listing,textarea|No Reason",
			"hitlist3"=>"ACCTID of third user on the list,int|0",
			"reason3"=>"Reason for listing,textarea|No Reason",
			"hitlist4"=>"ACCTID of fourth user on the list,int|0",
			"reason4"=>"Reason for listing,textarea|No Reason",
			"hitlist5"=>"ACCTID of fifth user on the list,int|0",
			"reason5"=>"Reason for listing,textarea|No Reason",
			"hitlist6"=>"ACCTID of sixth user on the list,int|0",
			"reason6"=>"Reason for listing,textarea|No Reason",
			"hitlist7"=>"ACCTID of seventh user on the list,int|0",
			"reason7"=>"Reason for listing,textarea|No Reason",
			"hitlist8"=>"ACCTID of eighth user on the list,int|0",
			"reason8"=>"Reason for listing,textarea|No Reason",
			"hitlist9"=>"ACCTID of nineth user on the list,int|0",
			"reason9"=>"Reason for listing,textarea|No Reason",
            "hitlist10"=>"ACCTID of tenth user on the list,int|0",
			"reason10"=>"Reason for listing,textarea|No Reason",
            ),
		"prefs"=>array(
			"Clan hitlist - User Preferences,title",
			"marked"=>"is user on a clan hitlist?,bool|0",
		),
	);
	return $info;
}


function clanhitlist_install() {
	if (!is_module_installed('clanhitlist')){
		output("`n`c`b`QClanhitlist Module - Installed`0`b`c");
	}else{
		output("`n`c`b`QClanhitlist Module - Updated`0`b`c");
		$sql = "SELECT * FROM ".db_prefix("module_objprefs")." WHERE modulename='clanhitlist' AND objtype='clan'";
		$result = db_query($sql);
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			set_module_objpref("clans", $row['objid'], $row['setting'],$row['value'],"clanhitlist");
		}
		$sql = "DELETE FROM " . db_prefix("module_objprefs") . " WHERE objtype='clan' AND modulename='clanhitlist'";
		db_query($sql);
	}
	module_addhook("footer-clan");
	return true;
}

function clanhitlist_uninstall() {
	output("`n`c`b`QClanhitlist Module - Uninstalled`0`b`c");
	return true;
}

function clanhitlist_dohook($hookname,$args) {
	global $session;
	switch ($hookname) {
		case "footer-clan":
			if ($session['user']['clanid']!=0&&httpget("op")=="" && $session['user']['clanrank']>CLAN_APPLICANT) {
				clanhitlist_navs();
			}
		break;
	}
	return $args;
}

function clanhitlist_run() {
	global $session;
    
    $op = httpget('op');
    $order = "acctid";
    if ($sort!="") $order = "$sort";
    $display = 0;
    $query = httppost('q');
    if ($query === false) $query = httpget('q');

	page_header("Clan hit list");

	switch ($op) {
		case "":
        	$name = translate_inline("Name");
            $location = translate_inline("Last seen in");
            $level = translate_inline("Level");
            $dks = translate_inline("Tks");
            $reason = translate_inline("Reason");
            $clan = translate_inline("Guild");
            $count=1;
            if($session['user']['clanrank']>=CLAN_OFFICER) addnav("Edit hit list","runmodule.php?module=clanhitlist&op=edit");
            While($count<11){
				require_once("lib/nltoappon.php");
				if(get_module_objpref("clans", $session['user']['clanid'], "hitlist$count","clanhitlist")>0){
					$sql="select name, level, clanid, acctid, dragonkills, location from ".db_prefix("accounts")." where acctid=".get_module_objpref("clans", $session['user']['clanid'], "hitlist$count","clanhitlist")."";
					$result=db_query($sql);
					$row = db_fetch_assoc($result);
					$reas=get_module_objpref("clans", $session['user']['clanid'], "reason$count","clanhitlist");        
					rawoutput("<table border=1 width=500>");
					rawoutput("<tr><td width=200>$name: ");
					output_notl($row['name']);
					if($row['clanid']!=""){
						$sql2="select clanname from ".db_prefix("clans")." where clanid=".$row['clanid'];
						$result2=db_query($sql2);
						$row2 = db_fetch_assoc($result2);
						$clanname=$row2['clanname'];
					}else{
						$clanname=translate_inline("No Clan");
					}
					output("`n%s: %s`n",$clan, $clanname);
					if (is_module_active('avatars')) $avatar = show_avatar($row['acctid']);
					rawoutput("$avatar");
					rawoutput("<br>$level: ".$row['level']." $dks: ".$row['dragonkills']."<br>$location: ".$row['location']."</td><td>");
					output("%s",nltoappon(stripslashes($reas)));
					rawoutput("</td></tr></table><br>");
				}
			$count++;
			}
            clanhitlist_navs();
		break;
        
        Case "add":
            addnav("Back to List","runmodule.php?module=clanhitlist");
			output("Who do you want to add to the list?`n");
            rawoutput("<form action='runmodule.php?module=clanhitlist&op=add' method='POST'>");
            rawoutput("<input name='q' id='q'>");
            $se = translate_inline("Search");
            rawoutput("<input type='submit' class='button' value='$se'>");
            rawoutput("</form>");
            rawoutput("<script language='JavaScript'>document.getElementById('q').focus();</script>");
            addnav("","runmodule.php?module=clanhitlist&op=add");
	
            $searchresult = false;
            $where = "";
            $op="";
            $sql = "SELECT acctid,login,name FROM " . db_prefix("accounts");
            if ($query != "") {
                $where = "WHERE login='$query' OR name='$query'";
                $searchresult = db_query($sql . " $where  ORDER BY '$order' LIMIT 2");
            }

            if ($query !== false || $searchresult) {
                if (db_num_rows($searchresult) != 1) {
                    $where="WHERE login LIKE '%$query%' OR acctid LIKE '%$query%' OR name LIKE '%$query%' OR emailaddress LIKE '%$query%' OR lastip LIKE '%$query%' OR uniqueid LIKE '%$query%' OR gentimecount LIKE '%$query%' OR level LIKE '%$query%'";
                    $searchresult = db_query($sql . " $where  ORDER BY '$order' LIMIT 101");
                }
                if (db_num_rows($searchresult)<=0){
                    output("`\$No results found`0");
                    $where="";
                }elseif (db_num_rows($searchresult)>100){
                    output("`\$Too many results found, narrow your search please.`0");
                    $op="";
                    $where="";
                }else{
                    $op="";
                    $display=1;
                }
            }
    
            if ($display == 1){
            $q = "";
            if ($query) {
                $q = "&q=$query";
            }

            $nm =translate_inline("Name");
    
            rawoutput("<table>");
            rawoutput("<tr class='trhead'><td>$nm</td></tr>");
            $rn=0;
            $oorder = "";
            for ($i=0;$i<db_num_rows($searchresult);$i++){
                $row=db_fetch_assoc($searchresult);
                if ($row[$order]!=$oorder) $rn++;
                $oorder = $row[$order];
                rawoutput("<tr class='".($rn%2?"trlight":"trdark")."'>");
                rawoutput("<td nowrap>");
                addnav("","runmodule.php?module=clanhitlist&op=add2&hit={$row['acctid']}");
                output_notl("<a href='runmodule.php?module=clanhitlist&op=add2&hit={$row['acctid']}'>`&%s`0</a>", $row['name'],true);
                rawoutput("</td></tr>");
            }
            rawoutput("</table>");
        }            
        break;
        
        case "add2":
            $hit = httpget('hit');
            $count=1;
            $hitlist="hitlist".$count;
            if(get_module_objpref("clans",$session['user']['clanid'], $hitlist,"clanhitlist")==0){
				set_module_pref("marked",1,"clanhitlist",$hit);
				output("The hit has been added to the list!");
				set_module_objpref("clans", $session['user']['clanid'], $hitlist,$hit,"clanhitlist");
            }else{
                $count=2;
                $hitlist="hitlist".$count;
                if(get_module_objpref("clans",$session['user']['clanid'], $hitlist,"clanhitlist")==0){
					set_module_pref("marked",1,"clanhitlist",$hit);
					output("The hit has been added to the list!");
					set_module_objpref("clans", $session['user']['clanid'], $hitlist,$hit,"clanhitlist");
                }else{   
                    $count=3;
                    $hitlist="hitlist".$count;
                    if(get_module_objpref("clans",$session['user']['clanid'],$hitlist,"clanhitlist")==0){
						set_module_pref("marked",1,"clanhitlist",$hit);
						output("The hit has been added to the list!");
						set_module_objpref("clans", $session['user']['clanid'],$hitlist,$hit,"clanhitlist");
                    }else{
                        $count=4;
                        $hitlist="hitlist".$count;
                        if(get_module_objpref("clans",$session['user']['clanid'],$hitlist,"clanhitlist")==0){
							set_module_pref("marked",1,"clanhitlist",$hit);
							output("The hit has been added to the list!");
							set_module_objpref("clans", $session['user']['clanid'], $hitlist,$hit,"clanhitlist");
                        }else{
                            $count=5;
							$hitlist="hitlist".$count;
                            if(get_module_objpref("clans",$session['user']['clanid'],$hitlist,"clanhitlist")==0){
								set_module_pref("marked",1,"clanhitlist",$hit);
								output("The hit has been added to the list!");
								set_module_objpref("clans", $session['user']['clanid'],$hitlist,$hit,"clanhitlist");
                            }else{
                                $count=6;
                                $hitlist="hitlist".$count;
                                if(get_module_objpref("clans",$session['user']['clanid'],$hitlist,"clanhitlist")==0){
									set_module_pref("marked",1,"clanhitlist",$hit);
									output("The hit has been added to the list!");
									set_module_objpref("clans", $session['user']['clanid'],$hitlist,$hit,"clanhitlist");
                                }else{
                                    $count=7;
                                    $hitlist="hitlist".$count;
                                    if(get_module_objpref("clans",$session['user']['clanid'],$hitlist,"clanhitlist")==0){
									set_module_pref("marked",1,"clanhitlist",$hit);
									output("The hit has been added to the list!");
									set_module_objpref("clans", $session['user']['clanid'],$hitlist,$hit,"clanhitlist");
                                    }else{   
                                        $count=8;
                                        $hitlist="hitlist".$count;                                        
                                        if(get_module_objpref("clans",$session['user']['clanid'],$hitlist,"clanhitlist")==0){
											set_module_pref("marked",1,"clanhitlist",$hit);
											output("The hit has been added to the list!");
											set_module_objpref("clans", $session['user']['clanid'],$hitlist,$hit,"clanhitlist");
                                        }else{
                                            $count=9;
                                            $hitlist="hitlist".$count;                                            
                                            if(get_module_objpref("clans",$session['user']['clanid'],$hitlist,"clanhitlist")==0){
												set_module_pref("marked",1,"clanhitlist",$hit);
												output("The hit has been added to the list!");
												set_module_objpref("clans", $session['user']['clanid'],$hitlist,$hit,"clanhitlist");
                                            }else{
                                                $count=10;
                                                $hitlist="hitlist".$count;
                                                if(get_module_objpref("clans",$session['user']['clanid'],$hitlist,"clanhitlist")==0){
													set_module_pref("marked",1,"clanhitlist",$hit);
													set_module_objpref("clans", $session['user']['clanid'],$hitlist,$hit,"clanhitlist");
                                                    output("The hit has been added to the list!");
                                                }else{
                                                    output("There are not available slots!  You must delete a hit before you can add any more.");
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            addnav("Back to list","runmodule.php?module=clanhitlist");
            if($session['user']['clanrank']>=CLAN_OFFICER) addnav("Edit hit list","runmodule.php?module=clanhitlist&op=edit");
        break;
        
        case "edit":
			addnav("Back to list","runmodule.php?module=clanhitlist");
			addnav("Add to list","runmodule.php?module=clanhitlist&op=add");
			rawoutput("<table>");
        	$delete = translate_inline("Delete");
        	$reason = translate_inline("Edit Reason");            
                $count=1;
                while($count<11){
                rawoutput("<tr class='".($rn%2?"trlight":"trdark")."'>");
                addnav("","runmodule.php?module=clanhitlist&op=del&hit=hitlist$count");
                addnav("","runmodule.php?module=clanhitlist&op=editreason&reason=reason$count");
                $hitlist="hitlist".$count;
                $hit=get_module_objpref("clans",$session['user']['clanid'],$hitlist,"clanhitlist");
                $hitreas="reason".$count;
                require_once("lib/nltoappon.php");
                $reas=get_module_objpref("clans",$session['user']['clanid'],$hitreas,"clanhitlist");
                $sql="select name from ".db_prefix("accounts")." where acctid=$hit";
                $result=db_query($sql);
                $row=db_fetch_assoc($result);
                $name=$row['name'];
                rawoutput("<td><a href='runmodule.php?module=clanhitlist&op=del&hit=hitlist$count'>$delete</a></td><td><a href='runmodule.php?module=clanhitlist&op=editreason&reason=reason$count'>$reason</a></td><td>");
                output_notl("$name");
                rawoutput("</td></tr>");
                rawoutput("<tr><td>");
                output("%s",nltoappon($reas));
                rawoutput("</td></tr>");
                $count++;
                }
                rawoutput("</table>");
        break;
        
        case "del":
            $hit=httpget('hit');
            addnav("Options");
            addnav("Yes, I'm sure","runmodule.php?module=clanhitlist&op=del2&hit=$hit");
            addnav("Navigation");
            addnav("Back to list","runmodule.php?module=clanhitlist");
            output("Are you sure you want to delete that hit?");
        break;
        
        case "editreason":
            $reasonnum=httpget('reason');
            $darea = httppost('darea');
            output("the reason $dareason");
            require_once("lib/nltoappon.php");
            $dareas=get_module_objpref("clans", $session['user']['clanid'],$reasonnum,"clanhitlist");
            if ($darea == ""){
                output("`@The current reason is:`n`c`^%s`c`0",nltoappon($dareas));
                rawoutput("<form action='runmodule.php?module=clanhitlist&op=editreason&reason=$reasonnum' method='POST'>");
                rawoutput("<textarea name='darea' rows='10' cols='60' class='input'>".htmlentities($dareas)."</textarea>");
                rawoutput("<br><input type='submit' class='button' value='".translate_inline("Change")."'></form>");
			}else{
                set_module_objpref("clans",$session['user']['clanid'],$reasonnum,$darea,"clanhitlist");
                output("The reason has been updated.");
			}
            addnav("","runmodule.php?module=clanhitlist&op=editreason&reason=$reasonnum");
            addnav("Back to list","runmodule.php?module=clanhitlist");
            if($session['user']['clanrank']>=CLAN_OFFICER)addnav("Edit the list","runmodule.php?module=clanhitlist&op=edit");
        break;
        
        case "del2":
            $hit=httpget('hit');
            addnav("Navigation");
            addnav("Back to list","runmodule.php?module=clanhitlist");
            if($session['user']['clanrank']>=CLAN_OFFICER)addnav("Edit the list","runmodule.php?module=clanhitlist&op=edit");
            set_module_objpref("clans", $session['user']['clanid'],$hit,0,"clanhitlist");
                            set_module_pref("marked",0,"clanhitlist",$hit);
            output("That hit has been removed.");
        break;        
    }
	page_footer();
}

function clanhitlist_navs() {
	if (httpget('module')=='clanhitlist') {
		addnav("Navigation");
		addnav("C?Return to Guild","clan.php");
		if (httpget('op')!='') addnav("M?Back to list","runmodule.php?module=clanhitlist");
		villagenav();
	} elseif (httpget('module')=='') {
		addnav("Guild Amenities");
		addnav("H?View Hit List","runmodule.php?module=clanhitlist");
	}
}

function show_avatar($id){
    //thanks to anpera for the avatar module... altered copy and paste code from avatars.php below
    if (is_module_active('avatars')){
    if (get_module_pref('user_showavatar','avatars')==1){
            $avatar = get_module_pref("user_avatar","avatars",$id);
            $avatar = stripslashes(preg_replace("'[\"\'\\><@?*&#; ]'","",$avatar));
            if ($avatar <> ""){
                $maxwidth = (get_module_setting("maxwidth","avatars") * .6);
                $maxheight = (get_module_setting("maxheight","avatars") * .6);
                $pic_size = @getimagesize($avatar);
                $pic_width = ($pic_size[0] * .6);
                $pic_height = ($pic_size[1] * .6);
                if ($pic_width > $maxwidth) $pic_width = $maxwidth;
                if ($pic_height > $maxheigt) $pic_height = $maxheight;
                $av .= "<center><IMG SRC=\"".$avatar."\"";
                if ($pic_size[0] > 0) $av .= "title=\"\" alt=\"\" style=\"width: ".$pic_width."px; height: ".$pic_height."px;\"";
                $av .= "></center><BR>\n";
            }
        }
    }
    return $av;
}
?>