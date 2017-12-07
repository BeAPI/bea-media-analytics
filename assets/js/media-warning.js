(function ($, w) {
    w.bea_find_media_warn = function (media_id) {
        var string = bea_find_media.i18n.warning_confirm;
        var counter = jQuery('#post-'+media_id).find('.bea-find-media-counter a').text();
        return confirm(string.replace('%s', counter));
    }
})(jQuery, window);