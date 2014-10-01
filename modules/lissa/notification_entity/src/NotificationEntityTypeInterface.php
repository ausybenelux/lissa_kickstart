<?php

/**
 * @file
 * Contains \Drupal\node\NotificationEntityTypeInterface.
 */

namespace Drupal\notification_entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Config\Entity\ThirdPartySettingsInterface;

/**
 * Provides an interface defining a notification type entity.
 */
interface NotificationEntityTypeInterface extends ConfigEntityInterface, ThirdPartySettingsInterface {

  /**
   * Determines whether the notification type is locked.
   *
   * @return string|false
   *   The module name that locks the type or FALSE.
   */
  public function isLocked();
}
