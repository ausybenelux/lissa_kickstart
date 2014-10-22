/**
 * @file
 * Defines Javascript behaviors for attaching form tooltips.
 */

(function ($, Drupal, drupalSettings) {

  "use strict";

  Drupal.behaviors.formTooltips = {
    attach: function (context) {
      $(document).once('form-tooltip').tooltip({
        items: "input[type=text]",
        content: function() {
          var $description = $(this).sibling('.description');
          if ($description.length) {
            return $description.html();
          }
        }
      });
    }
  };

})(jQuery, Drupal, drupalSettings);
