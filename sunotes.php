<?php
define("SUNOTES_ARRAY_DELIM",":|:");
define("SUNOTES_NOTE_DELIM","/,/");

function sunotes_getmoduleinfo(){
        $info = array(
            "name"=>"Admin Notes",
            "author"=>"Kuma Waru",
            "version"=>"0.1",
            "category"=>"Administrative",
			"download"=>"",
			"vertxtloc"=>"",
			"description"=>"User with Moderate Comments Flag can add notes to any user's bio, visible only to other CMs.",
        );
    return $info;
}

function sunotes_install(){
    module_addhook_priority("biotop",60);
    return true;
}

function sunotes_uninstall(){
    return true;
}

function sunotes_dohook($hookname,$args){
    global $session; 
    switch ($hookname){
        case "biotop":
	   if ($session['user']['superuser'] & SU_EDIT_COMMENTS) {
		$id = $args['acctid'];		
		if ((httpget('act')=='addnote') && (httppost('sunote')!='')) {
			sunotes_addnote(httppost('sunote'),$id,$session['user']['login']);
		}
		rawoutput("<table width='90%'><tr><td colspan='4'></td></tr>");
		if (httpget('act')=='delnote'){
			$noteid = httpget('noteid');
			sunotes_delnote($id, $noteid);
		}					
		$notesarray = sunotes_explode($id);
		if ($notesarray != ''){
			foreach ($notesarray as $key => $line){
				rawoutput("<tr class='".($key%2?"trdark":"trlight")."'><td width='6%'>");
				$del = translate_inline("Delete");
				rawoutput("<a href='bio.php?char=".rawurlencode($args['login'])."&ret=".rawurlencode(httpget("ret"))."&act=delnote&noteid=".$key."'>".$del."</a>");
				addnav("","bio.php?char=".rawurlencode($args['login'])."&ret=".rawurlencode(httpget("ret"))."&act=delnote&noteid=".$key);
				rawoutput("</td><td width='10%'>".$line[0]."</td><td width='15%'>".$line[1]."</td><td>");
				output(stripslashes($line[2]));
				rawoutput("</td></tr>");
			}
		}
		rawoutput("</table>");
		
		rawoutput("<form action='bio.php?char=".rawurlencode($args['login'])."&ret=".rawurlencode(httpget("ret"))."&act=addnote' method='post'>");
		output("`nAdd note:");
		rawoutput("<input id='sunote' name='sunote' width=15>");
		rawoutput("<input type='submit' name='addnote' class='button' value='".translate_inline("Add")."'>");
		addnav("","bio.php?char=".rawurlencode($args['login'])."&ret=".rawurlencode(httpget("ret"))."&act=addnote");
		rawoutput("</form>");
		
	}
        break;
    }
    return $args;
}

function sunotes_run(){
}

function sunotes_addnote($newnote,$id,$who){
	$notes = get_module_pref("sunotes",false,$id);
	$notes .= date("Y-m-d").SUNOTES_NOTE_DELIM.$who.SUNOTES_NOTE_DELIM.$newnote.SUNOTES_ARRAY_DELIM;
	set_module_pref("sunotes",$notes,false,$id);
}

function sunotes_explode($id){
	$notes = get_module_pref("sunotes",false,$id);
	$array = explode(SUNOTES_ARRAY_DELIM,trim($notes,SUNOTES_ARRAY_DELIM));
	if (is_array($array) && count($array > 0) && $array[0]!=''){
		foreach ($array as $note){
			$newarray[] = explode(SUNOTES_NOTE_DELIM,trim($note,SUNOTES_NOTE_DELIM));
		}
		return  $newarray;
	} else {
		return '';
	}
}
	
function sunotes_implode($array){
	$notes = array();
	foreach ($array as $line){
		if (is_array($array) && count($array > 0)){
				$notes[] = implode(SUNOTES_NOTE_DELIM,$line);
		}
	}
	return implode(SUNOTES_ARRAY_DELIM, $notes).SUNOTES_ARRAY_DELIM;
}

function sunotes_delnote($id, $noteid){
	$notesarray = sunotes_explode($id);
	unset($notesarray[$noteid]);
	$update = sunotes_implode($notesarray);
	set_module_pref("sunotes",$update,false,$id);
}

?>
