<?php

function api_getmoduleinfo()
{
    return [
        'name' => 'API',
        'author' => 'Stephen Kise',
        'version' => '0.1b',
        'category' => 'Administrative',
        'description' => 'Adds an API callback, mainly for the use of JavaScript updates.',
        'allowanonymous' => true,
        'override_forced_nav' => true,
     ];
}

function api_install()
{
    module_addhook_priority('javascript', 100);
    return true;
}

function api_uninstall()
{
    return true;
}

function api_dohook($hook, $args)
{
    switch ($hook) {
        case 'javascript':
            echo "<script type='text/javascript' src='modules/js/api.js' /></script>";
            break;
    }
    return $args;
}

function api_run()
{
    global $output, $session, $apiRequestMethod, $payload;
    $apiRequestMethod = $_SERVER['REQUEST_METHOD'];
    $payload = file_get_contents('php://input');
    $output = '';
    header('Content-Type: application/json');
    $args = modulehook('api');
    if (httpget('act') == 'help') {
        print_r(json_encode($args, JSON_PRETTY_PRINT));
    }
    else if (httpget('mod') == '' || httpget('act') == '' && httpget('act') != 'help') {
        print_r(json_encode('You need :mod and :act to use the api! Please go back to the main API directory and review the source!', JSON_PRETTY_PRINT));
        die();
    }
    else if (function_exists($current = $args[httpget('mod')][httpget('act')][0])) {
        print_r(json_encode($current(), JSON_PRETTY_PRINT));
    }
    else {
        print_r(json_encode(
            ['status' => '-1', 'errorMessage' => 'No such module and function pairing exists. Please re-read the documentation!'],
            JSON_PRETTY_PRINT));
        die();
    }
    exit();
}
