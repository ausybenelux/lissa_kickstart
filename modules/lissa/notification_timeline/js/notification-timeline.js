/**
 * @file
 * Defines Javascript behaviors for the notification timeline module.
 */

(function ($, Drupal, drupalSettings) {

  "use strict";

  Drupal.behaviors.notificationTimelineTypeSelector = {
    attach: function (context) {
      var $context = $(context);
      var currentActiveLink = $context.find('a[href="#notification-entity-current"]');

      /**
       * Update the active link in the navigation.
       *
       * @param $new
       *   The new active link.
       */
      var updateCurrentActiveLink = function($new) {
        // Remove old selected timeline link
        $('.timeline-active-link').removeClass('timeline-active-link');

        currentActiveLink = $new;
        currentActiveLink.addClass('timeline-active-link');
      };

      /**
       * Generate a new navigation.
       */
      var generateNavigation = function() {
        var $timeline = $('#notification-timeline-notification-form');
        var $navigation = $('<div class="timeline-navigation"><nav class="js-timeline-navigation"><ul></ul></nav></div>');
        $('.timeline-navigation').remove();
        $navigation.insertBefore($timeline);
        var $navigationList = $navigation.find('ul');

        // Add the current item.
        $('<li data-step="current"><a href="#main-content" class="js-timeline-link">' + Drupal.t('Current') + '</a></li>')
            .appendTo($navigationList);

        // Add the steps to the timeline.
        $('[data-timeline-step]').each(function() {
          var step = $(this).attr('data-timeline-step');
          if (!$navigationList.find('[data-step="' + step + '"]').length) {
            $('<li data-step="' + step + '"><a href="#' + $(this).attr('id') + '" class="js-timeline-link">' + step + '</a></li>')
                .appendTo($navigationList);
          }
        });

        // Add the start item.
        $('<li data-step="start"><a href="#notification-entity-start" class="js-timeline-link">' + Drupal.t('Start') + '</a></li>')
            .appendTo($navigationList);
      }();

      // Toggle notification entity forms based on type selection.
      $('.notification-timeline-notification-form select').once('not-time-select').change(function(e) {
        var $activeForm = $context.find('#notification-forms .' + $(this).val() + '-notification-entity-form');
        // Toggle the forms.
        $context.find('#notification-forms form').addClass('js-hide');
        $context.find('.notification-timeline-notification-form').addClass('js-hide');
        $activeForm.removeClass('js-hide');

        // Reset the timeline data.
        var today = new Date();
        // Go back 30 seconds.
        today.setTime(today.getTime() - 30000);
        var month = (today.getMonth() + 1).toString();
        if (month.length < 2) {
          month = "0" + month;
        }
        var day = today.getFullYear() + "-" + month + "-" + today.getDate();
        var $dateElements = $activeForm.find("[name^='timeline']");
        $dateElements.filter("[type=date]").val(day);
        $dateElements.filter("[type=time]").val(today.toLocaleTimeString());

        // Reset the select value.
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

      $('#js-notification-list').on('ajaxSubmit', function () {
        
        removeContentsFromDiv('#js-notification-list');

      });

      // Ugly but necessary because of https://www.drupal.org/node/736066
      var removeContentsFromDiv = function (selector) {
        var div = $(selector).children(':first')[0];

        if (div.tagName === 'DIV') {
          var innerHtml = div.innerHTML;
          $(selector).prepend(innerHtml);
          $(div).remove();
        }

      };

      // Sort the notifications based on the timeline time


      // Adds smooth scrolling to the timeline links
      // Credits to: http://css-tricks.com/snippets/jquery/smooth-scrolling/
      $('.js-timeline-link').once('not-time-link').click(function() {
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

        // Set active class to timeline link when clicked.
        updateCurrentActiveLink($(this));
      });

      // Add waypoints so active timeline link updates when scrolling
      $('.js-notification-entity').once('not-time-entity').each(function () {
        $(this).waypoint(function () {
          // Find the timeline link for this notification entity
          var notification_entity = $(this).attr('id');
          var link = $('.js-timeline-navigation').find('a[href="#' + notification_entity + '"]');

          // If a link exists for the notification entity, make it active
          if (link.length) {
            updateCurrentActiveLink(link);
          }
        });
      });

      // Makes the timeline navigation sticky with the jQuery Sticky plugin
      $(".js-timeline-navigation").once('not-time-nav').sticky({topSpacing:100});
    }
  };

})(jQuery, Drupal, drupalSettings);
