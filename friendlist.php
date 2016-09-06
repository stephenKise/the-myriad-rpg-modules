<?php
//version info is in readme.txt
require_once("lib/http.php");

function friendlist_getmoduleinfo(){
	$info = array(
		"name"=>"Friend List",
		"version"=>"2.3",
		"author"=>"`@CortalUX `^modified by `2Oliver Brendel `7& `i`b`&Xpert`i`b",
		"override_forced_nav"=>true,
		"category"=>"Mail",
		"download"=>"http://dragonprime.net",
		"settings"=>array(
			"Friend List - Settings,title",
			"allowStat"=>"Allow users to see the amount of online friends they have?,bool|1",
			"showType"=>"Allow users to see the names of their online friends in their charstats?,bool|1",
			"allowType"=>"Can users see the names of users- or just their login?,enum,0,Just their Login,1,Names as well|0",
			"linkType"=>"What should names in the friend list do when you click them?,enum,0,Nothing,1,Send Mails,2,Link to Bio (on some pages)|1",
			"`\$Do not touch below,note",
			"check_head"=>"`^Which stat heading will this information come under?,enum,Vital Info,Vital Info,Personal Info,Personal Info,Extra Info,Extra Info,Friends,Friends|Vital Info",
		),
		"prefs"=>array(
			"Friend List,title",
			"friends"=>"`^Friends?,text|",
			"ignored"=>"`^Ignored this user?,text|",
			"iveignored"=>"`^This user's ignored?,text|",
			"request"=>"`^Requests?,text|",
			"nnames"=>"Nicknames,text|a:0:{}",
			"note"=>"`@(pipe seperated for each user id),viewonly",
			"note2"=>"`\$`bItems below are for the user to edit and make no sense if you're in the admin area:`b,viewonly",
			"check_show"=>"`@Do you want to see anything in your character stats?,bool|1",
			"check_login"=>"`@Do you want to see your friends names under your character stats?,bool|1",
			"check_names"=>"`@Do you want to see your friends names under your character stats?,enum,0,No Thanks,1,Just their short name,2,Their full names|1",
		),
	);
	return $info;
}

function rexplode($array) {
	if ($array=='') return array();
		else
		return explode("|",$array);
}
function rimplode($array) {
	if ($array==array()) return "";
		else {
			$array=array_unique($array);
			$array=array_diff($array,array(""));
			return implode("|",$array);
		}
}


function friendlist_install(){
	if (!is_module_active('friendlist')){
		output("`n`c`b`QFriend List Module - Installed`0`b`c");
	}else{
		output("`n`c`b`QFriend List Module - Updated`0`b`c");
	}
	module_addhook("checkuserpref");
	module_addhook("faq-toc");
	module_addhook_priority("mailfunctions", "75");
	module_addhook("charstats");
	module_addhook("biotop");
	return true;
}

function friendlist_uninstall(){
	output("`n`c`b`QFriend List Module - Uninstalled`0`b`c");
	return true;
}

