<?php

/**
 * @file
 * Contains \Drupal\notification_entity\Plugin\Field\FieldFormatter\TimelineDateFormatter.
 */

namespace Drupal\notification_entity\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'timeline_date' formatter.
 *
 * Renders a datetime field as an offset from the timeline start time.
 *
 * @FieldFormatter(
 *   id = "timeline_date",
 *   label = @Translation("Timeline date"),
 *   field_types = {
 *     "datetime"
 *   }
 * )
 */
class TimelineDateFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items) {
    $elements = array();
    $entity = $items->getEntity();
    $timeline_date =  $entity->get('host_id')->entity->get('field_event_timeline_start')->date;

    if ($timeline_date) {
      foreach ($items as $delta => $item) {
        $formatted_date = '';
        $iso_date = '';

        if ($item->date) {
          $difference = $item->date->format("U") - $timeline_date->format("U");
          $minutes = (string) floor($difference / 60);
          $seconds = (string) ($difference % 60);
          $minutes = strlen($minutes) < 2 ? '0' . $minutes : $minutes;
          $seconds = strlen($seconds) < 2 ? '0' . $seconds : $seconds;
          $formatted_date = $minutes . ':' . $seconds;
          $iso_date = $item->date->format("Y-m-d\TH:i:s") . 'Z';
        }

        // Display the date using theme datetime.
        $elements[$delta] = array(
          '#theme' => 'time',
          '#text' => $formatted_date,
          '#html' => FALSE,
          '#attributes' => array(
            'datetime' => $iso_date,
          ),
        );
        if (!empty($item->_attributes)) {
          $elements[$delta]['#attributes'] += $item->_attributes;
          // Unset field item attributes since they have been included in the
          // formatter output and should not be rendered in the field template.
          unset($item->_attributes);
        }
      }
    }

    return $elements;
  }
}
