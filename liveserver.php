<?php
function liveserver_getmoduleinfo(){
	$info = array(
		"name"=>"Live Server Time",
		"version"=>"1.2",
		"author"=>"`@KaosKaizer`0 for and modified by `i`)Ae`7ol`&us`i`0",
		"category"=>"General",
		"override_forced_nav"=>true,
		"settings"=>array(
			"Live Server Time Settings,title",
			"date"=>"Show date?,bool|1",
		),
		"prefs"=>array(
			"Live Server Time Prefs,title",
			"user_catg"=>"Display under what category?,text|Vital Info",
			"user_show"=>"Show Live Server Time in stats?,bool|1",
			"`^The next preference overrides the above one!,note",
			"user_hide"=>"Hide Live Server Time in stats completely?,bool|0",
		),
	);
	return $info;
}

function liveserver_install(){
	module_addhook("charstats");
	return true;
}

function liveserver_uninstall(){
	return true;
}

function liveserver_dohook($hook,$args){
	global $session;
	switch($hook){
		case "charstats":
			if (!get_module_pref('user_hide')){
				$date = date("F d, Y h:i:s A", time()); // Fix months below!
				$script = "<script type=\"text/javascript\">var currenttime = '".$date."'
					var montharray=new Array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec')
					var serverdate=new Date(currenttime)
					function padlength(what){
						var output=(what.toString().length==1)? \"0\"+what : what
						return output
					}";
				if (get_module_setting("date") == 1){
					$script .= "function displaytime(){
						serverdate.setSeconds(serverdate.getSeconds()+1)
						var datestring=montharray[serverdate.getMonth()]+\" \"+padlength(serverdate.getDate())+\", \"+serverdate.getFullYear()
						var timestring=padlength(serverdate.getHours())+\":\"+padlength(serverdate.getMinutes())+\":\"+padlength(serverdate.getSeconds())
						var fullstring=formatDate(datestring+\" \"+timestring)
						document.getElementById(\"servertime\").innerHTML=fullstring
					}";
				}else{
					$script .= "function displaytime(){
						serverdate.setSeconds(serverdate.getSeconds()+1)
						var timestring=padlength(serverdate.getHours())+\":\"+padlength(serverdate.getMinutes())+\":\"+padlength(serverdate.getSeconds())
						timestring=formatDate(timestring)
						document.getElementById(\"servertime\").innerHTML=timestring
					}";
				}
				$script .= "window.onload=function(){
						setInterval(\"displaytime()\", 1000)
					}
					function formatDate(date) {
						var hh = serverdate.getHours()
						var m = serverdate.getMinutes()
						var s = serverdate.getSeconds()
						var dd = \"AM\"
						var h = hh
						if (h >= 12) {
							h = hh-12
							dd = \"PM\"
						}
						if (h == 0) {
							h = 12
						}
						m = m<10?\"0\"+m:m
						s = s<10?\"0\"+s:s
						var pattern = new RegExp(\"0?\"+hh+\":\"+m+\":\"+s)
						var ret = h+\":\"+m+\":\"+s+\" \"+dd;
						return date.replace(pattern,ret);
					}</script>";
					
					if (get_module_pref('user_show')){
						$onoroff = "`nTurn Off";
						$span = "<span id='servertime'></span>";
					} else {
						$onoroff = "Turn On";
						$span = "";
					}
					
					$link = "<a href='runmodule.php?module=liveserver&op=change' onClick=\"".popup("runmodule.php?module=liveserver&op=change").";return false;\" target='_blank' align='center'>$onoroff</a>";
					
					rawoutput($script);
					addcharstat(get_module_pref('user_catg'));
					addcharstat("Server Time",$span.$link);
			}
		break;
	}
	return $args;
}

function liveserver_run(){
	global $session;
	$op = httpget('op');
	
	popup_header("Live Server Time");
	
	if ($op == 'change'){
		if (get_module_pref('user_show')){
			$which = "`\$OFF";
			set_module_pref('user_show',0);
		} else {
			$which = "`@ON";
			set_module_pref('user_show',1);
		}
		
		output("`&That Live Server time in your Stats are now turned `b%s`b`&. Close this window, and navigate or refresh your original page to view the changes.`n`n",$which);
		output("`i`^Note that the Live Server Time can be completely hidden by going to your Preferences.`n`n");
		rawoutput("<form action='runmodule.php?module=liveserver&op=close' method='POST'>");
		rawoutput("<input type='submit' value='Close' name='saveclose'>");
		rawoutput("</form>");
	}
	
	if ($op == 'close'){
		if (httppost("saveclose")>""){
			rawoutput("<script language='javascript'>window.close();</script>");
		}
	}
	
	popup_footer();
}

?>