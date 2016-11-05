<?php

function ngchat_getmoduleinfo()
{
    return [
        'name' => 'Angular Chat',
        'author' => '`&`bStephen Kise`b',
        'version' => '0.1b',
        'category' => 'Gameplay',
        'description' => 'A way of using angular and data binding to remake the commentary.',
        'allowanonymous' => true,
        'override_forced_nav' => true,
        'prefs' => [
            'in_chat' => 'Is this user in chat?, bool| 0',
        ]
    ];
}

function ngchat_install()
{
    module_addhook('api');
    // Make sure that the document loads the DraNGon module and executes before chat does.
    module_addhook_priority('javascript', 80);
    module_addhook('blockcommentarea');
    module_addhook('everyheader-loggedin');
    // Avoid tampering with all of the footer modules.
    module_addhook_priority('everyfooter-loggedin', 1);
    return true;
}

function ngchat_uninstall()
{
    return true;
}

function ngchat_dohook($hook, $args)
{
    switch ($hook) {
        case 'api':
            $args['ngchat'] = [
                'fetchCommentary' => [
                    'ngchatGetCommentsForSection',
                    'GET commentary for the section that the player is in',
                ],
                'sendCommentary' => [
                    'ngchatPostToSection',
                    'POST value of insertComment and POST value of commentarySection to comment in.',
                ],
                'removeComment' => [
                    'ngchatRemoveComment',
                    'POST commid of message to delete, where it is available to the moderator.',
                ]
            ];
            break;
        case 'javascript':
            echo "<script src='modules/js/ngchat.js'></script>";
            break;
        case 'blockcommentarea':
            global $SCRIPT_NAME, $session, $ngBlock;
            if ($SCRIPT_NAME != 'moderate.php' && $ngBlock == 0) {
                $session['commentary_section'] = $args['section'];
                $ngBlock++;
                $URI = urlencode($_SERVER['REQUEST_URI']);
                set_module_pref('in_chat', 1);
                rawoutput(
                    "<div class='commentaryModule'
                        ng-controller='commentary' ng-init='init(\"{$session['user']['name']}\", \"{$session['user']['acctid']}\", \"$URI\")'>
                        <div class='commentary-item' ng-repeat='x in comments'>");
                if ($session['user']['superuser'] & SU_EDIT_USERS) {
                    output(
                        "<a href=''  style='float: left; margin-right: 5px; display: inline-block;' ng-click='removeComment(x.commentid)'>`2[`0X`2]`0</a>",
                     true);
                }
                rawoutput("<span ng-bind-html='formatColors(x.comment, true, x.name, x.author, x.deleted)'></span>
                        </div>
                        <div ng-show='alert'>{{alertMessage}}</div>
                        <br />
                        <form ng-submit='sendCommentary()' class='commentaryForm'>
                            <span ng-bind-html='insertCommentFormatted'></span>
                            <textarea class='input' ng-model='insertComment' ng-change='handleInput()' ng-enter='sendCommentary()'></textarea><br />
                            <input type='submit' id='submit' value='Submit' class='input commentarySubmit'/>
                            <input type='button' value='Refresh' ng-click='fetchCommentary(true)' class='input commentaryButton'>
                        </form>
                    </div>"
                );
                rawoutput("<div class='block-commentary' style='display: none;'>");
                $sql = db_query("SELECT acctid FROM $accounts WHERE loggedin = '1'");
                while ($row = db_fetch_assoc($sql)) {
                    addnav('', 'bio.php?char=' . $row['acctid'] . '&ret=' . urlencode($_SERVER['REQUEST_URI']));
                }
            }
            break;
        case 'everyheader-loggedin':
            global $SCRIPT_NAME;
            if ($SCRIPT_NAME != 'bio.php' && get_module_pref('in_chat') == 1) {
                set_module_pref('in_chat', 0);
            }
            break;
        case 'everyfooter-loggedin':
            global $SCRIPT_NAME, $ngBlock;
            if ($SCRIPT_NAME != 'moderate.php' && $ngBlock != 0) {
                rawoutput("</div>");
            }
            output("`0");
            break;
    }
    return $args;
}

    /* Structure for all of the commentary
        USER
        {
            userName: Legend,
            userClan: Cloud9,
            emoteColor: `#,
            actionColor: `&,
            friends: {[userID: 2, userName: S1mple]} // Sanitize the names so it's login based.
        }
        COMMENTARY
        {
            currentSection: superuser, (based on session)
            lastCommentID: 324564, (last post)
            responses: (object of all comment data we need)
                {[
                    userID: 1,
                    userName: Legend, // Sanitize the names so it's login based.
                    userClan: Cloud9,
                    comment: What is this shit?,
                    time: Y-m-d H:i:s,
                    deleted: (bool) 0,
                ],
                [
                    userID: 2,
                    userName: S1mple, // Sanitize the names so it's login based.
                    userClan: Na`Vi,
                    comment: this is a comment,
                    time: Y-m-d H:i:s,
                    deleted: (bool) 0,
                ]}
            deletionPower: (bool) 0
        }



    */
function ngchatGetCommentsForSection()
{
    global $session, $apiRequestMethod;
    $accounts = db_prefix('accounts');
    $commentary = db_prefix('commentary');
    $date = date('Y-m-d H:i:s');
    header('Content-Type: application/json');
    if ($session['user']['loggedin'] != 1) {
        return [
            'status' => '-2',
            'errorMessage' => 'User is not online'
        ];
        die();
    }
    db_query(
        "UPDATE $accounts SET
        laston = '$date'
        WHERE acctid = '{$session['user']['acctid']}'"
    );
    $deleted =  ($session['user']['superuser'] & SU_EDIT_COMMENTS ? "" : "AND deleted = '0'");
    $sql = db_query(
        $str = "SELECT comm.*, acc.name FROM
        (
            (SELECT * FROM
                (SELECT * FROM $commentary
                WHERE section = 'globalooc'
                $deleted
                ORDER BY commentid+0 DESC
                LIMIT 0, 10)
            AS c
            ORDER BY c.commentid+0 ASC
            LIMIT 0, 10)
            UNION (
                SELECT * FROM
                (SELECT * FROM $commentary
                WHERE section = '{$session['commentary_section']}'
                $deleted
                ORDER BY commentid+0 DESC
                LIMIT 0, 25)
                AS c
                ORDER BY c.commentid+0 ASC
                LIMIT 0, 25
            )
        ) AS comm
        LEFT JOIN $accounts AS acc
        ON acc.acctid = comm.author"
    );
    $json = [];
    while ($row = db_fetch_assoc($sql)) {
        $row['name'] = appoencode($row['name']);
        array_push($json, $row);
    }

    if ($apiRequestMethod == 'GET') {
        return $json;
    }
    else {
        return [
            'status' => '-1',
            'errorMessage' => 'Incorrect method used.'
        ];
    }
}

function ngchatPostToSection()
{
    global $session, $mysqli_resource, $apiRequestMethod;
    require_once('lib/sanitize.php');
    $commentary = db_prefix('commentary');
    $post = json_decode(file_get_contents('php://input'), true);
    $post['comment'] = mb_convert_encoding($post['comment'], 'UTF-8');
    $post['comment'] = comment_sanitize($post['comment']);
    $post['comment'] = addslashes($post['comment']);
    $post['commentid'] = filter_var($post['commentid'], FILTER_SANITIZE_NUMBER_INT);
    if ($apiRequestMethod != 'POST') {
        return [
            'status' => '-1',
            'errorMessage' => 'Incorrect method used.',
        ];
    }
    else {
        if ($post['commentid'] > 0) {
            $sql = db_query(
                "SELECT author
                FROM $commentary
                WHERE commentid = '{$post['commentid']}'
                LIMIT 1"
            );
            $row = db_fetch_assoc($sql);
            if ($row['author'] == $session['user']['acctid'] ||
                $session['user']['superuser'] & SU_EDIT_COMMENTS) {
                db_query(
                    "UPDATE $commentary
                    SET comment = '{$post['comment']}'
                    WHERE commentid = '{$post['commentid']}'"
                );
            }
            else {
                return [
                    'status' => 'x',
                    'errorMessage' => 'Permission not granted to edit declared comment!'
                ];
            }
        }
        else {
            db_query(
                "INSERT INTO $commentary
                (comment, section, author) VALUES
                ('{$post['comment']}', '{$session['commentary_section']}', '{$session['user']['acctid']}')"
            );
            return [
                'status' => '0',
                'errorMessage' => 'No response expected.'
            ];
        }
    }
}

function ngchatRemoveComment()
{
    global $session;
    $commentary = db_prefix('commentary');
    $post = file_get_contents('php://input');
    if (is_array($post)) {
        $post = $post['commentid'];
    }
    $post = filter_var($post, FILTER_SANITIZE_NUMBER_INT);
    $sql = db_query(
        "SELECT author, deleted FROM $commentary
        WHERE commentid = '$commentid'
        LIMIT 1"
    );
    $row = db_fetch_assoc($sql);
    if ($session['user']['superuser'] & SU_EDIT_USER ||
        $row['author'] == $session['user']['acctid']) {
        db_query(
            "UPDATE $commentary SET deleted = '1'
            WHERE commentid = '$post'
            AND section = '{$session['commentary_section']}'"
        );
        return [
            'status' => '0',
            'errorMessage' => 'No response expected.'
        ];
    }
    else {
        return [
            'status' => '-5',
            'errorMessage' => 'Improper usage. Please refer to the documentation!'
        ];
    }
}
