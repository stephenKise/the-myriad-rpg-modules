<?php
/**
	18/03/09 - v0.0.2
	+ Added settings for a 'rules' page linked to from home.php
*/
function faq_addown_getmoduleinfo()
{
	$info = array(
		"name"=>"FAQ - Add Your Own",
		"description"=>"Add your own questions and answers without having to edit any files.",
		"version"=>"0.0.2", 
		"author"=>"`@MarcTheSlayer`2, written for Sara",
		"category"=>"FAQ",
		"download"=>"http://dragonprime.net/index.php?topic=9940.0",
		"allowanonymous"=>TRUE,
		"override_forced_nav"=>TRUE,
		"settings"=>array(
			"Add Your Own FAQ,title",
			"title"=>"Link text to this FAQ?,|Frequently Asked Questions that Sara knows the Answers to",
			"qanda"=>"Enter your questions and answers:,textarearesizeable,35|`^1) How do I add my own FAQ?`n`n`@You simply install this module and replace this text with your own.`n`n`^2) Is it really that simple?`n`n`@Why yes yes it is.`n`n`^3) So I just put each question and answer on its own line?`n`n`@Put a blank line between each and everything will appear fine.",
			"`^Note: The default FAQ colours are yellow for questions and green for answers.`0,note",
			"index"=>"Link to Rules on hompage?,bool|1",
			"title2"=>"Link text to the homepage link.,|Site Rules",
			"randg"=>"Enter your rules and guidelines:,textarearesizeable,35|1) Obey Staff at all times.`n`n2) `bDo not`b cause trouble.`n`n3) `bDo not`b spam the chat areas.",
			"`^Note: Feel free to add your own colour codes.`0,note",
		)
	);
	return $info;
}

function faq_addown_install()
{
	output("`c`b`Q%s 'faq_addown' Module.`b`n`c", translate_inline(is_module_active('faq_addown')?'Updating':'Installing'));
	module_addhook('index');
	module_addhook('faq-toc');
	return TRUE;
}

function faq_addown_uninstall()
{
	output("`n`c`b`Q'faq_addown' Module Uninstalled`0`b`c");
	return TRUE;
}

function faq_addown_dohook($hookname,$args)
{
	switch( $hookname )
	{
		case 'index':
			if( get_module_setting('index') )
			{
				addnav('Game Functions');
				addnav(array('%s',get_module_setting('title2')),'runmodule.php?module=faq_addown');
			}
		break;

		case 'faq-toc':
			$title = '`@' . get_module_setting('title') . '`0';
			output_notl('&#149;<a href="runmodule.php?module=faq_addown&faq=yes">' . $title . '</a><br />', TRUE);
		break;
	}

	return $args;
}

function faq_addown_run()
{
	$faq = httpget('faq');

	if( isset($faq) && !empty($faq) )
	{
		tlschema('faq');
		$title = get_module_setting('title');
		popup_header(full_sanitize($title));
		$x = translate_inline('Return to Contents');
		output_notl('<strong><a href="petition.php?op=faq">' . $x . '</a></strong><hr>`n',TRUE);
		$text = get_module_setting('qanda');
	}
	else
	{
		$title = get_module_setting('title2');
		page_header(full_sanitize($title));
		$text = get_module_setting('randg');
	}

	$text = str_replace("\r\n", '`n', $text);
	output_notl('`3%s`0`n',$text);

	if( isset($faq) && !empty($faq) )
	{
		output_notl('<hr><strong><a href="petition.php?op=faq">' . $x . '</a></strong>',TRUE);
		popup_footer();
	}
	else
	{
		addnav('Return to Homepage','home.php');
		page_footer();
	}
}
?>