(function ($, w) {
    w.bea_find_media_warn_list = function (media_id) {
        var string = bea_find_media.i18n.warning_confirm;
        var counter = jQuery('#post-' + media_id).find('.bea-media-analytics-counter a').text();
        var counter_label = Number(counter) > 1 ? bea_find_media.i18n.time_plural : bea_find_media.i18n.time_singular;
        return confirm(string.replace('%s', counter + ' ' + counter_label));
    }
})(jQuery, window);
(function ($, w) {
    w.bea_find_media_warn_single = function (counter) {
        var string = bea_find_media.i18n.warning_confirm;
        var counter_label = Number(counter) > 1 ? bea_find_media.i18n.time_plural : bea_find_media.i18n.time_singular;
        return confirm(string.replace('%s', counter + ' ' + counter_label));
    }
})(jQuery, window);