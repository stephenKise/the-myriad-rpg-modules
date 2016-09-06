<?php
/**
	Modified by MarcTheSlayer

	07/03/09 - v1.0.2
	+ Didn't like how you could only see the superusers for one flag at a time.
	  Now you can see at a glance who all your superusers are and by clicking
	  their names, what superuser flags they have.

	14/07/09 - v1.0.3
	+ Added 2nd page that shows player's names under each flag that they have.
*/
function check_flags_getmoduleinfo()
{
	$info = array(
		"name"=>"Check Superuser Flags",
		"description"=>"Show all your superusers and what flags they have.",
		"version"=>"1.1",
		"author"=>"Chris Vorndran`2, modified by `@MarcTheSlayer `&& Shadow",
		"category"=>"Administrative",
		"download"=>"",
	);
	return $info;
}

function check_flags_install()
{
	module_addhook('superuser');
	return TRUE;
}

function check_flags_uninstall()
{
	return TRUE;
}

function check_flags_dohook($hookname,$args)
{
	global $session;

	if( $session['user']['superuser'] & SU_MEGAUSER )
	{
		addnav('Mechanics');
		addnav('`lCheck SU Flags','runmodule.php?module=check_flags&op=flags');
	}

	return $args;
}

function check_flags_run()
{
	global $session;

	page_header('Check Superuser Flags');

	$op = httpget('op');

	$rows = array();
	$form = array();

	$sql = "SELECT acctid, name, superuser
			FROM " . db_prefix('accounts') . "
			WHERE superuser != 0
			ORDER BY acctid ASC";
	$result = db_query($sql);

	if( $op == 'flags' )
	{
		$megauser = $config = $users = $mounts = $creatures = $equipment = $riddles = $modules = $gamemaster = $petitions = $comments = $clans = $moderation = array();
		$warning = $motd = $donations = $paylog = $days = $developer = $translator = $debug = $phpnotice = $rawsql = $source = $grotto = $expire = array();
        while( $row = db_fetch_assoc($result) )
		{
			if( $row['superuser'] & SU_MEGAUSER ) 				$megauser[] 	= '<a href="user.php?op=edit&userid=' . $row['acctid'] . '">'.$row['name'].'</a>,viewonly';
			if( $row['superuser'] & SU_EDIT_CONFIG ) 			$config[] 		= '<a href="user.php?op=edit&userid=' . $row['acctid'] . '">'.$row['name'].'</a>,viewonly';
			if( $row['superuser'] & SU_EDIT_USERS ) 			$users[] 		= '<a href="user.php?op=edit&userid=' . $row['acctid'] . '">'.$row['name'].'</a>,viewonly';
			if( $row['superuser'] & SU_EDIT_MOUNTS ) 			$mounts[] 		= '<a href="user.php?op=edit&userid=' . $row['acctid'] . '">'.$row['name'].'</a>,viewonly';
			if( $row['superuser'] & SU_EDIT_CREATURES ) 		$creatures[] 	= '<a href="user.php?op=edit&userid=' . $row['acctid'] . '">'.$row['name'].'</a>,viewonly';
			if( $row['superuser'] & SU_EDIT_EQUIPMENT ) 		$equipment[] 	= '<a href="user.php?op=edit&userid=' . $row['acctid'] . '">'.$row['name'].'</a>,viewonly';
			if( $row['superuser'] & SU_EDIT_RIDDLES ) 			$riddles[] 		= '<a href="user.php?op=edit&userid=' . $row['acctid'] . '">'.$row['name'].'</a>,viewonly';
			if( $row['superuser'] & SU_MANAGE_MODULES ) 		$modules[] 		= '<a href="user.php?op=edit&userid=' . $row['acctid'] . '">'.$row['name'].'</a>,viewonly';
			if( $row['superuser'] & SU_IS_GAMEMASTER ) 			$gamemaster[] 	= '<a href="user.php?op=edit&userid=' . $row['acctid'] . '">'.$row['name'].'</a>,viewonly';
			if( $row['superuser'] & SU_EDIT_PETITIONS ) 		$petitions[] 	= '<a href="user.php?op=edit&userid=' . $row['acctid'] . '">'.$row['name'].'</a>,viewonly';
			if( $row['superuser'] & SU_EDIT_COMMENTS ) 			$comments[] 	= '<a href="user.php?op=edit&userid=' . $row['acctid'] . '">'.$row['name'].'</a>,viewonly';
			if( $row['superuser'] & SU_MODERATE_CLANS ) 		$clans[] 		= '<a href="user.php?op=edit&userid=' . $row['acctid'] . '">'.$row['name'].'</a>,viewonly';
			if( $row['superuser'] & SU_AUDIT_MODERATION ) 		$moderation[] 	= '<a href="user.php?op=edit&userid=' . $row['acctid'] . '">'.$row['name'].'</a>,viewonly';
			if( $row['superuser'] & SU_OVERRIDE_YOM_WARNING ) 	$warning[] 		= '<a href="user.php?op=edit&userid=' . $row['acctid'] . '">'.$row['name'].'</a>,viewonly';
			if( $row['superuser'] & SU_POST_MOTD ) 				$motd[] 		= '<a href="user.php?op=edit&userid=' . $row['acctid'] . '">'.$row['name'].'</a>,viewonly';
			if( $row['superuser'] & SU_EDIT_DONATIONS ) 		$donations[] 	= '<a href="user.php?op=edit&userid=' . $row['acctid'] . '">'.$row['name'].'</a>,viewonly';
			if( $row['superuser'] & SU_EDIT_PAYLOG ) 			$paylog[] 		= '<a href="user.php?op=edit&userid=' . $row['acctid'] . '">'.$row['name'].'</a>,viewonly';
			if( $row['superuser'] & SU_INFINITE_DAYS ) 			$days[] 		= '<a href="user.php?op=edit&userid=' . $row['acctid'] . '">'.$row['name'].'</a>,viewonly';
			if( $row['superuser'] & SU_DEVELOPER ) 				$developer[] 	= '<a href="user.php?op=edit&userid=' . $row['acctid'] . '">'.$row['name'].'</a>,viewonly';
			if( $row['superuser'] & SU_IS_TRANSLATOR ) 			$translator[] 	= '<a href="user.php?op=edit&userid=' . $row['acctid'] . '">'.$row['name'].'</a>,viewonly';
			if( $row['superuser'] & SU_DEBUG_OUTPUT ) 			$debug[] 		= '<a href="user.php?op=edit&userid=' . $row['acctid'] . '">'.$row['name'].'</a>,viewonly';
			if( $row['superuser'] & SU_SHOW_PHPNOTICE ) 		$phpnotice[] 	= '<a href="user.php?op=edit&userid=' . $row['acctid'] . '">'.$row['name'].'</a>,viewonly';
			if( $row['superuser'] & SU_RAW_SQL ) 				$rawsql[] 		= '<a href="user.php?op=edit&userid=' . $row['acctid'] . '">'.$row['name'].'</a>,viewonly';
			if( $row['superuser'] & SU_VIEW_SOURCE ) 			$source[] 		= '<a href="user.php?op=edit&userid=' . $row['acctid'] . '">'.$row['name'].'</a>,viewonly';
			if( $row['superuser'] & SU_GIVE_GROTTO ) 			$grotto[] 		= '<a href="user.php?op=edit&userid=' . $row['acctid'] . '">'.$row['name'].'</a>,viewonly';
			if( $row['superuser'] & SU_NEVER_EXPIRE ) 			$expire[] 		= '<a href="user.php?op=edit&userid=' . $row['acctid'] . '">'.$row['name'].'</a>,viewonly';

			addnav('','user.php?op=edit&userid=' . $row['acctid']);
		}
		
		$form[] = 'Megauser,title';
		$form = array_merge($form, $megauser);
		$form[] = 'Edit Config,title';
		$form = array_merge($form, $config);
		$form[] = 'Edit Users,title';
		$form = array_merge($form, $users);
		$form[] = 'Edit Mounts,title';
		$form = array_merge($form, $mounts);
		$form[] = 'Edit Creatures,title';
		$form = array_merge($form, $creatures);
		$form[] = 'Edit Equipment,title';
		$form = array_merge($form, $equipment);
		$form[] = 'Edit Riddles,title';
		$form = array_merge($form, $riddles);
		$form[] = 'Manage Modules,title';
		$form = array_merge($form, $modules);
		$form[] = 'Is Gamemaster,title';
		$form = array_merge($form, $gamemaster);
		$form[] = 'Edit Petitions,title';
		$form = array_merge($form, $petitions);
		$form[] = 'Edit Comments,title';
		$form = array_merge($form, $comments);
		$form[] = 'Moderate Clans,title';
		$form = array_merge($form, $clans);
		$form[] = 'Audit Moderation,title';
		$form = array_merge($form, $moderation);
		$form[] = 'Override YoM Warning,title';
		$form = array_merge($form, $warning);
		$form[] = 'Post MoTD,title';
		$form = array_merge($form, $motd);
		$form[] = 'Edit Donations,title';
		$form = array_merge($form, $donations);
		$form[] = 'Edit Paylog,title';
		$form = array_merge($form, $paylog);
		$form[] = 'Infinite Days,title';
		$form = array_merge($form, $days);
		$form[] = 'Developer,title';
		$form = array_merge($form, $developer);
		$form[] = 'Is Translator,title';
		$form = array_merge($form, $translator);
		$form[] = 'Debug Output,title';
		$form = array_merge($form, $debug);
		$form[] = 'Show PHP Notice,title';
		$form = array_merge($form, $phpnotice);
		$form[] = 'Raw SQL,title';
		$form = array_merge($form, $rawsql);
		$form[] = 'View Source,title';
		$form = array_merge($form, $source);
		$form[] = 'Give Grotto,title';
		$form = array_merge($form, $grotto);
		$form[] = 'Never Expire,title';
		$form = array_merge($form, $expire);
	}

	require_once('lib/showform.php');
	showform($form,$rows,TRUE);

	require_once('lib/superusernav.php');
	superusernav();

	page_footer();
}
?>			