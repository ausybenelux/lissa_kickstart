<?php

/**
 * @file
 * Contains \Drupal\NotificationEntityAccessControlHandler.
 */

namespace Drupal\notification_entity;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the Notification entity type.
 *
 * @see \Drupal\taxonomy\NotificationEntity\Term
 */
class NotificationEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, $langcode, AccountInterface $account) {
    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'access content');
        break;

      case 'update':
        return AccessResult::allowedIfHasPermissions($account, ["edit notification entity"]);
        break;

      case 'delete':
        return AccessResult::allowedIfHasPermissions($account, ["delete notification entity"]);
        break;

      default:
        // No opinion.
        return AccessResult::neutral();
    }
  }

}
