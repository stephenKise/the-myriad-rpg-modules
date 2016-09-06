<?php
/**
	Originally by Lonny Luberts, modified by Danila Stern-Sapad with help from Ariadoss.

	Rewritten by MarcTheSlayer
	06/04/2012 - v3.0.0
	+ Added categories with thread and post counts and last post.
	+ Added pagination for categories and threads with settings for 'per page' values.
	+ Moderators can delete individual posts or entire threads, can also lock threads.
	+ Added the full list of clickable colours. Emoticons have been moved to another module.
	+ Certain bbcodes are supported.
	+ Bunch of other stuff. :)

	06/08/2010 - v3.0.1
	+ Added hook to 'checkuserpref' and changed user prefs 'user_' to 'check_'. Now a check is
	  performed and only SU players see these on their preferences page.
*/
function superuserforum_getmoduleinfo()
{
	$colourtags = 'enum,,None,`!,Light Blue,`1,Dark Blue,`@,Light Green,`2,Dark Green,`#,Light Cyan,`3,Dark Cyan,`$,Light Red,`4,Dark Red,`%,Light Magenta,`5,Dark Magenta,`^,Light Yellow,`6,Dark Yellow,`&,Light White,`7,Dark White,`),Light Black,`~,Black,`Q,Light Orange,`q,Dark Orange,`t,Light Brown,`T,Dark Brown,`E,Light Rust,`e,Dark Rust,`L,Light LinkBlue,`l,Dark LinkBlue,`y,Khaki,`Y,Dark Khaki,`K,Dark Seagreen,`r,Rose,`R,Rose,`v,Ice Violet,`V,Blue Violet,`g,XLtGreen,`G,XLtGreen,`j,MdGrey,`J,MdBlue,`x,Burlywood,`X,Beige,`k,Aquamarine,`p,Light Salmon,`P,Salmon,`m,Wheat,`M,Tan';

	$info = array(
		"name"=>"Superuser Forum",
		"version"=>"3.0.1",
		"author"=>"`#Lonny Luberts `$<br>Modified by Danilo Stern-Sapad`2, rewritten by `@MarcTheSlayer",
		"category"=>"Administrative",
		"download"=>"http://dragonprime.net/index.php?topic=12122.0",
		"settings"=>array(
			"Superuser Forum Settings,title",
				"newthreads"=>"Only Forum Moderators can start new threads?,bool|1",
				"threadspp"=>"Threads per page:,int|15",
				"postspp"=>"Posts per page:,int|15",
				"quickreply"=>"Show quick reply box?,bool|1",
				"showcolours"=>"Show colour keys?,bool|1",
				"showbbcodes"=>"Show bbcodes?,bool|1",
				"showsmilies"=>"Show image smilies?,bool",
				"`3Requires my rewritten version of the 'emoticons' module.,note",
				"linksclick"=>"Turn urls into clickable links?,bool",
				"`3That's urls not inside bbcode tags.,note",
				"newestpostdate"=>"Most recent post,text|2005-01-01 01:00:00",
				"`2If the 'avatar' module is installed then player's avatars will be displayed.,note",
		),
		"prefs"=>array(
			"Superuser Forum Prefs,title",
				"check_timestamp"=>"Show timestamps how?,enum,1,Real Time [12/25 1:27pm],2,Relative Time (1h35m)|1",
				"check_timeformat"=>"Format for Real Time:,string,20|F j, Y, g:i a",
				"check_color"=>"Default Forum text Colour,$colourtags",
				"forummod"=>"Forum Moderator,bool",
				"dateread"=>"Date/time last entered superuserforum,string,20|2005-01-01 01:00:00",
				"lastthread"=>"Last Thread Viewed,text|",
		),
	);
	return $info;
}

