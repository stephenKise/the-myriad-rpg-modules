<?php

//usable with 1.1.1 and up too
function clanranks_getmoduleinfo(){
	$info = array(
	    "name"=>"Clan Ranks",
		"description"=>"This gives clans the possibility to have up to 30 own defined ranks",
		"version"=>"1.01",
		"author"=>"`2Oliver Brendel`0",
		"category"=>"Clan",
		"download"=>"http://dragonprime.net/users/Nightborn/clanranks.zip",
		/*"settings"=>array(
		"Guild Ranks - Preferences,title",
		/*"dks"=>"At how many DKs will the CV be available?,range,1,100,1|50",
		"showclanranks"=>"Show a title in the users bioinfo?,bool|1",
		"Note: These setting will be overridden once you select the field to be kept in the editor,note",
		"startgold"=>"How many gold will the resetted player have,int|500",
		"startgems"=>"How many gems will the resetted player have,int|0",
		"maxhitpoints"=>"How many maxhitpoints will the resetted player have,int|10", 

		),*/
		/*"prefs"=>array(
		    "clanranks Vitae - User prefs,title",
			"circuli"=>"Number of CVs the player did,int|0",
			"Note: don't change this if you don't need to... it is changed by the module!,note",

		),*/
		);
    return $info;
}

function clanranks_install(){
	module_addhook_priority("clanranks",50);
	if (is_module_active("clanranks")) debug("Guild Ranks updated");

	return true;
}

function clanranks_uninstall()
{
  output_notl ("Performing Uninstall on Guild Ranks. Thank you for using!`n`n");
  return true;
}


function clanranks_dohook($hookname, $args){
	global $session,$SCRIPT_NAME;
		switch($hookname) {
			case "clanranks":
				if (httpget('op')=="" && ($SCRIPT_NAME=="clan.php" || httpget('module')=="clanranks")) {
					tlschema("clans");
					addnav("Management");
					tlschema();
					$sql="SELECT clanshort FROM ".db_prefix("clans")." WHERE clanid={$args['clanid']};";
					$result=db_query($sql);
					$row=db_fetch_assoc($result);
					if ($session['user']['clanid']>0) addnav(array("`^%s's Titles`0",$row['clanshort']),"runmodule.php?module=clanranks&op=viewtitles&clanid={$args['clanid']}");
					if ($session['user']['clanrank']>CLAN_OFFICER && $session['user']['clanid']!=0 && !defined("ALREADY_DID_THESE_CLANRANKS")) {
						define("ALREADY_DID_THESE_CLANRANKS",1);	//kill multiple execution
						addnav("Management");
						addnav("Edit Ranks","runmodule.php?module=clanranks&op=editor");
					}
				}
				require_once("modules/clanranks/func.php");
				$array=clanranks_getallranks($args['clanid'],$args['ranks']);
				$args['ranks']=$array;
				break;
		}
	return $args;
}

