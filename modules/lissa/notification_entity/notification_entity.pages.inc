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
