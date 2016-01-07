var srcLog;
var sizeLog;
var intervalLog;

var timer;

refrehLog = function() {
    /* seeLogUrl var defined in view */
    new Ajax.Request(seeLogUrl, {
        method: 'post',
        parameters: 'log=' + srcLog + '&size=' + sizeLog + '&interval=' + intervalLog,
        onComplete: function(transport) {
            Element.hide('loadingmask');
            Element.show('logs');
            $('admintools_logs').update(transport.responseText);
            Element.show('delete_log');
        }
    });
}

showMeLogs = function() {
    $('admintools_logs').innerHTML = "";

    srcLog = $('srcLog').getValue();
    sizeLog = $('sizeLog').getValue();
    intervalLog = $('intervalLog').getValue();

    Element.show('loadingmask');
    Element.hide('delete_log');

    clearInterval(timer);
    timer = setInterval(refrehLog, intervalLog);
    //refrehLog();
};

deleteLog = function() {
    var srcLog = $('srcLog').getValue();
    /* deleteLogUrl var defined in view and translation*/
    deleteConfirm(Translator.translate('delete confirm message'), deleteLogUrl + 'log/' + srcLog);
};

refreshPhpinfoLevel = function() {
    document.getElementById('iframePhpinfo').src = document.getElementById('phpinfoLevel').value;
}