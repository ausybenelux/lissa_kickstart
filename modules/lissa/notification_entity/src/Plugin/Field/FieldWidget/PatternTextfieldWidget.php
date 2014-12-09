<?php

/**
 * @file
 * Contains \Drupal\notification_entity\Plugin\Field\FieldWidget\PatternTextfieldWidget.
 */

namespace Drupal\notification_entity\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\StringTextfieldWidget;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'pattern_textfield' widget.
 *
 * @FieldWidget(
 *   id = "pattern_textfield",
 *   label = @Translation("Pattern Textfield"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class PatternTextfieldWidget extends StringTextfieldWidget {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'pattern' => '',
    ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);

    $element['pattern'] = array(
      '#type' => 'textfield',
      '#title' => t('Pattern'),
      '#default_value' => $this->getSetting('pattern'),
      '#description' => t('Text that will be shown inside the field until a value is entered. This hint is usually a sample value or a brief description of the expected format.'),
    );

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();

    $pattern = $this->getSetting('pattern');
    if (!empty($pattern)) {
      $summary[] = t('Pattern: @pattern', array('@pattern' => $pattern));
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['value'] = $element + array(
        '#type' => 'pattern_textfield',
        '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : NULL,
        '#size' => $this->getSetting('size'),
        '#placeholder' => $this->getSetting('placeholder'),
        '#maxlength' => $this->getFieldSetting('max_length'),
        '#attributes' => array('class' => array('text-full')),
        '#pattern' => $this->getSetting('pattern'),
      );

    return $element;
  }

}
