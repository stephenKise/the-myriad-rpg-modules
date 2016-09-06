<?php
function quotes_getmoduleinfo(){
    $info = array(
        "name"=>"Random Quotes",
        "version"=>"1.8.1",
        "author"=>"`@CortalUX\n`&Edited by `%Kickme, modified and debugged by `i`)Ae`7ol`&us`i`0",
        "category"=>"Administrative",
        "override_forced_nav"=>true,
        "vertxtloc"=>"http://dragonprime.net/users/kickme/",
        "download"=>"http://simon.welsh.co.nz/logd/quotes.zip",
        "settings"=>array(
            "Quotes - General,title",
            "footer"=>"Show on the Footer of pages,bool|1",
            "header"=>"Show on the Header of pages,bool|0",
            "Quotes - Where,title",
            "epage"=>"Show on every page?,bool|1",
            "(these only applies if you don't want quotes to display on every page:),note",
            "index"=>"Show on index page?,bool|1",
            "village"=>"Show in the village?,bool|1",
            "shades"=>"Show in the shades?,bool|1",
            "mlength"=>"Maximum characters per quote,int|1400",
        ),
        "prefs"=>array(
            "Quotes,title",
            "canadd"=>"Can this user add quotes?,bool|0",
            "note"=>"`@(overridden- all SU_EDIT_COMMENTS admin can do this),hidden",
            "user_squotes"=>"Show Random Quotes?,bool|1",
        ),
    );
    return $info;
}

function quotes_install(){
    // We open the file, then close it. This way, we don't have
    // to use the module settings to store the quotes.
	$f = fopen("modules/quotes.txt","a+");
    fclose($f);
	
    module_addhook("everyfooter");
    module_addhook("shades");
    module_addhook("village");
    module_addhook("everyheader");
    module_addhook("header-shades");
    module_addhook("header-village");
    module_addhook("superuser");
    module_addhook("checkuserpref");
    return true;
}

function quotes_uninstall(){
    if (file_exists("modules/quotes.txt")) unlink("modules/quotes.txt");
    return true;
}

function quotes_dohook($hookname,$args){
    global $session;
	
	$header = get_module_setting('header');
	$footer = get_module_setting('footer');
	$user_squotes = get_module_pref('user_squotes');
	$editor = ($session['user']['superuser']&SU_EDIT_COMMENTS)||get_module_pref('canadd');
	
    $s = false;
	switch ($hookname) {
		case "everyfooter":
			if ($footer && get_module_setting('epage') && $session['user']['loggedin'] == 1) $s = true;
			break;
		case "shades":
			if ($footer && !get_module_setting('epage') && get_module_setting('shades')) $s = true;
			break;
		case "village":
			if ($footer && !get_module_setting('epage') && get_module_setting('village')) $s = true;
			break;
		case "everyheader":
			if ($header && get_module_setting('epage')) $s = true;
			break;
		case "header-shades":
			if ($header && !get_module_setting('epage') && get_module_setting('shades')) $s = true;
			break;
		case "header-village":
			if ($header && !get_module_setting('epage') && get_module_setting('village')) $s = true;
			break;
		case "header-home":
			if ($header && !get_module_setting('epage') && get_module_setting('index')) $s = true;
			break;
		case "superuser":
			if ($editor) {
				addnav("Editors");
				addnav("Quote Editor","runmodule.php?module=quotes&op=list");
			}
			break;
		case "checkuserpref":
			$s = false;
			$args['allow']=false;
			if ($editor) $args['allow']=true;
			break;
	}
    
    if ($user_squotes && $s) {
        if ($editor) {
			output_notl("`n`n`c`@[<a href='runmodule.php?module=quotes&op=list'>Quote Editor</a>]`c",true);
			addnav("","runmodule.php?module=quotes&op=list");
        } else {
			output("`n`n");
		}
        output_notl(quotes_return(), true);
    }
    return $args;
}

