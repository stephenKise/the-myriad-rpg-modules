<?php

function clanpyramid_getmoduleinfo()
{
    $info = [
        "name" => "Clan Pyramid",
        "author" => "`b`&Ka`6laza`&ar`b `#based on an idea by`b`$ STANG`b",
        "description" => "Battle to take and defend one of three Pyramids in your Guilds name",
        "version" => "1.3.2",
        "download" => "http://dragonprime.net/index.php?module=Downloads;catd=20",
        "category" => "Clan",
        "settings" => [
            "Guild Pyramids Settings,title",
            "villagepercent" => "How often will you see the Pyramid in the village square?,range,0,100,1|20",
            "Coding Settings, title",
            "Coding purposes only only edit these if you're sure you know what you're doing,note",
            "lastreset" => "date of last reset,int|",
            "lastwinner" => "Guildname of last winner,int|",
            "owned1" => "Guildid that owns Pyramid 1,int|",
            "owned2" => "Guildid that owns Pyramid 2,int|",
            "owned3" => "Guildid that owns Pyramid 3,int|",
        ],
        "prefs" => [
            "Guild Vaults Prefs,title",
            "user_see" => "How do you wish to see the maps?,enum,0,all,1,entry,2,popup,3,none|0",
            "select no for maps to turn them off recommended for dial up users,note",
            "for coding purposes only,note",
            "time" => "time killed or maimed or ran from pyramid?,int",
            "square" => "Which square is player on?,int",
            "defender" => "defending pyramid,bool|0",
        ],
        "prefs-clans" => [
            "clanwins" => "points awarded,int|",
            "door locations,note",
            "doorno" => "north outer P1,int|",
            "doorso" => "south outer P1,int|",
            "doorwo" => "west outer P1,int|",
            "dooreo" => "east outer P1,int|",
            "doorni" => "north inner P1,int|",
            "doorsi" => "south inner P1,int|",
            "doorwi" => "west inner P1,int|",
            "doorei" => "east inner P1,int|",
            "doort" => "throne P1,int|",
            "transport portal locations,note",
            "tp1" => "transport portal 1,int|",
            "tp2" => "transport portal 2,int|",
            "tp3" => "transport portal 3,int|",
            "tp4" => "transport portal 4,int|",
            "tp5" => "transport portal 5,int|",
            "tp6" => "transport portal 6,int|",
            "tp7" => "transport portal 7,int|",
            "tp8" => "transport portal 8,int|",
            "tp9" => "transport portal 9,int|",
            "tp10" => "transport portal 10,int|",
            "tp11" => "transport portal 11,int|",
            "there are varying square numbers that will be cleared and reset see the arrays in codes for walls,note",
        ],
    ];
    return $info;
}

function clanpyramid_install()
{
    require_once("modules/clanpyramid/install.php");
}

function clanpyramid_uninstall()
{
return true;
}

function clanpyramid_dohook($hookname,$args)
{
    global $session;
    require_once("lib/villagenav.php");
    $u=&$session['user'];
    $op=httpget('op');
    $time=get_module_pref("time");
    $timeout=date("Y-m-d H:i:s",strtotime("-600 seconds"));
    $owned1 = get_module_setting("owned1");
    $owned2=get_module_setting("owned2");
    $owned3=get_module_setting("owned3");
    $which=get_module_pref("pyramid");
    switch ($hookname) {
        case "forest-desc":
            require_once("modules/clanpyramid/dohook/forest-desc.php");
            break;
        case "battle-victory":
            require_once("modules/clanpyramid/dohook/battle-victory.php");
            break;
        case "village-desc":
            require_once("modules/clanpyramid/dohook/village-desc.php");
            break;
        case "village":
            require_once("modules/clanpyramid/dohook/village.php");
            break;
        case "charstats":
            require_once("modules/clanpyramid/dohook/charstats.php");
            break;
        case "newday":
            require_once("modules/clanpyramid/dohook/newday.php");
            break;
    }
    return $args;
}

function clanpyramid_run()
{
require_once("lib/villagenav.php");
require_once("modules/clanpyramid/clanpyramid_func.php");
require_once("modules/clanpyramid/warriors_func.php");
require_once("modules/clanpyramid/walls_func.php");
include("modules/clanpyramid/clanpyramid.php");
}
?>