<?php

function git_getmoduleinfo()
{
    $info = [
        'name' => 'Git Management',
        'author'=> '`&`bStephen Kise`b',
        'version' => '0.2b',
        'category' => 'Administrative',
        'description' =>
            'Manage the git repository.',
        'requires' => [
            'changelog' => '0.1b |Stephen Kise, nope',
        ],
        'download' => 'nope',
    ];
    return $info;
}

function git_install()
{
    module_addhook('superuser');
    return true;
}

function git_uninstall()
{
    return true;
}

function git_dohook($hook, $args)
{
    switch ($hook) {
        case 'superuser':
            global $session;
            $gamelog = db_prefix('gamelog');
            $sql = db_query("SELECT message FROM $gamelog ORDER BY logid+0 DESC LIMIT 1");
            $row = db_fetch_assoc($sql);
            if ($session['user']['superuser'] & SU_MANAGE_MODULES) {
                addnav('Mechanics');
                addnav('Git Pull', 'superuser.php?git=pull');
                require_once('lib/gamelog.php');
                if (httpget('git') == 'pull') {
                    shell_exec('git pull');
                    $output = shell_exec('git log --format=%B -1');
                    $output = explode(PHP_EOL, $output);
                    unset($output[0]);
                    $output = trim(implode(PHP_EOL, $output));
                    if ($output != $row['message']) {
                        debug($row['message']);
                        gamelog($output, get_module_setting('category', 'changelog'));
                    }
                }
            }
            break;
    }
    return $args;
}