function superuserforum_install()
{
	if( !is_module_active('superuserforum') )
	{
		output("`4Installing Superuser Forum Module.`n");
	}
	else
	{
		output("`4Updating Superuser Forum Module.`n");
	}
	require_once('lib/tabledescriptor.php');
	$forumposts = array(
		'postid'		=>array('name'=>'postid',		'type'=>'smallint(5)	unsigned',	'null'=>'0',	'extra'=>'auto_increment'),
		'threadid'		=>array('name'=>'threadid',		'type'=>'smallint(5)	unsigned',	'null'=>'0',	'default'=>'0'),
		'catid'			=>array('name'=>'catid',		'type'=>'smallint(5)	unsigned',	'null'=>'0',	'default'=>'0'),
		'postorder'		=>array('name'=>'postorder',	'type'=>'smallint(5)	unsigned',	'null'=>'0',	'default'=>'1'),
		'locked'		=>array('name'=>'locked',		'type'=>'tinyint(1)		unsigned',	'null'=>'0',	'default'=>'0'),
		'postdate'		=>array('name'=>'postdate',		'type'=>'datetime',					'null'=>'0',	'default'=>'0000-00-00 00:00:00'),
		'editdate'		=>array('name'=>'editdate',		'type'=>'datetime',					'null'=>'0',	'default'=>'0000-00-00 00:00:00'),
		'userid'		=>array('name'=>'userid',		'type'=>'smallint(5)	unsigned',	'null'=>'0',	'default'=>'0'),
		'edituserid'	=>array('name'=>'edituserid',	'type'=>'smallint(5)	unsigned',	'null'=>'0',	'default'=>'0'),
		'title'			=>array('name'=>'title',		'type'=>'varchar(100)',				'null'=>'0',	'default'=>''),
		'content'		=>array('name'=>'content',		'type'=>'text',						'null'=>'1'),
		'editreason'	=>array('name'=>'editreason',	'type'=>'varchar(100)',				'null'=>'0',	'default'=>''),
		'key-PRIMARY'	=>array('name'=>'PRIMARY',		'type'=>'primary key',	'unique'=>'1',	'columns'=>'postid'),
		'key-postid'	=>array('name'=>'postid',		'type'=>'key',							'columns'=>'postid')
	);
	$forumcats = array(
		'catid'			=>array('name'=>'catid', 		'type'=>'smallint(5)	unsigned',	'null'=>'0',	'extra'=>'auto_increment'),
		'catname'		=>array('name'=>'catname', 		'type'=>'varchar(100)', 			'null'=>'0',	'default'=>''),
		'description'	=>array('name'=>'description', 	'type'=>'text',						'null'=>'1'),
		'catorder'		=>array('name'=>'catorder', 	'type'=>'smallint(5)	unsigned',	'null'=>'0',	'default'=>'1'),
		'key-PRIMARY'	=>array('name'=>'PRIMARY', 		'type'=>'primary key',	'unique'=>'1', 	'columns'=>'catid')
	);

	output('`3Installing \'superuserforum\' tables...`0`n');
	synctable(db_prefix('superuserforum'), $forumposts, TRUE);
	synctable(db_prefix('superuserforumcats'), $forumcats, TRUE);

	if( !is_module_active('superuserforum') ) db_query("INSERT INTO ".db_prefix('superuserforumcats')." (catname,description) VALUES ('General','Default category for forum threads.')");

	module_addhook('header-superuser');
	module_addhook('checkuserpref');
	return TRUE;
}

function superuserforum_uninstall()
{
	output("`4Un-Installing Superuser Forum Module.`n");
	db_query("DROP TABLE ".db_prefix('superuserforum'));
	db_query("DROP TABLE ".db_prefix('superuserforumcats'));
	return TRUE;
}

function superuserforum_dohook($hookname,$args)
{
	switch( $hookname )
	{
		case 'header-superuser':
            global $session;
            require_once('lib/villagenav.php');
            addnav('*Check Often*');
            if ($session['user']['superuser'] & SU_EDIT_COMMENTS) {
                if( get_module_pref('dateread') < get_module_setting('newestpostdate')  )
    			{
    				addnav('`b`^Superuser Forum - `$NEW`0`b','runmodule.php?module=superuserforum');
    			}
    			else
    			{
    				addnav('`b`^Superuser Forum`0`b','runmodule.php?module=superuserforum');
    			}
            }
		break;

		case 'checkuserpref':
			global $session;
			// To be a forum moderator you have to be a SU.
			if( $session['user']['superuser'] <= 0 ) $args['allow'] = FALSE;
		break;
	}

	return $args;
}

function superuserforum_run()
{
	global $session;

	require_once('lib/sanitize.php');
	require_once('lib/showform.php');

	page_header('Superuser Forum');

	addnav('Options');

	set_module_pref('dateread',date("Y-m-d H:i:s"));

	$op = httpget('op');
    $op = explode(':',$op);
    $start = ( httpget('start') ) ? httpget('start') : 0;
	$start = ( isset($op[4]) ) ? $op[4] : $start;
	$from = 'runmodule.php?module=superuserforum';

	include_once('modules/superuserforum/superuserforum.php');

	if( $op <> '' ) addnav('Forum Home',$from);

	addnav('Help');
	addnav('Forum Help',$from.'&op=help');
	addnav('Superuser');
	addnav('Return to Grotto','superuser.php');

	page_footer();	
}

