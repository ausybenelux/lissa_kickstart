/**
 * @file
 * Defines Javascript behaviors for Selectize.
 */

(function ($, Drupal, drupalSettings) {

  "use strict";

  Drupal.behaviors.selectize = {
    attach: function (context) {
      $(context).find('select').once('selectize').selectize({

      });
    }
  };

})(jQuery, Drupal, drupalSettings);
