(function ($, w) {
    w.bea_find_media_warn = function (media_id) {
        var string = bea_find_media.i18n.warning_confirm;
        var counter = jQuery('#post-' + media_id).find('.bea-find-media-counter a').text();
        var counter_label = Number(counter) > 1 ? bea_find_media.i18n.time_plural : bea_find_media.i18n.time_singular;
        return confirm(string.replace('%s', counter + ' ' + counter_label));
    }
})(jQuery, window);