<?php
/**
 * @file
 * Contains \Drupal\event_monitor\Form\SelectEventsForm.
 */

namespace Drupal\event_monitor\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * This form lets the user select events which he wants to monitor.
 */
class SelectEventsForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'event_monitor_select_events_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#method'] = 'get';

    $header = [
      'event' => t('Event'),
    ];

    $options = $this->getEvents();

    $form['events'] = [
      '#type' => 'tableselect',
      '#header' => $header,
      '#options' => $options,
      ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Monitor events'),
    ];

    return $form;
  }

  private function getEvents() {
    $return = [];

    $events = entity_load_multiple_by_properties('node', ['type' => 'soccer_event']);

    foreach ($events as $event) {
      $return[$event->id()] = [
        'event' => $event->getTitle(),
      ];
    }

    return $return;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

  public function getPageTitle() {
    return t('Select Events');
  }
}
