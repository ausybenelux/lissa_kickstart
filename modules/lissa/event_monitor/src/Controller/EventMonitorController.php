<?php

/**
 * @file
 * Contains \Drupal\event_monitor\Controller\EventMonitorController.
 */

namespace Drupal\event_monitor\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\Request;


/**
 * Returns responses for the event monitor module
 */
class EventMonitorController extends ControllerBase {

  /**
   * Route page callback: returns the HTML for the default monitor event page.
   *
   * @param Request $request
   *   The http request object containing url params.
   *
   * @return array
   *   A render array containing the data to display.
   */
  public function loadEvents($events) {
    $build = [];

    $event_entities = entity_load_multiple('node', json_decode($events));

    $build['events'] = [
      '#attributes' => array('id' => 'events-list'),
      '#type' => 'html_tag',
      '#tag' => 'div',
    ];

    $view_builder = \Drupal::entityManager()->getViewBuilder('node');
    foreach ($event_entities as $event) {
      $build['events'][$event->id()] = $view_builder->view($event);
    }

    return $build;
  }
}
