<?php
/**
	Modified by MarcThSlayer

	11/04/09 - v1.1
	+ Added a deleted character count to nav link.
	+ Changed the search boxes to a dropdown menu showing all the names and how long ago they expired.
	+ Added options to delete the character files.
	+ Made minor alterations to code and text.

	06/09/09 - v1.2
	+ Select multiple names for deletion.

	26/09/09 - v1.3
	+ Bug from last update fixed.

	01/12/09 - v1.4
	+ Now resets 'sentnotice' in player's account so they'll get an email warning on next notice.
	+ Sends an email to the person saying their account has been restored if the email is found to be valid.

	24/11/10 - v1.5
	+ Fixed the foreach call stack error when deleting a file after a restore.

	21/05/2013 - v1.6
	+ Added a priority of 90 to the hook.
	+ Added a check for the argument 'dodel'. If this is false then no point creating the file.
*/
function charrestore_getmoduleinfo()
{
	$info = array(
		"name"=>"Character Restorer",
		"description"=>"Backup characters before they are deleted. These can be restored at a later date.",
		"version"=>"1.6",
		"author"=>"Eric Stevens`2, modified by `@MarcTheSlayer",
		"category"=>"Administrative",
		"download"=>"http://dragonprime.net/index.php?topic=10038.0",
		"settings"=>array(
			"Main Settings,title",
				"auto_snapshot"=>"Create character snapshots upon character expiration?,bool|1",
				"dk_threshold"=>"Dragon Kill threshold above which snapshots will be taken?,int|5",
				"lvl_threshold"=>"Level within this DK above which snapshots will be taken?,int|0",
				"manual_snapshot"=>"Create a snapshot when a char is manually deleted?,bool|1",
				"suicide_snapshot"=>"Create a snapshot when a user deletes themselves?,bool|0",
				"snapshot_dir"=>"Location to store snapshots,string|./logd_snapshots",
				"`^On a Unix server make sure the location is chmod 0777.,note",
				"`^Also check that the location is correct by deleting a test account.,note",
			"Email Settings,title",
				"sub"=>"Email Subject:,text|Your LotGD Account has been Restored.",
				"msg"=>"Email Message:,text|Welcome back to the Realm. -- The Staff.",
				"`^Note: If you changed the person's login name then it will be added to the end of the message.,note"
		),
	);
	return $info;
}

function charrestore_install()
{
	output("`c`b`Q%s 'charrestore' Module.`0`b`c`n", translate_inline(is_module_active('charrestore')?'Updating':'Installing'));
	module_addhook_priority('delete_character',90);
	module_addhook('superuser');
	return TRUE;
}

function charrestore_uninstall()
{
	output("`n`c`b`Q'charrestore' Module Uninstalled`0`b`c");
	return TRUE;
}

