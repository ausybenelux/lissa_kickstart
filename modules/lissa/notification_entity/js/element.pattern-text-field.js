(function ($, Drupal, drupalSettings) {

  "use strict";

  /**
   * Attach the pattern form element behavior.
   */
  Drupal.behaviors.patternTextField = {
    /**
     * Attaches the behavior.
     *
     * @param settings.patternTextField
     *   A list of elements to process, keyed by the HTML ID of the form element
     *   containing the human-readable value. Each element is a string containing
     *   a pattern to replace its value with. The pattern can have tokens
     *   corresponding to the HTML input names of the other elements in the form,
     *   surrounded by square brackets.
     */
    attach: function (context, settings) {
      var self = this;
      var $context = $(context);

      function clickEditHandler(e) {
        var data = e.data;
        e.preventDefault();
        data.$target.show();
        data.$target.trigger('focus');
        data.$preview.hide();
        data.$link.hide();
        data.$siblings.off('keyup.pattern change.pattern input.pattern');
      }

      function patternHandler(e) {
        var data = e.data;
        var value = self.replacePattern(data.$target, data.$siblings, data.pattern);
        self.showPatternValue(value, data);
      }

      Object.keys(settings.patternTextField).forEach(function (source_id) {
        var value, eventData;
        var pattern = settings.patternTextField[source_id];

        var $target = $context.find(source_id).addClass('pattern-textfield-target').once('pattern-textfield');
        var $wrapper = $target.closest('.form-item');
        var $parentForm = $target.parents('form');
        var $siblings = $parentForm.find('input[type=text], select, input[type=radio], input[type=checkbox]')
            .not($target);

        // All elements have to exist.
        if (!$target.length || !$siblings.length) {
          return;
        }
        // Skip processing upon a form validation error on the machine name.
        if ($target.hasClass('error')) {
          return;
        }

        // Hide the form item container of the machine name form element.
        $target.hide();
        // Determine the initial machine name value. Unless the machine name form
        // element is disabled or not empty, the initial default value is based on
        // the human-readable form element value.
        if ($target.is(':disabled') || $target.val() !== '') {
          value = $target.val();
        }
        else {
          value = self.replacePattern($target, $siblings, pattern);
        }
        // Append the machine name preview to the source field.
        var $preview = $('<span class="machine-name-value">' + Drupal.checkPlain(value) + '</span>');
        $wrapper.append(' ').append($preview);

        // If the machine name cannot be edited, stop further processing.
        if ($target.is(':disabled')) {
          return;
        }

        // If it is editable, append an edit link.
        var $link = $('<span class="admin-link"><button type="button" class="link">' + Drupal.t('Edit') + '</button></span>');

        eventData = {
          $target: $target,
          $wrapper: $wrapper,
          $preview: $preview,
          $siblings: $siblings,
          $link: $link,
          pattern: pattern
        };

        $link.on('click', eventData, clickEditHandler);
        $wrapper.append(' ').append($link);

        // Preview the machine name in realtime when the human-readable name
        // changes, but only if there is no machine name yet; i.e., only upon
        // initial creation, not when editing.
        if ($target.val() === '') {
          $siblings.on('keyup.pattern change.pattern input.pattern', eventData, patternHandler)
            // Initialize machine name preview.
              .trigger('keyup');
        }
      });
    },

    replacePattern: function ($target, $sources, pattern) {
      function isEmptyValue(value) {
        return (value === '' || value === '_none');
      }

      var value = pattern;
      $sources.each(function () {
        var $source = $(this);
        if (!isEmptyValue($source.val())) {
          value = value.replace('[' + $source.attr('name') + ']', $source.val());
        }
      });

      // Leave the value empty if not all tokens have been replaced.
      if (value.indexOf('[') != -1 && value.indexOf(']') != -1) {
        return '';
      }

      return value;
    },

    showPatternValue: function (value, data) {
      data.$target.val(value);
      data.$preview.html(Drupal.checkPlain(value));
    }
  };

})(jQuery, Drupal, drupalSettings);
