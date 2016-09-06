<?php
function search_modules_getmoduleinfo(){
	$info = array(
		"name" => "Search Modules",
		"author" => "`i`)Ae`7ol`&us`i`0, using code from modules.php",
		"version" => "1.0",
		"category" => "Administrative",
	);
	return $info;
}

function search_modules_install(){
	module_addhook("header-modules");
	return true;
}

function search_modules_uninstall(){
	return true;
}

function search_modules_dohook($hookname, $args){
	output_notl("<form action='runmodule.php?module=search_modules' method='post'>", true);
	output_notl("Search: <input name='search' />", true);
	output_notl("<input type='submit' class='button' value='".translate_inline("Search")."' />", true);
	output_notl("</form><br />", true);
	addnav("", "runmodule.php?module=search_modules");
	return $args;
}

function search_modules_run(){
	global $session;
	require_once("lib/sanitize.php");
	require_once("lib/superusernav.php");
	
	page_header("Search Modules");
	check_su_access(SU_MANAGE_MODULES);
	
	superusernav();
	addnav("Module Categories");
	
	$op = httpget('op');
	$order = httpget('order');
	$sortby = httpget('sortby');
	$module = httpget('submodule');
	
	$deactivate = translate_inline("Deactivate");
	$activate = translate_inline("Activate");
	$uninstall = translate_inline("Uninstall");
	$reinstall = translate_inline("Reinstall");
	$strsettings = translate_inline("Settings");
	$strnosettings = translate_inline("`\$No Settings`0");
	$uninstallconfirm = translate_inline("Are you sure you wish to uninstall this module?  All user preferences and module settings will be lost.  If you wish to temporarily remove access to the module, you may simply deactivate it.");
	
	$ops = translate_inline("Options");
	$status = translate_inline("Status");
	$active = translate_inline("`@Active`0");
	$inactive = translate_inline("`\$Inactive`0");
	$mname = translate_inline("Module Name");
	$categ = translate_inline("Category");
	$mauth = translate_inline("Module Author");
	
	output_notl("<form action='runmodule.php?module=search_modules' method='post'>", true);
	output_notl("Search: <input name='search' />", true);
	output_notl("<input type='submit' class='button' value='".translate_inline("Search")."' />", true);
	output_notl("</form><br />", true);
	addnav("", "runmodule.php?module=search_modules");
	
	if ($op == 'mass'){
		if (httppost("activate")) $op = "activate";
		if (httppost("deactivate")) $op = "deactivate";
		if (httppost("uninstall")) $op = "uninstall";
		if (httppost("reinstall")) $op = "reinstall";
		if (httppost("install")) $op = "install";
		$module = httppost("module");
	}
	$theOp = $op;
	if (is_array($module)){
		$modules = $module;
	}else{
		if ($module) $modules = array($module);
		else $modules = array();
	}
	reset($modules);
	while (list($key,$module)=each($modules)){
		$op = $theOp;
		output("`2Performing `^%s`2 on `%%s`0`n", translate_inline($op), $module);
		if($op=="install"){
			if (install_module($module)){

			}else{
				httpset('cat','');
			}
			$op="";
			httpset('op', "");
		}elseif($op=="uninstall"){
			if (uninstall_module($module)) {
			} else {
				output("Unable to inject module.  Module not uninstalled.`n");
			}
			$op="";
			httpset('op', "");
		}elseif($op=="activate"){
			activate_module($module);
			$op="";
			httpset('op', "");
			invalidatedatacache("inject-$module");
		}elseif($op=="deactivate"){
			deactivate_module($module);
			$op="";
			httpset('op', "");
			invalidatedatacache("inject-$module");
		}elseif($op=="reinstall"){
			$sql = "UPDATE " . db_prefix("modules") . " SET filemoddate='0000-00-00 00:00:00' WHERE modulename='$module'";
			db_query($sql);
			// We don't care about the return value here at all.
			injectmodule($module, true);
			$op="";
			httpset('op', "");
			invalidatedatacache("inject-$module");
		}
	}

	$install_status = get_module_install_status();
	$uninstmodules = $install_status['uninstalledmodules'];
	$seencats = $install_status['installedcategories'];
	$ucount = $install_status['uninstcount'];

	ksort($seencats);
	addnav(array("U?Uninstalled - (%s module%s)", $ucount, $ucount==1?"":"s"), "modules.php");
	reset($seencats);
	foreach ($seencats as $cat=>$count) {
		addnav(array(" ?%s - (%s module%s)", $cat, $count, $count==1?"":"s"), "modules.php?cat=$cat");
	}
	
	
	$i = 0;
	$search = httppost('search');
	if (!$search) $search = httpget('search');
	
	if ($search){
		if (!$sortby) $sortby = 'formalname';
		
		$sql = "SELECT * FROM " . db_prefix("modules") . " WHERE formalname LIKE '%$search%' OR modulename LIKE '%$search%' ORDER BY ".$sortby." ".($order?"DESC":"ASC");
		$result = db_query($sql);
		if (db_num_rows($result)){
			rawoutput("<form action='runmodule.php?module=search_modules&op=mass&search=$search' method='POST'>");
			addnav("","runmodule.php?module=search_modules&op=mass&search=$search");
			output("`&`bSearch string: %s`b`0", $search);
			rawoutput("<table border='0' cellpadding='2' cellspacing='1' bgcolor='#999999'>");
			rawoutput("<tr class='trhead'><td>");
			rawoutput("<input type='checkbox' onClick=\"
				var elem = document.getElementsByTagName('input');
				for(var i = 0; i < elem.length; i++) {
					if(elem[i].name == 'module[]') elem[i].checked = this.checked;
				}\"/>");
			rawoutput("</td><td>$ops</td>
				<td>
					$status<br />
					<a href='runmodule.php?module=search_modules&sortby=active&search=$search'>&#9650;</a>
					<a href='runmodule.php?module=search_modules&sortby=active&search=$search&order=desc'>&#9660;</a>
				</td>
				<td>
					$mname<br />
					<a href='runmodule.php?module=search_modules&sortby=formalname&search=$search'>&#9650;</a>
					<a href='runmodule.php?module=search_modules&sortby=formalname&search=$search&order=desc'>&#9660;</a>
				</td>
				<td>
					$categ<br />
					<a href='runmodule.php?module=search_modules&sortby=category&search=$search'>&#9650;</a>
					<a href='runmodule.php?module=search_modules&sortby=category&search=$search&order=desc'>&#9660;</a>
				</td>
				<td>$mauth</td>
			</tr>");
			addnav("","runmodule.php?module=search_modules&sortby=formalname&search=$search");
			addnav("","runmodule.php?module=search_modules&sortby=formalname&search=$search&order=desc");
			addnav("","runmodule.php?module=search_modules&sortby=active&search=$search");
			addnav("","runmodule.php?module=search_modules&sortby=active&search=$search&order=desc");
			addnav("","runmodule.php?module=search_modules&sortby=category&search=$search");
			addnav("","runmodule.php?module=search_modules&sortby=category&search=$search&order=desc");
			while ($row = db_fetch_assoc($result)){
				rawoutput("<tr class='".($i%2?"trlight":"trdark")."'>",true);
				rawoutput("<td nowrap valign='top'>");
				rawoutput("<input type='checkbox' name='module[]' value=\"{$row['modulename']}\">");
				rawoutput("</td><td valign='top' nowrap>[");
				if ($row['active']){
					output_notl("<a href='runmodule.php?module=search_modules&op=deactivate&submodule={$row['modulename']}&search=$search'>".$deactivate."</a>", TRUE);
					addnav("","runmodule.php?module=search_modules&op=deactivate&submodule={$row['modulename']}&search=$search");
				}else{
					output_notl("<a href='runmodule.php?module=search_modules&op=activate&submodule={$row['modulename']}&search=$search'>".$activate."</a>", TRUE);
					addnav("","runmodule.php?module=search_modules&op=activate&submodule={$row['modulename']}&search=$search");
				}
				output_notl("| <a href='runmodule.php?module=search_modules&op=uninstall&submodule={$row['modulename']}&search=$search' onClick='return confirm(\"$uninstallconfirm\");'>".$uninstall."</a>", TRUE);
				output_notl("| <a href='runmodule.php?module=search_modules&op=reinstall&submodule={$row['modulename']}&search=$search'>".$reinstall."</a>", TRUE);
				
				addnav("","runmodule.php?module=search_modules&op=uninstall&submodule={$row['modulename']}&search=$search");
				addnav("","runmodule.php?module=search_modules&op=reinstall&submodule={$row['modulename']}&search=$search");

				if ($session['user']['superuser'] & SU_EDIT_CONFIG) {
					if (strstr($row['infokeys'], "|settings|")) {
						output_notl("| <a href='configuration.php?op=modulesettings&module={$row['modulename']}'>".$strsettings."</a>", TRUE);
						addnav("","configuration.php?op=modulesettings&module={$row['modulename']}");
					} else {
						output_notl("| %s", $strnosettings);
					}
				}

				rawoutput("] </td><td valign='top'>");
				output_notl($row['active']?$active:$inactive);
				rawoutput("</td><td nowrap valign='top'><span title=\"".
						(isset($row['description'])&&$row['description']?
						 $row['description']:sanitize($row['formalname']))."\">");
				output_notl("%s %s", $row['formalname'], $row['version']);
				rawoutput("<br>");
				output_notl("(%s) ", $row['modulename']);
				rawoutput("</span></td><td valign='top'>");
				output_notl("%s", $row['category']);
				rawoutput("</td><td valign='top'>");
				output_notl("`#%s`0", $row['moduleauthor'], true);
				rawoutput("</td></tr>");
				$i++;
			}
			rawoutput("</table>");
			rawoutput("<input type='submit' name='activate' class='button' value='$activate'>");
			rawoutput("<input type='submit' name='deactivate' class='button' value='$deactivate'>");
			rawoutput("<input type='submit' name='reinstall' class='button' value='$reinstall'>");
			rawoutput("<input type='submit' name='uninstall' class='button' value='$uninstall'>");
			rawoutput("</form>");
		} else {
			output("`n`#`iNone found for %s.`i`0`n`n", $search);
		}
	} else {
		output("`n`4`iNothing in search..`i`0`n`n");
	}
	
	page_footer();
}
?>