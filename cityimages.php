<?php
function cityimages_getmoduleinfo(){
    $info = array(
        "name"=>"City Images",
        "version"=>"20070207",
        "author"=>"<a href='http://www.sixf00t4.com' target=_new>Sixf00t4</a>",
        "category"=>"Village",
		"download"=>"http://dragonprime.net/index.php?module=Downloads;sa=dlview;id=1198",
		"vertxtloc"=>"http://www.legendofsix.com/",
        "description"=>"Allows images to be attached to each village",
		"prefs-city"=>array(
            "cityimg"=>"Where is the image for this city?,text|",
            "cityimgtags"=>"What tags to use after the image?,text|",
        ),    
		"requires"=>array(
            "cityprefs"=>"20051113|By Sixf00t4, available on DragonPrime",
		),
	);
	return $info;
}

function cityimages_install(){
    module_addhook_priority("village-desc", 70); 
    module_addhook('villagetext');
    return true;
}

function cityimages_uninstall() {
	return true;
}

function cityimages_dohook($hookname,$args) {
	global $session;
    

	switch ($hookname) {
        case "villagetext":
            require_once("modules/cityprefs/lib.php");
            $cityid=get_cityprefs_cityid("cityname",$session['user']['location']);
            $args['image'] = get_module_objpref('city', $cityid, 'cityimg');
            break;
              
	}
	return $args;
}

function cityimages_run(){}
php?>