function clanranks_run(){
	global $session;
	$dks=get_module_setting("dks");
	$op=httpget('op');
	$mode=httpget('mode');
	$clanid=$session['user']['clanid'];
	require_once("modules/clanranks/func.php");
	switch ($op) {
		case "viewtitles":
			$id=httpget('clanid');
			if ($id) $clanid=$id;
			page_header("Guild Ranks");
			addnav("Back to the Guild Hall","clan.php");
			output("`b`gegg`b `\$Overview:`n");
			$dks = translate_inline("# of Guild Rank");
			$mtit = translate_inline("Rank");
			rawoutput("<table border=0 cellspacing=0 cellpadding=2 width='100%' align='center'>");
			rawoutput("<tr class='trhead'><td>$dks</td><td>$mtit</td></tr>"); //<td>$ftit</td>
			$titlearray=clanranks_getallranks($clanid);
			while (list($key,$rank) = each ($titlearray)) {
				rawoutput("<tr class='".($i%2?"trlight":"trdark")."'>");
				rawoutput("<td>");
				output_notl("`&%s`0",$key);
				rawoutput("</td><td>");
				output_notl("`2%s`0",translate_inline($rank));
				rawoutput("</td></tr>");
				$i++;
			}
			rawoutput("</table>");
			break;
		case "editor":
			page_header("Guild Rank Editor");
			addnav("Back to the Guild","clan.php");
			addnav("Guild Ranks");
			addnav("Guild Rank Editor","runmodule.php?module=clanranks&op=editor");
			addnav("Operations");
		//mainly copy+paste from titleedit.php
			$id = httpget('id');
			$editarray=array(
				"Titles,title",
				"titleid"=>"# of Coporation Rank,viewonly",
				"title"=>"Rank Title,text|",
				//"female"=>"Female Title,text|",
				);
			$title=httpget('title');
			$titleid=httppost('titleid');
			if ($title=="save") {
				$titleid=httppost('titleid');
				$title = httppost('title');
				$pretitle=stripslashes(rawurldecode(httpget('hardsettitle')));
				if ($pretitle) $title=$pretitle;
				//$female = httppost('female');
				if ($id == -1) {
					if (clanranks_get_title($titleid,$clanid)) {
						$here=translate_inline("here");
						output("`^Title already exists. Nothing saved, choose a number that is not occupied`nIf you want to change the current title displayed below, please click %s.`0","<a href=runmodule.php?module=clanranks&op=editor&title=save&id=$titleid&hardsettitle=".rawurlencode($title).">$here</a>",true);
						addnav("","runmodule.php?module=clanranks&op=editor&title=save&id=$titleid&hardsettitle=".rawurlencode($title));
						$formertitle=$title;
						addnav(array("Change title `^%s`0 to `2%s`0",$titleid,$formertitle),"runmodule.php?module=clanranks&op=editor&title=save&id=$titleid&hardsettitle=".rawurlencode($title));
						$title="add";
						} else {
						clanranks_set_title($titleid,$clanid,$title);
						//clanranks_set_title($titleid,$male,$female);
						output("`^New title added.`0");
						$title = "";
						}
					}else {
						clanranks_set_title($id,$clanid,$title);
						//clanranks_set_title($tempid,$male,$female);
						output("`^Title modified.`0");
						$title = "";
					}
		} elseif ($title=="delete") {
				$sql = "DELETE FROM ".db_prefix("module_objprefs")." WHERE modulename='clanranks' AND objtype='clanranks_title' AND setting=$clanid AND objid=$id";
				$result=db_query($sql);
				output("`^Title deleted.`0");
				$title = "";
			}
			switch ($title) {
				case"add":case "edit":
				require_once("lib/showform.php");
				if ($title=="edit"){
					$titlename=clanranks_get_title($id,$clanid);
					//$female=clanranks_get_title($id,"female");
					$row = array('titleid'=>$id, 'title'=>$titlename);//, 'female'=>$female);
				} elseif ($title=="add") {
					$row = array('titleid'=>($titleid?$titleid:1), 'title'=>$formertitle);//, 'female'=>'');
					$editarray['titleid']="# of Coporation Rank,range,1,30,1";
					$id = -1;
				}
				rawoutput("<form action='runmodule.php?module=clanranks&op=editor&title=save&id=$id' method='POST'>");
				addnav("","runmodule.php?module=clanranks&op=editor&title=save&id=$id");
				showform($editarray,$row);
				rawoutput("</form>");
				title_help();
				output_notl("`n`n");
				output("`\$Short Overview:`n");
				$dks = translate_inline("# of Guild Rank");
				$mtit = translate_inline("Rank");
				rawoutput("<table border=0 cellspacing=0 cellpadding=2 >");
				rawoutput("<tr class='trhead'><td>$dks</td><td>$mtit</td></tr>"); //<td>$ftit</td>
				$titlearray=clanranks_getallranks($clanid);
				while (list($key,$rank) = each ($titlearray)) {
					rawoutput("<tr class='".($i%2?"trlight":"trdark")."'>");
					rawoutput("<td>");
					output_notl("`&%s`0",$key);
					rawoutput("</td><td>");
					output_notl("`2%s`0",translate_inline($rank));
					rawoutput("</td></tr>");
					$i++;
				}
				rawoutput("</table>");
				break;
			default:
				output("`@`c`b-=Title Editor=-`b`c");
				$ops = translate_inline("Ops");
				$dks = translate_inline("# of Guild Rank");
				$mtit = translate_inline("Rank");
				//$ftit = translate_inline("Female Title");
				$edit = translate_inline("Edit");
				$del = translate_inline("Delete");
				$delconfirm = translate_inline("Are you sure you wish to delete this title?");
				rawoutput("<table border=0 cellspacing=0 cellpadding=2 width='100%' align='center'>");
				rawoutput("<tr class='trhead'><td>$ops</td><td>$dks</td><td>$mtit</td></tr>"); //<td>$ftit</td>
				$titlearray=clanranks_getallranks($clanid);
				while (list($key,$rank) = each ($titlearray)) {
					rawoutput("<tr class='".($i%2?"trlight":"trdark")."'>");
					rawoutput("<td>[<a href='runmodule.php?module=clanranks&op=editor&title=edit&id=$key'>$edit</a>|<a href='runmodule.php?module=clanranks&op=editor&title=delete&id=$key' onClick='return confirm(\"$delconfirm\");'>$del</a>]</td>");
					addnav("","runmodule.php?module=clanranks&op=editor&title=edit&id=$key");
					addnav("","runmodule.php?module=clanranks&op=editor&title=delete&id=$key");
					rawoutput("<td>");
					output_notl("`&%s`0",$key);
					rawoutput("</td><td>");
					output_notl("`2%s`0",translate_inline($rank));
					//rawoutput("</td><td>");
					//output_notl("`6%s`0",clanranks_get_title($i,"female"));
					rawoutput("</td></tr>");
					$i++;
				}
				rawoutput("</table>");
				addnav("Functions");
				addnav("Add a Title", "runmodule.php?module=clanranks&op=editor&title=add");
				title_help();
			break;
			}
				//addnav("Operations");
				//addnav("Rank Editor","runmodule.php?module=clanranks&op=editor");
				break;
		default:

		break;
	}
	page_footer();
}


?>