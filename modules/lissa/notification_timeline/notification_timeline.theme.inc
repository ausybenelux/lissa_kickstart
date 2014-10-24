<?php

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Render\Element;

/**
 * @file
 * Theme preprocess functions for notification timelines.
 */

/**
 * Prepares variables for displaying a notification timeline.
 */
function template_preprocess_notification_timeline(&$variables) {
  $notifications = $variables['notifications'];
  $variables['notifications'] = \Drupal::entityManager()->getViewBuilder('notification_entity')->viewMultiple($notifications, 'full');
}
