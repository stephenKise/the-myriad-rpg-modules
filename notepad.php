<?php
function notepad_getmoduleinfo() {
	$info = array(
		"name" => "Notepad",
		"version" => "1.0",
		"author" => "`!Boris735",
		"category" => "General",
		"description" => "Per-player editable notes",
		"override_forced_nav" => true,
		"prefs" => array(
			"Notepad preferences,title",
			"user_winwidth" => "Width of notepad (pixels),int|600",
			"user_winheight" => "Height of notepad (pixels),int|400",
			"user_textwidth" => "Width of edit area (columns),int|60",
			"user_textheight" => "Height of edit area (rows),int|15",
			"user_heading" => "Section of stats to appear under,text|Other",
			"notetext" => "Text of user's notepad,text|",
		),
	);
	return $info;
}

function notepad_install() {
	module_addhook("charstats");
	return true;
}

function notepad_uninstall() {
}

function notepad_anchor($optext=false) {
	$link = "runmodule.php?module=notepad";
	if ($optext !== false && $optext != "")
		$link .= "&$optext";

	$width = get_module_pref("user_winwidth");
	$height = get_module_pref("user_winheight");
	$jspopwin = "window.open('$link', 'notepad', 'scrollbars=yes,resizable=yes,width=$width,height=$height').focus()";
	$anchor = "<a href='$link' target='notepad' onClick=\"$jspopwin; return false;\">";

	return $anchor;
}

function notepad_linktext($linktext, $optext=false) {
	$anchor = notepad_anchor($optext);
	$anchor .= "$linktext</a>";
	return $anchor;
}

function notepad_outputlink($linktext, $optext=false) {
	// Should not use output() for links, since if someone has translation
	// enabled then the tlbutton will try to nest links and it will end up
	// being unclickable.  Thus, translate first.

	$trans_linktext = translate_inline($linktext);
	rawoutput(notepad_anchor($optext));
	output_notl($trans_linktext);
	rawoutput("</a>");
}

function notepad_dohook($hookname, $args) {
	switch ($hookname) {
	case "charstats":
		$heading = get_module_pref("user_heading");
		if ($heading == "")
			$heading = "Personal Info";

		$openmsg = translate_inline("Take Notes");

		addcharstat($heading);
		addcharstat("Notepad", notepad_linktext($openmsg, "op=read"));
		addnav("","runmodule.php?module=notepad");
		break;
	}
	return $args;
}

function notepad_emptytext() {
	$text = "Your notepad is enticingly empty, leaving plenty of room for your keen insights into the world.";
	return $text;
}

function notepad_run() {
	popup_header("Your Notepad");

	$op = httpget("op");

	$text = get_module_pref("notetext");
	$text = stripslashes($text);

	switch ($op) {
	case "read":
		notepad_outputlink("Edit Notes", "op=edit");
		output("`n`n");

		$text = str_replace("\n", "`n", $text);
		if ($text == "")
			$text = notepad_emptytext();

		output("%s", $text);
		output("`n`n");
		break;

	case "save":
		$newtext = httppost("notes");
		$newtext = stripslashes($newtext);
		$newtext = str_replace("`n", "\n", $newtext);
		if ($newtext != $text) {
			// if (strlen($newtext) > 
			$text = $newtext;
			set_module_pref("notetext", $text);
			output("`^Modified text saved`n`n");
		}
		/* fall through */
	case "edit":

		$width = get_module_pref("user_textwidth");
		$height = get_module_pref("user_textheight");

		output("`0");
		rawoutput("<form action='runmodule.php?module=notepad&op=save' method='POST'>");
		notepad_outputlink("View Notes`0", "op=read");
		output("`\$(does `inot`i save)`0");

		rawoutput("<input type='submit' class='button' value='Save' style='float: right'>");

		output_notl("`n`n`c`0");

		$text = htmlentities($text, ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
		rawoutput("<textarea class='input' name='notes' cols='$width' rows='$height'>$text</textarea>");

		output_notl("`c`n`0");
		rawoutput("<input type='submit' class='button' value='Save' style='float: right'>");
		rawoutput("</form>");

		break;
	}

	popup_footer();
}
?>
