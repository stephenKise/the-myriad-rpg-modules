<?php

/**

	15/06/09 - v0.0.2

	+ Links on the 'home' page now open in a new window.



	04/09/10 - v0.0.3

	+ Fixed a problem with deleting banners.

	+ Fixed some formatting problems.



	07/05/11 - v0.0.4

	+ Fixed issue with htmlentities.

*/

function banner_exchange_getmoduleinfo()

{

	$info = array(

		"name"=>"Banner Exchange",

		"description"=>"Display banners/links to other websites linked to from home.php",

		"version"=>"0.0.4",

		"author"=>"`@MarcTheSlayer`2, ideas from JT Traub, `!`bRolland`b`0",

		"category"=>"Administrative",

		"allowanonymous"=>TRUE,

		"download"=>"http://dragonprime.net/index.php?topic=10153.0",

		"settings"=>array(

			"Banner Exchange - Settings,title",

				"homelink"=>"Display nav link as the following:,|Our Friends' Sites",

				"`^Leave blank if you do not wish to link to a separate page.`nEach banner has an individual option to display as a nav link.,note",

				"shuffle"=>"Shuffle the order of the banners?,bool|",

				"home"=>"Display a random banner at the foot of home.php?,bool|",

				"hometext"=>"Text to appear above home.php banner:,|Link Exchange",

				"override"=>"Set maximum banner width:,string,4|468",

				"`^Resize any banner that is wider than this value.,note",

			"Banner Data,title",

				"`^Use editor in the grotto to change this data.,note",

				"allprefs"=>"Banner Data:,viewonly",

			"Your Website Details,title",

				"showdetails"=>"Show your banner details?,bool|1",

				"`^This will allow other sites to add yours if they so choose.,note",

				"sitename"=>"Site Name:,|Legend of the Green Dragon",

				"siteurl"=>"Site Url:,|".getsetting('serverurl','http://localhost'),

				"sitedesc"=>"Description:,textarea,40|".getsetting('serverdesc','Another LoGD Server'),

				"bannerurl"=>"Banner Url:,|".getsetting('serverurl','http://localhost')."templates/jade/logo.jpg",

				"bannersize"=>"Banner Dimensions (width::height):,string,10|465::71",

		),

	);

	return $info;

}



function banner_exchange_install()

{

	if( is_module_active('banner_exchange') )

	{

		output("`c`b`QUpdating 'banner_exchange' Module.`b`n`c");

	}

	else

	{

		output("`c`b`QInstalling 'banner_exchange' Module.`b`n`c");

		set_module_setting('allprefs',serialize(array()));

	}



	module_addhook_priority('footer-home',40);

	module_addhook('superuser');

	return TRUE;

}



function banner_exchange_uninstall()

{

	output("`n`c`b`Q'banner_exchange' Module Uninstalled`0`b`c");

	return TRUE;

}



function banner_exchange_allprefs()

{

	$allprefs = @unserialize(get_module_setting('allprefs'));

	if( !is_array($allprefs) ) $allprefs = array();

	return $allprefs;

}



function banner_exchange_dohook($hookname,$args)

{

	switch( $hookname )

	{		

		case 'footer-home':

			addnav('Other Info');

			addnav('Affiliate Sites','runmodule.php?module=banner_exchange');

// 			if( get_module_setting('showdetails') == 1 ){

// 				addnav('Link To Us','runmodule.php?module=banner_exchange&op=linkus');

// 			}



			$banners = banner_exchange_allprefs();

			$pick = array();

			foreach( $banners as $key => $value ){

				if( $value['show'] == 1 && !empty($value['bannerurl']) )

				{

					$pick[] = $key;

				}

				if( $value['navlink'] == 1 )

				{

					addnav(array('%s',$value['sitename']), $value['siteurl'], FALSE, TRUE, '');

				}

			}

// 			if( get_module_setting('home') == 1 && count($pick) > 0 )

// 			{

// 				require_once('lib/sanitize.php');

// 				shuffle($pick);

// 				$banners = $banners[$pick[0]];

// 				$width = get_module_setting('override');

// 				$banners['width'] = ( $banners['width'] < $width ) ? $banners['width'] : $width;



// 				output_notl('`n`c%s`n', get_module_setting('hometext'));

// 				rawoutput('<a href="'.$banners['siteurl'].'" target="_blank"><img src="'.$banners['bannerurl'].'" width="'.$banners['width'].'" height="'.$banners['height'].'" border="0" alt="'.color_sanitize($banners['sitename']).'" title="'.color_sanitize($banners['sitename']).'" /></a>');

// 				output_notl('`c`n');

// 			}

		break;



		case 'superuser':

			global $session;

			if( $session['user']['superuser'] & SU_EDIT_CONFIG )

			{

				addnav('Editors');

				addnav('Banner Exchange Editor','runmodule.php?module=banner_exchange&op=editor');

			}

		break;

	}

	return $args;

}



