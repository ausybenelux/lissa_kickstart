<?php

/**
 * @file
 * Contains \Drupal\notification_timeline\Controller\NotificationTimelineController.
 */

namespace Drupal\notification_timeline\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Drupal\taxonomy\VocabularyInterface;


/**
 * Returns responses for devel module routes.
 */
class NotificationTimelineController extends ControllerBase {

  /**
   * Route page callback: returns the HTML for the default timeline page.
   *
   * @param NodeInterface $node
   *   The node to show the timeline for.
   *
   * @return array
   *   A render array containing the data to display.
   */
  public function nodeLoad(NodeInterface $node) {
    $build = array();
    $build['notification_form'] = $this->formBuilder()->getForm('Drupal\notification_timeline\Form\NotificationTimelineNotificationForm');
    return $build;
  }

  /**
   * Route page callback: returns the HTML for adding a notification
   *
   * @param NodeInterface $node
   *   The node to add a notification to.
   * @param string $notification_type
   *   The id of the notification type.
   *
   * @return array
   *   A render array containing the data to display.
   */
  public function addNotification(NodeInterface $node, $notification_type) {
    $build = array();
    $form_builder = \Drupal::service('entity.form_builder');
    $entity = \Drupal::entityManager()->getStorage('notification_entity')->create(array('type' => $notification_type));
    $build['notification_form'] = $form_builder->getForm($entity);
    $entities = $this->getNotifications($node);
    $build['timeline'] = \Drupal::entityManager()->getViewBuilder('notification_entity')->viewMultiple($entities, 'full');
    return $build;
  }

  /**
   * Returns an array of notifications linked to the specified node.
   */
  protected function getNotifications(NodeInterface $node) {
    $entity_query = \Drupal::entityQuery('notification_entity', 'AND');
    $entity_query->condition('host_id', $node->id());
    $entity_query->sort('timeline', 'DESC');
    $result = $entity_query->execute();
    return $result ? \Drupal::entityManager()->getStorage('notification_entity')->loadMultiple($result) : array();
  }
}
