<?php
function newdaybar_getmoduleinfo(){
	$info = array(
		"name"=>"Live New Day Timer",
		"version"=>"1.4A",
		"author"=>"Programmer16, with creative influence from Nicholas Moline, based off of New Day Bar by Joshua Ecklund<br>Additions and modifications by `i`)Ae`7ol`&us`i`0",
		"download"=>"http://www.hogwartsnow.com/customfiles/modules/newdaybar.zip",
		"category"=>"Stat Display",
		"settings"=>array(
			"New Day Bar Module Settings,title",
				"`^If both are set to No then `i`b`\$Hidden`b`i`^ will be displayed.,note",
				"showtime"=>"Show time to new day,bool|1",
				"showbar"=>"Show time as a bar,bool|1",
			"New Day Bar Colour Settings,title",
				"`^Use regular LOGD colour codes for this setting,note",
				"counter"=>"Colour of counter?,text|`@",
				"`^User HTML-style colour codes for these settings (make sure they're contrasting!),note",
				"bar"=>"Colour of bar?,text|#00FF00",
				"ebar"=>"Colour of empty bar?,text|#777777",
		),
	);
	return $info;
}

function newdaybar_install(){
	module_addhook("charstats");
	return true;
}

function newdaybar_uninstall(){
	return true;
}

function newdaybar_dohook($hookname,$args){
	global $session;
	require_once("lib/datetime.php");
	
	switch($hookname){
		case "charstats":
			$settings = get_all_module_settings();
			
            $details = gametimedetails();
            $secstonewday = secondstonextgameday($details);
			
			// Just in case some fool decides to forget to put in the correct codes first, especially with th HTML codes..
			if (!strstr($settings['bar'],"#")) $settings['bar'] = "#".$settings['bar'];
			if (!strstr($settings['ebar'],"#")) $settings['ebar'] = "#".$settings['ebar'];
			if (!strstr($settings['counter'],"`")) $settings['counter'] = "`".$settings['counter'];
			
			$newdaytxt = "<span id=\"newdaytimer\">" . date("G\\h i\\m s\\s",$secstonewday) . "</span>" . "
				
				<script type=\"text/javascript\">
					var nd_secondsleft = ".$details['realsecstotomorrow'].";
					var newdaytimer;
					function updatetimeleft () {
						var strdur = '';
						if (nd_secondsleft > 1) {
							nd_secondsleft--;
							
							var newdaypct = nd_secondsleft / ".$details['secsperday']." * 100;
							var newdaynon = 100 - newdaypct;
							if (newdaypct > 100) { newdaypct = 100; newdaynon = 0; }
							if (newdaypct < 0) { newdaypct = 0; newdaynon = 100; }
							
							var hours = Math.floor(nd_secondsleft/3600);
							var remainder = nd_secondsleft - (hours * 3600);
							var minutes = Math.floor(remainder/60);
							var seconds = remainder - (minutes * 60);
							
							if (".$settings['showtime']."){
								strdur = strdur + seconds + \"s\";
								if (hours + minutes > 0) { strdur = minutes + \"m \" + strdur; }
								if (hours > 0) { strdur = hours + \"h \" + strdur; }
							}
							if (".$settings['showbar'].") { strdur = strdur + \"<br><table style='border: solid 1px #000000' bgcolor='".$settings['ebar']."' cellpadding='0' cellspacing='0' width='70' height='5'><tr><td width='\" + newdaypct + \"%' bgcolor='".$settings['bar']."'></td><td width='\" + newdaynon + \"%'></td></tr></table>\"; }
							
							document.getElementById(\"newdaytimer\").innerHTML = strdur;
							setTimeout(updatetimeleft,1000);
						} else {
							if (".$settings['showtime']."){ strdur = strdur + \"New Day Here\"; }
							if (".$settings['showbar'].") { strdur = strdur + \"<br><table style='border: solid 1px #000000' bgcolor='".$settings['ebar']."' cellpadding='0' cellspacing='0' width='70' height='5'><tr><td width='100%'></td></tr></table>\"; }
							document.getElementById(\"newdaytimer\").innerHTML = strdur;
						}
					}
					updatetimeleft();
				</script>";
			
	        $new = "";
			if (!$settings['showtime'] && !$settings['showbar']) $new .= "`b`\$Hidden`b";
			if ( $settings['showtime'] ||  $settings['showbar']) $new .= $settings['counter'] . $newdaytxt;
			setcharstat("Personal Info", "Next Day", $new);
		break;
	}
	return $args;
}
?>