<?php
//This module was requested by Diego, here you go Diego. XD

function incentives_getmoduleinfo(){
	$info = array(
		"name" => "Donation Incentives",
		"version" => "1.0",
		"author" => "`b`!Rolland`b",
		"category" => "Administrative",
		"allowanonymous"=>true,
		"override_forced_nav"=>true,
		"download" => "",
		"settings" => array(
			"Donation Incentives,title",
	    "text"=>"Text to be displayed:,textarea|",
      "header"=>"Header for incentives pages:,text|Donation Incentives",
			"Stats Link,title",
      "dstats"=>"Display link to incentives in character stats?,bool|0",
      "stathead"=>"Stat section to display link under:,text|Donations",
      "slink"=>"Title for Link to incentives in character stats:,text|Incentives",
      "name"=>"Name of Link to incentives:,text|View",
			"Village Link,title",
      "dvillage"=>"Display link to incentives in villages?,bool|0",
      "villagehead"=>"Nav section to display link under in villages:,text|Other",
      "vlink"=>"Name of Link for incentives in villages:,text|Donation Incentives",
			"Home Page Link,title",
      "dhome"=>"Display link to incentives on home page?,bool|0",
      "homehead"=>"Nav section to display link under on home page:,text|Other Info",
      "hlink"=>"Name of Link for incentives on home page:,text|Donation Incentives",
			"Lodge Link,title",
      "dlodge"=>"Display link to incentives in lodge?,bool|0",
      "lodgehead"=>"Nav section to display link under in lodge:,text|Other Info",
      "llink"=>"Name of Link for incentives in lodge:,text|Incentives",
			"Shades Link,title",
      "dshades"=>"Display link to incentives in shades?,bool|0",
      "shadeshead"=>"Nav section to display link under in shades:,text|Other",
      "shadeslink"=>"Name of Link for incentives in shades:,text|Donation Incentives",
		),
	);
	return $info;
}

function incentives_install(){
	module_addhook("charstats");
	module_addhook("village");
	module_addhook("footer-lodge");
	module_addhook("footer-home");
	module_addhook("shades");
    return true;
}

function incentives_uninstall(){
	return true;
}

function incentives_dohook($hookname,$args){

	switch($hookname){
	case "charstats":
$dstats = get_module_setting("dstats");
	if ($dstats == 1) {
$stathead = get_module_setting("stathead");
$statlink = get_module_setting("slink");
$name = get_module_setting("name");
$fullname.="<a href='runmodule.php?module=incentives&op=stats' onClick=\"".popup("runmodule.php?module=incentives&op=stats").";return false;\" target='_blank' align='center'>$name</a>";
  addcharstat("$stathead");
	addcharstat("$statlink", $fullname);
	addnav("","runmodule.php?module=incentives&op=insentives");
}
		break;
	case "village":
$dvillage = get_module_setting("dvillage");
$villagehead = get_module_setting("villagehead");
$vlink = get_module_setting("vlink");
	if ($dvillage == 1) {
	addnav("$villagehead");
	addnav("$vlink","runmodule.php?module=incentives&op=village");
}
	break;
	case "footer-lodge":
$dlodge = get_module_setting("dlodge");
$lodgehead = get_module_setting("lodgehead");
$llink = get_module_setting("llink");
	if ($dlodge == 1) {
	addnav("$lodgehead");
	addnav("$llink","runmodule.php?module=incentives&op=lodge");
}
	break;
	case "footer-home":
$dhome = get_module_setting("dhome");
$homehead = get_module_setting("homehead");
$hlink = get_module_setting("hlink");
	if ($dhome == 1) {
	addnav("$homehead");
	addnav("$hlink","runmodule.php?module=incentives&op=home");
}
	break;
	case "shades":
$dshades = get_module_setting("dshades");
$shadeshead = get_module_setting("shadeshead");
$slink = get_module_setting("slink");
	if ($dshades == 1) {
	addnav("$shadeshead");
	addnav("$slink","runmodule.php?module=incentives&op=shades");
}
	break;
}
	return $args;
}

function incentives_run(){
$text = get_module_setting("text");
$header = get_module_setting("header");
$op = httpget('op');

	switch ($op){
  case"stats":
popup_header("$header");
output(nl2br($text),true);
popup_footer();
 break;
  case"village":
page_header("$header");
output(nl2br($text),true);
addnav("Return to Village","village.php");
page_footer();
  break;
  case"lodge":
page_header("$header");
output(nl2br($text),true);
addnav("Back to Lodge","lodge.php");
page_footer();
  break;
  case"home":
page_header("$header");
output(nl2br($text),true);
addnav("Login Page","home.php");
page_footer();
  break;
  case"shades":
page_header("$header");
output(nl2br($text),true);
addnav("Return to Shades","shades.php");
page_footer();
 break;
}
}
?>