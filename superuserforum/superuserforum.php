<?php
	$suforum = db_prefix('superuserforum');
	$suforumcats = db_prefix('superuserforumcats');
	$accounts = db_prefix('accounts');

	$fallen_name = translate_inline('`@F`2allen `@W`2arrior`0');

	switch( $op[0] )
	{
		case 'category':
			$catid = ( $op[1] ) ? $op[1] : 1;
			$start = ( $op[2] ) ? $op[2] : 0;

			set_module_pref('lastthread','');

			if( get_module_setting('newthreads') != 1 || get_module_pref('forummod') == 1 || $session['user']['superuser'] & SU_MEGAUSER )
			{
				addnav('`^New Thread`0','runmodule.php?module=superuserforum&op=newthread:'.$catid);
			}

			$edit = translate_inline('Edit');
			$del = translate_inline('Del');
			$delmsg = translate_inline('Are you sure you wish to delete this Category, all threads and post within will also be deleted?');
			$forumhome = translate_inline('Forum Home');
			$threads = translate_inline('Threads');
			$replies = translate_inline('Replies');
			$lastpost = translate_inline('Last Post');
			$goto = translate_inline('Goto last post');
			$link = $from.'&op=thread:'.$catid;
			$link1 = $from.'&op=category:'.$catid;
			$threads_per_page = get_module_setting('threadspp');
			$posts_per_page = get_module_setting('postspp');

			$sql = "SELECT catname
					FROM $suforumcats
					WHERE catid = '$catid'";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);

			$backlinks = '<a href="'.$from.'">'.appoencode($forumhome).'</a> :: <a href="'.$link1.'">'.appoencode(stripslashes($row['catname']).'`0').'</a><br /><br />';
			rawoutput($backlinks);
			addnav('',$from);
			addnav('',$link1);

			$sql = "SELECT count(postid) AS count
					FROM $suforum
					WHERE catid = '$catid'
						AND postorder = 1";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			$pagination = superuserforum_pagination($from.'&op=category:'.$catid, $row['count'], $start, $threads_per_page);
			rawoutput($pagination);

			rawoutput('<table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#999999" align="center">');
			if( get_module_pref('forummod') == 1 || $session['user']['superuser'] & SU_MEGAUSER )
			{
				rawoutput('<tr align="right" class="trlight"><td colspan="3">');
				output('Category Ops: [ ');
				rawoutput('<a href="'.$from.'&op=editcat:'.$catid.'">');
				addnav('',$from.'&op=editcat:'.$catid);
				output('Edit');
				rawoutput('</a>');
				output(' ]');

				if( $session['user']['superuser'] & SU_MEGAUSER )
				{
					output(' [ ');
					rawoutput('<a href="'.$from.'&op=delcat:'.$catid.'" onClick="return confirm(\''.$delmsg.'\')">');
					addnav('',$from.'&op=delcat:'.$catid);
					output('Del');
					rawoutput('</a>');
					output(' ]');
				}
		
				rawoutput('</td></tr>');
			}

			rawoutput("<tr class=\"trhead\"><td width=\"50%\">$threads</td><td align=\"center\">$replies</td><td align=\"center\">$lastpost</td></tr>");

			$sql = "SELECT $accounts.name, $suforum.*
					FROM $suforum 
						LEFT JOIN $accounts
					ON $accounts.acctid = $suforum.userid
					WHERE $suforum.catid = '$catid'
						AND $suforum.postorder = 1
					ORDER BY $suforum.postdate
					LIMIT $start, $threads_per_page";
			$result = db_query($sql);

			if( db_num_rows($result) > 0 )
			{
				$i=0;
				while( $row = db_fetch_assoc($result) )
				{
					$sql = "SELECT count(postid) AS countposts
							FROM $suforum
							WHERE threadid = '{$row['threadid']}'";
					$result2 = db_query($sql);
					$row2 = db_fetch_assoc($result2);
					$countposts = ( $row2['countposts'] > 0 ) ? $row2['countposts']-1 : 0;

					rawoutput('<tr valign="middle" class="'.($i%2?'trlight':'trdark').'"><td><a href="'.$link.':'.$row['threadid'].':'.$row['postid'].'">');
					addnav('',$link.':'.$row['threadid'].':'.$row['postid']);
					output_notl('`b%s`b`0', stripslashes($row['title']));
					rawoutput('</a><br />');
					$name = ( $row['name'] ) ? $row['name'] : $fallen_name;
					output('by %s`0`n', $name);
					rawoutput('<span style="font-size:smaller;">');

					if( ( $countposts + 1 ) > $posts_per_page )
					{
						$total_pages = ceil( ( $countposts + 1 ) / $posts_per_page );
						output(' [ Page: ');
						$threadnums = '';
						$times = 1;
						for( $j = 0; $j < $countposts + 1; $j += $posts_per_page )
						{
							$threadnums .= '<a href="'.$link.':'.$row['threadid'].':'.$row['postid'].':'.$j.'">' . $times . '</a>';
							addnav('',$link.':'.$row['threadid'].':'.$row['postid'].':'.$j);
							if( $times == 1 && $total_pages > 4 )
							{
								$threadnums .= ' ... ';
								$times = $total_pages - 3;
								$j += ( $total_pages - 4 ) * $posts_per_page;
							}
							elseif( $times < $total_pages )
							{
								$threadnums .= ', ';
							}
							$times++;
						}
						rawoutput($threadnums);
						output('] ');
					}
					rawoutput('</span></td><td align="center" valign="middle">'.$countposts.'</td><td valign="middle">');

					$sql = "SELECT $suforum.postid, $suforum.postdate, $suforum.title, $accounts.name
							FROM $suforum
								LEFT JOIN $accounts
								ON $accounts.acctid = $suforum.userid
							WHERE $suforum.threadid = '{$row['threadid']}'
							ORDER BY $suforum.postdate
							DESC LIMIT 1";
					$result2 = db_query($sql);
					if( $row2 = db_fetch_assoc($result2) )
					{
						$name = ( $row2['name'] ) ? $row2['name'] : $fallen_name;
						output('by %s`0', $name);
						rawoutput('<a href="'.$link.':'.$row['threadid'].':'.$row2['postid'].'#'.$row2['postid'].'">');
						addnav('',$link.':'.$row['threadid'].':'.$row2['postid'].'#'.$row2['postid']);
						rawoutput('<img src="images/oldscroll.GIF" width="16" height="16" title="'.$goto.'" alt="'.$goto.'" align="middle" border="0" /></a><br />' . superuserforum_timestamp($row2['postdate']));
					}
					else
					{
						output('`2No Replies`0');
					}

					rawoutput('</td></tr>');
					$i++;
				}
			}
			else
			{
				rawoutput('<tr class="trlight"><td colspan="3" align="center">');
				if( get_module_pref('newthreads') == 1 ) output('`3No threads found. Have a moderator make some.`0');
				else output('`3No threads found. Why don\'t you make one.`0');
				rawoutput('</td></tr>');
			}

			rawoutput('</table>');
			rawoutput($pagination);
			rawoutput('<br /><br />');
			rawoutput($backlinks);
		break;

		case 'thread':
			$catid = $op[1];
			$threadid = $op[2];
			$postid = $op[3];
			set_module_pref('lastthread',$catid.':'.$threadid.':'.$postid.':'.$start);

			$forumhome = translate_inline('Forum Home');
			$fallen_title = translate_inline('`qP`Qeasant`0');
			$delmsg = translate_inline('Are you sure you wish to delete this Message?');
			$delmsg2 = translate_inline('Are you sure you wish to delete this Thread?');
			$writemail = translate_inline('Write Mail');
			$online = translate_inline('`#(Online)`0');
			$del = translate_inline('Del');
			$edit = translate_inline('Edit');
			$quote = translate_inline('Quote');
			$locks = translate_inline(array('Lock','Unlock'));
			$none = translate_inline('Correcting a booboo.');
			$link = "$from&op=thread:$catid:$threadid:$postid";

			$sql = "SELECT locked
					FROM $suforum
					WHERE threadid = '$threadid'
					LIMIT 1";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			$locked = $row['locked'];

			if( $locked != 1 || (get_module_pref('forummod') == 1 || $session['user']['superuser'] & SU_MEGAUSER) )
			{
				addnav('`^Post Reply`0',$from.'&op=reply');
			}
		  	if( $locked == 1 )
		  	{
		  		output('`n`$This thread is `bLOCKED!`b Only moderators can post to it.`0`n');
		  	}

			$sql = "SELECT $suforumcats.catname, $suforum.title
					FROM $suforumcats
						JOIN $suforum
						ON $suforumcats.catid = $suforum.catid
					WHERE $suforum.catid = '$catid'
						AND $suforum.postid = $postid";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);

			$backlinks = '<a href="'.$from.'">'.appoencode($forumhome).'</a> :: <a href="'.$from.'&op=category:'.$catid.':0">'.appoencode(stripslashes($row['catname']).'`0').'</a> :: '.appoencode(stripslashes($row['title']).'`0').'<br /><br />';
			rawoutput($backlinks);
			addnav('',$from);
			addnav('',$from.'&op=category:'.$catid.':0');

			$posts_per_page = get_module_setting('postspp');
			$sql = "SELECT count(postid) AS count
					FROM $suforum
					WHERE threadid = '$threadid'";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			$pagination = superuserforum_pagination($link, $row['count'], $start, $posts_per_page);
			rawoutput($pagination);

			rawoutput('<table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#999999" align="center">');
			if( get_module_pref('forummod') == 1 || $session['user']['superuser'] & SU_MEGAUSER )
			{
				rawoutput('<tr align="right" class="trlight"><td colspan="2">');
				output('Thread Ops: [ ');
				rawoutput('<a href="'.$from.'&op=lockthread:'.$threadid.'">');
				addnav('',$from.'&op=delthread:'.$threadid);
				output_notl('%s', ($locked==1?$locks[1]:$locks[0]));
				rawoutput('</a>');
				output(' ] [ ');
				rawoutput('<a href="'.$from.'&op=delthread:'.$threadid.'" onClick="return confirm(\''.$delmsg2.'\')">');
				addnav('',$from.'&op=lockthread:'.$threadid);
				output('Del');
				rawoutput('</a>');
				output(' ]');
				rawoutput('</td></tr>');
			}

			$sql = "SELECT acc1.acctid, acc1.login, acc1.name, acc1.title, acc1.ctitle, acc1.loggedin, acc1.laston, acc2.name AS editname, $suforum.*
					FROM $suforum 
						LEFT JOIN $accounts AS acc1
					ON acc1.acctid = $suforum.userid
						LEFT JOIN $accounts AS acc2
					ON acc2.acctid = $suforum.edituserid
					WHERE $suforum.threadid = '$threadid'
					ORDER BY $suforum.postorder
					LIMIT $start,$posts_per_page";
			$result = db_query($sql);
			$count = db_num_rows($result);

			if( $count > 0 )
			{
				$sql = "SELECT count(postid) AS postcount, userid
						FROM $suforum
						GROUP BY userid";
				$result2 = db_query($sql);
				$postcounts = array();
				while( $row2 = db_fetch_assoc($result2) )
				{
					$postcounts[$row2['userid']] = $row2['postcount'];
				}
			}

			$show_avatars = FALSE;
			if( is_module_active('avatars') ) $show_avatars = TRUE;

			$first_title = '';
			$i=0;
			while( $row = db_fetch_assoc($result) )
			{
				$row['title'] = stripslashes($row['title']);
				if( $i+1 == $count ) $first_title = $row['title'];
				rawoutput('<tr class="trhead"><td colspan="2"><a name="#'.$row['postid'].'"></a>');
				output_notl('`3%s`0', $row['title']);
				rawoutput('</td></tr>');

				$row['content'] = stripslashes($row['content']);
				$row['content'] = superuserforum_bbcode($row['content']);
				if( get_module_setting('usesmiley') ) $row['content'] = superuserforum_emoticon($row['content']);
				$row['content'] = appoencode($row['content'],TRUE);

				if( $row['name'] )
				{
					$title = $row['title'];
					$name = $row['name'];
					if( $row['ctitle'] ) $title = $row['ctitle'];
					if( $title )
					{
						$x = strpos($row['name'], $title);
						if( $x !== false ) $name = trim(substr($row['name'],$x+strlen($title)));
						else $title = '';
					}
				}
				else
				{
					$title = $fallen_title;
					$name = $fallen_name;
				}

				rawoutput('<tr valign="middle" class="'.($i%2?'trlight':'trdark').'"><td valign="top" rowspan="3" width="30%">');
				output_notl('`^%s`0`n', $title);
				rawoutput('<a href="bio.php?char='.$row['acctid'].'&ret='.urlencode($_SERVER['REQUEST_URI']).'">');
				addnav('','bio.php?char=' . $row['acctid'] . '&ret=' . urlencode($_SERVER['REQUEST_URI']));
				output_notl('`^%s`0', $name);
				rawoutput('</a><br />');

				if( $show_avatars == TRUE ) rawoutput(superuserforum_show_avatar($row['userid']));

				if( isset($postcounts[$row['userid']]) ) output('`7Posts: `&%s`0`n', $postcounts[$row['userid']]);
				else output('`7Posts: `&Unknown`0`n');
				output_notl('%s`n`n', ($row['loggedin']?$online:''));

				rawoutput('<a href="mail.php?op=write&to='.rawurlencode($row['login']).'" target="_blank" onClick="'.popup('mail.php?op=write&to='.rawurlencode($row['login'])).';return false;">');
				rawoutput('<img src="images/newscroll.GIF" width="16" height="16" title="'.$writemail.'" alt="'.$writemail.'" border="0" /></a>');

				rawoutput('</td><td>');
				output('`7Posted: %s`0', superuserforum_timestamp($row['postdate']));
				rawoutput('</td></tr><tr valign="middle" class="'.($i%2?'trlight':'trdark').'"><td>');
				output_notl('%s', nl2br($row['content']), TRUE);
				rawoutput('<p>&nbsp;</p><span style="font-size:smaller;font-style:italic;"><span><span>');

				if( $row['editdate'] > $row['postdate'] )
				{
					$name = ( $row['editname'] ) ? $row['editname'] : $fallen_name;
					output('`3Edited by %s `3%s.`0`n', $name, superuserforum_timestamp($row['editdate']));
					output('`3Reason: %s`0', ($row['editreason']?$row['editreason']:$none));
				}

				rawoutput('</span></td></tr>');
				rawoutput('<tr valign="middle" class="'.($i%2?'trlight':'trdark').'"><td align="right">');

				if( get_module_pref('forummod') || $session['user']['superuser'] & SU_MEGAUSER )
				{
					rawoutput('[ <a href="'.$from.'&op=delpost:'.$row['postid'].'" onClick="return confirm(\''.$delmsg.'\')">'.$del.'</a> ]&nbsp;');
					addnav('',$from.'&op=delpost:'.$row['postid']);
				}
				if( ($locked != 1 && $row['userid'] == $session['user']['acctid']) || (get_module_pref('forummod') || $session['user']['superuser'] & SU_MEGAUSER) )
				{
					rawoutput('[ <a href="'.$from.'&op=editpost:'.$row['postid'].'">'.$edit.'</a> ]&nbsp;');
					addnav('',$from.'&op=editpost:'.$row['postid']);
				}

				if( $locked != 1 || (get_module_pref('forummod') || $session['user']['superuser'] & SU_MEGAUSER) )
				{
					rawoutput('[ <a href="'.$from.'&op=quote:'.$row['postid'].'">'.$quote.'</a> ]&nbsp;');
					addnav('',$from.'&op=quote:'.$row['postid']);
				}

				rawoutput('</td></tr>');
				$i++;
			}

			rawoutput('</table>');
			rawoutput($pagination);
			rawoutput('<br /><br />');
			rawoutput($backlinks);

			if( get_module_setting('quickreply') == 1 && ($locked != 1 || (get_module_pref('forummod') || $session['user']['superuser'] & SU_MEGAUSER)) )
			{
				rawoutput('<br /><br /><form action="'.$from.'&op=reply" method="POST" name="form" id="form">');
				addnav('',$from.'&op=reply');

				$info = array(
					'Post Reply,title',
					'title'=>'Title:,string,50',
					'content'=>'Message:,textarea,40',
				);

				$data = array(
					'title'=>full_sanitize($first_title),
					'content'=>'',
				);

				showform($info,$data);
				rawoutput('<input type="hidden" name="submit" value="1" /></form>');
		  		superuserforum_clickables();
		  	}
		break;

		case 'newthread':
			$catid = ( $op[1] ) ? $op[1] : 1;

			if( httppost('submit') )
			{
				extract(httpallpost());
				if( $title <> '' && $content <> '' )
				{
					$title = get_module_pref('check_color').soap($title);
					$title = addslashes(htmlentities($title));
					$content = get_module_pref('check_color').soap($content);
					$content = addslashes(htmlentities($content));
					$sql = "SELECT MAX(threadid) AS threadid
							FROM " . db_prefix('superuserforum');
					$result = db_query($sql);
					$row = db_fetch_assoc($result);
					$threadid = ( $row['threadid'] ) ? $row['threadid']+1 : 1;
					db_query("INSERT INTO $suforum (threadid, catid, postdate, userid, title, content) VALUES ('$threadid', '$catid', NOW(), '".$session['user']['acctid']."', '$title', '$content')");
					$postid = db_insert_id();
					set_module_setting('newestpostdate',date("Y-m-d H:i:s"));
					output('`n`3Your new thread has been added, click the continue nav link to return.`0`n');
					addnav('Continue',"runmodule.php?module=superuserforum&op=thread:$catid:$threadid:$postid");
					page_footer();	
				}
				else
				{
					output('`n`$Error: You need to input a title and message!`0');
				}
			}
			else
			{
				$title = $content = '';
			}

			rawoutput('<form action="'.$from.'&op=newthread:'.$catid.'" method="POST" name="form">');
			addnav('',$from.'&op=newthread:'.$catid);

			$info = array(
				'Make a New Thread,title',
				'title'=>'Title:,string,50',
				'content'=>'Message:,textarea,40',
			);

			$data = array(
				'title'=>$title,
				'content'=>$content,
			);

			showform($info,$data);
			rawoutput('<input type="hidden" name="submit" value="1" /></form>');
			superuserforum_clickables();
		break;

		case 'lockthread':
			$threadid = $op[1];

			$sql = "SELECT locked
					FROM " . db_prefix('superuserforum') . "
					WHERE threadid = $threadid
					LIMIT 1";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			if( $row['locked'] == 1 )
			{
				output('`n`3Thread Unlocked`0`n');
				$locked = 0;
			}
			else
			{
				output('`n`3Thread Locked`0`n');
				$locked = 1;
			}
			db_query("UPDATE $suforum SET locked = $locked WHERE threadid = '$threadid'");

			addnav('Return to Thread',$from.'&op=thread:'.get_module_pref('lastthread'));
		break;

		case 'delthread':
			$threadid = $op[1];

			db_query("DELETE FROM $suforum WHERE threadid = '$threadid'");
			output('`n`3Thread Deleted`0`n');
		break;

		case 'editpost':
			$postid = $op[1];
			$editreason = '';

			if( httppost('submit') )
			{
					extract(httpallpost());
					if( $title <> '' && $content <> '' )
					{
						$title = addslashes(htmlentities($title));
						$content = addslashes(htmlentities($content));
						$editreason = addslashes(htmlentities($editreason));
						db_query("UPDATE $suforum SET title = '$title', content = '$content', editdate = NOW(), edituserid = '{$session['user']['acctid']}', editreason = '$editreason' WHERE postid = $postid");
						set_module_setting('newestpostdate',date("Y-m-d H:i:s"));
						output('`n`3The post has been updated, click the continue nav link to return.`0`n');
						addnav('Continue',$from.'&op=thread:'.get_module_pref('lastthread'));
						page_footer();	
					}
					else
					{
						output('`n`$Error: You need to input a title and message!`0');
					}
			}
			else
			{
				$sql = "SELECT title, content, editreason
						FROM $suforum
						WHERE postid = $postid";
				$result = db_query($sql);
				$row = db_fetch_assoc($result);
				$title = html_entity_decode(stripslashes($row['title']));
				$content = html_entity_decode(stripslashes($row['content']));
				$editreason = html_entity_decode(stripslashes($row['editreason']));
			}

			rawoutput('<form action="'.$from.'&op=editpost:'.$postid.'" method="POST" name="form">');
			addnav('',$from.'&op=editpost:'.$postid);

			$info = array(
				'Edit Post,title',
				'title'=>'Title:,string,50',
				'content'=>'Message:,textarea,40',
				'editreason'=>'Edit Reason:,string,50'
			);

			$data = array(
				'title'=>$title,
				'content'=>$content,
				'editreason'=>$editreason
			);

			showform($info,$data);
			rawoutput('<input type="hidden" name="submit" value="1" /></form>');
			superuserforum_clickables();
		break;

		case 'quote':
		case 'reply':
			$quoteid = ( $op[1] ) ? $op[1] : 0;

			if( httppost('submit') )
			{
				extract(httpallpost());
				if( $content <> '' )
				{
					list($catid, $threadid, $postid, $start) = explode(':', get_module_pref('lastthread'));
					$sql = "SELECT MAX(postorder) AS ordernum
							FROM $suforum
							WHERE threadid = '$threadid'";
					$result = db_query($sql);
					$row = db_fetch_assoc($result);
					$row['ordernum']++;
					$title = get_module_pref('check_color').soap($title);
					$title = addslashes(htmlentities($title));
					$content = get_module_pref('check_color').soap($content);
					$content = addslashes(htmlentities($content));
					db_query("INSERT INTO $suforum (threadid, catid, postorder, postdate, userid, title, content) VALUES ('$threadid', '$catid', '{$row['ordernum']}', NOW(), '{$session['user']['acctid']}', '$title', '$content')");
					$postid = db_insert_id();
					$page_start = superuserforum_startpage(get_module_setting('postspp'), $threadid, $postid);
					set_module_setting('newestpostdate',date("Y-m-d H:i:s"));
					output('`n`3Your reply has been added, click the continue nav link to return.`0`n');
					addnav('Continue',$from.'&op=thread:'.$catid.':'.$threadid.':'.$postid.':'.$page_start);
					page_footer();	
				}
				else
				{
					output('`n`$Error: You need to input a message!`0');
				}
			}
			else
			{
				if( $quoteid > 0 )
				{
					$sql = "SELECT f.title, f.content
							FROM $accounts AS a
								INNER JOIN $suforum AS f
									ON a.acctid = f.userid
							WHERE f.postid = '$quoteid'";
					$result = db_query($sql);
					$row = db_fetch_assoc($result);
					$title = full_sanitize(html_entity_decode(stripslashes($row['title'])));
					$name = ( $row['name'] ) ? '='.$row['name'] : '';
					$content = '[quote'.$name.']'.html_entity_decode(stripslashes($row['content'])).'[/quote]';
				}
				else
				{
					$title = $content = '';
				}
			}

			rawoutput('<form action="'.$from.'&op=reply" method="POST" name="form">');
			addnav('',$from.'&op=reply');

			$info = array(
				'Post Reply,title',
				'title'=>'Title:,string,50',
				'content'=>'Message:,textarea,40',
			);

			$data = array(
				'title'=>$title,
				'content'=>$content,
			);

			showform($info,$data);
			rawoutput('<input type="hidden" name="submit" value="1" /></form>');
			superuserforum_clickables();
		break;

		case 'delpost':
			$postid = $op[1];

			db_query("DELETE FROM $suforum WHERE postid = '$postid'");

			output('`n`3Message Deleted`0`n');
			addnav('Return to Thread',$from.'&op=thread:'.get_module_pref('lastthread'));
		break;

		case 'newcat':
		case 'editcat':
			$catid = ( $op[1] ) ? $op[1] : 0;

			rawoutput('<form action="'.$from.'&op=newcat" method="POST" name="form">');
			addnav('',$from.'&op=newcat');

			if( httppost('submit') )
			{
				extract(httpallpost());
				if( $catname <> '' && $description <> '' )
				{
					$catname = soap($catname);
					$catname = addslashes(htmlentities($catname));
					$description = soap($description);
					$description = addslashes(htmlentities($description));
					if( $editcat > 0 )
					{
						db_query("UPDATE $suforumcats SET catname = '$catname', description = '$description' WHERE catid = $editcat");
						output('`n`3The category details have been updated, click the continue nav link to return.`0`n');
					}
					else
					{
						db_query("INSERT INTO $suforumcats (catname, description) VALUES ('$catname', '$description')");
						output('`n`3The category has been added, click the continue nav link to return.`0`n');
					}
					addnav('Continue',$from);
					page_footer();	
				}
				else
				{
					output('`n`$Error: You need to input a name and a description!`0');
				}
			}
			elseif( $catid > 0 )
			{
				$sql = "SELECT catname, description
						FROM $suforumcats
						WHERE catid = $catid";
				$result = db_query($sql);
				$row = db_fetch_assoc($result);
				$catname = html_entity_decode(stripslashes($row['catname']));
				$description = html_entity_decode(stripslashes($row['description']));
				rawoutput('<input type="hidden" name="editcat" value="'.$catid.'" />');

				$info = array('Edit Category,title');
			}
			else
			{
				$catname = $description = '';
				$info = array('Make a New Category,title');
			}

			$info['catname'] = 'Name:,string,50';;
			$info['description'] = 'Description:,textarea,40';

			$data = array(
				'catname'=>$catname,
				'description'=>$description,
			);

			showform($info,$data);
			rawoutput('<input type="hidden" name="submit" value="1" /></form>');
		break;

		case 'delcat':
			$catid = $op[1];

			db_query("DELETE FROM $suforumcats WHERE catid = '$catid'");
			db_query("DELETE FROM $suforum WHERE catid = '$catid'");
			output('`n`3Catgeory and all threads and posts within have been deleted`0`n');
		break;

		case 'help':
			output('`n`b`#Posting a New Thread`0`b`n');
			output("`3You need to enter a title and message to post a new thread. Make sure your title is descriptive of what the thread is about. The message again should describe the new thread's purpose. Colour codes do work for the title and the message and clickables work on the message section.`0`n`n");

			output('`b`#Replying`0`b`n');
			output("`3Either use the quick reply box at the bottom of each thread page, or use the '`^Post Reply`3' nav link. You simply need to type your message in this area and select reply. A title is optional.`0`n`n");

			output('`b`#Quoting`0`b`n');
			output("`3The quote link will appear for all messages. Quoting works just like replying except the quote text is entered for you. Quoting is useful in addressing a specific statement by another person, and can reduce confusion as to what or whom you are addressing.`0`n`n");

			output('`b`#Editing`0`b`n');
			output("`3You may edit the messages that you have posted to fix grammar and mistakes. Click the edit link and you will be taken to the message editor where you can make the needed changes. This link will not appear for locked threads. Moderators can edit ALL messages for the purpose of removing inappropriate material ONLY.`0`n`n");

			output('`b`#Clickables`0`b`n');
			output("`3There are clickable colour, bold/italics, and smileys in the New Thread, Reply and quote sections. The clickables will insert the code at your cursor's position.`0`n`n");

			if( get_module_pref('forummod') == 1 || $session['user']['superuser'] & SU_MEGAUSER )
			{
				output('`b`#Creating/Editing a Category`0`b`n');
				output("`3Only admins/moderators can create and edit categories.`0`n`n");

				output('`b`#Deleting a Category`0`b`n');
				output("`3Only admins can delete categories.`0`n`n");

				output('`b`#Locking Threads`0`b`n');
				output("`3Only admins/moderators can lock and unlock threads to stop people posting new messages to them. Even when locked, moderators can still post to them.`n`n");

			//	output('`b`#`0`b`n');
			//	output("`3`0`n`n");

				output('`b`#Deleting`0`b`n');
				output("`3Deleting messages is an admin/moderator only privilege. You can delete single posts or the entire thread.`0`n`n");
			}

			output('`b`#Player Preferences`0`b`n');
			output("`3In your preferences you'll find a tab for this forum with 3 options. A default colour option and 2 time format options for how you want the post dates displayed.`0`n`n");

			if( is_module_active('avatars') )
			{
				output('`b`#Avatars`0`b`n');
				output("`3If you submit an avatar then it will appear below your name.`0`n`n");
			}
		break;

		default:
			$categories = translate_inline('Categories');
			$lastpost = translate_inline('Last Post');
			$threads = translate_inline('Threads');
			$posts = translate_inline('Posts');
			$up = translate_inline('up');
			$dn = translate_inline('dn');
			$link = $from.'&order=';
			$link1 = $from.'&op=category:';
			$link2 = $from.'&op=thread:';

			if( get_module_pref('forummod') == 1 || $session['user']['superuser'] & SU_MEGAUSER )
			{
				addnav('`^New Category`0',$from.'&op=newcat');
			}

			rawoutput('<table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#999999" align="center">');
			rawoutput("<tr class=\"trhead\"><td width=\"50%\">$categories</td><td align=\"center\">$threads</td><td align=\"center\">$posts</td><td align=\"center\">$lastpost</td></tr>");

			$sql = "SELECT catid, catname, description
					FROM $suforumcats
					ORDER BY catorder";
			$result = db_query($sql);
			if( db_num_rows($result) > 0 )
			{
				$i=0;
				while( $row = db_fetch_assoc($result) )
				{
					$sql = "SELECT count(postid) AS countposts
							FROM $suforum
							WHERE catid = '{$row['catid']}'";
					$result2 = db_query($sql);
					$row2 = db_fetch_assoc($result2);
					$countposts = ( $row2['countposts'] > 0 ) ? $row2['countposts'] : 0;

					$sql = "SELECT count(threadid) AS countthreads
							FROM $suforum
							WHERE catid = '{$row['catid']}'
								AND postorder = 1";
					$result2 = db_query($sql);
					$row2 = db_fetch_assoc($result2);
					$countthreads = ( $row2['countthreads'] > 0 ) ? $row2['countthreads'] : 0;

					rawoutput('<tr valign="middle" class="'.($i%2?'trlight':'trdark').'"><td><span style="font-size:medium;"><a href="'.$from.'&op=category:'.$row['catid'].':0">');
					addnav('',$from.'&op=category:'.$row['catid'].':0');
					output_notl('`b%s`b`0', stripslashes($row['catname']));
					rawoutput('</a></span><br /><span style="font-size:smaller;">');
					output_notl('%s', stripslashes($row['description']));
					rawoutput('</span></td><td align="center" valign="middle">'.$countthreads.'</td><td align="center" valign="middle">'.$countposts.'</td><td>');

					$sql = "SELECT $suforum.postid, $suforum.threadid, $suforum.postdate, $suforum.title, $accounts.name
							FROM $suforum
								LEFT JOIN $accounts
								ON $suforum.userid = $accounts.acctid
							WHERE $suforum.catid = {$row['catid']}
							ORDER BY $suforum.postdate
							DESC LIMIT 1";
					$result2 = db_query($sql);
					if( $row2 = db_fetch_assoc($result2) )
					{
						$name = ( $row2['name'] ) ? $row2['name'] : $fallen_name;
						$page_start = superuserforum_startpage(get_module_setting('postspp'), $row2['threadid'], $row2['postid']);
						rawoutput('<a href="'.$from.'&op=thread:'.$row['catid'].':'.$row2['threadid'].':'.$row2['postid'].':'.$page_start.'#'.$row2['postid'].'">');
						addnav('',$from.'&op=thread:'.$row['catid'].':'.$row2['threadid'].':'.$row2['postid'].':'.$page_start.'#'.$row2['postid']);
						output_notl('%s`0', stripslashes($row2['title']));
						rawoutput('</a><br />');
						output('by %s`0', $name);
						rawoutput('<br />'.superuserforum_timestamp($row2['postdate']));
					}
					else
					{
						output('`2none.`0');
					}

					rawoutput('</td></tr>');
					$i++;
				}
			}
			else
			{
				rawoutput('<tr class="trlight"><td colspan="4" align="center">');
				output('`3No categories found. Have a moderator make some.`0');
				rawoutput('</td></tr>');
			}

			rawoutput('</table>');
		break;
	}
?>