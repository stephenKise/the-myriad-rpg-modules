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
            if ($session['user']['superuser'] & SU_MANAGE_MODULES) {
                addnav('Mechanics');
                addnav('Pull LotGD Source', 'superuser.php?git=pull');
                if (httpget('git') == 'pull') {
                    shell_exec('git pull');
                }
                $category = get_module_setting('category', 'changelog');
                $gamelog = db_prefix('gamelog');
                $core = shell_exec('git log -1 --format="%b (<a href=\"http://github.com/stephenKise/Legend-of-the-Green-Dragon/commit/%h\">%h</a>)"');
                $sql = db_query("SELECT logid FROM $gamelog WHERE message = '$core' LIMIT 1");
                if (db_num_rows($sql) == 0) {
                    require_once('lib/gamelog.php');
                    gamelog($core, $category);
                }
                $modules = shell_exec('cd modules && git log -1 --format="%b (<a href=\"http://github.com/stephenKise/xythen-modules/commit/%h\">%h</a>)"');
                $sql = db_query("SELECT logid FROM $gamelog WHERE message = '$modules' LIMIT 1");
                if (db_num_rows($sql) == 0) {
                    require_once('lib/gamelog.php');
                    gamelog($modules, $category);
                }
            }
            break;
    }
    return $args;
}
