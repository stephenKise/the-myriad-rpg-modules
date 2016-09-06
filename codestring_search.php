<?php
/**
	21/04/09 - v0.0.2
	+ Took into account that not all modules are just 1 file. Search files in sub-folders as well.
	+ Search all modules or only the active ones.
	+ A nice table to display them all in.

	21/05/09 - v0.0.3
	+ Option to search files for a second string, but only if a match to first string was found.
	  Helps to narrow down a file. :)

	05/06/09 - v0.0.4
	+ Fixed a couple of problems pointed out by kaizerDRAGON. :)

	13/09/2012 - v0.0.5
	+ Added core file support so these can be searched as well.

	29/08/2013 - v0.0.6
	+ Added setting to ignore certain folders from being searched. Suggestion from Eclypse ~ Xpert on DP.net
	+ Added some stats.
*/
function codestring_search_getmoduleinfo()
{
	$info = array(
		"name"=>"Code String Search",
		"description"=>"Search for a string within modules or core files.",
		"version"=>"0.0.6",
		"author"=>"`@MarcTheSlayer`2, based on `#Lonny Luberts `2codesearch module.",
		"category"=>"Administrative",
		"download"=>"http://dragonprime.net/index.php?topic=10040.0",
		"override_forced_nav"=>TRUE,
		"settings"=>array(
			"Settings,title",
				"ignore"=>"Enter folders to ignore:,textarearesizeable,50|datacache:phpmyadmin:images:stats:temp:templates:test",
				"`^Separate each folder name with a colon. Please be aware that there may be more than one folder with a particular name.,note",
				"block"=>"Select Yes if `bPQ LotGD Utils`b module is installed.,bool|",
				"`^All this does is block the nav link so you don't end up with 2 `bcode Search`b links.,note",
				"`@Any superuser can do a search but only those with the `bview source`b flag can actually view the source.,note",
			"Colour Highlighting,title",
				"`^Change the colour of the highlighted code if the default hurts your eyes.`nColour names `b*must not*`b be used.,note",
				"`QDefault: #FF8000 (orange),note",
				"comment"=>"PHP Comments colour:,string,20|#FF8000",
				"`1Default: #0000BB (blue),note",
				"default"=>"PHP Default colour:,string,20|#0000BB",
				"`~Default: #000000 (black),note",
				"html"=>"PHP HTML colour:,string,20|#000000",
				"`2Default: #007700 (green),note",
				"keyword"=>"PHP Keyword colour:,string,20|#007700",
				"`\$Default: #DD0000 (red),note",
				"string"=>"PHP String colour:,string,20|#DD0000",
				"`~Colour of the code when viewing in Mono. Default: #000000 (black),note",
				"monotext"=>"Mono text colour:,string,20|#000000",
				"`@Background colour behind matched code string. Default: #00FF00 (lime),note",
				"match"=>"Matched code background colour:,string,20|#00FF00",
		)
	);
	return $info;
}

function codestring_search_install()
{
	output("`c`b`Q%s 'codestring_search' Module.`b`n`c", translate_inline(is_module_active('codestring_search')?'Updating':'Installing'));
	module_addhook('superuser');
	return TRUE;
}

function codestring_search_uninstall()
{
	output("`n`c`b`Q'codestring_search' Module Uninstalled`0`b`c");
	return TRUE;
}

function codestring_search_dohook($hookname,$args)
{
	if( get_module_setting('block') )
	{
		blocknav('runmodule.php?module=lotgdutil&mode=codesearch');
	}

	addnav('Actions'); 
	addnav('`@Code Search','runmodule.php?module=codestring_search');

	return $args;
}

$codestring_search_stats = array('totalfiles'=>0,'folders'=>0,'size'=>0,'totalsize'=>0);

