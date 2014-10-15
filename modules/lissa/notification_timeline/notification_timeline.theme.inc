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
  // Amount of minutes to segment the timeline.
  $step = 5;
  // Show a timeline showing 5 minute segments between the start of the event
  // and the current time.
  $date =  $variables['node']->get('field_event_timeline_start')->date;
  $segments = array(
    'current' => t('Current'),
  );

  if ($date) {
    $start = $date->format('U');

    // List of already registered steps.
    $steps = array();

    // Generate the timeline navigation.
    foreach ($notifications as $notification) {
      $timeline_time = $notification->get('timeline')->date;
      if (!empty($timeline_time)) {
        $notification_time = $timeline_time->format('U');
        // Calculate the amount of minutes have passed since the start time.
        $minute = ceil(($notification_time - $start) / 60);
        // Calculate the current step in the timeline.
        $current_step = ($step * ceil($minute / $step));
        // If the step isn't in the timeline yet, add it.
        if (!in_array($current_step, $steps)) {
          $steps[] = $current_step;
          $current_step_formatted = $current_step . ':00';
          if (strlen($current_step_formatted) == 4) {
            $current_step_formatted = '0' . $current_step_formatted;
          }
          $segments[$notification->id()] = $current_step_formatted;
        }
      }
    }
  }

  $segments['start'] = t('Start');
  $variables['segments'] = $segments;
  $variables['notifications'] = \Drupal::entityManager()->getViewBuilder('notification_entity')->viewMultiple($notifications, 'full');
}
