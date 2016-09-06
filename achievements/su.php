<?php

function achievements_su(){
  page_header("Achievements Editor");

  switch(httpget('op2')){
    case "del":
      db_query("delete from ".db_prefix("player_achievements")." where ID=".httpget('id'));
      output("Achievement deleted.`n`n");
      break;
    case "grant":
	  require_once('lib/sanitize.php');
	  require_once('lib/names.php');
      rawoutput("<form method=post action=runmodule.php?module=achievements&op=su&op2=grant2>");
      addnav("","runmodule.php?module=achievements&op=su&op2=grant2");
      rawoutput("Player: <select name=acctid>");
      $sql = "select acctid,name from ".db_prefix("accounts")." order by acctid";
      $result = db_query($sql);
      while($row = db_fetch_assoc($result)){
        rawoutput("<option value={$row['acctid']}>".substr(full_sanitize(base_name($row['acctid'])),1)."</option>");
      }
      rawoutput("</select><br>Title: <input type=text size=20 name=title><br>");
      rawoutput("Description: <input type=text size=30 name=description><br>");
      rawoutput("<input type=submit value=Grant></form>");
      break;
    case "grant2":
      require_once("modules/achievements/add.php");
      $acctid = httppost('acctid');
      $title = httppost('title');
      $description = httppost('description');
      if($title <> "" && $description <> ""){
        achievements_add($title,$description,$acctid);
      }else{
        output("Achievement not added.  Title or description was blank.`n`n");
      }
      break;
  }
  
  $sql = "select * from ".db_prefix("player_achievements")." order by ID";
  
  $result = db_query($sql);
  
  rawoutput("<table><tr><td>Actions</td><td>Player</td><td>Title</td><td>Description</td></tr>");
  
  while($row = db_fetch_assoc($result)){
    $sql = "select name from ".db_prefix("accounts")." where acctid={$row['acctid']}";
    $r2 = db_query($sql);
    $rr2 = db_fetch_assoc($r2);
    $pname = $rr2['name'];
    rawoutput("<tr><td><a href=runmodule.php?module=achievements&op=su&op2=del&id={$row['ID']}>Delete</a></td>");
    output("<td>$pname</td><td>{$row['title']}</td><td>{$row['description']}</td></tr>",true);
  }
  
  rawoutput("</table>");
  
  addnav("Achievements");
  addnav("Grant Achievement","runmodule.php?module=achievements&op=su&op2=grant");
  addnav("Return");
  addnav("Grotto","superuser.php");
  villagenav();
  page_footer();
}