function charrestore_dohook($hookname,$args)
{
	switch( $hookname )
	{
		case 'superuser':
			global $session;
			if( $session['user']['superuser'] & SU_EDIT_USERS )
			{
				$path = charrestore_getstorepath();
				$count = 0;
				if( $handle = opendir($path) )
				{
					 while( false !== ($file = readdir($handle)) )
					 {
						  if( $file != "." && $file != ".." )
						  {
						  	// Last 8 characters should be the date, make sure they're numeric.
						  	$string = substr($file, -8, 8);
						  	if( is_numeric(intval($string)) )
						  	{
									$count++;
							}
						  }
					 }
					 closedir($handle);
				}
				addnav('Actions');
				addnav(array('Restore a deleted char (%s)', $count),'runmodule.php?module=charrestore');
			}
		break;

		case 'delete_character':
			if( $args['dodel'] == FALSE ) return $args;
			if( $args['deltype'] == CHAR_DELETE_AUTO 		&& !get_module_setting('auto_snapshot') ) 		return $args;
			if( $args['deltype'] == CHAR_DELETE_MANUAL 		&& !get_module_setting('manual_snapshot') ) 	return $args;
			if( $args['deltype'] == CHAR_DELETE_SUICIDE 	&& !get_module_setting('suicide_snapshot') ) 	return $args;

			//time to create a snapshot.
			$sql = "SELECT * FROM " . db_prefix('accounts') . " WHERE acctid = '" . $args['acctid'] . "'";
			$result = db_query($sql);
			if( db_num_rows($result) > 0 )
			{
				$row = db_fetch_assoc($result);

				//test if the user is below the snapshot threshold
				if( $args['deltype'] == CHAR_DELETE_AUTO )
				{
					if ( $row['dragonkills'] < get_module_setting('dk_threshold') || $row['dragonkills'] == get_module_setting('dk_threshold') && $row['level'] < get_module_setting('lvl_threshold') )
					{
						return $args;
					}
				}

				$user = array('account'=>array(),'prefs'=>array());

				//set up the user's account table fields
				//reduces storage footprint.
				$nosavefields = array('output'=>true,'allowednavs'=>true);
				while( list($key,$val) = each($row) )
				{
					if( !isset($nosavefields[$key]) )
					{
						$user['account'][$key] = $val;
					}
				}

				//set up the user's module preferences
				$sql = "SELECT * FROM " . db_prefix('module_userprefs') . " WHERE userid = '" . $args['acctid'] . "'";
				$prefs = db_query($sql);
				while( $row = db_fetch_assoc($prefs) )
				{
					if( !isset($user['prefs'][$row['modulename']]) )
					{
						$user['prefs'][$row['modulename']] = array();
					}
					$user['prefs'][$row['modulename']][$row['setting']] = $row['value'];
				}

				//write the file
				$path = charrestore_getstorepath();
				$fp = @fopen($path.str_replace(" ","_",$user['account']['login'])."|".date("Ymd"),"w+");
				fwrite($fp,serialize($user));
				fclose($fp);
			}
		break;
	}
	return $args;
}

function charrestore_getstorepath()
{
	//returns a valid path name where snapshots are stored.
	$path = get_module_setting('snapshot_dir');
	if( substr($path,-1) != "/" && substr($path,-1) != "\\" )
	{
		$path = $path."/";
	}
	return $path;
}

function charrestore_deleted_when($date)
{
// Function written by skyhawk133 - March 2, 2005
// http://www.dreamincode.net/code/snippet86.htm

	 // array of time period chunks
	 $chunks = array(
		  array(60 * 60 * 24 * 365, 'year'),
		  array(60 * 60 * 24 * 30, 'month'),
		  array(60 * 60 * 24 * 7, 'week'),
		  array(60 * 60 * 24, 'day'),
		  array(60 * 60, 'hour'),
		  array(60, 'minute'),
	 );
	 
	 $today = time();
	 $since = $today - $date;
	 
	 // $j saves performing the count function each time around the loop
	 for( $i=0, $j=count($chunks); $i<$j; $i++ )
	 {
		  
		  $seconds = $chunks[$i][0];
		  $name = $chunks[$i][1];
		  
		  // finding the biggest chunk (if the chunk fits, break)
		  if( ($count = floor($since / $seconds)) != 0 )
		  {
				break;
		  }
	 }
	 
	 $print = ($count == 1) ? '1 '.$name : "$count {$name}s";
	 
	 if( $i + 1 < $j )
	 {
		  // now getting the second item
		  $seconds2 = $chunks[$i + 1][0];
		  $name2 = $chunks[$i + 1][1];
		  
		  // add second item if it's greater than 0
		  if( ($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0 )
		  {
				$print .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}s";
		  }
	 }
	 return $print;
}

