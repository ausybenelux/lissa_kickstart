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
  public function title(EntityInterface $notification_entity) {
    return String::checkPlain($notification_entity->label());
  }

  /**
   * {@inheritdoc}
   */
  public function view(EntityInterface $notification_entity, $view_mode = 'full', $langcode = NULL) {
    $build = array('nodes' => parent::view($notification_entity));

    $build['#title'] = $build['nodes']['#title'];
    unset($build['nodes']['#title']);

    foreach ($notification_entity->uriRelationships() as $rel) {
      // Set the node path as the canonical URL to prevent duplicate content.
      $build['#attached']['drupal_add_html_head_link'][] = array(
        array(
          'rel' => $rel,
          'href' => $notification_entity->url($rel),
        ),
        TRUE,
      );

      if ($rel == 'canonical') {
        // Set the non-aliased canonical path as a default shortlink.
        $build['#attached']['drupal_add_html_head_link'][] = array(
          array(
            'rel' => 'shortlink',
            'href' => $notification_entity->url($rel, array('alias' => TRUE)),
          ),
          TRUE,
        );
      }
    }

    return $build;
  }

}
