<?php

use \Drupal\notification_entity\Entity\NotificationEntity;

/**
 * @file
 * Hooks specific to the Notification push module.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Alter notification data before it gets pushed to the clients.
 *
 * @param array $context
 *   An array containing the data that will be sent. The array has the
 *   following keys:
 *   - excluded_fields: an array of notification field names that should not
 *     be sent to the client.
 *   - merge_data: an array of additional data to send to the client.
 * @param \Drupal\notification_entity\Entity\NotificationEntity $notification
 *   The notification entity object this notification is for.
 * @param string $action
 *   The action on the notification, one of create, update or delete.
 */
function hook_notification_push_context_alter(array &$context, NotificationEntity $notification, $action = 'create') {

}