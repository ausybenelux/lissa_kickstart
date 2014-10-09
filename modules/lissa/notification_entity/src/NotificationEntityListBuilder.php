<?php
/**
 * @file
 * Contains \Drupal\notification_entity\NotificationEntityListBuilder.
 */
namespace Drupal\notification_entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Provides a list builder for notifications
 */
class NotificationEntityListBuilder extends EntityListBuilder {
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header = array();
    $header['title'] = $this->t('Title');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row = array();
    $row['title'] = array(
      'data' => $this->getLabel($entity),
      'class' => array('menu-label'),
    );

    return $row + parent::buildRow($entity);
  }
}
