miniShop2.Message = {
    initialize: function () {
        miniShop2.Message.close = function () {
        };
        miniShop2.Message.show = function (message) {
            if (message != '') {
                alert(message);
            }
        };

        if (typeof($.fn.jGrowl) === 'function') {
            $.jGrowl.defaults.closerTemplate = '<div>[ ' + miniShop2Config.close_all_message + ' ]</div>';
            miniShop2.Message.close = function () {
                $.jGrowl('close');
            };
            miniShop2.Message.show = function (message, options) {
                if (message != '') {
                    $.jGrowl(message, options);
                }
            }
        }
    },
    success: function (message) {
        if (typeof($.fn.jGrowl) === 'function') {
            miniShop2.Message.show(message, {
                theme: 'ms2-message-success',
                sticky: false
            });
        }
    },
    error: function (message) {
        if (typeof($.fn.jGrowl) === 'function') {
            miniShop2.Message.show(message, {
                theme: 'ms2-message-error',
                sticky: false
            });
        }
    },
    info: function (message) {
        if (typeof($.fn.jGrowl) === 'function') {
            miniShop2.Message.show(message, {
                theme: 'ms2-message-info',
                sticky: false
            });
        }
    }
};
