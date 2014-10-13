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
  $variables['notification_entity'] = $variables['elements']['#notification_entity'];
  /** @var \Drupal\notification_entity\NotificationEntityInterface $notification_entity */
  $notification_entity = $variables['notification_entity'];
  $variables['id'] = $notification_entity->id();
  $variables['label'] = $variables['elements']['title'];
  unset($variables['elements']['title']);

  // Helpful $content variable for templates.
  $variables += array('content' => array());
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}