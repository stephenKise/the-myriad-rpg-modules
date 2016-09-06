<?php

function namechange_getmoduleinfo(){
	$info = array(
		"name"=>"Names Department",
		"author"=>"Derek0, Chris Vorndran",
		"version"=>"1.0",
		"category"=>"Administrative",
		"download"=>"",
		"vertxtloc"=>"",
		"settings"=>array(
			"Names Department Settings,title",
			"cost"=>"Cost `iin gems`i for a Name,int|5",
			"namechangeloc"=>"Where does the Names Department appear,location|".getsetting("villagename", LOCATION_FIELDS)
		),
		"prefs"=>array(
			"Name Changes,title",
			"changednames"=>"Name Changes,viewonly|",
			"lastname"=>"What was the player's last name,viewonly|"
		),
	);
	return $info;
}
function namechange_install(){
	module_addhook("village");
	module_addhook("changesetting");
	module_addhook("lastnames");
	return true;
}
function namechange_uninstall(){
	return true;
}
function namechange_dohook($hookname,$args){
	global $session;
	switch ($hookname){
		case "village":
			if ($session['user']['location'] == get_module_setting("namechangeloc")) {
				tlschema($args['schemas']['marketnav']);
		        addnav($args['marketnav']);
				tlschema();
		        addnav("`b`~I`b`)d`i`7e`kn`i`3t`b`#i`b`Lt`i`7y`i `b`)C`b`~r`b`)i`b`i`7s`b`ki`b`3s`i `#I`Ln`i`7c`i`). ","runmodule.php?module=namechange&op=enter");
			}
			break;
		case "changesetting":
			if ($args['setting'] == "villagename") {
				if ($args['old'] == get_module_setting("namechangeloc")) {
			        set_module_setting("namechangeloc", $args['new']);
			    }
			}
	    	break;
	    case "lastnames":
	    	$name = $args['acctid'];
	    	$names = unserialize(get_module_pref("lastname","namechange",$args['acctid']));
	    	$num_names = count($names);
	    	if ($num_names > 0) output("`c`i`^Last known as ".($names[$num_names-1])."`i`c`n");
	    	break;
		}
	return $args;
}
function namechange_run(){
	global $session;
	require_once('lib/sanitize.php');
	require_once('lib/names.php');
	page_header("Identity Crisis Inc.");
	
	$op = httpget('op');
	$namec = httppost('name');
	$name = $session['user']['name'];
	$cost = get_module_setting("cost");
	$rename = translate_inline("Rename");

	switch ($op){
		case "enter":
			if ($session['user']['gems'] >= $cost){
				output("`qYou wander into the town hall, and spot a sign that says '`b`~I`b`)d`i`7e`kn`i`3t`b`#i`b`Lt`i`7y`i `b`)C`b`~r`b`)i`b`i`7s`b`ki`b`3s`i `#I`Ln`i`7c`i`). '.");
				output(" `qYou choose to check it out, wondering if a new name would be nice.");
				output(" As you walk down the hall, you notice someone else walking back.");
				output(" You notice that he has a nice new colored name, and wonder if you could have one as good as that.");
				output(" 'You want to have a new name just like his, don't you' said a man, almost as if he could read your mind.");
				output(" Before you could answer, he says, 'follow me'. You follow him down the hall into an office that could most likely be be mistaken for a cosy living room.");
				output(" As you sit down in one of the cosy leather chairs, the man says, '`QAll new names cost `%%s `QGems`q'",$cost);
				addnav("Legal Name Options");
				addnav("Change your name","runmodule.php?module=namechange&op=name");
			} else {
				output("`QAs you walk in, you wonder how much the name costs.");
				output(" You start to look for a sign of some sorts, one telling you if a new name could fit into your budget.");
				output(" After searching around yu finally notice a sign saying, '`QAll new names cost `%%s `Qgems.'",$cost);
				output(" Disapointed, you walk back to the door.");
			}
				break;
		case "name":
			if ($namec == ""){
				output("`qThe man looks at you and then opens a desk droor, searching for a fle.");
				output(" 'Here we go. Your name is `&%s`q. You seem to have a rather dull name. I can see why you may want a new one'",$name);
				output("So, what would you like your new name to be?");
				rawoutput("<form action='runmodule.php?module=namechange&op=name' method='POST'>");
                output("`^New Name:");
                rawoutput("<input id='input' name='name' value='$name' maxlength='25'> <input type='submit' class='button' value='$rename'>");
                rawoutput("</form>");
                output("<script language='javascript'>document.getElementById('input').focus();</script>",true);
                addnav ("", "runmodule.php?module=namechange&op=name");
				output("`n`n`&`i(Note: Do be aware that changing your name will not give you colours. Any colours added with be automatically removed.)`i`n`0");
			} else {
				output("`qYou shake the man's hand, smile, and hand him `%%s `qgems.",$cost);
				output(" You turn around and walk out of the town hall, smiling happily.");
//				NAME CHANGES ARRAY
				$changednames = unserialize(get_module_pref("changednames"));
				if (!is_array($changednames)) $changednames = array();
				array_push($changednames,base_name($session['user']['acctid']));
//				array_shift($changednames);
				set_module_pref("changednames",serialize($changednames));
//				END ARRAY

				$lastname = unserialize(get_module_pref("lastname"));
				if (!is_array($changednames)) $changednames = array();
				array_push($changednames,base_name($session['user']['acctid']));
//				array_shift($changednames);
				set_module_pref("lastname",serialize($changednames));
				$session['user']['gems'] -= $cost;
				$session['user']['name'] = $namec;
			}
		break;
	}
	addnav("Leave");
	villagenav();
	page_footer();
}
?>