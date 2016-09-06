<?php

/**
 * @author Stephen Kise
 * @todo Include A/B tests for different logins, calculate how many home page views are actually new, and track the progression of a server.
 */

function abtesting_getmoduleinfo()
{
    $info = [
        "name" => "A/B Testing and Statistics",
        "author" => "`&`bStephen Kise`b",
        "version" => "0.1b",
        "description" => "Testing the patterns of players and visitors",
        "category" => "Administrative",
        "settings" => [
            "A/B Testing Settings,title",
            "save_to_file" => "Should we export your data each week to a file?, bool| 0",
            "Results,title",
            "weekly_data" => "JSON object of the results for this week:, viewonly| ",
            "todays_data" => "Today's Data:, viewonly | ",
        ],
        "prefs" => [
            "previous_page" => "User's last page:, viewonly| ",
            "current_page" => "User's current page:, viewonly| ",
        ],
    ];
    return $info;
}

function abtesting_install()
{
    module_addhook('index');
    module_addhook('player-login');
    module_addhook('player-logout');
    module_addhook('everyhit');
    return true;
}

function abtesting_uninstall()
{
    return true;
}

function abtesting_dohook($hook, $args)
{
    //Grab today's data, and update the settings if they changed.
    $data = json_decode(trim(get_module_setting('todays_data')), true);
    $check = $data;
    $keys = ['visitors', 'new_visitors', 'logins', 'logouts', 'scripts_ran', 'module_occurrences'];
    foreach ($keys as $key) {
        if (!$data[$key]) {
            $data[$key] = [];
        }
    }
    switch ($hook) {
        case 'index':
            global $_SERVER, $_COOKIE;
            //Check for visitors
            $lgi = addslashes($_COOKIE['lgi']);
            $ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
            $accounts = db_prefix('accounts');
            $sql = db_query(
                "SELECT login FROM $accounts
                WHERE lastip = '$ip'
                AND uniqueid = '$lgi'
                ORDER BY acctid+0 ASC LIMIT 0, 1"
            );
            if (db_num_rows($sql) == 0) {
                $data['new_visitors'][$ip] += 1;
            } else {
                $row = db_fetch_assoc($sql);
                $login = trim($row['login']);
                $data['visitors'][$login]++;
            }
            break;
        case 'player-login':
            global $session;
            $data['logins'][$session['user']['login']]++;
            break;
        case 'player-logout':
            global $session;
            $data['logouts'][$session['user']['login']]++;
            break;
        case 'everyhit':
            global $modulehook_queries, $SCRIPT_NAME;
            $ignore = [];
            $runModule = httpget('module') ?: $SCRIPT_NAME;
            if (!httpget('module')) {
                $scriptName = $SCRIPT_NAME;
            } else {
                $scriptName = 'runmodule.php?module=' . httpget('module');
            }
            $data['scripts_ran'][$scriptName]++;
            foreach ($modulehook_queries as $hook => $modules) {
                foreach ($modules as $key => $moduleData) {
                    if (!in_array($moduleData['modulename'], $ignore) && !strpos($scriptName, 'runmodule.php')) {
                        $data['module_occurrences'][$moduleData['modulename']]++;
                        $ignore[] = $moduleData['modulename'];
                    }
                }
            }
            arsort($data['module_occurrences']);
            //debug($data);
            break;
    }
    if ($data != $check) {
        set_module_setting('todays_data', json_encode($data));
    }
    return $args;
}

function abtesting_run(){}



?>