function blockUI() {
    $('.loading-overlay').show();
}

function unblockUI() {
    $('.loading-overlay').hide();
}

function blockingUIAjax(ajax, success, fail) {
    blockUI();
    $.ajax(ajax)
        .done(success)
        .fail(fail)
        .always(function() {
            unblockUI();
        })
}

var delay = (function(){
    var timer = 0;
    return function(callback, ms, params){
        clearTimeout (timer);
        timer = setTimeout(function() {callback(params)}, ms);
    }
})();