<?php
// Everything below was done by Boris735 with no modification

// Possible values to set experience to after the level change:
// UNCHANGED:      no change
// PROPORTIONAL:   same proportion of advancement toward next level
// LEVELMIN:       minimum experience for the new level
// LEVELMAX:       maximum experience for the new level

if (!defined("EXP_UNCHANGED"))     define("EXP_UNCHANGED", 0);
if (!defined("EXP_PROPORTIONAL"))  define("EXP_PROPORTIONAL", 1);
if (!defined("EXP_LEVELMIN"))      define("EXP_LEVELMIN", 2);
if (!defined("EXP_LEVELMAX"))      define("EXP_LEVELMAX", 3);


// I can envisage situations where the caller wants to do the reporting
// of the various attribute gains themselves, hence the $report option.
// (Can't turn off the specialty gaining printing, though; some changes
// need to be made for that to work.)  I'm not wedded to it, though, so
// it wouldn't bother me greatly if it disappeared.

function adjust_player_level($levelgain = 1, $expmode = EXP_UNCHANGED, $report = true) {
  global $session;

  // Disable any level change for players with level > 15, assumed to
  // be admin accounts of some kind
  $level = $session['user']['level'];
  if ($level > 15) 
    return;

  // Otherwise, keep levels within the appropriate range 
  $newlevel = $level + $levelgain;
  if ($newlevel < 1)
    $newlevel = 1;
  if ($newlevel > 15) 
    $newlevel = 15;
  $levelgain = $newlevel - $oldlevel;

  // If there is no level gain, we might still change the experience
  if ($levelgain == 0 && $expmode < EXP_LEVELMIN)
    return;

  if ($levelgain != 0) {
    $session['user']['level'] = $newlevel;
    $session['user']['maxhitpoints'] += 10*$levelgain;
    $session['user']['soulpoints'] += 5*$levelgain;
    $session['user']['attack'] += $levelgain;
    $session['user']['defense'] += $levelgain;

    // no permadead people allowed!
    $min_maxhitpoints = 10*$newlevel - 9;
    if ($session['user']['maxhitpoints'] < $min_maxhitpoints) {
      debuglog("Would have ended up perma-dead after level gain (from level $level to level $newlevel); maxhitpoints set to $min_maxhitpoints instead of {$session['user']['maxhitpoints']}");
      $session['user']['maxhitpoints'] = $min_maxhitpoints;
    }

    if ($levelgain > 0) {
      if ($report) {
        output("`#You advance to level `^%s`#!`n", $newlevel);
        output("Your maximum hitpoints are now `^%s`#!`n", $session['user']['maxhitpoints']);
        if ($levelgain == 1) {
          output("You gain an attack point!`n");
          output("You gain a defense point!`n");
        } else {
          output("You gain %s attack points!`n", $levelgain);
          output("You gain %s defense points!`n", $levelgain);
        }
      }
    } else {
      if ($report) {
        output("`#You revert to level `^%s`#!`n", $newlevel);
        output("Your maximum hitpoints are now `^%s`#!`n", $session['user']['maxhitpoints']);
        if ($levelgain == -1) {
          output("You lose an attack point!`n");
          output("You lose a defense point!`n");
        } else {
          output("You lose %s attack points!`n", $levelgain);
          output("You lose %s defense points!`n", $levelgain);
        }
      }
    }

    // There is no get_block_new_output() routine as of v1.1.2
    // if (!$report) {
    //  set_block_new_output(true);
    //  $oldblockvalue = get_block_new_output();
    // }

    increment_specialties("`^", false, $levelgain);

    // if (!$report)
    //  set_block_new_output($oldblockvalue);

    check_for_referral();

    // Adjust companion levels
    if (getsetting("companionslevelup", 1) == true) {
      $newcompanions = $companions;
      foreach ($companions as $name => $companion) {
        $companion['attack'] += $levelgain * $companion['attackperlevel'];
        $companion['defense'] += $levelgain * $companion['defenseperlevel'];
        $companion['maxhitpoints'] += $levelgain * $companion['maxhitpointsperlevel'];
        $companion['hitpoints'] = $companion['maxhitpoints'];
        $newcompanions[$name] = $companion;
      }
      $companions = $newcompanions;
    }

    invalidatedatacache("list.php-warsonline");
  }

  if ($expmode != EXP_UNCHANGED) {
    require_once("lib/experience.php");

    $dks = $session['user']['dragonkills'];
    switch ($expmode) {
    case EXP_PROPORTIONAL:
      $currlevelexp = exp_for_next_level($level - 1, $dks);
      $nextlevelexp = exp_for_next_level($level, $dks);
      $currexp = $session['user']['experience'];
      $proportion = ($exp - $currlevelexp) / ($nextlevelexp - $currlevelexp);

      $newlevelexp = exp_for_next_level($newlevel - 1, $dks);
      $nextlevelexp = exp_for_next_level($newlevel, $dks);
      $session['user']['experience'] = $newlevelexp + round($proportion*($nextlevelexp - $newlevelexp));
      break;

    case EXP_LEVELMIN:
      $session['user']['experience'] = exp_for_next_level($newlevel-1, $dks);
      break;

    case EXP_LEVELMAX:
      $session['user']['experience'] = exp_for_next_level($newlevel, $dks) - 1;
      break;
    }
  }
}


// Could have inlined this above since it only makes sense to check on
// player level change, but feels cleaner to split it out anyway.

function check_for_referral() {
  global $session;

  if ($session['user']['referer'] > 0 && ($session['user']['level'] >= getsetting("referminlevel",4) || $session['user']['dragonkills'] > 0) && $session['user']['refererawarded'] < 1) {
    require_once("lib/systemmail.php");

    $sql = "UPDATE " . db_prefix("accounts") . " SET donation=donation+".getsetting("refereraward",25)." WHERE acctid={$session['user']['referer']}";
    db_query($sql);

    $session['user']['refererawarded'] = 1;
    $subj = array("`%One of your referrals advanced!`0");
    $body = array("`&%s`# has advanced to level `^%s`#, and so you have earned `^%s`# points!", $session['user']['name'], $session['user']['level'], getsetting("refereraward", 25));
    systemmail($session['user']['referer'],$subj,$body);
  }
}


// Would like the $gain argument to be supported by increment_specialty.
// In the meantime, this separate function is provided instead.

function increment_specialties($colorcode, $spec=false, $gain=1) {
  global $session;

  require_once("lib/increment_specialty.php");

  // Since we can't properly decrement specialties yet, just return
  // in that case.  The "amount" argument in the "incrementspecialty"
  // module hook would let this be done properly, too.
  if ($gain <= 0)
    return;

  if ($spec !== false) {
    $revertspec = $session['user']['specialty'];
    $session['user']['specialty'] = $spec;
  }
  tlschema("skills");
  if ($session['user']['specialty'] != "") {
    // The "amount" option won't be supported yet, but when (if) it is,
    // we can uncomment the following...

    // $specialties = modulehook("incrementspecialty",
    //                   array("color" => $colorcode, "amount" = $gain));

    // ... and remove the following
    for ($i = 0; $i < $gain; $i++) {
      $specialties = modulehook("incrementspecialty",
                         array("color" => $colorcode));
    }
  } else {
    output("`7You have no direction in the world, you should rest and make some important decisions about your life.`0`n");
  }
  tlschema();
  if ($spec !== false) {
    $session['user']['specialty'] = $revertspec;
  }
}