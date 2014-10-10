<?php

/**
 * @file
 * Contains \Drupal\notification_entity\Controller\NotificationEntityController.
 */

namespace Drupal\notification_entity\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\notification_entity\NotificationEntityTypeInterface;
use Drupal\notification_entity\NotificationEntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for notification_entity routes.
 */
class NotificationEntityController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays add content links for available content types.
   *
   * Redirects to notification/add/[type] if only one content type is available.
   *
   * @return array
   *   A render array for a list of the notification_entity types that can be added; however,
   *   if there is only one node type defined for the site, the function
   *   redirects to the node add page for that one node type and does not return
   *   at all.
   *
   */
  public function addPage() {
    $content = array();

    // Only use notification types the user has access to.
    foreach ($this->entityManager()->getStorage('notification_type')->loadMultiple() as $type) {
      if ($this->entityManager()->getAccessControlHandler('notification_type')->createAccess($type->type)) {
        $content[$type->type] = $type;
      }
    }

    // Bypass the notification_entity/add listing if only one content type is available.
    if (count($content) == 1) {
      $type = array_shift($content);
      return $this->redirect('notification_entity.add', array('notification_type' => $type->type));
    }

    return array(
      '#theme' => 'notification_entity_add_list',
      '#content' => $content,
    );
  }

  /**
   * Provides the notification_entity submission form.
   *
   * @param \Drupal\notification_entity\NotificationEntityTypeInterface $notification_type
   *   The notification type entity for the notification_entity.
   *
   * @return array
   *   A notification_entity submission form.
   */
  public function add(NotificationEntityTypeInterface $notification_type) {
    $notification_entity = $this->entityManager()->getStorage('notification_entity')->create(array(
      'type' => $notification_type->id(),
    ));

    $form = $this->entityFormBuilder()->getForm($notification_entity);
    return $form;
  }

  /**
   * The _title_callback for the notification_entity.add route.
   *
   * @param \Drupal\notification_entity\NotificationEntityTypeInterface $notification_type
   *   The current notification type.
   *
   * @return string
   *   The page title.
   */
  public function addTitle(NotificationEntityTypeInterface $notification_type) {
    return $this->t('Add @name notification', ['@name' => $notification_type->name]);
  }
}
