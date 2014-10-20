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

        $cancel.appendTo($context.find('#notification-forms .form-actions'));
      });

      // Adds smooth scrolling to the timeline links
      // Credits to: http://css-tricks.com/snippets/jquery/smooth-scrolling/
      $context.find('.js-timeline-link').click(function() {
        if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
          var target = $(this.hash);
          target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
          if (target.length) {
            $('html,body').animate({
              scrollTop: target.offset().top
            }, 1000);
            return false;
          }
        }
      });

      var currentActiveLink = $context.find('#notification-entity-current');
      var updateCurrentActiveLink = function($new) {
        // Remove old selected timeline link
        currentActiveLink.removeClass('timeline-active-link');

        currentActiveLink = $new;
        currentActiveLink.addClass('timeline-active-link');
      };

      // Set active class to timeline link
      $context.find('.js-timeline-link').click(function() {
        updateCurrentActiveLink($(this));
      });

      // Automatically update selected timeline link when scrolling
      $context.scroll(function () {
        var top = window.pageYOffset;
        console.log('top:' + top);

        $context.find('.js-timeline-link').each(function () {
          var distance = top - $(this).offset().top;
          console.log($(this).offset().top);
          console.log('distance: ' + distance);
          var hash = $(this).attr('href');

          if (distance < 30 && distance > -30 && currentActiveLink.attr('href') !== hash) {
            console.log(hash);
            updateCurrentActiveLink($(this));
          }
        })
      });

      // Makes the timeline navigation sticky with the jQuery Sticky plugin
      $context.find(".js-timeline-navigation").sticky({topSpacing:100});
    }
  };

})(jQuery, Drupal, drupalSettings);