function superuserforum_pagination($link, $total, $start, $per_page = 15, $prevnext = TRUE)
{
	$next = translate_inline('Next');
	$prev = translate_inline('Previous');

	$j=0;
	$pagination = '';

	$total_pages = ceil($total / $per_page);

	if( $total_pages == 1 ) return '';

	$on_page = floor($start / $per_page) + 1;

	for( $i = 1; $i < $total_pages + 1; $i++ )
	{
		if( $i == $on_page )
		{
			$pagination .= '<b>' . $i . '</b>';
		}
		else
		{
			$link2 = $link . ':' . ( ( $i - 1 ) * $per_page );
			$pagination .= '<a href="' . $link2 . '">' . $i . '</a>';
			addnav('',$link2);
		}
		if( $i <  $total_pages )
		{
			$pagination .= ', ';
		}
	}

	if( $prevnext == TRUE )
	{
		if( $on_page > 1 )
		{
			$link2 = $link . ':' . ( ( $on_page - 2 ) * $per_page );
			$pagination = ' <a href="' . $link2 . '">' . $prev . '</a>&nbsp;&nbsp;' . $pagination;
			addnav('',$link2);
		}

		if( $on_page < $total_pages )
		{
			$link2 = $link . ':' . ( $on_page * $per_page );
			$pagination .= '&nbsp;&nbsp;<a href="' . $link2 . '">' . $next . '</a>';
			addnav('',$link2);
		}
	}

	return $pagination;
}

function superuserforum_startpage($per_page = 15, $threadid, $postid)
{
	// Calculates which page a post is on.
	$sql = "SELECT count(postid) AS count
			FROM " . db_prefix('superuserforum') . "
			WHERE threadid = '$threadid'
				AND postid <= $postid";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);

	$page = ceil($row['count'] / $per_page);
	if( $page == 1 ) return 0;

	return ( ( $page - 1 ) * $per_page );
}

function superuserforum_timestamp($timestamp)
{
	$ago = translate_inline('ago');

	if( get_module_pref('check_timestamp') == 1 )
	{
		global $session;
		$time = strtotime($timestamp) + ($session['user']['prefs']['timeoffset'] * 60 * 60);
		return date(get_module_pref('check_timeformat'),$time);
	}
	elseif( get_module_pref('check_timestamp') == 2 )
	{
		return reltime(strtotime($timestamp)) . ' ' . $ago;
	}
}

function superuserforum_bbcode($string)
{

	if( get_module_setting('linksclick') )
	{
		$string = ' ' . $string;
		$string = preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $string);
		$string = preg_replace("#(^|[\n ])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $string);
		$string = substr($string, 1);
	}

	$quote = translate_inline('`#Quote: `3');
	$quoting = translate_inline('`#Quoting: `3');

	$bbcode = array(
		//Text Apperence
		'#\[b\](.*?)\[/b\]#si' => '<span style="font-weight:bold">\\1</span>',
		'#\[i\](.*?)\[/i\]#si' => '<span style="font-style:italic">\\1</span>',
		'#\[u\](.*?)\[/u\]#si' => '<span style="text-decoration:underline">\\1</span>',
		'#\[s\](.*?)\[/s\]#si' => '<span style="text-decoration:line-through">\\1</span>',
		//Font Color
		'#\[color=(.*?)\](.*?)\[/color\]#si' => '<span style="color:\\1">\\2</span>',
		//Other
		"#\[url\]([\w]+?://([\w\#$%&~/.\-;:=,?@\]+]+|\[(?!url=))*?)\[/url\]#is" => '<a href="\\1" target="_blank">\\1</a>',
		"#\[url\]((www|ftp)\.([\w\#$%&~/.\-;:=,?@\]+]+|\[(?!url=))*?)\[/url\]#is" => '<a href="http://\\1" target="_blank">\\1</a>',
		"#\[url=([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*?)\]([^?\n\r\t].*?)\[/url\]#is" => '<a href="\\1" target="_blank">\\2</a>',
		"#\[url=((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*?)\]([^?\n\r\t].*?)\[/url\]#is" => '<a href="http://\\1" target="_blank">\\3</a>',
		'#\[quote\](.*?)\[/quote\]#si' 	=> '<fieldset><legend>'.$quote.'`0</legend>\\1</fieldset>',
		'#\[quote=(.*?)\](.*?)\[/quote\]#si' => '<fieldset><legend>'.$quoting.'\\1`0</legend>\\2</fieldset>',
		'#\[img\](https?://.*?\.(?:jpg|jpeg|gif|png))\[/img\]#si' 		=> '<img src="\\1" alt="" />',
		'#\[email\]([a-z0-9&\-_.]+?@[\w\-]+\.([\w\-\.]+\.)?[\w]+)\[/email\]#si' 	=> '<a href="mailto:\\1">\\1</a>',
		'#(script|about|applet|activex|chrome):#is' => '\\1&#058;'
	);

	$string = preg_replace(array_keys($bbcode), array_values($bbcode), $string);

	// The `& was being turned into `&amp; by htmlentities(). Easy way to fix this. :)
	$string = str_replace('`&amp;', '`&', $string);

	return $string;
}