function friendlist_dohook($hookname,$args){
	global $session,$SCRIPT_NAME,$battle;
	switch($hookname){
		case "checkuserpref":
			$args['allow']=false;
			if (get_module_setting('allowStat')&&$args['name']=="check_show") {
				$args['allow']=true;
			} elseif (get_module_Setting('showType')&&get_module_pref('check_show')) {
				if ($args['name']=="check_login"&&get_module_setting('allowType')==0||$args['name']=="check_names"&&get_module_setting('allowType')==1||$args['name']=="check_head") {
					$args['allow']=true;
				}
			}
		break;
		case "faq-toc":
			$t = translate_inline("`@Frequently Asked Questions on Friend Lists`0");
			output_notl("&#149;<a href='runmodule.php?module=friendlist&op=faq'>$t</a><br/>", true);
			addnav("","runmodule.php?module=friendlist&op=faq");
		break;
		case "mailfunctions":
				
				output("&bull;<a href='runmodule.php?module=friendlist&op=list'>`^Friends List</a>",TRUE);
					$request = rexplode(get_module_pref('request'));
					$request=array_unique($request);
						$sql = "SELECT name FROM ".db_prefix("accounts")." WHERE acctid IN (".implode(',',$request).") AND locked=0 ORDER BY login";
						$result = db_query($sql);
						$awaiting = db_num_rows($result);
						if ($awaiting!=0){
							output("`4- `b`#NEW!`b");
						}
						output("`n");
				if (httpget('op')=='send') {
					$sql = "SELECT acctid,name FROM ".db_prefix("accounts")." WHERE login='".httppost('to')."'";
					$result = db_query($sql);
					if (db_num_rows($result)>0) {
						$row = db_fetch_assoc($result);
						if (in_array($session['user']['acctid'],explode('|',get_module_pref('iveignored','friendlist',$row['acctid'])))) {
							popup_header("Friends List");
							output_notl("`c`^[`%");
							$t = translate_inline("Back to your Mail");
							rawoutput("<a href='mail.php'>$t</a>");
							output_notl("`^]`c`Q`n");
							$info = translate_inline("%s`Q has ignored you, so you cannot send %s`Q Messages.");
							$info = str_replace('%s',$row['name'],$info);
							output_notl($info);
							popup_footer();
							die();
						}
					}
				}
		break;
		case "charstats":
			if (get_module_setting('allowStat')&&get_module_pref('check_show')) { // I could so other 'if' checks here, but if admin have it turned off, it'd increase load, when it isn't needed anyway
				$friends = rexplode(get_module_pref('friends'));
				$nnames = unserialize(get_module_pref('nnames'));
				if (!$nnames) $nnames = array();
				$x=0;
				$last = date("Y-m-d H:i:s", strtotime("-".getsetting("LOGINTIMEOUT", 900)." sec"));
				$addon="";
				if (get_module_setting("allowType")==0&&get_module_pref("check_login")==1||get_module_setting("allowType")==1&&get_module_pref("check_names")==1) {
					$addon=",login";
				} elseif (get_module_setting("allowType")==1&&get_module_pref("check_names")==2) {
					$addon=",name";
				}
				$onlinelist="";
				$bl=false;
				if ($battle===false||!isset($battle)||empty($battle)) {
					if (httpget('module')==''&&$session['user']['specialinc']==''&&$session['user']['specialmisc']=='') {
						$bl=true;
					}
				}
				if (implode(",",$friends)!='') {
					$sql = "SELECT acctid,loggedin,laston$addon FROM ".db_prefix("accounts")." WHERE acctid IN (".implode(",",$friends).") AND locked=0";
					$result = db_query_cached($sql,"friendliststat-".$session['user']['acctid'],60);
					while ($row=db_fetch_assoc($result)) {
						$loggedin=$row['loggedin'];
						if ($row['laston']<$last) {
							$loggedin=false;
						}
						if ($loggedin) {
							$x++;
							if ($addon!="") {
								if ($onlinelist!="") $onlinelist.=", ";
								if (get_module_setting('linkType')==1) {
									$onlinelist.="<a href='mail.php?op=write&to={$row['login']}' class='colLtWhite' target='_blank' onClick=\"".popup("mail.php?op=write&to={$row['login']}").";return false;\">";
								} elseif (get_module_setting('linkType')==2&&$bl) {
									$link="bio.php?char=".rawurlencode($row['login'])."&ret=".URLEncode($_SERVER['REQUEST_URI']);
									$onlinelist.="<a href='$link' class='colLtWhite'>";
									addnav($link,"");
								}
								if (!isset($nnames[$row['acctid']])){
									if ($addon==",login") {
										$onlinelist.=sanitize($row['login']);
									} else {
										$onlinelist.=sanitize($row['name']);
									}
								} else {
									$onlinelist.=$nnames[$row['acctid']];
								}
								if (get_module_setting('linkType')==1||get_module_setting('linkType')==2&&$bl) $onlinelist.="</a>";
							}
						}
					}
				}
				$onlinelist.=".";
				if ($onlinelist!=".") {
				setcharstat(translate_inline(get_module_setting('check_head')),translate_inline("Friends"),$onlinelist);
				} else { 
				setcharstat(translate_inline(get_module_setting('check_head')),translate_inline("Friends"), "`i`&No one`i");
				}
				invalidatedatacache('friendliststat-'.$session['user']['acctid']);
			}
		break;
		case "biotop":
			addnav("Friend List");
			$ignorearray = explode("|",get_module_pref("iveignored","friendlist"));
			if (in_array($args['acctid'],$ignorearray)){
				addnav("=?`\$Unignore User","runmodule.php?module=friendlist&op=unignore&ac=".$args['acctid']."&bio=yes");
			}else{
			addnav("-?`\$Ignore User","runmodule.php?module=friendlist&op=ignore&ac=".$args['acctid']."&bio=yes");
			}
			
			$friendarray = explode("|",get_module_pref("friends","friendlist"));
			if (in_array($args['acctid'],$friendarray)){
				addnav("-?`\$Remove User","runmodule.php?module=friendlist&op=deny&ac=".$args['acctid']."&bio=yes");
			}else{
				addnav("+?`@Add User","runmodule.php?module=friendlist&op=request&ac=".$args['acctid']."&bio=yes");
			}
		break;
	}
	return $args;
}

function friendlist_run(){
	global $session;
	$bio = httpget('bio');
	$op = httpget('op');
	if ($op=='faq'){
		popup_header("Frequently Asked Questions on Friend Lists");
	} elseif ($bio=='yes'){
		page_header('Friends List');
		addnav("Return to their bio","runmodule.php?module=newbio&char=".httpget('ac'));
	}	else{
		popup_header("Friends List");
	}
	require_once("modules/friendlist/friendlist_$op.php");
	if ($op=='deny') {
		friendlist_deny();
		$op="list";
		require_once("modules/friendlist/friendlist_list.php");
	}
	$fname="friendlist_".$op;
	$fname();
	if ($bio!='yes') popup_footer();
		else page_footer();
}
?>
