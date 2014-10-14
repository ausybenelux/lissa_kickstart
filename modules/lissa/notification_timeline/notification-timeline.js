/**
 * @file
 * Defines Javascript behaviors for the notification timeline module.
 */

(function ($, Drupal, drupalSettings) {

  "use strict";

  Drupal.behaviors.notificationTimelineTypeSelector = {
    attach: function (context) {
      var $context = $(context);

      // Toggle notification entity forms based on type selection.
      $context.find('.notification-timeline-notification-form select').once('not-time-select').change(function(e) {
        $context.find('#notification-forms form').addClass('js-hide');
        $context.find('#notification-forms .' + $(this).val() + '-notification-entity-form').removeClass('js-hide');
        $context.find('.notification-timeline-notification-form').addClass('js-hide');
        $(this).val('0');
      });

      // Add a cancel link to all notification entity forms.
      $context.find('#notification-forms').once('not-time-form').each(function() {
        var $cancel = $('<a href="#" class="form-cancel">' + Drupal.t('Cancel') + '</a>');
        $cancel.click(function(e) {
          e.preventDefault();
          $context.find('#notification-forms form').addClass('js-hide');
          $context.find('.notification-timeline-notification-form').removeClass('js-hide');
        });

        $cancel.appendTo($context.find('#notification-forms form'));
      });
    }
  };

})(jQuery, Drupal, drupalSettings);
