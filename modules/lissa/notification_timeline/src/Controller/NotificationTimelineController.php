<?php

/**
 * @file
 * Contains \Drupal\notification_timeline\Controller\NotificationTimelineController.
 */

namespace Drupal\notification_timeline\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;


/**
 * Returns responses for devel module routes.
 */
class NotificationTimelineController extends ControllerBase {

  public function nodeLoad(NodeInterface $node) {
    return 'Timeline';
  }
}
