angular
.module('LOTGD', [])
.controller('commentary', function ($scope, $http, $sce, $interval) {
    $scope.username = '';
    $scope.acctid = 0;
    $scope.insertComment = '';
    $scope.insertCommentFormatted = '';
    $scope.commentsArray = [];
    $scope.recentComment = [];
    $scope.doEdit = false;
    $scope.timeToStop = 120;
    $scope.timeRun = 0;
    $scope.alert = false;
    $scope.alertMessage = '';
    var time, tempTime = new Date;
    $scope.init = (name, acctid, URI) => {
        $scope.username = $scope.formatColors(name, false);
        $scope.acctid = acctid;
        $scope.URI = URI;
        $scope.fetchCommentary();
        $interval($scope.fetchCommentary, 100000);
    }
    $scope.fetchCommentary = (force = false) => {
        if ($scope.timeRun <= $scope.timeToStop || force === true) {
            $scope.timeRun++;
            //var response = [];
            $http.get('runmodule.php?module=api&mod=ngchat&act=fetchCommentary')
                .then(
                    (data, headers) => {
                        $scope.formatCommentary(data);
                        $scope.commentsArray = data;
                        data.data.forEach(function (arr) {
                            if (arr.author == $scope.acctid && arr.deleted != 1) {
                                $scope.recentComment = {commentid: arr.commentid, comment: arr.comment};
                            }
                        });
                    },
                    (data, status, headers) => {
                        console.log(status);
                    }
                );
            if (force === true) {
                $scope.wipeTimer();
            }
        }
        else {
            $scope.killCommentary();
        }
    }
    $scope.sendCommentary = () => {
        var payload = {commentid: 0, comment: $scope.insertComment};
        time = new Date;
        x = Math.floor((time - tempTime)/1000);
        if (x < 5) {
            $scope.alertMessage = 'Please wait ' + (5 - x) + ' seconds before posting!';
            $scope.alert = true;
            return false;
        }
        tempTime = new Date;
        if ($scope.insertComment == '') {
            return false;
        }
        if ($scope.doEdit === true) {
            payload = {
                    commentid: +$scope.recentComment['commentid'],
                    comment: $scope.insertComment,
            };
        }
        $http.post('runmodule.php?module=api&mod=ngchat&act=sendCommentary', payload)
            .then(
                () => {
                    $scope.fetchCommentary();
                    $scope.wipeTimer();
                },
                () => {
                    console.log('fail');
                }
            );
        console.log(payload);
        $scope.clearInput();
        $scope.fetchCommentary();
        $scope.wipeTimer();
    }
    $scope.removeComment = (id) => {
        console.log('Removing comment id #' + id);
        $http.post('runmodule.php?module=api&mod=ngchat&act=removeComment', id)
            .then(
                () => {
                    $scope.fetchCommentary();
                    $scope.wipeTimer();
                },
                () => {
                    console.log('fail');
                }
            );
    }
    $scope.killCommentary = () => {
        $scope.comments = [
            {
                'commentid': '0',
                'section': 'null',
                'author': '0',
                'comment': ':`^`bPlease continue typing or hit refresh to reload the chat!`b',
                'deleted': '0',
                'postdate': '0000-00-00 00:00:00',
                'name': ''
            }
        ];
    }
    $scope.handleInput = () => {
        format = $scope.formatColors($scope.insertComment, true);
        $scope.insertCommentFormatted = format;
        if ($scope.timeRun >= $scope.timeToStop) {
            $scope.fetchCommentary(true);
        }
        $scope.wipeTimer();
        if ($scope.insertComment == '') {
            $scope.clearInput();
        }
        else if ($scope.insertComment == '/edit') {
            $scope.insertComment = $scope.recentComment['comment'];
            $scope.doEdit = true;
        }
        else if ($scope.insertComment == '/nvm' ||
                $scope.insertComment == '/rmv') {
            $scope.removeComment($scope.recentComment['commentid']);
            $scope.clearInput();
        }
        
    }
    $scope.clearInput = () => {
        $scope.insertComment = '';
        $scope.insertCommentFormatted = '';
        $scope.doEdit = false;
    }
    $scope.wipeTimer = () => {
        $scope.alert = false;
        $scope.timeRun = 0;
    }
    $scope.formatColors = (text, commentary, user = $scope.username, acctid = 0, deleted = 0) => {
        var out = user;
        if (acctid != 0) {
            out = "<a href='bio.php?char=" + acctid + "&ret=" + $scope.URI + "'>" + out + "</a>";
        }
        if (deleted == 1) {
            out = "<div style='opacity: .5;'>" + out;
        }
        var end = '</span>';
        var x = 0;
        var y = '';
        var z = '';
        if (commentary) {
            if (text.substr(0, 2) == '::') {
                x = 2;
            }
            else if (text.substr(0, 1) == ':') {
                x = 1;
            }
            else if (text.substr(0, 3) == '/me') {
                x = 3;
            }
            else if (text.substr(0, 5) == '/ooc ' || text.substr(0, 5) == ':ooc ') {
                x = 5;
            }
            if (text.substr(0, 5) == '/ooc ' || text.substr(0, 5) == ':ooc ') {
                out = '<span class=\'colLtOrange\'>(OOC)</span> ' + out;
            }
            if (text.substr(0, 2) == '::' || text.substr(0, 1) == ':' || text.substr(0, 3) == '/me' || text.substr(0, 4) == ':ooc') {
                out += '</span> <span class=\'colLtWhite\'>';
            }
            else {
                out += '</span> <span class=\'colDkCyan\'>says, "</span><span class=\'colLtCyan\'>';
                end += '</span><span class=\'colDkCyan\'>"';
            }
        }
        for (; x < text.length; x++) {
            y = text.substr(x, 1);
            if (y == '<') {
                out += '&lt;';
                continue;
            }
            else if (y == '>') {
                out += '&gt;';
                continue;
            }
            else if (y == '\n') {
                out += '<br />';
                continue;
            }
            else if (y == '`') {
                if (x < text.length-1) {
                    z = text.substr(x+1, 1);
                    switch (z) {
                        case '0':
                            out += '</span>';
                            break;
                        case '1':
                            out += '</span><span class=\'colDkBlue\'>';
                            break;
                        case '2':
                            out += '</span><span class=\'colDkGreen\'>';
                            break;
                        case '3':
                            out += '</span><span class=\'colDkCyan\'>';
                            break;
                        case '4':
                            out += '</span><span class=\'colDkRed\'>';
                            break;
                        case '5':
                            out += '</span><span class=\'colDkMagenta\'>';
                            break;
                        case '6':
                            out += '</span><span class=\'colDkYellow\'>';
                            break;
                        case '7':
                            out += '</span><span class=\'colDkWhite\'>';
                            break;
                        case 'q':
                            out += '</span><span class=\'colDkOrange\'>';
                            break;
                        case '!':
                            out += '</span><span class=\'colLtBlue\'>';
                            break;
                        case '@':
                            out += '</span><span class=\'colLtGreen\'>';
                            break;
                        case '#':
                            out += '</span><span class=\'colLtCyan\'>';
                            break;
                        case '$':
                            out += '</span><span class=\'colLtRed\'>';
                            break;
                        case '%':
                            out += '</span><span class=\'colLtMagenta\'>';
                            break;
                        case '^':
                            out += '</span><span class=\'colLtYellow\'>';
                            break;
                        case '&':
                            out += '</span><span class=\'colLtWhite\'>';
                            break;
                        case 'Q':
                            out += '</span><span class=\'colLtOrange\'>';
                            break;
                        case ')':
                            out += '</span><span class=\'colLtBlack\'>';
                            break;
                        case 'r':
                            out += '</span><span class=\'colRose\'>';
                            break;
                        case 'R':
                            out += '</span><span class=\'colRose\'>';
                            break;
                        case 'v':
                            out += '</span><span class=\'coliceviolet\'>';
                            break;
                        case 'V':
                            out += '</span><span class=\'colBlueViolet\'>';
                            break;
                        case 'g':
                            out += '</span><span class=\'colXLtGreen\'>';
                            break;
                        case 'G':
                            out += '</span><span class=\'colXLtGreen\'>';
                            break;
                        case 'T':
                            out += '</span><span class=\'colDkBrown\'>';
                            break;
                        case 't':
                            out += '</span><span class=\'colLtBrown\'>';
                            break;
                        case '~':
                            out += '</span><span class=\'colBlack\'>';
                            break;
                        case 'j':
                            out += '</span><span class=\'colMdGrey\'>';
                            break;
                        case 'J':
                            out += '</span><span class=\'colMdBlue\'>';
                            break;
                        case 'e':
                            out += '</span><span class=\'colDkRust\'>';
                            break;
                        case 'E':
                            out += '</span><span class=\'colLtRust\'>';
                            break;
                        case 'l':
                            out += '</span><span class=\'colDkLinkBlue\'>';
                            break;
                        case 'L':
                            out += '</span><span class=\'colLtLinkBlue\'>';
                            break;
                        case 'x':
                            out += '</span><span class=\'colburlywood\'>';
                            break;
                        case 'X':
                            out += '</span><span class=\'colbeige\'>';
                            break;
                        case 'y':
                            out += '</span><span class=\'colkhaki\'>';
                            break;
                        case 'Y':
                            out += '</span><span class=\'coldarkkhaki\'>';
                            break;
                        case 'k':
                            out += '</span><span class=\'colaquamarine\'>';
                            break;
                        case 'K':
                            out += '</span><span class=\'coldarkseagreen\'>';
                            break;
                        case 'p':
                            out += '</span><span class=\'collightsalmon\'>';
                            break;
                        case 'P':
                            out += '</span><span class=\'colsalmon\'>';
                            break;
                        case 'm':
                            out += '</span><span class=\'colwheat\'>';
                            break;
                        case 'M':
                            out += '</span><span class=\'coltan\'>';
                            break;
                    }
                    x++;
                }
            }
            else {
            out += y;
            }
        }
        return $sce.trustAsHtml(out.replace(/\booc\b/g, '') + end);
    }
    $scope.formatCommentary = (data) => {
        var response = data.data;
        $scope.comments = [];
        for (var i = 0; i < response.length; i++) {
            temp = $scope.formatColors("this is a comment", true);
            $scope.comments[i] = response[i];
        }
        return $scope.comments;
    }
})
.directive('ngEnter', () => {
    return (scope, element, attrs) => {
        element.bind('keydown keypress', (event) => {
            if (event.which === 13) {
                scope.$apply(() => {
                    scope.$eval(attrs.ngEnter);
                });
                event.preventDefault();
            }
        });
    };
});
