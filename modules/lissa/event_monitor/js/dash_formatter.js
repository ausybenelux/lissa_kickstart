/**
 * @file
 * Defines Javascript behaviors for the notification timeline module.
 */

(function ($, Drupal, drupalSettings) {

    "use strict";

    Drupal.behaviors.dashFormattedField = {
        attach: function (context) {
            var $context = $(context);

            $context.find('.js-dash-video-player').each(function () {
                var url = $(this).data('url');
                var id = $(this).data('id');

                var context = new Dash.di.DashContext();
                var player = new MediaPlayer(context);
                player.startup();

                // This can not be a jquery element or the dash library crashes!
                player.attachView(document.querySelector('#js-video-'+ id));
                player.setAutoPlay(true);
                player.attachSource(url);
            });
        }
    };

})(jQuery, Drupal, drupalSettings);
