<?php
if ($session['user']['clanid']) {
    tlschema($args['schemas']['gatenav']);
    addnav($args['gatenav']);
    tlschema();
    addnav('`b`^Sacred Vaults`b','runmodule.php?module=clanpyramid&op=enter');
}
if (get_module_pref('square')) {
    clear_module_pref('square');
    clear_module_pref('defender');
    debug('Clearing the square');
}
?>