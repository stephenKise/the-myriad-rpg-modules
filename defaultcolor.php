<?php
/*
 * Title:	   Default Commentary Color Selection
 * Date:	Sep 06, 2004
 * Version:	1.11
 * Author:	  Joshua Ecklund
 * Email:	   m.prowler@cox.net
 * Purpose:	 Allow user to select a default color for their
 *			  commentary and/or emote text.
 *
 * --Change Log--
 *
 * Date:		Sep 06, 2004
 * Version:	1.0
 * Purpose:	 Initial Release
 *
 * Date:		Sep 06, 2004
 * Version:	1.1
 * Purpose:	 Fixed emote bug, emotes are now colored just like
 *			  other commentary.
 *
 * Date:		Sep 06, 2004
 * Version:	1.11
 * Purpose:	 Changed to allow each user to set both commentary
 *			  color and emote color for themselves.
 *
 */
/**
	Modified by MarcTheSlayer

	20/05/09 - v1.2.0
	+ Addhook priority 1, we don't our added colour code to screw anything up.
	+ Changed code to work with /game or any other switch.
	+ Colour codes now hard coded into the player's prefs as a dopdown menu.
*/
function defaultcolor_getmoduleinfo()
{
	$info = array(
		"name"=>"Default Commentary Color",
		"description"=>"Allow players to set a default commentary colour.",
		"version"=>"1.2.0",
		"author"=>"Joshua Ecklund`2, modified by `@MarcTheSlayer",
		"category"=>"Commentary",
		"download"=>"http://dragonprime.net/index.php?topic=10127.0",
		"settings"=>array(
			"Readme,title",
				"`^Switches need to be entered with a trailing space for them to work!,note",
		),
		"prefs"=>array(
			"Default Commentary Color,title",
				"user_color"=>"Color code for chatting:,enum,0,None,`!,Light Blue,`1,Dark Blue,`@,Light Green,`2,Dark Green,`#,Light Cyan,`3,Dark Cyan,`$,Light Red,`4,Dark Red,`%,Light Magenta,`5,Dark Magenta,`^,Light Yellow,`6,Dark Yellow,`&,Light White,`7,Dark White,`),Light Black,`~,Black,`Q,Light Orange,`q,Dark Orange,`t,Light Brown,`T,Dark Brown,`E,Light Rust,`e,Dark Rust,`L,Light LinkBlue,`l,Dark LinkBlue,`y,Khaki,`Y,Dark Khaki,`K,Dark Seagreen,`r,Rose,`R,Rose,`v,Ice Violet,`V,Blue Violet,`g,XLtGreen,`G,XLtGreen,`j,MdGrey,`J,MdBlue,`x,Burlywood,`X,Beige,`k,Aquamarine,`p,Light Salmon,`P,Salmon,`m,Wheat,`M,Tan|0",
				"user_emote"=>"Color code for emoting:,enum,0,None,`!,Light Blue,`1,Dark Blue,`@,Light Green,`2,Dark Green,`#,Light Cyan,`3,Dark Cyan,`$,Light Red,`4,Dark Red,`%,Light Magenta,`5,Dark Magenta,`^,Light Yellow,`6,Dark Yellow,`&,Light White,`7,Dark White,`),Light Black,`~,Black,`Q,Light Orange,`q,Dark Orange,`t,Light Brown,`T,Dark Brown,`E,Light Rust,`e,Dark Rust,`L,Light LinkBlue,`l,Dark LinkBlue,`y,Khaki,`Y,Dark Khaki,`K,Dark Seagreen,`r,Rose,`R,Rose,`v,Ice Violet,`V,Blue Violet,`g,XLtGreen,`G,XLtGreen,`j,MdGrey,`J,MdBlue,`x,Burlywood,`X,Beige,`k,Aquamarine,`p,Light Salmon,`P,Salmon,`m,Wheat,`M,Tan|0",
		),
	);
	return $info;
}

function defaultcolor_install()
{
	output("`c`b`Q%s 'defaultcolor' Module.`0`b`c`n", translate_inline(is_module_active('defaultcolor')?'Updating':'Installing'));
	module_addhook_priority('commentary',1);
	return TRUE;
}
					
function defaultcolor_uninstall()
{
	output("`c`b`QUn-Installing 'defaultcolor' Module.`0`b`c`n");
	return TRUE;
}

function defaultcolor_dohook($hookname, $args)
{
	$comment = $args['commentline'];

	$color = get_module_pref('user_color');
	$emote = get_module_pref('user_emote');

	if( !empty($emote) && ($comment{0} == ':' || $comment{0} == '/') )
	{
		$len = strlen($comment);
		if( substr($comment,0,2) == '::' )
		{
			$comment = '::' . $emote . substr($comment,2,$len-2);
		}
		elseif( $comment{0} == ':' )
		{
			$comment = ':' . $emote . substr($comment,1,$len-1);
		}
		elseif( $comment{0} == '/' )
		{
			// To catch /me, /game, or any other switches that may be expected by installed modules.
			// Requires a space after the switch though.
			$pos = 0;
			for( $i=1; $i<$len; $i++ )
			{
				if( $comment{$i} == ' ' )
				{
					$pos = $i;
					break;
				}
			}
			$comment = substr($comment,0,$pos+1) . $emote . substr($comment,$pos,$len-$pos);
		}
		else
		{
			$comment = $emote . $comment;
		}
	}
	elseif( !empty($color) )
	{
		$comment = $color . $comment;
	}

	$args['commentline'] = $comment;

	return $args;
}

function defaultcolor_run()
{
}
?>