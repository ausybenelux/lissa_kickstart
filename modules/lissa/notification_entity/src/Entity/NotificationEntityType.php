<?php

/**
 * @file
 * Contains \Drupal\notification_entity\Entity\NodeType.
 */

namespace Drupal\notification_entity\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\Core\Config\Entity\ThirdPartySettingsTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\notification_entity\NotificationEntityTypeInterface;

/**
 * Defines the Node type configuration entity.
 *
 * @ConfigEntityType(
 *   id = "notification_type",
 *   label = @Translation("Notification type"),
 *   handlers = {
 *     "access" = "Drupal\notification_entity\NotificationEntityTypeAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\notification_entity\NotificationEntityTypeForm",
 *       "edit" = "Drupal\notification_entity\NotificationEntityTypeForm",
 *       "delete" = "Drupal\notification_entity\NotificationEntityTypeDeleteConfirm"
 *     },
 *     "list_builder" = "Drupal\notification_entity\NotificationEntityTypeListBuilder"
 *   },
 *   admin_permission = "administer content types",
 *   config_prefix = "type",
 *   bundle_of = "notification_entity",
 *   entity_keys = {
 *     "id" = "type",
 *     "label" = "name"
 *   },
 *   links = {
 *     "edit-form" = "entity.notification_type.edit_form",
 *     "delete-form" = "entity.notification_type.delete_form"
 *   }
 * )
 */
class NotificationEntityType extends ConfigEntityBundleBase implements NotificationEntityTypeInterface {
  use ThirdPartySettingsTrait;

  /**
   * The machine name of this node type.
   *
   * @var string
   *
   * @todo Rename to $id.
   */
  public $type;

  /**
   * The human-readable name of the node type.
   *
   * @var string
   *
   * @todo Rename to $label.
   */
  public $name;

  /**
   * A brief description of this notification type.
   *
   * @var string
   */
  public $description;

  /**
   * Help information shown to the user when creating a notification of this type.
   *
   * @var string
   */
  public $help;

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->type;
  }

  /**
   * {@inheritdoc}
   */
  public function isLocked() {
    $locked = \Drupal::state()->get('node.type.locked');
    return isset($locked[$this->id()]) ? $locked[$this->id()] : FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);

    if ($update) {
      // Clear the cached field definitions as some settings affect the field
      // definitions.
      $this->entityManager()->clearCachedFieldDefinitions();
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function postDelete(EntityStorageInterface $storage, array $entities) {
    parent::postDelete($storage, $entities);

    // Clear the cache to reflect the removal.
    $storage->resetCache(array_keys($entities));
  }

}
