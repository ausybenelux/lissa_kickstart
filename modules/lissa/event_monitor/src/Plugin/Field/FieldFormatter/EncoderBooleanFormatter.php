<?php

/**
 * @file
 * Contains \Drupal\event_monitor\Plugin\Field\FieldFormatter\EncoderBooleanFormatter.
 */

namespace Drupal\event_monitor\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'video_mpegdash' formatter.
 *
 * @FieldFormatter(
 *   id = "encoder_boolean",
 *   label = @Translation("Custom formatter for encoder field "),
 *   field_types = {
 *     "boolean",
 *   }
 * )
 */
class EncoderBooleanFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items) {
    $elements = array();

    foreach ($items as $delta => $item) {
      $elements[$delta] = array(
        '#theme' => 'encoder_boolean',
        '#markup' => $item->value ? $this->getFieldSetting('on_label') : $this->getFieldSetting('off_label'),
        '#running' => $item->value,
      );
    }

    return $elements;
  }

}