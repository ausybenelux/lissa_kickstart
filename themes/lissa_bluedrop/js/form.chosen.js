/**
 * @file
 * Defines Javascript behaviors for Chosen.
 */

(function ($, Drupal, drupalSettings) {

  "use strict";

  Drupal.behaviors.chosen = {
    attach: function (context) {
      $(context).find('#notification-forms select').once('chosen').chosen({
        width: '300px'
      });
    },
    weight: 100
  };

})(jQuery, Drupal, drupalSettings);