function banner_exchange_run()

{

	global $session;



	page_header('Link Exchange');



	$op = httpget('op');

	if( $op == 'editor' )

	{

		if( $session['user']['superuser'] & SU_EDIT_CONFIG )

		{

			$banners = banner_exchange_allprefs();

			$count = count($banners);



			if( httpget('subop') == 'save' )

			{

				$count++;

				$banners = array();

				$postdata = httpallpost();

				for( $i=1; $i<=$count; $i++ )

				{

					if( $postdata["del$i"] != 1 )

					{

						$banners[$i]['show'] = $postdata["show$i"];

						$banners[$i]['navlink'] = $postdata["navlink$i"];

						$banners[$i]['sitename'] = $postdata["sitename$i"];

						$banners[$i]['siteurl'] = $postdata["siteurl$i"];

						$banners[$i]['bannerurl'] = $postdata["bannerurl$i"];

						$banners[$i]['desc'] = $postdata["desc$i"];

						$banners[$i]['width'] = abs((int)$postdata["width$i"]);

						$banners[$i]['height'] = abs((int)$postdata["height$i"]);

					}

				}



				// This resets the keys so that there are no gaps. Gaps are bad, mmkay.

				$banners2 = array();

				$i = 1;

				foreach( $banners as $value )

				{

					$banners2[$i] = $value;

					$i++;

				}

				$banners = $banners2;

				$count = count($banners);

				set_module_setting('allprefs',serialize($banners));



				output('`#Banner data has been updated`0`n');

			}



			$banner_data = $form_data = array();

			foreach( $banners as $key => $value )

			{

				$banner = array(

					"del$key"=>0,

					"show$key"=>$value['show'],

					"navlink$key"=>$value['navlink'],

					"sitename$key"=>stripslashes($value['sitename']),

					"siteurl$key"=>stripslashes($value['siteurl']),

					"desc$key"=>stripslashes($value['desc']),

					"bannerurl$key"=>stripslashes($value['bannerurl']),

					"width$key"=>$value['width'],

					"height$key"=>$value['height']

				);



				$form = array(

					"Banner $key,title",

						"del$key"=>"Delete This Banner?,bool",

						"show$key"=>"Show this website?,bool",

						"navlink$key"=>"Link on home.php?,bool",

						"`^Will add a nav link to this site on home.php,note",

						"sitename$key"=>"Name:,",

						"siteurl$key"=>"URL:,",

						"desc$key"=>"Description:,textarea,40",

						"bannerurl$key"=>"Banner URL:,",

						"width$key"=>"Banner Width:,int,3",

						"height$key"=>"Banner Height:,int,3"

				);



				$banner_data = array_merge($banner_data, $banner);

				$form_data = array_merge($form_data, $form);

			}



			$count++;



			// Add a set of empty input boxes for an additional banner.

			$banner = array(

				"del$count"=>1,

				"show$count"=>0,

				"navlink$count"=>0,

				"sitename$count"=>'',

				"siteurl$count"=>'',

				"desc$count"=>'',

				"bannerurl$count"=>'',

				"width$count"=>0,

				"height$count"=>0

			);



			$form = array(

				"Add A Banner,title",

					"del$count"=>"Add This Banner?,enum,1,No,0,Yes",

					"show$count"=>"Show this website?,bool",

					"navlink$count"=>"Link on home.php?,bool",

					"`^Will add a nav link to this site on home.php,note",

					"sitename$count"=>"Name:,",

					"siteurl$count"=>"URL:,",

					"desc$count"=>"Description:,textarea,40",

					"bannerurl$count"=>"Banner URL:,",

					"width$count"=>"Banner Width:,int",

					"height$count"=>"Banner Height:,int"

			);



			$banner_data = array_merge($banner_data, $banner);

			$form_data = array_merge($form_data, $form);



			require_once('lib/showform.php');

			rawoutput('<form action="runmodule.php?module=banner_exchange&op=editor&subop=save" method="POST">');

			addnav('','runmodule.php?module=banner_exchange&op=editor&subop=save');

			showform($form_data,$banner_data,TRUE);

			$submit = translate_inline('Save');

			rawoutput('<input type="submit" class="button" value="'.$submit.'" /></form>');



			addnav('Options');

			addnav('View Banners','runmodule.php?module=banner_exchange');

		}

		else

		{

			addnav('Read The News','news.php');

		}

	}

	elseif( $op == 'linkus' )

	{

		require_once('lib/nltoappon.php');

		output('`2If you would like to add us to your own link exchange page, then please feel free to copy and paste the following details:`n`n');

		output('`b`#Sitename:`b`n`3%s`n', translate_inline(get_module_setting('sitename')));

		rawoutput('<input type="text" size="50" value="'.get_module_setting('sitename').'" />');

		output('`n`n`b`#Site url:`b`n`3%s`n', get_module_setting('siteurl'));



		$desc = stripslashes(get_module_setting('sitedesc'));

		output('`n`n`b`#Description:`b`n`3%s`n', nltoappon(translate_inline($desc)));

		rawoutput('<textarea cols="40" rows="6">'.$desc.'</textarea>');

		output('`n`n`b`#Banner url:`b `3%s`n', get_module_setting('bannerurl'));

		rawoutput('<img src="'.get_module_setting('bannerurl').'" width="'.$sizes[0].'" height="'.$sizes[1].'" />');

		$sizes = explode('::', get_module_setting('bannersize'));

		output('`n`b`#Banner width:`b `3%s`n', $sizes[0]);

		output('`b`#Banner height:`b `3%s`n', $sizes[1]);

		output('`n`n`2Likewise, if you would like your site to be considered for our exchange page, then please send us your details in a petition.`0`n');



		if( ($homelink = get_module_setting('homelink')) != FALSE )

		{

			addnav('Options');

			addnav(array('%s',$homelink),'runmodule.php?module=banner_exchange');

		}

	}

	else

	{

		//

		// Display the banners.

		//

		$banners = banner_exchange_allprefs();

		$count = count($banners);



		if( get_module_setting('shuffle') == 1 )

		{

			shuffle($banners);

			// Shuffling creates new keys starting at 0, but we want to start at 1

			// so just move the first to the end. :)

			$banners[$count] = $banners[0];

			unset($banners[0]);

		}



		require_once('lib/sanitize.php');

		require_once('lib/nltoappon.php');



		$width = get_module_setting('override');



		for( $i=1; $i<=$count; $i++ )

		{

			rawoutput('<table border="0" width="100%" cellpadding="2" cellspacing="0">');

			if( $banners[$i]['show'] == 1 )

			{

				rawoutput('<tr><td><a href="'.$banners[$i]['siteurl'].'" target="_blank" style="font-weight: bold">');

				output('%s', stripslashes(translate_inline($banners[$i]['sitename'])));

				rawoutput('</a></td></tr>');

				if( !empty($banners[$i]['desc']) )

				{

					rawoutput('<tr><td>');

					output('%s', nltoappon(stripslashes(translate_inline($banners[$i]['desc']))));

					rawoutput('</td></tr>');

				}

				if( !empty($banners[$i]['bannerurl']) )

				{

					$banners[$i]['width'] = ( $banners[$i]['width'] < $width ) ? $banners[$i]['width'] : $width;

					rawoutput('<tr><td><a href="'.$banners[$i]['siteurl'].'" target="_blank"><img src="'.$banners[$i]['bannerurl'].'" align="left" width="'.$banners[$i]['width'].'" height="'.$banners[$i]['height'].'" border="0" alt="'.color_sanitize(htmlentities($banners[$i]['sitename'])).'" title="'.color_sanitize(htmlentities($banners[$i]['sitename'])).'" /></a></td></tr>');

				}

			}

			rawoutput('<tr><td>&nbsp;</td></tr></table>');

		}



		if( get_module_setting('showdetails') == 1 )

		{

			addnav('Options');

			addnav('Link To Us','runmodule.php?module=banner_exchange&op=linkus');

		}

	}



	if( $session['user']['loggedin'] == 1 && $session['user']['superuser'] > 0 )

	{

		addnav('Superuser');

		if( $session['user']['superuser'] & SU_EDIT_CONFIG )

		{

			addnav('Banner Exchange Editor','runmodule.php?module=banner_exchange&op=editor');

		}

		if( $session['user']['superuser'] & SU_MANAGE_MODULES )

		{

			addnav('Module Settings','configuration.php?op=modulesettings&module=banner_exchange');

		}

		addnav('Return to the Grotto','superuser.php');

	}

	else

	{

		addnav('Homepage');

		addnav('Login Page','home.php');

	}



	page_footer();

}

?>