function superuserforum_emoticon($string)
{
	if( get_module_setting('showsmilies') )
	{
		$string = modulehook('emoticonparse', $string);
	}
	return $string;
}

function superuserforum_clickables()
{
	if( get_module_setting('showcolours') || get_module_setting('showsmilies') || get_module_setting('showbbcodes') )
	{
		rawoutput('<script language="JavaScript" type="text/javascript">');
		/**
		rawoutput('function insertstring(textValue) {
				//Get textArea HTML control 
				var txtArea = document.getElementById("content");
				if ( document.selection )
				{ //IE
					txtArea.focus();
					var sel = document.selection.createRange();
					sel.text = textValue;
					return;
				}
				else if ( txtArea.selectionStart || txtArea.selectionStart == "0" )
				{ //Firefox, chrome, mozilla
					var startPos = txtArea.selectionStart;
					var endPos = txtArea.selectionEnd;
					var scrollTop = txtArea.scrollTop;
					txtArea.value = txtArea.value.substring(0, startPos) + textValue + txtArea.value.substring(endPos, txtArea.value.length);
					txtArea.focus();
					txtArea.selectionStart = startPos + textValue.length;
					txtArea.selectionEnd = startPos + textValue.length;
				}
				else
				{
					txtArea.value += textValue;
					txtArea.focus();
				}
			}
		</script>');
		*/
		rawoutput('function insertstring(textValue)
		{
		//		var txtArea = document.getElementById("content");
		//		txtArea.value += textValue;
				document.form.content.value += textValue;
    			document.form.content.focus();
		}
		</script>');
	}

	output_notl('`c');
	if( get_module_setting('showcolours') )
	{
		$colours = array('`!'=>'colLtBlue','`1'=>'colDkBlue','`@'=>'colLtGreen','`2'=>'colDkGreen','`#'=>'colLtCyan','`3'=>'colDkCyan','`$'=>'colLtRed','`4'=>'colDkRed',
		'`%'=>'colLtMagenta','`5'=>'colDkMagenta','`^'=>'colLtYellow','`6'=>'colDkYellow','`&'=>'colLtWhite','`7'=>'colDkWhite','`)'=>'colLtBlack','`~'=>'colBlack','`Q'=>'colLtOrange',
		'`q'=>'colDkOrange','`t'=>'colLtBrown','`T'=>'colDkBrown','`E'=>'colLtRust','`e'=>'colDkRust','`L'=>'colLtLinkBlue','`l'=>'colDkLinkBlue','`y'=>'colkhaki','`Y'=>'coldarkkhaki',
		'`K'=>'coldarkseagreen','`r'=>'colRose','`R'=>'colRose','`v'=>'coliceviolet','`V'=>'colblueviolet','`g'=>'colXLtGreen','`G'=>'colXLtGreen','`j'=>'colMdGrey','`J'=>'colMdBlue','`x'=>'colburlywood',
		'`X'=>'colbeige','`k'=>'colaquamarine','`p'=>'collightsalmon','`P'=>'colsalmon','`m'=>'colwheat','`M'=>'coltan');

		$colournames = translate_inline(array('Light Blue','Dark Blue','Light Green','Dark Green','Light Cyan','Dark Cyan','Light Red','Dark Red','Light Magenta','Dark Magenta',
		'Light Yellow','Dark Yellow','Light White','Dark White','Light Black','Black','Light Orange','Dark Orange','Light Brown','Dark Brown','Light Rust','Dark Rust','Light LinkBlue',
		'Dark LinkBlue','Khaki','Dark Khaki','Dark Seagreen','Rose','Rose','Ice Violet','Blue Violet','XLtGreen','XLtGreen','MdGrey','MdBlue','Burlywood','Beige','Aquamarine','Light Salmon',
		'Salmon','Wheat','Tan'));
		
		output('`n`2-=-=Clickable Colours=-=-`n');
		$count = count($colours)-1;
		$i=0;
		foreach( $colours as $key => $value )
		{
			rawoutput('<a class="'.$value.'" onClick="insertstring(\''.$key.'\')" onmouseover="this.style.cursor=\'hand\';">'.$colournames[$i].'</a>&nbsp;');
			if( $count != $i ) output_notl('`&, ');
			$i++;
		}
	}

	if( get_module_setting('showbbcodes') )
	{
		$bbcode_trans = translate(array('Bold','Italic','Underline','Srikethrough','Quote','Image','Weblink'));
		output('`n`n`2-=-=Clickable Codes=-=-`n');
		rawoutput('<input type="button" class="button" value=" B " style="font-weight:bold; width: 30px" onClick="insertstring(\'[b][/b]\')" onmouseover="this.style.cursor=\'hand\';" title="'.$bbcode_trans[0].'" />
			  <input type="button" class="button" value=" i " style="font-style:italic; width: 30px" onClick="insertstring(\'[i][/i]\')" onmouseover="this.style.cursor=\'hand\';" title="'.$bbcode_trans[1].'" />
			  <input type="button" class="button" value=" u " style="text-decoration:underline; width: 30px" onClick="insertstring(\'[u][/u]\')" onmouseover="this.style.cursor=\'hand\';" title="'.$bbcode_trans[2].'" />
			  <input type="button" class="button" value=" s " style="text-decoration: width: 30px" onClick="insertstring(\'[s][/s]\')" onmouseover="this.style.cursor=\'hand\';" title="'.$bbcode_trans[3].'" />
			  <input type="button" class="button" value="Quote" style="width: 50px" onClick="insertstring(\'[quote][/quote]\')" onmouseover="this.style.cursor=\'hand\';" title="'.$bbcode_trans[4].'" />
			  <input type="button" class="button" value="Img" style="width: 40px" onClick="insertstring(\'[img][/img]\')" onmouseover="this.style.cursor=\'hand\';" title="'.$bbcode_trans[5].'" />
			  <input type="button" class="button" value="URL" style="text-decoration: underline; width: 40px" onClick="insertstring(\'[url][/url]\')" onmouseover="this.style.cursor=\'hand\';" title="'.$bbcode_trans[6].'" />');
	}

	if( get_module_setting('showsmilies') )
	{
		output('`2-=-=Clickable Smilies=-=-`0`n');
		modulehook('emoticonshow', array());
	}

	output_notl('`c');
}

function superuserforum_show_avatar($userid)
{
	//thanks to anpera for the avatar module... altered copy and paste code from avatars.php below
	$av = '';
	$alt = translate_inline('Avatar Image');
	if( get_module_pref('user_showavatar','avatars',$userid) == 1 )
	{
			$avatar = get_module_pref('user_avatar','avatars',$userid);
			$avatar = stripslashes(preg_replace("'[\"\'\\><@?*&#; ]'","",$avatar));
			if( $avatar <> '' )
			{
				$maxwidth = (get_module_setting('maxwidth','avatars') * .5);
				$maxheight = (get_module_setting('maxheight','avatars') * .5);
				$pic_size = @getimagesize($avatar);
				$pic_width = ($pic_size[0] * .5);
				$pic_height = ($pic_size[1] * .5);
				if( $pic_width > $maxwidth ) $pic_width = $maxwidth;
				if( $pic_height > $maxheigt ) $pic_height = $maxheight;
				$av .= '<center><img src="'.$avatar.'"';
				if( $pic_size[0] > 0 ) $av .= 'title="'.$alt.'" alt="'.$alt.'" width="'.$pic_width.'" height="'.$pic_height.'"';
				$av .= ' /></center><br />';
			}
	}
	return $av;
}
?>