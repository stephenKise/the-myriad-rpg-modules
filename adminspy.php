<?php

function adminspy_getmoduleinfo(){
	$info = array(
			"name"=>"Admin Spy",
			"category"=>"Administrative",
			"author"=>"`b`&Stephen Kise`b",
			"version"=>"1.0",
			"download"=>"nope",
			"override_forced_nav"=>true
			);
	return $info;
}
	
function adminspy_install(){
	module_addhook("bioinfo");
	return TRUE;
}

function adminspy_uninstall(){
	return TRUE;
}

function adminspy_dohook($hookname,$args){
	global $session;
	switch ($hookname){
		case "bioinfo":
			$acctid = $args['acctid'];
			if ($session['user']['superuser'] & SU_MEGAUSER){
				addnav("6?Admin Spy","runmodule.php?module=adminspy&op=spy&acctid=$acctid",false,true,"1024x768");
			}
			
		break;
	}
	return $args;
}

function adminspy_run(){
	global $session;
	$op = httpget('op');
	$acct = httpget('acctid');
	popup_header("SPY");
	switch ($op){
		case "inner_spy":
			output("<meta http-equiv='refresh' content='.05'>",true);
			//You should modify the content='.05' if your server is a bit slower.
			//I only submitted this as-is, and did not introduce a setting for it because it would use even more memory.
			$result = db_query("SELECT output FROM " . db_prefix("accounts_output") . " WHERE acctid='$acct'");
			$row = db_fetch_assoc($result);
			$md5 = md5($row['output']);
			if (httpget('md5') != $md5)
				output("<script>parent.location='runmodule.php?module=adminspy&op=spy&acctid=$acct';</script>",true);
		break;
		case "spy":
			$result = db_query("SELECT output FROM " . db_prefix("accounts_output") . " WHERE acctid='$acct'");
			$row = db_fetch_assoc($result);
			$md5 = md5($row['output']);
			rawoutput(str_replace(".focus();",".blur();",str_replace("<iframe src=","<iframe Xsrc=",$row['output'])));
			rawoutput("<iframe src='runmodule.php?module=adminspy&op=inner_spy&acctid=$acct&md5=$md5' width='0px' height='0px'>");
		break;
	}
	popup_footer();
}
?>
