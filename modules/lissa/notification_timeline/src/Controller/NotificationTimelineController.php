<?php

/**
 * @file
 * Contains \Drupal\notification_timeline\Controller\NotificationTimelineController.
 */

namespace Drupal\notification_timeline\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\notification_entity\Entity\NotificationEntity;


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
    $build['notification_type_forms'] = self::buildNotificationForms();
    $build['timeline'] = $this->getTimeline($node);
    return $build;
  }

  /**
   * Builds a render array containing all notification form types.
   *
   * @return array
   */
  public static function buildNotificationForms() {
    $element = array(
      '#attributes' => array('id' => 'notification-forms'),
      '#type' => 'container',
      '#attached' => array(
        'library' => array(
          'notification_timeline/notification_timeline',
        ),
      ),
    );
    $form_builder = \Drupal::service('entity.form_builder');
    // Add all type forms and hide them.
    \Drupal::entityManager()->getStorage('notification_entity')->resetCache();
    foreach (\Drupal::entityManager()->getStorage('notification_type')->loadMultiple() as $type) {
      $entity = \Drupal::entityManager()->getStorage('notification_entity')->create(array('type' => $type->id()));
      // Force empty user input so this form is rebuilt from scratch on an ajax
      // call.
      $element[$type->id()] = $form_builder->getForm($entity, 'default', array('input' => array()));
      $element[$type->id()]['form_label'] = array(
        '#markup' => '<h2>' . t('Add !notification_type', array('!notification_type' => $type->label())) . '</h2>',
        '#weight' => -100,
      );
      $element[$type->id()]['#attributes']['class'][] = 'js-hide';
    }

    return $element;
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
    $build['timeline'] = self::getTimeline($node);
    return $build;
  }

  /**
   * Returns a render array with the timeline of the specified node.
   */
  protected static function getTimeline(NodeInterface $node) {
    $entities = self::getNotifications($node);
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
  protected static function getNotifications(NodeInterface $node) {
    $entity_query = \Drupal::entityQuery('notification_entity', 'AND');
    $entity_query->condition('host_id', $node->id());
    $entity_query->sort('timeline', 'DESC');
    $result = $entity_query->execute();
    return $result ? \Drupal::entityManager()->getStorage('notification_entity')->loadMultiple($result) : array();
  }

  /**
   * Checks access to the node timeline.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The current node.
   *
   * @return string
   *   A \Drupal\Core\Access\AccessInterface constant value.
   */
  public function checkTimelineAccess(NodeInterface $node) {
    $node_type = $node->type->entity;
    $enabled = $node_type->getThirdPartySetting('notification_timeline', 'enabled', FALSE);
    return AccessResult::allowedIf($enabled);
  }
}
