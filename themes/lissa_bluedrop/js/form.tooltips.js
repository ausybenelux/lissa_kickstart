/**
 * @file
 * Defines Javascript behaviors for attaching form tooltips.
 */

(function ($, Drupal, drupalSettings) {

  "use strict";

  Drupal.behaviors.formTooltips = {
    attach: function (context) {
      $(document).tooltip({
        items: "input[type=text]",
        content: function() {
          var $description = $(this).siblings('.description');
          if ($description.length) {
            return $description.html();
          }
        },
        position: {
          my: "left top",
          at: "right+5 top-5"
        }
      });
    }
  };

})(jQuery, Drupal, drupalSettings);
