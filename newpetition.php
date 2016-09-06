<?php

function newpetition_getmoduleinfo()
{
    $info = [
        'name' => 'New Petitions',
        'author' => '`&`bStephen Kise`b',
        'version' => '0.1b',
        'category' => 'Administrative',
        'descriptions' => 'Revamp the petitions and the petition viewer.',
        'settings' => [
            'recaptcha_key' => 'What is the key for the reCAPTCHA form?, text|',
        ],
        'prefs' => [
            'open_petitions' => 'Array of petitions open, viewonly| []',

        ]
    ];
}

function newpetition_install()
{
    // Modify the petitions table to include reply_status, attachment, reponse_arraY (JSON), and title
    // Reorganize the way petitions are handled.
    module_addhook('petition-form'); // Rewrite the petition form entirely - add ability to make a title, attach file, and a google reCAPTCHA.
    module_addhook('header-viewpetition'); // Block the current petition viewer, create a new one from scratch.
    module_addhook('everyheader'); // templatereplace('petitioncount') to have a properly organized template part based on categories.
    module_addhook('mailfunctions'); // Add the ability to create a petition; Ability to forward a petition from mails if the person is reading? Maybe.
}