function quotes_run(){
    global $session;
	require_once("lib/superusernav.php");
	
    $op = httpget('op');
	if (!get_module_pref('canadd')) check_su_access(SU_EDIT_COMMENTS);
	
	switch ($op) {
		case "multidelete":
			page_header("Multi Quotes Deletion");
			$quotes = httppost('quote');
			if (!is_array($quotes) || count($quotes)<1) {
				output("`b`^You didn't check any quotes for deletion...`b`n`7");
			} else {
				$stuff = explode('|@&@|',file_get_contents("./modules/quotes.txt"));
				$stuff = array_filter($stuff);
				foreach ($quotes as $val) {
					if (isset($stuff[$val])) unset($stuff[$val]);
				}
				$x = implode("|@&@|", $stuff);
				$f = fopen("./modules/quotes.txt","w");
				fwrite($f,$x);
				fclose($f);
				output("`c`b`@Quotes Deleted.`0`b`c");
			}
		case "list":
			if ($op=='list') page_header("Quotes List");
			superusernav();
			addnav("Options");
			addnav("Refresh","runmodule.php?module=quotes&op=list");
			quotes_list();
			page_footer();
		break;
		case "delete":
			popup_header("Delete Quote");
			$stuff = explode('|@&@|',file_get_contents("modules/quotes.txt"));
			$z = "";
			$n = 0;
			$q = httpget('q');
			foreach ($stuff as $val) {
				if ($val!="") {
					$n++;
					if ($n!=$q) $z.="|@&@|".$val;
				}
			}
			$f = fopen("modules/quotes.txt","w");
			fwrite($f,$z);
			fclose($f);
			output("`c`b`@Quote Deleted.`0`b`c");
			popup_footer();
		break;
		case "add":
			$author = $session['user']['login'];
			popup_header("Quote Addition");
			rawoutput("<form action='runmodule.php?module=quotes&op=save' method='POST'>");
			addnav("","runmodule.php?module=quotes&op=save");
			output("`&`bQuoter:`b ");
			rawoutput("<input name='qauth' value='$author'><br/>");
			output("`%Leave the author empty, for no author.`n");
			output("`&`bTip:`b ");
			rawoutput("<input size='70' name='quote' value=''><br/>");
			$save = translate_inline("Save");
			rawoutput("<input type='submit' class='button' value=\"$save\">");
			rawoutput("</form>");
			popup_footer();
		break;
		case "save":
			popup_header("Quote Addition");
			$qauth = quotes_r(httppost('qauth'));
			quotes_stripwhite($qauth);
			$quote = str_replace('\"','"',str_replace("\'","'",quotes_r(httppost('quote'))));
			$x = file_get_contents("modules/quotes.txt")."|@&@|`&".$qauth."`0|@^@|".$quote;
			$f = fopen("modules/quotes.txt","w");
			fwrite($f,$x);
			fclose($f);
			output("`@Quote Added.");
			output_notl("`n<a href='runmodule.php?module=quotes&op=add'>%s</a>",translate_inline("Add another?"),true);
			addnav("","runmodule.php?module=quotes&op=add");
			popup_footer();
		break;
		case "edit":
			popup_header("Quotes Editor");
			$stuff = explode('|@&@|',file_get_contents("modules/quotes.txt"));
			$z = "";
			$n = 0;
			$q = httpget('q');
			foreach ($stuff as $key=>$val) {
				if ($val!="") {
					// $n++;
					if ($key==$q) $z=explode('|@^@|',$val);
				}
			}
			$author = (string)$z[0];
			$quote = $z[1];
			$quote = quotes_format($quote);
			rawoutput("<form action='runmodule.php?module=quotes&op=esave&q=".$q."' method='POST'>");
			addnav("","runmodule.php?module=quotes&op=esave&q=".$q);
			output("`&`bQuoter:`b ");
			rawoutput("<input name='auth' value=\"".quotes_fix(quotes_r($author))."\" maxlength=\"70\"><br/>");
			output("`%Leave the author empty, for no author.`n");
			output("`&`bTip:`b ");
			$mlength = get_module_setting("mlength");
			rawoutput("<input size='70' name='quote' value=\"".htmlentities(quotes_fix(quotes_r($quote)),ENT_QUOTES)."\" maxlength=\"$mlength\"><br/>");
			$save = translate_inline("Save");
			rawoutput("<input type='submit' class='button' value=\"$save\">");
			rawoutput("</form>");
			popup_footer();
		break;
		case "esave":
			popup_header("Quotes Editor");
			$q = httpget('q');
			$auth = quotes_r(httppost('auth'));
			quotes_stripwhite($auth);
			$quote = quotes_r(httppost('quote'));
			$a = "";
			$stuff = explode('|@&@|',file_get_contents("modules/quotes.txt"));
			$n = 0;
			if ($quote=='') {
				output("`@That quote is empty.");
				popup_footer();
				break;
			}
			foreach ($stuff as $val) {
				if ($val!="") {
					$n++;
					if ($n==$q) $a.="|@&@|`&".$auth."`0|@^@|".$quote;
					if ($n!=$q) $a.="|@&@|".$val;
				}
			}
			$f = fopen("modules/quotes.txt","w");
			fwrite($f,$a);
			fclose($f);
			output("`@Quote updated.");
			popup_footer();
		break;
	}
}

function quotes_stripwhite(&$s){
	if ($s[0].$s[1].$s[strlen($s)-2].$s[strlen($s)-1] == "`&`0"){
		$s = substr($s, 2, -2);
		quotes_stripwhite($s);
	}
}