function codestring_search_listdir($start_dir='./modules')
{
	global $codestring_search_stats;
//
// Add every PHP file in the modules directory (even those in sub-directories) to an array.
//
	$files = array();
	$ignore = explode(':', get_module_setting('ignore'));

	$fh = opendir($start_dir);
	while( ($file = readdir($fh)) !== FALSE )
	{
		if( strcmp($file, '.') == 0 || strcmp($file, '..') == 0 ) continue;
		if( in_array($file, $ignore) )
		{
	//		debug("Not reading the directory '$start_dir/$file' as it has been set to ignore.");
			continue;
		}
		$filepath = ( $start_dir == './' ) ? $start_dir . $file : $start_dir . '/' . $file;
		if( is_dir($filepath) )
		{
			$codestring_search_stats['folders']++;
			$files = array_merge($files, codestring_search_listdir($filepath));
		}
		else
		{
			$file2 = strtolower($file);
			$ext = ( ($pos = strrpos($file2, '.')) !== FALSE ) ? substr($file2, $pos+1) : '';
			if( $ext == 'php' )
			{
			//	$new_path = str_replace('./modules/', '', $filepath);
			//	$new_path = explode('/', $new_path);
			//	if( $new_path[0] != '' )
			//	{
			//		$files[basename($new_path[0], '.php')][] = $filepath;
			//	}
			//	else
			//	{
					$codestring_search_stats['totalfiles']++;
					$files[basename($file, '.php')][] = $filepath;
			//	}
			}
		}
	}
	closedir($fh);

	return $files;
}

