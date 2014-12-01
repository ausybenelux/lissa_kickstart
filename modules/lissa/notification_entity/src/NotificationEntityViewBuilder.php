<?php

/**
 * @file
 * Definition of Drupal\notification_entity\NotificationEntityViewBuilder.
 */

namespace Drupal\notification_entity;

use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityViewBuilder;

/**
 * Render controller for notification entities.
 */
class NotificationEntityViewBuilder extends EntityViewBuilder {

  /**
   * {@inheritdoc}
   */
  protected function alterBuild(array &$build, EntityInterface $entity, EntityViewDisplayInterface $display, $view_mode, $langcode = NULL) {
    /** @var \Drupal\notification_entity\NotificationEntityInterface $entity */
    parent::alterBuild($build, $entity, $display, $view_mode, $langcode);
    $build['#contextual_links']['notification_entity'] = [
      'route_parameters' => array('notification_entity' => $entity->id()),
      'metadata' => array('changed' => $entity->getChangedTime()),
    ];
  }

}