/**
	Function taken from,
	Validate an E-Mail Address with PHP, the Right Way
	June 1st, 2007 by Douglas Lovell
	http://www.linuxjournal.com/article/9585
*/
function charrestore_validemail($email)
{
	$is_valid = TRUE;
	$atIndex = strrpos($email, "@");
	if( is_bool($atIndex) && !$atIndex )
	{
		$is_valid = FALSE;
	}
	else
	{
		$domain = substr($email, $atIndex+1);
		$local = substr($email, 0, $atIndex);
		$localLen = strlen($local);
		$domainLen = strlen($domain);
		if( $localLen < 1 || $localLen > 64 )
		{
			// local part length exceeded
			$is_valid = FALSE;
		}
		elseif( $domainLen < 1 || $domainLen > 255 )
		{
			// domain part length exceeded
			$is_valid = FALSE;
		}
		elseif( $local[0] == '.' || $local[$localLen-1] == '.' )
		{
			// local part starts or ends with '.'
			$is_valid = FALSE;
		}
		elseif( preg_match('/\\.\\./', $local) )
		{
			// local part has two consecutive dots
			$is_valid = FALSE;
		}
		elseif( !preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain) )
		{
			// character not valid in domain part
			$is_valid = FALSE;
		}
		elseif( preg_match('/\\.\\./', $domain))
		{
			// domain part has two consecutive dots
			$is_valid = FALSE;
		}
		elseif( !preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local)) )
		{
			// character not valid in local part unless 
			// local part is quoted
			if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local)))
			{
				$is_valid = FALSE;
			}
		}
		if ($is_valid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")) )
		{
			// domain not found in DNS
			$is_valid = FALSE;
		}
	}
	return $is_valid;
}

