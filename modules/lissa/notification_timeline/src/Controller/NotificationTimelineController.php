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
    $build['notification_type_forms'] = array(
      '#attributes' => array('id' => 'notification-forms'),
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attached' => array(
        'js' => array(
          drupal_get_path('module', 'notification_timeline') . '/js/plugins/Sticky/jquery.sticky.js',
          drupal_get_path('module', 'notification_timeline') . '/js/notification-timeline.js'
        ),
      ),
    );
    $form_builder = \Drupal::service('entity.form_builder');
    // Add all type forms and hide them.
    foreach (\Drupal::entityManager()->getStorage('notification_type')->loadMultiple() as $type) {
      $entity = \Drupal::entityManager()->getStorage('notification_entity')->create(array('type' => $type->id()));
      $build['notification_type_forms'][$type->id()] = $form_builder->getForm($entity);
      $build['notification_type_forms'][$type->id()]['#attributes']['class'][] = 'js-hide';
    }

    $build['timeline'] = $this->getTimeline($node);
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
    $build['timeline'] = $this->getTimeline($node);
    return $build;
  }

  /**
   * Returns a render array with the timeline of the specified node.
   */
  protected function getTimeline(NodeInterface $node) {
    $entities = $this->getNotifications($node);
    $output = array(
      '#theme' => 'notification_timeline',
      '#node' => $node,
      '#notifications' => $entities,
    );
    return $output;
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
