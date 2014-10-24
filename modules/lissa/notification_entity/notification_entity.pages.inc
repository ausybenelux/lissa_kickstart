<?php

/**
 * @file
 * Callbacks for adding, editing, and deleting notification_entities and managing revisions.
 *
 * Also includes validation, submission and other helper functions.
 */

use Drupal\Component\Utility\Xss;
use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Render\Element;

/**
 * Prepares variables for list of available node type templates.
 *
 * Default template: node-add-list.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - content: An array of notification_entity types.
 *
 * @see notification_entity_add_page()
 */
function template_preprocess_notification_entity_add_list(&$variables) {
  $variables['types'] = array();
  if (!empty($variables['content'])) {
    foreach ($variables['content'] as $type) {
      $variables['types'][$type->type] = array(
        'type' => $type->type,
        'add_link' => \Drupal::l($type->name, new Url('notification_entity.add', array('notification_type' => $type->type))),
        'description' => Xss::filterAdmin($type->description),
      );
    }
  }
}

/**
 * Prepares variables for notification_entity templates.
 */
function template_preprocess_notification_entity(&$variables) {
  $variables['view_mode'] = $variables['elements']['#view_mode'];
  $entity = $variables['elements']['#notification_entity'];
  $variables['notification_entity'] = $entity;
  /** @var \Drupal\notification_entity\NotificationEntityInterface $notification_entity */
  $notification_entity = $variables['notification_entity'];
  $variables['id'] = $notification_entity->id();
  $variables['label'] = $variables['elements']['title'];
  unset($variables['elements']['title']);

  // Add the step inside the timeline this notification should appear.
  $variables['timeline_step'] = 'start';
  // Amount of minutes to segment the timeline.
  $step = 5;
  $date =  $entity->get('host_id')->entity->get('field_event_timeline_start')->date;

  if ($date) {
    $start = $date->format('U');
    $timeline_time = $entity->get('timeline')->date;
    if (!empty($timeline_time)) {
      $notification_time = $timeline_time->format('U');
      // Calculate the amount of minutes have passed since the start time.
      $minute = ceil(($notification_time - $start) / 60);
      // Calculate the current step in the timeline.
      $current_step = ($step * ceil($minute / $step));
      $current_step_formatted = $current_step . ':00';
      if (strlen($current_step_formatted) == 4) {
        $current_step_formatted = '0' . $current_step_formatted;
      }
      $variables['timeline_step'] = $current_step_formatted;
    }
  }

  // Helpful $content variable for templates.
  $variables += array('content' => array());
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}