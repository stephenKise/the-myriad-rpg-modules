<?php

function achievements_add($title,$description,$acctid=0){
  global $session;
  $pid = $session['user']['acctid'];
  if($acctid> 0)
    $pid = $acctid;
  $sql = "select title from ".db_prefix("player_achievements")." where title='$title'".
    " and acctid={$pid}";
  $result = db_query($sql);
  if(db_num_rows($result)>0) return 0; // already has achievement
  $sql = "insert into ".db_prefix("player_achievements")." (ID,acctid,title,description) values(NULL,".
    $pid.",'$title','$description')";
  db_query($sql);
}