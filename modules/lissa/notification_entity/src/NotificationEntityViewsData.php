<?php

/**
 * @file
 * Contains \Drupal\notification_entity\NotificationEntityViewsData.
 */

namespace Drupal\notification_entity;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides the views data for the node entity type.
 */
class NotificationEntityViewsData extends EntityViewsData implements EntityViewsDataInterface {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['notification_entity']['nid']['field']['id'] = 'notification_entity';
    $data['notification_entity']['nid']['field']['argument'] = [
      'id' => 'notification_entity_nid',
      'name field' => 'title',
      'numeric' => TRUE,
      'validate type' => 'nid',
    ];

    $data['notification_entity']['host_id']['help'] = t('The notification host node.');
    $data['notification_entity']['host_id']['filter']['id'] = 'host_id';
    $data['notification_entity']['host_id']['field']['id'] = 'node';
    $data['notification_entity']['host_id']['relationship']['title'] = t('Host node');
    $data['notification_entity']['host_id']['relationship']['help'] = t('Relate notifications to the host node');
    $data['notification_entity']['host_id']['relationship']['label'] = t('node');

    return $data;
  }

}