function codestring_search_run()
{
	global $session;

	$op = httpget('op');

	if( $op == 'list' )
	{
		//
		// Display a list of the active modules with links to the source code.
		//
		page_header('All Active Modules');

		$sql = "SELECT modulename
				FROM " . db_prefix('modules') . "
				WHERE active = 1
				ORDER BY modulename";
		$result = db_query($sql);
		while( $row = db_fetch_assoc($result) )
		{
			if( $session['user']['superuser'] & SU_VIEW_SOURCE )
			{
				rawoutput("<a href=\"runmodule.php?module=codestring_search&op=source&op2=".$row['modulename'].".php\" target=\"_blank\" name=\"codestring_search\">".$row['modulename']."</a><br />");
				addnav('','runmodule.php?module=codestring_search&op=source&op2='.$row['modulename'].'.php');
			}
			else
			{
				output_notl('%s`n', $row['modulename']);
			}
		}

		addnav('Options');
		addnav('Search Code','runmodule.php?module=codestring_search');
	}
	elseif( $op == 'source' )
	{
		if( $session['user']['superuser'] & SU_VIEW_SOURCE )
		{
			//
			// Display the source code for a module.
			//
			$op2 = httpget('op2');
			$core = ( httpget('core') !== FALSE ) ? 1 : 0;
			$op22 = ( $core == 1 ) ? './'.$op2 : './modules/'.$op2;
			if( file_exists($op22) )
			{
				$op3 = httpget('op3');
				$op4 = httpget('op4');
				$searchstr = stripslashes(urldecode(html_entity_decode($op3)));
				$searchstr2 = ( empty($op4) ) ? '' : stripslashes(urldecode(html_entity_decode($op4)));
				$anchor = httpget('op5');

				if( !empty($searchstr) )
				{
					//
					// Open the file and check each line for the searched code string and put the line number in an array if found.
					//
					if( $f = fopen($op22, 'r') )
					{
						$i = 1;
						$lines = array();
						do
						{
							$lines[$i] = FALSE;
							$currentline = fgets($f,4096);
							if( stristr($currentline,$searchstr) )
							{
								$lines[$i] = TRUE;
							}
							if( $lines[$i] == FALSE && !empty($searchstr2) )
							{
								if( stristr($currentline,$searchstr2) )
								{
									$lines[$i] = TRUE;
								}
							}
							$i++;
						}
						while( !feof($f) );
					}
					fclose($f);
				}

				popup_header('Source of: "'.$op22.'"');

				$coreop = ( $core == 1 ) ? '&core=1' : '';
				rawoutput('<form action="runmodule.php?module=codestring_search'.$coreop.'&op=source&op2='.$op2.'&op3='.htmlentities(urlencode($searchstr)).'&op4='.htmlentities(urlencode($searchstr2)).'&op5='.$anchor.'#L'.$anchor.'" method="POST">');
				addnav('','runmodule.php?module=codestring_search'.$coreop.'&op=source&op2='.$op2.'&op3='.htmlentities(urlencode($searchstr)).'&op4='.htmlentities(urlencode($searchstr2)).'&op5='.$anchor.'#L'.$anchor);
				$submit = ( httppost('highlight') == 1 ) ? translate_inline('Colour') : translate_inline('Mono');
				$value = ( httppost('highlight') == 1 ) ? 0 : 1;
				rawoutput('<input type="hidden" name="highlight" value="'.$value.'" /><input type="submit" value=" '.$submit.' " class="button" />');

				rawoutput('<style type="text/css">a.num{color: #000}.linenum{color: #000; padding-right: 5px; padding-left: 5px; text-align: right; width: 20px; border-right: 1px solid #000; background-color: #808080;}.line{color: '.get_module_setting('monotext').';padding-left: 2px;}</style>');
				rawoutput('<table border="0" cellpadding="0" cellspacing="0" style="background-color: #C0C0C0">');

				ini_set('highlight.comment', get_module_setting('comment'));
				ini_set('highlight.default', get_module_setting('default'));
				ini_set('highlight.html',    get_module_setting('html'));
				ini_set('highlight.keyword', get_module_setting('keyword'));
				ini_set('highlight.string',  get_module_setting('string'));

				//
				// Open the file again but with the syntax highlight function.
				// Split the returned string into an array.
				//
				$code = highlight_file($op22, TRUE);
				$arr = explode('<br />', $code);

				$i = 1;
				foreach( $arr as $line )
				{
					$line = rtrim($line);

					if( httppost('highlight') != 1 )
					{
						//
						// Go through each line and check for any non-closed colours.
						// These need to be continued onto the next line.
						//
						if( preg_match('`^(&nbsp;)*$`',$line) )
						{
							$line = '&nbsp;';
						}
						if( !empty($last_colour) )
						{
							$line = '<span style="color:'.$last_colour .'">' . $line;
						}
						if( substr_count($line, '<span') - substr_count($line, '</span') > 0 )
						{
							$line .= '</span>';
							$last_colour = explode('<span ',$line);
							$last_colour = $last_colour[count($last_colour)-1];
							$last_colour = substr($last_colour,strpos($last_colour,'#'),7);
						}
						else
						{
							$last_colour = NULL;
						}
					}
					else
					{
						//
						// Display source code in Mono by stripping out the tags put in by highlighting.
						//
						$line = strip_tags($line);
					}

					$bg_colour = ( $i % 2 ) ? ' style="background-color: #C8C8C8"' : '';
					if( !empty($lines[$i]) )
					{
						//
						// Change the <td> background colour for the line where the code string found a match.
						//
						$bg_colour = ' style="background-color: #A0A0A0"';
						//
						// Where possible, change the background colour behind the code string to make it stand out on the line.
						// This works better in mono as the syntax highlighting colour tags sometimes get in the way.
						//
						$match = get_module_setting('match');
						$find = array(' ','<','>');
						$replace = array('&nbsp;','&lt;','&gt;');
						$string = str_replace($find, $replace, $searchstr);
						$line = str_ireplace($string, '<span style="background-color:'.$match.'">'.$string.'</span>', $line);
						if( !empty($searchstr2) )
						{
							$string = str_replace($find, $replace, $searchstr2);
							$line = str_ireplace($string, '<span style="background-color:'.$match.'">'.$string.'</span>', $line);
						}
					}

					//
					// Display the current row.
					//
					rawoutput('<tr><td valign="top" class="linenum"><a href="#L'.$i.'" name="L'.$i.'" class="num">'. $i .'</a></td><td class="line"'.$bg_colour.'>'.$line.'</td></tr>');
					$i++;
				}

				rawoutput('</table></form>');

				popup_footer();
			}
			else
			{
				output('`$Error: The file "`@%s" `$could not be found!`0`n`n', $op22);
			}
		}
		else
		{
			page_header('Code Search');

			output('`@You are not allowed to view the source code.`n`nIf you believe this to be an error then please YoM the Administrator.`0');
		}
	}
	else
	{
		//
		// Display the form.
		//
		page_header('Code String Search');

		$searchstr = stripslashes(httppost('searchstr'));
		$searchstr2 = stripslashes(httppost('searchstr2'));
		$active = ( httppost('active') == 1 ) ? 1 : 0;
		$core = ( httppost('core') == 1 ) ? 1 : 0;

		rawoutput('<form action="runmodule.php?module=codestring_search" method="POST">');
		addnav('','runmodule.php?module=codestring_search');

		$info = array(
			'Code String Search,title',
			'`3String search is insensitive. ie: dragon will match DrAgOn.`nNot all strings will be identified by a green background; though the line it\'s found on will be.,note',
			'searchstr'=>'String to search for:,',
			'active'=>'Search *only* active modules?,bool',
			'core'=>'Include core files in search?,bool',
			'Narrow Search Results,title',
			'`3Any modules that contain the first string will also be search for the second if available.,note',
			'searchstr2'=>'2nd string to search for:,',
		);

		$data = array(
			'searchstr'=>$searchstr,
			'active'=>$active,
			'core'=>$core,
			'searchstr2'=>$searchstr2,
		);

		require_once('lib/showform.php');
		showform($info,$data,TRUE);
		$submit = translate_inline('Begin Search');
		rawoutput('<br /><input type="submit" value="'.$submit.'" /></form>');

		addnav('Options');
		addnav('List Active Modules','runmodule.php?module=codestring_search&op=list');

		if( !empty($searchstr) )
		{
			global $codestring_search_stats;
			//
			// Display list of modules where a match was found.
			//
			$sql = "SELECT modulename, active FROM " . db_prefix('modules');
			$result = db_query($sql);
			$active_modules = array();
			while( $row = db_fetch_assoc($result) )
			{
				$active_modules[$row['modulename']] = $row['active'];
			}

			$dir = ( $core == 1 ) ? './' : './modules';
			$files = codestring_search_listdir($dir);
			ksort($files);

			$coref = translate_inline('Core File');
			$title1 = translate_inline('Installed');
			$title2 = translate_inline('Active');
			$title3 = translate_inline('Module');
			$title4 = translate_inline('File Path');
			$yes = translate_inline('`@Yes');
			$no = translate_inline('`$No');

			$first = $second = $corefirst = $coresecond = array();
			foreach( $files as $key1 => $value1 )
			{
				$corefile = ( strpos($value1[0], '/modules/') !== FALSE ) ? FALSE : TRUE;
				if( $corefile == FALSE )
				{
					$in_array = FALSE;
					$is_active = appoencode($no);
					$is_installed = appoencode($no);

					if(	array_key_exists($key1, $active_modules) )
					{
						$is_installed = appoencode($yes);
						if( $active_modules[$key1] == 1 )
						{
							$in_array = TRUE;
							$is_active = appoencode($yes);
						}
					}
				}

				if( $in_array == TRUE || $active == 0 || $corefile == TRUE )
				{
					foreach( $value1 as $key => $value )
					{
						//
						// Search files for first string, if found put details in array.
						//
						$found = FALSE;
						$filesize = filesize($value);
						$codestring_search_stats['totalsize']+=$filesize;
						if( $f1 = fopen($value, 'r') )
						{
							$i = 0;
							do
							{
								$currentline = fgets($f1,4096);
								if( stristr($currentline,$searchstr) )
								{
									if( $corefile == TRUE )
									{
										$filename = basename($value, '.php');
										$corefirst[$filename]['path'][] = $value;
										$corefirst[$filename]['line'][] = $i;
									}
									else
									{
										$first[$key1]['path'][] = $value;
										$first[$key1]['line'][] = $i;
										$first[$key1]['active'][] = $is_active;
										$first[$key1]['installed'][] = $is_installed;
									}
									$codestring_search_stats['size']+=$filesize;
									$found = TRUE;
									break;
								}
								$i++;
							}
							while( !feof($f1) );
						}
						fclose($f1);

						if( $found == TRUE && $searchstr2 != '' )
						{
							//
							// Search file for second string.
							//
							if( $f2 = fopen($value, 'r') )
							{
								$i = 0;
								do
								{
									$currentline = fgets($f2,4096);
									if( stristr($currentline,$searchstr2) )
									{
										if( $corefile == TRUE )
										{
											$filename = basename($value, '.php');
											$coresecond[$filename]['path'][] = $value;
											$coresecond[$filename]['line'][] = $i;
											unset($corefirst[$key1]);
										}
										else
										{
											$second[$key1]['path'][] = $value;
											$second[$key1]['line'][] = $i;
											$second[$key1]['active'][] = $is_active;
											$second[$key1]['installed'][] = $is_installed;
											unset($first[$key1]);
										}
										break;
									}
									$i++;
								}
								while( !feof($f2) );
							}
							fclose($f2);
						}
					}
				}
			}

			$countfirst = count($first);
			$countsecond = count($second);
			$countcorefirst = count($corefirst);
			$countcoresecond = count($coresecond);

			if( !$countfirst && !$countsecond && !$countcorefirst && !$countcoresecond ) output('`3No matches were found.`0`n`n');

			$tl_file = translate_inline(array('file','files'));

			if( $countcoresecond > 0 )
			{
				//
				// Output list of core files where both strings were found.
				//
				output('`3%s Core %s containing both strings: `#%s `3and `#%s`0`n`n', $countcoresecond, ($countcoresecond==1?$tl_file[0]:$tl_file[1]), $searchstr, $searchstr2);
				rawoutput('<table border="0" cellspacing="0" cellpadding="2">');
				rawoutput('<tr class="trhead"><td align="center">'.$coref.'</td><td align="left">'.$title4.'</td></tr>');

				$j = 1;
				foreach( $coresecond as $key1 => $value1 )
				{
					foreach( $value1['path'] as $key => $value )
					{
						if( $session['user']['superuser'] & SU_VIEW_SOURCE )
						{
							$value = str_replace('./', '', $value);
							$i = $value1['line'][$key];
							rawoutput("<tr class=\"".($j%2?'trlight':'trdark')."\"><td>$key1</td><td><a href=\"runmodule.php?module=codestring_search&core=1&op=source&op2=$value&op3=".htmlentities(urlencode($searchstr))."&op4=".htmlentities(urlencode($searchstr2))."&op5=$i#L$i\" target=\"_blank\">$value</a></td></tr>");
							addnav('','runmodule.php?module=codestring_search&core=1&op=source&op2='.$value.'&op3='.htmlentities(urlencode($searchstr)).'&op4='.htmlentities(urlencode($searchstr2)).'&op5='.$i.'#L'.$i);
						}
						else
						{
							rawoutput('<tr class="'.($j%2?'trlight':'trdark').'"><td>'.$key1.'</td><td>&nbsp;</td></tr>');
						}
						$j++;
					}
				}

				rawoutput('</table><br /><br />');
			}
			if( $countcorefirst > 0 )
			{
				//
				// Output list of core files where first string was found.
				//
				output('`3%s Core %s containing only the first string: `#%s`0`n`n', $countcorefirst, ($countcorefirst==1?$tl_file[0]:$tl_file[1]), $searchstr);
				rawoutput('<table border="0" cellspacing="0" cellpadding="2">');
				rawoutput('<tr class="trhead"><td align="center">'.$coref.'</td><td align="left">'.$title4.'</td></tr>');

				$j = 1;
				foreach( $corefirst as $key1 => $value1 )
				{
					foreach( $value1['path'] as $key => $value )
					{
						if( $session['user']['superuser'] & SU_VIEW_SOURCE )
						{
							$value = str_replace('./', '', $value);
							$i = $value1['line'][$key];
							rawoutput("<tr class=\"".($j%2?'trlight':'trdark')."\"><td>$key1</td><td><a href=\"runmodule.php?module=codestring_search&core=1&op=source&op2=$value&op3=".htmlentities(urlencode($searchstr))."&op5=$i#L$i\" target=\"_blank\">$value</a></td></tr>");
							addnav('','runmodule.php?module=codestring_search&core=1&op=source&op2='.$value.'&op3='.htmlentities(urlencode($searchstr)).'&op5='.$i.'#L'.$i);
						}
						else
						{
							rawoutput('<tr class="'.($j%2?'trlight':'trdark').'"><td>'.$key1.'</td><td>&nbsp;</td></tr>');
						}
						$j++;
					}
				}

				rawoutput('</table><br /><br />');

			}
			if( $countsecond > 0 )
			{
				//
				// Output list of module files where both strings were found.
				//
				output('`3%s Module %s containing both strings: `#%s `3and `#%s`0`n`n', $countsecond, ($countsecond==1?$tl_file[0]:$tl_file[1]), $searchstr, $searchstr2);
				rawoutput('<table border="0" cellspacing="0" cellpadding="2">');
				rawoutput('<tr class="trhead"><td align="center">'.$title1.'</td><td align="center">'.$title2.'</td><td align="center">'.$title3.'</td><td align="left">'.$title4.'</td></tr>');

				$j = 1;
				foreach( $second as $key1 => $value1 )
				{
					foreach( $value1['path'] as $key => $value )
					{
						if( $session['user']['superuser'] & SU_VIEW_SOURCE )
						{
							$value = str_replace('./modules/', '', $value);
							$i = $value1['line'][$key];
							rawoutput("<tr class=\"".($j%2?'trlight':'trdark')."\"><td align=\"center\">{$value1['installed'][$key]}</td><td align=\"center\">{$value1['active'][$key]}</td><td>$key1</td><td><a href=\"runmodule.php?module=codestring_search&op=source&op2=$value&op3=".htmlentities(urlencode($searchstr))."&op4=".htmlentities(urlencode($searchstr2))."&op5=$i#L$i\" target=\"_blank\">$value</a></td></tr>");
							addnav('','runmodule.php?module=codestring_search&op=source&op2='.$value.'&op3='.htmlentities(urlencode($searchstr)).'&op4='.htmlentities(urlencode($searchstr2)).'&op5='.$i.'#L'.$i);
						}
						else
						{
							rawoutput('<tr class="'.($j%2?'trlight':'trdark').'"><td align="center">'.$value1['installed'][$key].'</td><td align="center">'.$value1['active'][$key].'</td><td>'.$key1.'</td><td>&nbsp;</td></tr>');
						}
						$j++;
					}
				}

				rawoutput('</table><br /><br />');
			}
			if( $countfirst > 0 )
			{
				//
				// Output list of module files where first string was found.
				//
				output('`3%s Module %s Containing only the first string: `#%s`0`n`n', $countfirst, ($countfirst==1?$tl_file[0]:$tl_file[1]), $searchstr);
				rawoutput('<table border="0" cellspacing="0" cellpadding="2">');
				rawoutput('<tr class="trhead"><td align="center">'.$title1.'</td><td align="center">'.$title2.'</td><td align="center">'.$title3.'</td><td align="left">'.$title4.'</td></tr>');

				$j = 1;
				foreach( $first as $key1 => $value1 )
				{
					foreach( $value1['path'] as $key => $value )
					{
					
						if( $session['user']['superuser'] & SU_VIEW_SOURCE )
						{
							$value = str_replace('./modules/', '', $value);
							$i = $value1['line'][$key];
							rawoutput("<tr class=\"".($j%2?'trlight':'trdark')."\"><td align=\"center\">{$value1['installed'][$key]}</td><td align=\"center\">{$value1['active'][$key]}</td><td>$key1</td><td><a href=\"runmodule.php?module=codestring_search&op=source&op2=$value&op3=".htmlentities(urlencode($searchstr))."&op5=$i#L$i\" target=\"_blank\">$value</a></td></tr>");
							addnav('','runmodule.php?module=codestring_search&op=source&op2='.$value.'&op3='.htmlentities(urlencode($searchstr)).'&op5='.$i.'#L'.$i);
						}
						else
						{
							rawoutput('<tr class="'.($j%2?'trlight':'trdark').'"><td align="center">'.$value1['installed'][$key].'</td><td align="center">'.$value1['active'][$key].'</td><td>'.$key1.'</td><td>&nbsp;</td></tr>');
						}
						$j++;
					}
				
				}

				rawoutput('</table>');
			}

			output('`n`n`^Files with a Match: %s (%s bytes)`n', $countfirst+$countsecond+$countcorefirst+$countcoresecond, number_format($codestring_search_stats['size']));
			output('Directores Read: %s`nFiles Read: %s (%s bytes)`n', $codestring_search_stats['folders'], $codestring_search_stats['totalfiles'], number_format($codestring_search_stats['totalsize']));
		}
	}

	addnav('Superuser');
	if( $session['user']['superuser'] & SU_MANAGE_MODULES )
	{
		addnav('Module Settings','configuration.php?op=modulesettings&module=codestring_search');
	}
	addnav('Back to the Grotto','superuser.php');

	page_footer();
}
?>