function quotes_r($q,$i=true) {
    $x = str_replace('\"','"',$q);
    if ($i) $y = str_replace("|@^@|","",$x);
    if ($i) $y = str_replace("|@&@|","",$y);
    $y = str_replace("\'","'",$y);
    return $y;
}

function quotes_return() {
    $stuff = explode('|@&@|',file_get_contents("modules/quotes.txt"));
    $n = 0;
    $y = array();
    foreach ($stuff as $val) {
        if ($val!="") {
            $n++;
            $y[$n]=$val;
        }
    }
    if ($n!=0) {
        $i=rand(1,$n);
        $x=$y[$i];
        $v = explode('|@^@|', $x);
        $q = quotes_r($v[1]);
        $q = quotes_fix($q);
        $emots = array("");
        if (is_module_active('emoticons')&&get_module_pref('user_display','emoticons')==1) {
            foreach ($emots as $shrt => $img) {
                $q = str_replace($shrt,"<IMG SRC=\"".$img."\">",$q);
            }
        }
        $v[0]=str_replace("`&amp;","`&",htmlentities($v[0]));
        $z = "`n`c`i`&Tip:`i ".$q."`n`n";
        $z.="`0`c";
    }
	$z = str_replace("/","&#47;",$z);
    return appoencode($z,true);
}

function quotes_list() {
    $stuff = explode('|@&@|',file_get_contents("modules/quotes.txt"));
	$stuff = array_filter($stuff);
	if (!is_array($stuff)) $stuff = array();
	if (count($stuff)){
		$stuff = array_combine(array_reverse(array_keys($stuff)), array_reverse(array_values($stuff)));
	}
	$qu_count = count($stuff);
    $qexist = false;
	
	$perpage = 15;
	$p_count = ceil($qu_count/$perpage);
	$page = ( httpget('page') ? httpget('page') : 1 );
	addnav("Pages");
	for ($i=1; $i<=$p_count; $i++){
		if ($page == $i)
			addnav(array("`#`bPage %s`b`0", $i), "runmodule.php?module=quotes&op=list&page=".$i);
		else
			addnav(array("Page %s", $i), "runmodule.php?module=quotes&op=list&page=".$i);
	}
	addnav("Quote Count");
	addnav("`<$qu_count`<", "");
	
	$qu_min = $perpage * ($page - 1);
	$stuff = array_slice($stuff, $qu_min, $perpage, true);
	
    output_notl("`c`b[<a target='_blank' href='runmodule.php?module=quotes&op=add' onClick=\"".popup("runmodule.php?module=quotes&op=add").";return false;\">".translate_inline("Add a Quote")."</a>]`b`c`n",true);
    if ($qu_count) {
        output("`@There are the following quotes...`n");
    }
    output_notl("<form action='runmodule.php?module=quotes&op=multidelete' method='POST'>",true);
    addnav("","runmodule.php?module=quotes&op=multidelete");
    addnav("","runmodule.php?module=quotes&op=add");
    output_notl("<table><tr class='trhead'><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>",translate_inline("Quote Deletion"),translate_inline("Quote"),translate_inline("Quoter"),translate_inline('Operations'),true);
    foreach ($stuff as $m => $val) {
        if ($val!="") {
            $qexist = true;
            $a = explode('|@^@|',$val);
            if ($a[0]=='') $a[0]='`iNone`i';
            $q =(string)quotes_r($a[1]);
            $q = quotes_fix($q);
            $emots = array("*frown*"=>"images/frown.gif","*grin*"=>"images/grin.gif","*biggrin*"=>"images/grin2.gif","*happy*"=>"images/happy.gif","*laugh*"=>"images/laugh.gif","*love*"=>"images/loveface.gif","*angry*"=>"images/mad.gif","*mad*"=>"images/mad2.gif","*music*"=>"images/musicface.gif","*order*"=>"images/order.gif","*purple*"=>"images/purpleface.gif","*red*"=>"images/redface.gif","*rofl*"=>"images/rofl.gif","*rolleyes*"=>"images/rolleyes.gif","*shock*"=>"images/shock.gif","*shocked*"=>"images/shocked.gif","*slimer*"=>"images/slimer.gif","*spineyes*"=>"images/spineyes.gif","*sarcastic*"=>"images/srcstic.gif","*tongue*"=>"images/tongue.gif","*tongue2*"=>"images/tongue2.gif","*wink*"=>"images/wink.gif","*wink2*"=>"images/wink2.gif","*wink3*"=>"images/wink3.gif","*confused*"=>"images/confused.gif","*embarassed*"=>"images/embarassed.gif","*rose*"=>"images/rose.gif","*drool*"=>"images/drool.gif","*sick*"=>"images/sick.gif","*kiss*"=>"images/kiss.gif","*brokeheart*"=>"images/brokeheart.gif","*wimper*"=>"images/wimper.gif","*whew*"=>"images/whew.gif","*cry*"=>"images/cry.gif","*angel*"=>"images/angel.gif","*nerd*"=>"images/nerd.gif","*stop*"=>"images/stop.gif","*zzz*"=>"images/zzz.gif","*shhh*"=>"images/shhh.gif","*nottalking*"=>"images/nottalking.gif","*party*"=>"images/party.gif","*yawn*"=>"images/yawn.gif","*doh*"=>"images/doh.gif","*clap*"=>"images/clap.gif","*lie*"=>"images/lie.gif","*bateyes*"=>"images/bateyes.gif","*pray*"=>"images/pray.gif","*peace*"=>"images/peace.gif","*nono*"=>"images/nono.gif","*bow*"=>"images/bow.gif","*groove*"=>"images/groove.gif","*giggle*"=>"images/giggle.gif","*yakyak*"=>"images/yakyak.gif");
            if (is_module_active('emoticons')&&get_module_pref('user_display','emoticons')==1) {
                foreach ($emots as $shrt => $img) {
                    $q = str_replace($shrt,"<IMG SRC=\"".$img."\">",$q);
                }
            }
            $a = (string)quotes_r($a[0]);
			
            output_notl("<tr class='".($m%2?"trlight":"trdark")."'>",TRUE);
			output_notl("<td align='center'>[ <input type='checkbox' name='quote[]' value='$m'> ]</td>",TRUE);
			output_notl("<td>`^%s</td>",str_replace("/","&#47;",$q),TRUE);
			output_notl("<td>`@%s</td>",$a,TRUE);
			output_notl("<td>`# [<a target='_blank' href='runmodule.php?module=quotes&op=edit&q=".$m."' onClick=\"".popup("runmodule.php?module=quotes&op=edit&q=".$m).";return false;\">".translate_inline("Edit")."</a>] - ",TRUE);
			output_notl("[<a target='_blank' href='runmodule.php?module=quotes&op=delete&q=".$m."' onClick=\"".popup("runmodule.php?module=quotes&op=delete&q=".$m).";return false;\">".translate_inline("Delete")."</a>] </td></tr>",TRUE);
			
			addnav("","runmodule.php?module=quotes&op=edit&q=".$m);
            addnav("","runmodule.php?module=quotes&op=delete&q=".$m);
        }
    }
    if (!$qexist) {
        output_notl("<tr class='trhilight'><td colspan='4'>`c%s`c</td></tr>",translate_inline("There are no quotes."),true);
    }
    output_notl('</table>',true);
    if ($qexist) {
        $quote = HTMLEntities(translate_inline("Delete Checked Quotes"));
        output_notl("<input type='submit' class='button' value=\"".$quote."\"></form>",true);
    }
}

