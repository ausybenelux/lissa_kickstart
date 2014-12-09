/**
 * @file
 * Defines Javascript behaviors for the notification timeline module.
 */

(function ($, Drupal, drupalSettings) {

  "use strict";


  if (!Drupal.notificationTimeline) {
    Drupal.notificationTimeline = {};
  }

  /**
   * Update the active link in the navigation.
   *
   * @param $new
   *   The new active link.
   */
  Drupal.notificationTimeline.updateCurrentActiveLink = function($new) {
    // Remove old selected timeline link
    $('.timeline-active-link').removeClass('timeline-active-link');
    Drupal.notificationTimeline.currentActiveLink = $new;
    Drupal.notificationTimeline.currentActiveLink.addClass('timeline-active-link');
  };

  /**
   * Generate a new navigation.
   */
  Drupal.notificationTimeline.generateNavigation = function() {
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

    // Adds smooth scrolling to the timeline links
    // Credits to: http://css-tricks.com/snippets/jquery/smooth-scrolling/
    $('.js-timeline-link').click(function() {
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
      Drupal.notificationTimeline.updateCurrentActiveLink($(this));
    });

    // Add waypoints so active timeline link updates when scrolling
    $('.js-notification-entity').once('not-time-entity').each(function () {
      $(this).waypoint(function () {
        // Find the timeline link for this notification entity
        var notification_entity = $(this).attr('id');
        var link = $('.js-timeline-navigation').find('a[href="#' + notification_entity + '"]');

        // If a link exists for the notification entity, make it active
        if (link.length) {
          Drupal.notificationTimeline.updateCurrentActiveLink(link);
        }
      });
    });

    // Makes the timeline navigation sticky with the jQuery Sticky plugin
    $(".js-timeline-navigation").once('not-time-nav').sticky({topSpacing:100});
  };

  /**
   * Sort the notifications based on the timeline time.
   */
  Drupal.notificationTimeline.sortItems = function() {
    var notifications = $('.notification-entity');

    var ordered_notifications = notifications.sort(function (a, b) {
      var a_time = $(a).data('timeline-time');
      var b_time = $(b).data('timeline-time');

      if (a_time == b_time) {
        return 0;
      }

      return a_time > b_time ? -1 : 1;
    });

    $('#js-notification-list').html(ordered_notifications);
  };

  /**
   * Toggle notification entity forms based on type selection.
   */
  Drupal.notificationTimeline.generateToggleForm = function($context) {
    $context.find('.notification-timeline-notification-form').once('toggle-form').on('update', function() {
      var $form = $(this);
      $form.removeClass('js-hide');
      $form.find('select').once('not-time-select').change(function (e) {
        var $activeForm = $('#notification-forms .' + $(this).val() + '-notification-entity-form');
        // Toggle the forms.
        $('.notification-timeline-notification-form').addClass('js-hide');
        $activeForm.removeClass('js-hide');

        // Reset the timeline data.
        var today = new Date();
        // Go back 30 seconds.
        today.setTime(today.getTime() - 30000);
        var month = (today.getMonth() + 1).toString();
        var day = today.getDate().toString();
        if (month.length < 2) {
          month = "0" + month;
        }
        if (day.length < 2) {
          day = "0" + day;
        }
        var dayString = today.getFullYear() + "-" + month + "-" + day;
        var $dateElements = $activeForm.find("[name^='timeline']");
        $dateElements.filter("[type=date]").val(dayString);
        $dateElements.filter("[type=time]").val(today.toLocaleTimeString());

        // Reset the select value.
        $(this).val('0');
      });
    })
    .trigger('update');
  };

  /**
   * Add cancel links to all forms.
   */
  Drupal.notificationTimeline.addCancelLinks = function() {
    $('#notification-forms').once('not-time-form').each(function() {
      var $cancel = $('<a href="#" class="form-cancel">' + Drupal.t('Cancel') + '</a>');
      $cancel.click(function(e) {
        e.preventDefault();
        $('#notification-forms form').addClass('js-hide');
        $('.notification-timeline-notification-form').removeClass('js-hide');
      });

      $cancel.appendTo($('#notification-forms .form-actions'));
    });
  };

  Drupal.behaviors.notificationTimeline = {
    attach: function (context) {
      var $context = $(context);
      Drupal.notificationTimeline.currentActiveLink = $('a[href="#notification-entity-current"]');
      Drupal.notificationTimeline.sortItems();
      Drupal.notificationTimeline.generateNavigation();
      Drupal.notificationTimeline.generateToggleForm($context);
      Drupal.notificationTimeline.addCancelLinks();
    }
  };

})(jQuery, Drupal, drupalSettings);
