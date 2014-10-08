<?php

/**
 * @file
 * Contains \Drupal\notification_entity\Controller\NotificationEntityViewController.
 */

namespace Drupal\notification_entity\Controller;

use Drupal\Component\Utility\String;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\Controller\EntityViewController;

/**
 * Defines a controller to render a single notification.
 */
class NotificationEntityViewController extends EntityViewController {

  /**
   * The _title_callback for the page that renders a single notification.
   *
   * @param \Drupal\Core\Entity\EntityInterface $notification
   *   The current notification.
   *
   * @return string
   *   The page title.
   */
  public function title(EntityInterface $notification) {
    return String::checkPlain($notification->label());
  }

}