function charrestore_run()
{
	global $session;

	check_su_access(SU_EDIT_USERS);

	page_header('Character Restore');

	$file = ( httpget('file') != '' ) ? httpget('file') : httppost('file');
	$op = ( httppost('delete') != '' ) ? 'delete' : httpget('op');

	if( $op == 'beginrestore' && !empty($file) )
	{
		if( is_array($file) ) $file = $file[0];
		$user = unserialize(join('',file(charrestore_getstorepath().$file)));
		$sql = "SELECT count(*) AS c
				FROM " . db_prefix('accounts') . "
				WHERE login = '" . $user['account']['login'] . "'";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);

		rawoutput('<form action="runmodule.php?module=charrestore&op=finishrestore&file=' . rawurlencode(stripslashes($file)) . '" method="POST">');
		addnav('','runmodule.php?module=charrestore&op=finishrestore&file='.rawurlencode(stripslashes($file)));

		if( $row['c'] > 0 )
		{
			output("`@The user's login conflicts with an existing login in the system.`n`bHas a backup for this user aleady been restored?`b`n`n");
			output("`2You will have to provide a new one if that is not the case.`n");
			output("`^New Login: ");
			rawoutput('<input name="newlogin" value="" /><br />');
		}

		output("`n`#Some user info:`0`n");
		$vars = array(
			"login"=>"Login",
			"name"=>"Name",
			"laston"=>"Last On",
			"emailaddress"=>"Email",
			"dragonkills"=>"DKs",
			"level"=>"Level",
			"gentimecount"=>"Total hits",
		);

		while( list($key,$val) = each($vars) )
		{
			output("`^$val: `#%s`n",$user['account'][$key]);
		}

		$submit = translate_inline('Continue Restore');
		rawoutput('<input type="submit" value="' . $submit . '" class="button" /></form>');
	}
	elseif( $op == 'finishrestore' )
	{
		$user = @unserialize(join('',file(charrestore_getstorepath().$file)));
		$newlogin = ( httppost('newlogin') > '' ) ? httppost('newlogin') : '';
		$sql = "SELECT count(*) AS c
				FROM " . db_prefix('accounts') . "
				WHERE login = '" . ($newlogin > '' ? $newlogin : $user['account']['login']) . "'";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		if( $row['c'] > 0 )
		{
			redirect('runmodule.php?module=charrestore&op=beginrestore&file='.rawurlencode(stripslashes($file)));
		}
		else
		{
			if( $newlogin > '' )
			{
				$user['account']['login'] = $newlogin;
				$old_accountid = $user['account']['acctid'];
				unset($user['account']['acctid']); // Remove the acctid field so new id will be given on DB insert.
			}
			$sql = "DESCRIBE " . db_prefix('accounts');
			$result = db_query($sql);
			$known_columns = array();
			while( $row = db_fetch_assoc($result) )
			{
				$known_columns[$row['Field']] = TRUE;
			}

			$keys = array();
			$vals = array();

			while( list($key,$val) = each($user['account']) )
			{
				if( $key == 'laston' )
				{
					array_push($keys,$key);
					array_push($vals,"'".date("Y-m-d H:i:s",strtotime("-1 day"))."'");
				}
				elseif ( !isset($known_columns[$key]) )
				{
					output("`2Dropping the column `^%s`n",$key);
				}
				else
				{
					array_push($keys,$key);
					array_push($vals,"'".addslashes($val)."'");
				}
			}

			// Reset the sent notice value, otherwise they wont get a expire warning email next time.
			$sentnotice_key = array_search('sentnotice', $keys);
			$vals[$sentnotice_key] = 0;

			$sql = "INSERT INTO " . db_prefix('accounts') . " (\n".join("\t,\n",$keys).") VALUES (\n".join("\t,\n",$vals).")";
			// $sql is debugged below if there's an error.
			db_query($sql);
			$id = db_insert_id();
			if( $id > 0 )
			{
				// Send email.
				$emailaddress_key = array_search('emailaddress', $keys);
				debug($vals[$emailaddress_key]);
				debug(charrestore_validemail($vals[$emailaddress_key]));
				if( !empty($vals[$emailaddress_key]) && charrestore_validemail($vals[$emailaddress_key]) !== FALSE )
				{
					$message = translate_inline(get_module_setting('msg'));
					if( $newlogin > '' )
					{
						$message .= "\r\n\r\n" . translate_inline("You're new login name is ") . $newlogin;
					}
					mail($vals[$emailaddress_key],translate_inline(get_module_setting('sub')),$message,"From: ".getsetting('gameadminemail','postmaster@localhost.com'));
			//		output('To: %s`nFrom: %s`nSub: %s`nMsg: %s', $vals[$emailaddress_key], getsetting('gameadminemail','postmaster@localhost.com'), get_module_setting('sub'), $message);
				}

				addnav('Edit restored user',"user.php?op=edit&userid=$id");
				if( $id != $old_accountid )
				{
					output("`^The account was restored, though the account ID was not preserved; things such as news, mail, comments, debuglog, and other items associated with this account that were not stored as part of the snapshot have lost their association.");
					output("The original ID was `&%s`^, and the new ID is `&%s`^.", $old_accountid, $id);
					output("The most common cause of this problem is another account already present with the same ID.");
					output("Did you do a restore of an already existing account?  If so, the existing account was not overwritten.`n");
				}
				else
				{
					output("`#The account was restored.`n");
				}
				output("`#Now working on module preferences.`n");
				while( list($modulename,$values) = each($user['prefs']) )
				{
					output("`3Module: `2%s`3...`n",$modulename);
					if( is_module_installed($modulename) )
					{
						while( list($prefname,$value) = each($values) )
						{
							set_module_pref($prefname,$value,$modulename,$id);
						}
					}
					else
					{
						output("`\$Skipping prefs for module `^%s`\$ because this module is not currently installed.`n",$modulename);
					}
				}
				output('`#The preferences were restored.`n`n');

				rawoutput('<form action="runmodule.php?module=charrestore&op=delete" method="POST">');
				addnav('','runmodule.php?module=charrestore&op=delete');
				$submit = translate_inline('Yes, Delete');
				output('`#Do you wish to delete the file now?');
				rawoutput('<input type="hidden" name="file[]" value="' . stripslashes($file) . '" />');
				rawoutput('<input type="submit" value="' . $submit . '" class="button" /></form>');
			}
			else
			{
				output("`\$Something funky has happened, preventing this account from correctly being created.");
				output("I'm sorry, you may have to recreate this account by hand. The SQL I tried was:`n");
				rawoutput("<pre>".htmlentities($sql, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</pre>");
				debug(htmlentities($sql, ENT_COMPAT, getsetting("charset", "ISO-8859-1")));
			}
		}
	}
	elseif( $op == 'delete' )
	{
		if( is_array($file) && !empty($file) )
		{
			foreach( $file as $key => $filename )
			{
				$path = charrestore_getstorepath().$filename;
				if( file_exists($path) )
				{
					@chmod($path, 0777);
					@unlink($path);
					if( !file_exists($path) )
					{
						output('`@The file "`7%s`@" was deleted successfully.`n', $filename);
					}
					else
					{
						chmod($path, 0644);
						output('`$Error: The file "`7%s`$" was not deleted.`n', $filename);
					}
				}
				else
				{
					output('`$Error: The file "`7%s`$" does not exist. Check that the path is correct. (%s)`n', $filename, charrestore_getstorepath());
				}
			}
		}
		else
		{
			output('`$Error: No files were selected for deletion.`0`n');
		}
	}
	else
	{
		output('`2Please note that only characters that have reached at least level %s with %s DKs will have been saved!`nThese characters are listed below.`n`n', get_module_setting('lvl_threshold'), get_module_setting('dk_threshold'));
		output('`@Note: `2Only one character can be restored at a time where as multiple characters can be deleted together.`n`n');

		$path = charrestore_getstorepath();
		if( $handle = opendir($path) )
		{
			$i = 0;
			$names = array();
			 while( false !== ($file = readdir($handle)) )
			 {
				  if( $file != "." && $file != ".." )
				  {
				  	$string = substr($file, -8, 8);
				  	if( is_numeric(intval($string)) )
				  	{
				  		// Added this check because for some reason my files have a + as a separator. 
				  		$sep = ( ($pos = strpos($file, '|')) === FALSE ) ? '+' : '|';
				  		list($name, $date) = explode($sep, $file);
				  		$lname = strtolower($name);
				  		// Put names into array so that they can be sorted.
				  		$names[$lname.$i]['file'] = $file;
				  		$names[$lname.$i]['date'] = $date;
				  		$names[$lname.$i]['name'] = $name;
				  		$i++;
						}
				  }
			 }
			 closedir($handle);
		}

		if( !empty($names) )
		{
			$i = 1;
			$select = '';
			$ago = translate_inline('ago');
			ksort($names);
			foreach( $names as $key )
			{
				$day = substr($key['date'], 6, 2);
				$month = substr($key['date'], 4, 2);
				$year = substr($key['date'], 0, 4);
				$timestamp = mktime(0, 0, 0, $month, $day, $year);
				$time = charrestore_deleted_when($timestamp);
				$style = ( $i % 2 ) ? 'trlight' : 'trdark';
		  		$name = str_replace('_', ' ', $key['name']);
				$select .= '<option value="' . $key['file'] . '" class="' . $style . '">' . $name . ' (' . $time . ' ' . $ago . ')</option>';
				$i++;
			}

			rawoutput('<form action="runmodule.php?module=charrestore&op=beginrestore" method="POST">');
			addnav('','runmodule.php?module=charrestore&op=beginrestore');
			rawoutput('<select name="file[]" size="10" multiple>'.$select.'</select>');
			$submit = translate_inline('Begin Restore');
			rawoutput('<br /><br /><input type="submit" value="' . $submit . '" class="button" />');
			$submit = translate_inline('Delete Selected File(s)');
			rawoutput('<br /><br /><input type="submit" name="delete" value="' . $submit . '" class="button" /></form>');
		}
		else
		{
			output('`$There are no deleted character files available to restore.');
		}
	}

	if( !empty($op) )
	{
		addnav('Options');
		addnav('Character List','runmodule.php?module=charrestore');
	}

	if( $session['user']['superuser'] & SU_MANAGE_MODULES )
	{
		addnav('Module');
		addnav('Module Settings','configuration.php?op=modulesettings&module=charrestore');
	}

	require_once('lib/superusernav.php');
	superusernav();

	page_footer();
}
?>