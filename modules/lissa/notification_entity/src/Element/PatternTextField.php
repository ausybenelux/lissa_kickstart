<?php

/**
 * @file
 * Contains \Drupal\notification_entity\Element\PatternTextField.
 */

namespace Drupal\notification_entity\Element;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\Textfield;
use Drupal\Core\Render\Element;

/**
 * Provides a pattern text field render element.
 *
 * Provides a form element to generate a text field value based on a pattern
 * and the value of other fields.
 *
 * @FormElement("pattern_textfield")
 */
class PatternTextField extends Textfield {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return array(
      '#input' => TRUE,
      '#default_value' => NULL,
      '#required' => TRUE,
      '#maxlength' => 64,
      '#size' => 60,
      '#autocomplete_route_name' => FALSE,
      '#process' => array(
        array($class, 'processAutocomplete'),
        array($class, 'processAjaxForm'),
      ),
      '#after_build' => array(
        array($class, 'afterBuild'),
      ),
      '#pre_render' => array(
        array($class, 'preRenderTextfield'),
      ),
      '#theme' => 'input__textfield',
      '#theme_wrappers' => array('form_element'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
    return NULL;
  }

  /**
   * Processes a pattern form element after other elements have been built.
   *
   * @param array $element
   *   The form element to process. Properties used:
   *   - #pattern: The pattern with placeholders to use as value. Surround each
   *       placeholder with square brackets.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The processed element.
   */
  public static function afterBuild(array $element, FormStateInterface $form_state) {
    if (empty($element['#pattern'])) {
      return $element;
    }

    // We need to pass the langcode to the client.
    $language = \Drupal::languageManager()->getCurrentLanguage();

    $js_settings = array(
      'type' => 'setting',
      'data' => array(
        'patternTextField' => array(
          '#' . $element['#id'] => $element['#pattern'],
        ),
      ),
    );
    $element['#attached']['library'][] = 'notification_entity/element.pattern-text-field';
    $element['#attached']['js'][] = $js_settings;

    return $element;
  }
}