function quotes_fix($q) {
    $q = html_entity_decode($q);
    return $q;
}

function quotes_format($q){
	$replace1 = array("<b>", "</b>", "<i>", "</i>", "<s>", "</s>", "<u>", "</u>", "<br>", "</span>");
	$replace2 = array("`b", "`b", "`i", "`i", "`s", "`s", "`u", "`u", "`n", "");
	$q = str_replace($replace1, $replace2, $q);
	$colors = array( "1" => "colDkBlue", "2" => "colDkGreen", "3" => "colDkCyan", "4" => "colDkRed", "5" => "colDkMagenta", "6" => "colDkYellow", "7" => "colDkWhite", "~" => "colBlack", "!" => "colLtBlue", "@" => "colLtGreen", "#" => "colLtCyan", "\$" => "colLtRed", "%" => "colLtMagenta", "^" => "colLtYellow", "&" => "colLtWhite", ")" => "colLtBlack", "e" => "colDkRust", "E" => "colLtRust", "g" => "colXLtGreen", "G" => "colXLtGreen", "j" => "colMdGrey", "J" => "colMdBlue", "k" => "colaquamarine", "K" => "coldarkseagreen", "l" => "colDkLinkBlue", "L" => "colLtLinkBlue", "m" => "colwheat", "M" => "coltan", "p" => "collightsalmon", "P" => "colsalmon", "q" => "colDkOrange", "Q" => "colLtOrange", "R" => "colRose", "T" => "colDkBrown", "t" => "colLtBrown", "V" => "colBlueViolet", "v" => "coliceviolet", "x" => "colburlywood", "X" => "colbeige", "y" => "colkhaki", "Y" => "coldarkkhaki", "D" => "colTardisBlue" );
	foreach ($colors as $logd => $html){
		$q = str_replace("<span class='".$html."'>", "`".$logd, $q);
	}
	return $q;
}
?>