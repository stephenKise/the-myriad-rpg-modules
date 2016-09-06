<?php

function achievements_getmoduleinfo(){

$settings = array(
  "achievements settings,title",
  "title"=>"title for achievements,|achievements",
  );

return(
  array(
    "name"=>"Achievements",
    "author"=>"`&overlord",
    "version"=>"1.0",
    "download"=>"http://dragonprime.net/index.php?topic=10752.0",
    "category"=>"Biography",
    "settings"=>$settings,
  )
  );

}

function achievements_run(){
  if(httpget('op')=="su"){
    require_once("modules/achievements/su.php");
    achievements_su(); exit(0); // call superuser page and then terminate
  }

  $mod_title = get_module_setting("title");

  global $session;
  $acctid = httpget('char');
  
  page_header(ucfirst($mod_title));
  
  output("`b`cPlayer %s`c`b`n`n",ucfirst($mod_title));
  
  $sql = "select title,description from ".db_prefix("player_achievements")." where acctid=$acctid order by ID";
  $result = db_query($sql);
  
  while($row = db_fetch_assoc($result)){
    output_notl("`b`c{$row['title']}`c`b`n`c{$row['description']}`c`n");
  }
  
  addnav("Return");
  addnav("Biography","bio.php?char=$acctid");
  villagenav();
  page_footer();
}

function achievements_dohook($hook,$args){
  global $session;
  $mod_title = get_module_setting("title");
  switch($hook){
    case "superuser":
      if (($session['user']['superuser'] & SU_EDIT_USERS) || get_module_pref("canedit")) {
        addnav("Editors");
        addnav(ucfirst($mod_title),"runmodule.php?module=achievements&op=su");
      }
      break;
    case "biotop":
      $sql = "select ID from ".db_prefix("player_achievements")." where acctid={$args['acctid']}";
      $result = db_query($sql);
      if(db_num_rows($result)>0){
        addnav(ucfirst($mod_title));
        addnav("V?View ".ucfirst($mod_title),"runmodule.php?module=achievements&char={$args['acctid']}");
      }
      break;
  }
  return $args;
}

function achievements_uninstall(){
  db_query("drop table ".db_prefix("player_achievements"));
}

function achievements_install(){
  $sql = 'CREATE TABLE IF NOT EXISTS `'.db_prefix("player_achievements").'` (`ID` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY, `acctid` INT(10) NOT NULL, `title` VARCHAR(100) NOT NULL, `description` VARCHAR(255) NOT NULL) ENGINE = MyISAM';
  db_query($sql);
  module_addhook("biotop");
  module_addhook("superuser");
}