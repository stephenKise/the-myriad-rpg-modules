<?php
function avatars_getmoduleinfo(){
	$info = array(
		"name"=>"Avatars",
		"version"=>"2.6",
		"vertxtloc"=>"http://dragonprime.net/users/CortalUX/",
		"author"=>"`^CortalUX`@- Based upon code by `#Lonnyl`@ and `#Anpera`@.",
		"category"=>"Character",
		"download"=>"http://dragonprime.net/users/CortalUX/avatars.zip",
		"override_forced_nav"=>true,
		"settings"=>array(
			"General,title",
			"maxwidth"=>"Max. width of Avatars (Pixel),range,20,1000,20|200",
			"maxheight"=>"Max. height of Avatars (Pixel),range,20,1000,20|200",
			"suApprove"=>"Can superusers (of any kind) upload/set avatars without them having to be approved?,bool|0",
			"picApprove"=>"Do avatars have to be approved?,bool|0",
			"cost"=>"Do users have to pay?,int|0",
			"(if this is set to 0- it's free),note",
		),
		"prefs"=>array(
			"Avatars,title",
			"user_showavatar"=>"Display your avatar in your bio?,bool|1",
			"user_avatar"=>"URL of your avatar|",
			"`^Please upload the URL to the actual image file&#44; and not a URL to a webpage holding the image,note",
			"`^Do not enter a link that is explicit in any way. Xythen is PG-13 and we'd like to keep it that way. Thank you,note",
			"mostrecent_avatar"=>"URL of last known approved avatar|",
			"approved"=>"Is this users avatar approved?,bool|0",
			"systemmail"=>"Has user received mail?,bool|0",
			"banUser"=>"Is this user banned from having an avatar?,bool|0",
			"alApprove"=>"Can this user upload/set avatars without it having to be approved?,bool|0",
		),
	);
	return $info;
}

function avatars_install(){
	module_addhook("biotop");
	module_addhook("footer-prefs");
	return true;
}

function avatars_uninstall(){
	return true;
}

function avatars_dohook($hookname,$args){
	global $session;
	switch($hookname){
		case "biotop":
			if (get_module_pref("user_showavatar")) {
				avatars_resizeshow($args['acctid'], $args['name']);
			}
		break;
		case "footer-prefs":
			$user_avatar = get_module_pref("user_avatar");
			$allowed = array(".gif", ".GIF", ".jpg", ".JPEG", ".jpeg", ".JPG", ".png", ".PNG", ".tif", ".tiff");
			if (!get_module_pref("banUser")){
				$a = false;
				foreach ($allowed as $ext){
					if (in_array(substr($user_avatar, -1*strlen($ext)), $allowed)) $a = true;
				}
				if ($a){
					set_module_pref("approved",1);
					set_module_pref("mostrecent_avatar",$user_avatar);
				}else{
					output("`\$Avatars can only end in ".implode(" or ",$allowed)."!`0");
				}
			} else {
				clear_module_pref('user_avatar');
				addnav("You are banned from avatars.","");
			}
		break;
	}
	return $args;
}

function avatars_resizeshow($acctid, $name) {
	require_once("lib/sanitize.php");
	$avatar = get_module_pref("mostrecent_avatar","avatars",$acctid);
	$approved = get_module_pref("approved","avatars",$acctid);
	
	$avatar = stripslashes(preg_replace("'[\"\'\\><@?*&#; ]'","",$avatar));
	
	if ($avatar && $approved){
		$maxwidth = get_module_setting("maxwidth");
		$maxheight = get_module_setting("maxheight");
		
		$pic_size = @getimagesize($avatar);
		$pic_width = $pic_size[0];
		$pic_height = $pic_size[1];
		
		rawoutput("<center><img src='$avatar' ");
		
		if ($pic_width > $maxwidth || $pic_height > $maxheight){
			$pic_width_diff = $pic_width/$maxwidth;
			$pic_height_diff = $pic_height/$maxheight;
			$largest_diff = max($pic_width_diff, $pic_height_diff);
			$new_width = $pic_width/$largest_diff;
			$new_height = $pic_height/$largest_diff;
			rawoutput("width='$new_width' ");
			rawoutput("height='$new_height' ");
		}
		rawoutput("alt='".sanitize($name)."'></center>");
		output_notl("`n`n");
	}
}
?>