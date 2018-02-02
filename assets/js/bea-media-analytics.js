(function ($, w) {
    w.bea_media_analytics_warn_list = function (media_id) {
        var string = bea_media_analytics.i18n.warning_confirm;
        var counter = jQuery('#post-' + media_id).find('.bea-media-analytics-counter a').text();
        var counter_label = Number(counter) > 1 ? bea_media_analytics.i18n.time_plural : bea_media_analytics.i18n.time_singular;
        return confirm(string.replace('%s', counter + ' ' + counter_label));
    }
})(jQuery, window);
(function ($, w) {
    w.bea_media_analytics_warn_single = function (counter) {
        var string = bea_media_analytics.i18n.warning_confirm;
        var counter_label = Number(counter) > 1 ? bea_media_analytics.i18n.time_plural : bea_media_analytics.i18n.time_singular;
        return confirm(string.replace('%s', counter + ' ' + counter_label));
    }
})(jQuery, window);