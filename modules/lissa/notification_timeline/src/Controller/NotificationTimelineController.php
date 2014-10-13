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

  public function nodeLoad(NodeInterface $node) {
    $build = array();
    $build['notification_form'] = $this->formBuilder()->getForm('Drupal\notification_timeline\Form\NotificationTimelineNotificationForm');
    return $build;
  }

  public function addNotification(NodeInterface $node, $notification_type) {
    $build = array();
    $form_builder = \Drupal::service('entity.form_builder');
    $entity = \Drupal::entityManager()->getStorage('notification_entity')->create(array('type' => $notification_type));
    $build['notification_form'] = $form_builder->getForm($entity);
    return $build;
  }
}
