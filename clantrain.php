<?php
require_once("lib/commentary.php");
require_once("lib/villagenav.php");
require_once("lib/systemmail.php");
require_once("lib/sanitize.php");
require_once("lib/http.php");

##############################################################
#Server: luekinghost.net                                     #
#Creator: Tristan Lueking                                     #
#Modified by Brian Parker for 9.8 and for the clans system     #
#Date: May 17, 2004                                             #
#Modified on : February 12th, 2005                             #
##############################################################


######################
#   ***Settings***   #
######################

//switch($HTTP_GET_VARS[op])
//{
$u = $session['user'];

//if ($session[user][level] < 10)
  //     $goldturns = 1;
  //  elseif($session[user][level] < 14)
  //      $goldturns = 2;
  //  else
  //      $goldturns = 3;
// }

//$goldturns = 1;
##########################
#   ***End-Settings***   #
##########################

function clantrain_getmoduleinfo() {
    $info = array(
        "name"=>"Clan Turn Training",
        "author"=>"Tristan Lueking (original concept), modifications by Lightbringer, Akuma, Kody Sumter and Sloth",
        "version"=>"1.00",
        "category"=>"Clan",
        "download"=>"http://dragonprime.net/users/Lightbringer/clantrain05.zip",
			"settings"=>array(
				"expexchange"=>"How much gold is needed to pay for 1 experience point,int|1",
			),
			);
       return $info;
}
function clantrain_install() {
    module_addhook("footer-clan");
    return true;
}

function clantrain_uninstall() {
    return true;
}
function clantrain_dohook($hookname,$args) {
	global $session;
	switch ($hookname){
		case "footer-clan":
			if ($session['user']['clanrank'] != CLAN_APPLICANT){
				addnav("Guild Amenities");
				addnav("Guild Training","runmodule.php?module=clantrain&op=enter");
		}
		break;
	}
	return $args;
}
function clantrain_run(){
    global $session;
    $goldturns = get_module_setting("expexchange");
	page_header("Guild Training");
    
    switch (httpget("op")) {
        case "enter":
			output("`#You enter the training room and look around.");
			output(" Inside are various swords, dummies, and trainers.");
			output(" You can spend forest fights in here gaining experience points.");
			output(" `nAny gold used in the training room will be added to the guild fund!");
			output(" `nIt now also costs `^%s gold `#per experience.`n`n",$goldturns);
			output("The clock on the wall reads `^%s`#.`n",getgametime());
//Next New Day in ... is by JT from logd.dragoncat.net
$time = gametime();
$tomorrow = strtotime(date("Y-m-d H:i:s",$time)." + 1 day");
$tomorrow = strtotime(date("Y-m-d 00:00:00",$tomorrow));
$secstotomorrow = $tomorrow-$time;
$realsecstotomorrow = $secstotomorrow / getsetting("daysperday",4);
/*

    $sql = "SELECT acctid,dragonkills FROM oaccounts WHERE superuser=0";
        $result = db_query($sql);
$totdk = 0;
        if (db_num_rows($result)>0){
                $row1 = db_fetch_assoc($result);
                db_free_result($result);
                foreach($row1 as $row) {
                  $totdk = $totdk + $row['dragonkills'];
                  }
           }
*/
output("You figure a new day in: `^".date("G \\h\\o\\u\\r\\s i \\m\\i\\n\\u\\t\\e\\s s  \\s\\e\\c\\o\\n\\d\\s",strtotime("1976-01-01 00:00:00 + $realsecstotomorrow seconds"))."`0`n");
    if ($session['user']['turns'] < 1){
        output("`n`n`%You however do not have any Forest Fights left to train in!");
          }elseif($session['user']['gold'] + $session['user']['goldinbank'] < ($session['user']['level']*12+9)*$goldturns){
              output("`n`n`%You however may not have enough gold left to train");
    }else{
        output("`%How many turns do you want to spend training?`n");
        output("<form action='runmodule.php?module=clantrain&op=train2' method='POST'><input name='train' id='train'><input type='submit' class='button' value='".translate_inline("Train")."'></form>",true);
        output("<script language='JavaScript'>document.getElementById('train').focus();</script>",true); // Bravebrain
        addnav("","runmodule.php?module=clantrain&op=train2"); }
        break;

case "train2":
    page_header("Clan Training");
	$train = abs($_POST['train']);
    if ($session['user']['turns'] <= $train) $train = $session['user']['turns'];
    $session['user']['turns']-=$train;
    $exp = $session['user']['level']*e_rand(5,12)+e_rand(0,9);
    $totalexp = $exp*$train;
    $session['user']['experience']+=$totalexp;
    $goldfee =($totalexp * $goldturns);
        if ($goldfee > $session['user']['gold']) {
           $session['user']['goldinbank']+=$session['user']['gold'];
           $session['user']['gold']=0;
           $session['user']['goldinbank']-=$goldfee;
        }else{
			    $session['user']['gold'] -= $goldfee;
		        $newgold=(floor($goldfee*0.5)+$row['goldfund']);
			    output("`^You train for %s turns, for a cost of %s gold and gain %s experience!`n",$train,$goldfee,$totalexp);
		}
                break;
            }
		addnav("Back to Guild","clan.php");
		page_footer();
}
?>