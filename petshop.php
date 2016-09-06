<?php 
/************** 
Name: Pet Shop 
Author: Eth - ethstavern(at)gmail(dot)com  
Version: 3.81 
Release Date: 12-21-2005 
About: A place to buy and sell pets. Editor included. 
Translation compatible. Mostly.  
*****************/ 

/**
	Modified by MarcTheSlayer

	30/04/09 - v4.0.0
	+ Separated module into smaller files.
	+ Made use of allprefs to store the player's pet data.
	+ Fixed bugs/improved code where required. Including SQL table code.
	+ Breed age limit, shop owner's name, changeable breed categories.
	+ Incorporates Eth's 'wildpets' module with code modifications.
	+ Incorporates Eth's pets from his 'wildpets' and 'extrapets' modules.
	+ Fully translator ready. (fingers crossed :D)

	10/05/09 - v4.0.1
	+ Improvements to update code and elsewhere.
	+ New page to show list of players with pets. Admin link in player's bio to pet editor.
	+ Fixed a bug that tried to take money from you for a gift. Thanks Aura. :)
*/
function petshop_getmoduleinfo()
{
	$info = array(
		"name"=>"Pet Shop",
		"description"=>"A pet shop to buy pets, some give buffs and some don't. Chance to catch a wild animal(pet) in the forest.",
		"version"=>"4.0.1",
		"author"=>"Eth`2, modified by `@MarcTheSlayer",
		"category"=>"Pets",
		"download"=>"http://dragonprime.net/index.php?topic=10079.0",
		"settings"=>array(
			"Pet Shop - Main,title",
				"petshopname"=>"Name of the petshop,|Ye Olde Pet Shoppe",			
				"petshoploc"=>"Where does the petshop appear?,location|".getsetting('villagename', LOCATION_FIELDS),
				"ownersname"=>"Name of the female owner:,|`gS`@e`2a`gs`@o`2n`gs`0",
				"categories"=>"Breed categories:,|Common::Exotic::Mythical",
				"`^Separate each category with a double colon. `$::`n`^Don't alter the order once categories have pets.`n`@A 'storage' category is hard coded and numbered 99.,note",
				"givegift"=>"Allow players to buy pets as gifts?,bool|1",
				"selllevel"=>"At which level can a user sell a pet?,range,1,15,1|14",
				"checkup"=>"Allow how many checkups per day?,range,0,10,1|1",
				"`^A checkup is similar to feeding your mount.,note",
			"Pet Shop - Misc,title",
				"dklose"=>"Allow chance to lose pet after a DK?,bool|0",
				"losechance"=>"1 in x chance for player to lose pet:,range,1,25,1|3",
				"battlelose"=>"Allow chance to lose pet after a lost battle?,bool|0",			
				"petchance"=>"1 in x chance for player to lose pet after a defeat:,range,1,25,1|15",
				"oldage"=>"All pets to die of old age?,bool|0",
				"oldagechance"=>"1 in x chance for pet to die of old age:,range,1,25,1|5",							
				"`^Only once the pet has reach their types age limit will chance be a factor.,note",
				"turnslost"=>"Turns lost when a pet dies:,int|5",
			"Pet Shop - Events,title",
				"wildpets"=>"Allow wild pets to be caught?,bool|1",
				"forestodds"=>"Base chance of forest event:,range,0,100,5|75",
				"travelodds"=>"Base chance of travel event:,range,0,100,5|35",
		),
		"prefs"=>array(
			"Pet Shop User Preferences,title",
				"allprefs"=>"Allprefs data.,viewonly|",
				"`^Use allprefs editor to edit this player's data.,note",
		)
	);
	return $info; 
}

function petshop_install()
{
	require_once('modules/petshop/petshop_install.php');
	return TRUE; 
}

function petshop_uninstall()
{
	output("`c`b`Un-Installing 'petshop' Module.`0`b`c`n`n`#Dropping 'pets' table...`0`n");		
	db_query("DROP TABLE IF EXISTS " . db_prefix('pets'));
	return TRUE; 
}

function petshop_dohook($hookname,$args)
{
	global $session;

	$user_id = ( isset($args['acctid']) ) ? $args['acctid'] : $session['user']['acctid'];
	$allprefs = get_allprefs($user_id);

	require_once("modules/petshop/dohook/$hookname.php");

	return $args; 
}

function petshop_runevent($type,$from)
{
	global $session;

	$default_msg = TRUE;
	if( get_module_setting('wildpets') == 1 )
	{
		$default_msg = FALSE;
		$allprefs = get_allprefs($session['user']['acctid']);

		require_once('modules/petshop/petshop_runevent.php');
	}

	if( $default_msg == TRUE )
	{
		output('`2You hear rustling coming from nearby bushes. Fearing it may be some crazy wild animal you\'ve heard stories about, you quickly move on.`0`n');
	}
}

function petshop_run()
{
	global $session;

	$ownersname = get_module_setting('ownersname');
	$petshopname = get_module_setting('petshopname');
	page_header(full_sanitize($petshopname));

	$allprefs = get_allprefs($session['user']['acctid']);

	$op = httpget('op');

	require_once("modules/petshop/run/case_$op.php");

	addnav('Superuser');
	if( $op == 'editor' )
	{
		addnav(array('%s`0',get_module_setting('petshopname')),'runmodule.php?module=petshop&loc=village');
	}
	if( $session['user']['superuser'] & SU_EDIT_USERS )
	{
		addnav('List Pet Players','runmodule.php?module=petshop&op=editor&op2=players');
	}
	if( $session['user']['superuser'] & SU_EDIT_MOUNTS && $op != 'editor' )
	{
		addnav('Pet Editor','runmodule.php?module=petshop&op=editor&op2=view&cat='.$cat);
	}
	if( $session['user']['superuser'] & SU_MANAGE_MODULES )
	{
		addnav('Pet Shop Settings','configuration.php?op=modulesettings&module=petshop');
	}
	if( $session['user']['superuser'] > 0 && ($session['user']['superuser'] & SU_DOESNT_GIVE_GROTTO) )
	{
		addnav('The Grotto','superuser.php');
	}

	page_footer(); 
} 

function get_allprefs($user_id = FALSE, $data = FALSE)
{
	if( !empty($user_id) )
	{
		$allprefs = unserialize(get_module_pref('allprefs','petshop',$user_id));
	}
	if( empty($user_id) || !is_array($allprefs) )
	{
		$allprefs = array('haspet'=>0,'pettype'=>'','petgender'=>0,'petname'=>'','petage'=>0,'neglect'=>0,'petattack'=>0,'mindamage'=>0,'maxdamage'=>0,'petturns'=>0,'checkup'=>0,'special'=>'','wildpet'=>0,'giftid'=>0);

		if( isset($data) && is_array($data) )
		{
			foreach( $data as $key => $value )
			{
				if( array_key_exists($key, $allprefs) )
				{
					$allprefs[$key] = $value;
				}
			}
			debug($allprefs);
		}
	}
	return $allprefs;
}

function genders($gender = 0, $type = 0)
{
	$male = translate_inline(array('Male','his','he','him'));
	$female = translate_inline(array('Female','her','she','her'));

	if( empty($gender) )
	{
		return $male[$type];
	}
	else
	{
		return $female[$type];
	}
}

function pet_messages($msg, $allprefs)
{
	$search = array('%N','%B','%P','%O');
	$replace = array($allprefs['petname'],$allprefs['pettype'],genders($allprefs['petgender'], 1),genders($allprefs['petgender'], 2));
	$msg = str_replace($search, $replace, $msg);
	output_notl('`n`2%s`0`n', $msg);
}
?>